import json
import re


def convert_image_urls():
    # Leer el archivo imagenes.json
    try:
        with open('imagenes.json', 'r', encoding='utf-8') as f:
            data = json.load(f)
        print(f"✅ Archivo imagenes.json cargado correctamente")
    except FileNotFoundError:
        print("❌ Error: No se encontró el archivo imagenes.json")
        return
    except json.JSONDecodeError as e:
        print(f"❌ Error al parsear JSON: {e}")
        return

    # Contador para estadísticas
    total_urls = 0
    converted_urls = 0

    # Función para convertir URL
    def convert_url(url):
        if not url or not isinstance(url, str):
            return url

        # Buscar URLs que contengan rule= y cambiar el valor por original
        if 'rule=' in url:
            # Reemplazar cualquier valor después de rule= por original
            converted = re.sub(r'\?rule=[^&]+', '?rule=original', url)
            # También manejar casos donde rule= está en medio de la URL
            converted = re.sub(r'&rule=[^&]+', '&rule=original', converted)
            if converted != url:
                return converted

        return url

    # Procesar el JSON
    def process_data(obj):
        nonlocal total_urls, converted_urls

        if isinstance(obj, dict):
            for key, value in obj.items():
                if isinstance(value, str) and 'rule=' in value:
                    total_urls += 1
                    original_url = value
                    converted_url = convert_url(value)
                    if converted_url != original_url:
                        obj[key] = converted_url
                        converted_urls += 1
                        print(f"   🔄 Convertida: {original_url} → {converted_url}")
                elif isinstance(value, (dict, list)):
                    process_data(value)
        elif isinstance(obj, list):
            for item in obj:
                if isinstance(item, str) and 'rule=' in item:
                    total_urls += 1
                    original_url = item
                    converted_url = convert_url(item)
                    if converted_url != original_url:
                        # En listas, necesitamos reemplazar el elemento
                        index = obj.index(item)
                        obj[index] = converted_url
                        converted_urls += 1
                        print(f"   🔄 Convertida: {original_url} → {converted_url}")
                elif isinstance(item, (dict, list)):
                    process_data(item)

    print("🔄 Procesando URLs...")
    process_data(data)

    # Guardar el resultado
    try:
        with open('imagenes_original.json', 'w', encoding='utf-8') as f:
            json.dump(data, f, ensure_ascii=False, indent=2)
        print(f"✅ Archivo guardado como imagenes_original.json")
    except Exception as e:
        print(f"❌ Error al guardar archivo: {e}")
        return

    # Mostrar estadísticas
    print(f"\n📊 Estadísticas:")
    print(f"   Total de URLs procesadas: {total_urls}")
    print(f"   URLs convertidas: {converted_urls}")
    print(f"   URLs sin cambios: {total_urls - converted_urls}")

if __name__ == "__main__":
    print("🚀 Iniciando conversión de URLs de imágenes...")
    convert_image_urls()
    print("✅ Proceso completado!")
