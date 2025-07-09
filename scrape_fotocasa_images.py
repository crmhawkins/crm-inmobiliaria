import json
import time

from selenium import webdriver
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.common.by import By
from tqdm import tqdm

# Configuración de Selenium
chrome_options = Options()
chrome_options.add_argument("--headless")
chrome_options.add_argument("--window-size=1920,1080")
chrome_options.add_argument("--disable-gpu")
chrome_options.add_argument("--no-sandbox")
chrome_options.add_argument("--disable-dev-shm-usage")
chrome_options.add_argument("--user-agent=Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36")

# Cambia el path si chromedriver.exe no está en el PATH ni en la carpeta actual
# driver = webdriver.Chrome(executable_path='chromedriver.exe', options=chrome_options)
driver = webdriver.Chrome(options=chrome_options)

# Cargar el JSON
with open('viviendas2_formateado.json', encoding='utf-8') as f:
    viviendas = json.load(f)

for key in tqdm(viviendas, desc="Procesando propiedades"):
    url = viviendas[key].get('url')
    if not url:
        continue

    try:
        driver.get(url)
        time.sleep(3)  # Espera a que cargue la página

        # Buscar imágenes en el mosaico principal
        images = []
        try:
            mosaic_section = driver.find_element(By.CSS_SELECTOR, 'section.re-DetailMosaic-grid')
            picture_elements = mosaic_section.find_elements(By.CSS_SELECTOR, 'picture.re-DetailMosaicPhotoWrapper img.re-DetailMosaicPhoto')
            for img in picture_elements:
                src = img.get_attribute('src')
                if src and src not in images:
                    # Convertir a original si es necesario
                    if '?rule=original' not in src and 'static.fotocasa.es/images/ads/' in src:
                        src = src.split('?')[0] + '?rule=original'
                    images.append(src)
        except Exception:
            pass

        # Si no encuentra, buscar imágenes generales
        if not images:
            all_imgs = driver.find_elements(By.CSS_SELECTOR, 'img')
            for img in all_imgs:
                src = img.get_attribute('src')
                if src and 'static.fotocasa.es/images/ads/' in src and src not in images:
                    if '?rule=original' not in src:
                        src = src.split('?')[0] + '?rule=original'
                    images.append(src)

        viviendas[key]['images'] = images[:10]  # Máximo 10 imágenes

    except Exception as e:
        viviendas[key]['images'] = []
        print(f"Error en {url}: {e}")

    time.sleep(2 + (time.time() % 2))  # Espera aleatoria

driver.quit()

# Guardar el JSON actualizado
with open('viviendas2_formateado_con_imagenes.json', 'w', encoding='utf-8') as f:
    json.dump(viviendas, f, ensure_ascii=False, indent=2)

print("Scraping completado. Resultado en viviendas2_formateado_con_imagenes.json")
