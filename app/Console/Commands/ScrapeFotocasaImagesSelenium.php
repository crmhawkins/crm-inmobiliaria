<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Facebook\WebDriver\Chrome\ChromeDriver;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Illuminate\Support\Facades\Log;

class ScrapeFotocasaImagesSelenium extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fotocasa:scrape-selenium {--limit=10 : Number of properties to process} {--dry-run : Test without saving}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrape images from Fotocasa using Selenium/ChromeDriver';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting Fotocasa image scraping with Selenium...');

        try {
            // Leer el archivo JSON
            $jsonPath = base_path('viviendas2_formateado.json');
            if (!file_exists($jsonPath)) {
                $this->error('JSON file not found at: ' . $jsonPath);
                return 1;
            }

            $jsonContent = file_get_contents($jsonPath);
            $viviendas = json_decode($jsonContent, true);

            if (!$viviendas) {
                $this->error('Error parsing JSON file');
                return 1;
            }

            $limit = $this->option('limit');
            $dryRun = $this->option('dry-run');

            $this->info("Found " . count($viviendas) . " properties in JSON");
            $this->info("Processing first {$limit} properties" . ($dryRun ? ' (DRY RUN)' : ''));

            // Configurar ChromeDriver
            $driver = $this->setupChromeDriver();

            if (!$driver) {
                $this->error('Failed to setup ChromeDriver');
                return 1;
            }

            $processed = 0;
            $errors = [];
            $updated = 0;

            $progressBar = $this->output->createProgressBar(min($limit, count($viviendas)));
            $progressBar->start();

            foreach ($viviendas as $id => &$vivienda) {
                if ($processed >= $limit) break;

                try {
                    $url = $vivienda['url'] ?? '';
                    if (empty($url)) {
                        $this->line("\n⚠️  No URL found for property {$id}");
                        continue;
                    }

                    $this->line("\n🔍 Processing: {$vivienda['titulo']}");
                    $this->line("   URL: {$url}");

                    // Extraer imágenes usando Selenium
                    $images = $this->scrapeImagesWithSelenium($driver, $url);

                    if (!empty($images)) {
                        $vivienda['images'] = $images;
                        $this->line("   ✅ Found " . count($images) . " images");
                        $updated++;
                    } else {
                        $this->line("   ❌ No images found");
                    }

                    $processed++;

                } catch (\Exception $e) {
                    $errors[] = [
                        'id' => $id,
                        'titulo' => $vivienda['titulo'] ?? 'Sin título',
                        'error' => $e->getMessage()
                    ];

                    $this->line("\n❌ Error processing " . ($vivienda['titulo'] ?? 'property') . ": {$e->getMessage()}");

                    Log::error('Error scraping images with Selenium', [
                        'id' => $id,
                        'url' => $vivienda['url'] ?? '',
                        'error' => $e->getMessage()
                    ]);
                }

                $progressBar->advance();

                // Pausa aleatoria entre peticiones
                if (!$dryRun) {
                    $sleepTime = rand(3, 6);
                    sleep($sleepTime);
                }
            }

            $progressBar->finish();

            // Cerrar el driver
            $driver->quit();

            // Guardar el JSON actualizado
            if (!$dryRun && $updated > 0) {
                $updatedJson = json_encode($viviendas, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                file_put_contents($jsonPath, $updatedJson);
                $this->newLine(2);
                $this->info("✅ JSON updated successfully!");
            }

            $this->newLine(2);
            $this->info("Scraping completed!");
            $this->info("✅ Successfully processed: {$processed} properties");
            $this->info("✅ Updated with images: {$updated} properties");

            if (count($errors) > 0) {
                $this->warn("❌ Errors: " . count($errors));
                foreach ($errors as $error) {
                    $this->line("  - {$error['titulo']}: {$error['error']}");
                }
            }

            return 0;

        } catch (\Exception $e) {
            $this->error('General error: ' . $e->getMessage());
            Log::error('Error in Selenium scraping command', [
                'error' => $e->getMessage()
            ]);
            return 1;
        }
    }

    /**
     * Configurar ChromeDriver
     */
    private function setupChromeDriver()
    {
        try {
            // Buscar ChromeDriver en diferentes ubicaciones
            $chromedriverPaths = [
                base_path('chromedriver.exe'), // En la carpeta del proyecto
                'C:\chromedriver\chromedriver.exe', // En C:\chromedriver\
                'chromedriver', // En PATH
            ];

            $chromedriverPath = null;
            foreach ($chromedriverPaths as $path) {
                if (file_exists($path)) {
                    $chromedriverPath = $path;
                    break;
                }
            }

            if (!$chromedriverPath) {
                $this->error("❌ ChromeDriver not found!");
                $this->error("Please download ChromeDriver from: https://chromedriver.chromium.org/");
                $this->error("And place chromedriver.exe in your project root folder");
                return null;
            }

            $this->info("✅ Found ChromeDriver at: " . $chromedriverPath);

            // Configurar opciones de Chrome
            $options = new ChromeOptions();
            $options->addArguments([
                '--no-sandbox',
                '--disable-dev-shm-usage',
                '--disable-gpu',
                '--disable-extensions',
                '--disable-plugins',
                '--disable-images', // No cargar imágenes para mayor velocidad
                '--disable-javascript', // Deshabilitar JavaScript si no es necesario
                '--headless', // Ejecutar en modo headless
                '--window-size=1920,1080',
                '--user-agent=Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
            ]);

            $capabilities = DesiredCapabilities::chrome();
            $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);

            // Usar solo el directorio donde está chromedriver.exe
            $chromedriverDir = dirname($chromedriverPath);
            $service = \Facebook\WebDriver\Chrome\ChromeDriverService::createDefaultService($chromedriverDir);

            // Intentar conectar al ChromeDriver
            $driver = ChromeDriver::start($capabilities, $service);

            $this->info("✅ ChromeDriver started successfully");
            return $driver;

        } catch (\Exception $e) {
            $this->error("❌ Failed to start ChromeDriver: " . $e->getMessage());
            $this->error("Make sure ChromeDriver is installed and in your PATH");
            $this->error("Download from: https://chromedriver.chromium.org/");
            return null;
        }
    }

    /**
     * Extraer imágenes usando Selenium
     */
    private function scrapeImagesWithSelenium($driver, $url)
    {
        try {
            // Navegar a la URL
            $driver->get($url);

            // Esperar a que la página cargue
            sleep(3);

            // Esperar a que aparezcan las imágenes
            try {
                $driver->wait(10)->until(
                    WebDriverExpectedCondition::presenceOfElementLocated(
                        WebDriverBy::cssSelector('section.re-DetailMosaic-grid')
                    )
                );
            } catch (\Exception $e) {
                $this->line("   ⚠️  Mosaic section not found, trying alternative selectors");
            }

            $images = [];

            // Buscar imágenes en la sección principal
            try {
                $mosaicSection = $driver->findElement(WebDriverBy::cssSelector('section.re-DetailMosaic-grid'));
                $pictureElements = $mosaicSection->findElements(WebDriverBy::cssSelector('picture.re-DetailMosaicPhotoWrapper'));

                $this->line("   Found " . count($pictureElements) . " picture elements");

                foreach ($pictureElements as $picture) {
                    try {
                        $img = $picture->findElement(WebDriverBy::cssSelector('img.re-DetailMosaicPhoto'));
                        $src = $img->getAttribute('src');

                        if (!empty($src)) {
                            $originalUrl = $this->convertToOriginalUrl($src);
                            $images[] = $originalUrl;
                        }
                    } catch (\Exception $e) {
                        // Continuar con el siguiente elemento
                    }
                }

            } catch (\Exception $e) {
                $this->line("   ⚠️  Could not find mosaic section");
            }

            // Si no encontramos imágenes, buscar en toda la página
            if (empty($images)) {
                try {
                    $allImages = $driver->findElements(WebDriverBy::cssSelector('img[src*="static.fotocasa.es/images/ads/"]'));
                    $this->line("   Found " . count($allImages) . " Fotocasa images");

                    foreach ($allImages as $img) {
                        $src = $img->getAttribute('src');
                        if (!empty($src)) {
                            $originalUrl = $this->convertToOriginalUrl($src);
                            $images[] = $originalUrl;
                        }
                    }
                } catch (\Exception $e) {
                    $this->line("   ⚠️  Could not find any Fotocasa images");
                }
            }

            // Eliminar duplicados y limitar a máximo 10 imágenes
            $images = array_unique($images);
            $images = array_slice($images, 0, 10);

            return $images;

        } catch (\Exception $e) {
            Log::warning('Error scraping images with Selenium', [
                'url' => $url,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Convertir URL de imagen a formato original
     */
    private function convertToOriginalUrl($url)
    {
        // Si ya es una URL original, devolverla tal como está
        if (strpos($url, '?rule=original') !== false) {
            return $url;
        }

        // Si es una URL con regla específica, convertirla a original
        if (preg_match('/^(https:\/\/static\.fotocasa\.es\/images\/ads\/[a-f0-9-]+)/', $url, $matches)) {
            return $matches[1] . '?rule=original';
        }

        return $url;
    }
}
