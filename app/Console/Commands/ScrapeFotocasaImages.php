<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use DOMDocument;
use DOMXPath;

class ScrapeFotocasaImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fotocasa:scrape-images {--limit=10 : Number of properties to process} {--dry-run : Test without saving}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrape images from Fotocasa property pages and add them to JSON';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting Fotocasa image scraping...');

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

            $limit = 100;
            $dryRun = $this->option('dry-run');

            $this->info("Found " . count($viviendas) . " properties in JSON");
            $this->info("Processing first {$limit} properties" . ($dryRun ? ' (DRY RUN)' : ''));

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

                    // Extraer imágenes de la página
                    $images = $this->scrapeImagesFromUrl($url);

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

                    Log::error('Error scraping images', [
                        'id' => $id,
                        'url' => $vivienda['url'] ?? '',
                        'error' => $e->getMessage()
                    ]);
                }

                $progressBar->advance();

                // Pausa aleatoria para no sobrecargar el servidor (entre 2-4 segundos)
                if (!$dryRun) {
                    $sleepTime = rand(2, 4);
                    sleep($sleepTime);
                }
            }

            $progressBar->finish();

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
            Log::error('Error in scraping command', [
                'error' => $e->getMessage()
            ]);
            return 1;
        }
    }

    /**
     * Extraer imágenes de una URL de Fotocasa
     */
    private function scrapeImagesFromUrl($url)
    {
        try {
            // Rotación de User-Agents para evitar detección
            $userAgents = [
                'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36',
                'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/121.0',
                'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.1 Safari/605.1.15'
            ];

            $randomUserAgent = $userAgents[array_rand($userAgents)];

            $response = Http::withOptions([
                'verify' => false,
                'timeout' => 30,
                'headers' => [
                    'User-Agent' => $randomUserAgent,
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
                    'Accept-Language' => 'es-ES,es;q=0.9,en;q=0.8',
                    'Accept-Encoding' => 'gzip, deflate, br',
                    'DNT' => '1',
                    'Connection' => 'keep-alive',
                    'Upgrade-Insecure-Requests' => '1',
                    'Sec-Fetch-Dest' => 'document',
                    'Sec-Fetch-Mode' => 'navigate',
                    'Sec-Fetch-Site' => 'none',
                    'Sec-Fetch-User' => '?1',
                    'Cache-Control' => 'max-age=0',
                    'sec-ch-ua' => '"Not_A Brand";v="8", "Chromium";v="120", "Google Chrome";v="120"',
                    'sec-ch-ua-mobile' => '?0',
                    'sec-ch-ua-platform' => '"Windows"'
                ]
            ])->get($url);

            if (!$response->successful()) {
                throw new \Exception("HTTP Error: " . $response->status());
            }

            $html = $response->body();

            // Debug: mostrar las primeras líneas del HTML para verificar que se está cargando
            $this->line("   HTML length: " . strlen($html));

            // Si el HTML es muy pequeño, probablemente es una página de bloqueo
            if (strlen($html) < 50000) {
                $this->line("   ⚠️  HTML too small, might be blocked");
                return [];
            }

            // Crear DOM
            $dom = new DOMDocument();
            @$dom->loadHTML($html, LIBXML_NOERROR | LIBXML_NOWARNING);
            $xpath = new DOMXPath($dom);

            $images = [];

            // Buscar la sección de imágenes principal
            $mosaicSection = $xpath->query("//section[contains(@class, 're-DetailMosaic-grid')]");
            $this->line("   Found mosaic sections: " . $mosaicSection->length);

            if ($mosaicSection->length > 0) {
                // Extraer imágenes de la sección principal - buscar dentro de picture elements
                $pictureElements = $xpath->query(".//picture[contains(@class, 're-DetailMosaicPhotoWrapper')]", $mosaicSection->item(0));
                $this->line("   Found picture elements: " . $pictureElements->length);

                foreach ($pictureElements as $picture) {
                    // Buscar la imagen dentro del picture
                    $img = $xpath->query(".//img[contains(@class, 're-DetailMosaicPhoto')]", $picture)->item(0);
                    if ($img) {
                        $src = $img->getAttribute('src');
                        if (!empty($src)) {
                            // Convertir a URL original si es necesario
                            $originalUrl = $this->convertToOriginalUrl($src);
                            $images[] = $originalUrl;
                        }
                    }
                }

                // Si no encontramos imágenes en picture, buscar directamente img
                if (empty($images)) {
                    $imageElements = $xpath->query(".//img[contains(@class, 're-DetailMosaicPhoto')]", $mosaicSection->item(0));
                    $this->line("   Found direct img elements: " . $imageElements->length);

                    foreach ($imageElements as $img) {
                        $src = $img->getAttribute('src');
                        if (!empty($src)) {
                            $originalUrl = $this->convertToOriginalUrl($src);
                            $images[] = $originalUrl;
                        }
                    }
                }
            }

            // Si no encontramos imágenes en la sección principal, buscar en toda la página
            if (empty($images)) {
                // Buscar todas las imágenes de Fotocasa en la página
                $allImages = $xpath->query("//img[contains(@src, 'static.fotocasa.es/images/ads/')]");
                $this->line("   Found all Fotocasa images: " . $allImages->length);

                foreach ($allImages as $img) {
                    $src = $img->getAttribute('src');
                    if (!empty($src)) {
                        $originalUrl = $this->convertToOriginalUrl($src);
                        $images[] = $originalUrl;
                    }
                }
            }

            // También buscar en elementos picture que contengan imágenes de Fotocasa
            if (empty($images)) {
                $allPictures = $xpath->query("//picture");
                $this->line("   Found all picture elements: " . $allPictures->length);

                foreach ($allPictures as $picture) {
                    $img = $xpath->query(".//img[contains(@src, 'static.fotocasa.es/images/ads/')]", $picture)->item(0);
                    if ($img) {
                        $src = $img->getAttribute('src');
                        if (!empty($src)) {
                            $originalUrl = $this->convertToOriginalUrl($src);
                            $images[] = $originalUrl;
                        }
                    }
                }
            }

            // Buscar cualquier imagen que contenga 'fotocasa' en la URL
            if (empty($images)) {
                $fotocasaImages = $xpath->query("//img[contains(@src, 'fotocasa')]");
                $this->line("   Found any Fotocasa images: " . $fotocasaImages->length);

                foreach ($fotocasaImages as $img) {
                    $src = $img->getAttribute('src');
                    if (!empty($src)) {
                        $images[] = $src;
                    }
                }
            }

            // Eliminar duplicados y limitar a máximo 10 imágenes
            $images = array_unique($images);
            $images = array_slice($images, 0, 10);

            return $images;

        } catch (\Exception $e) {
            Log::warning('Error scraping images from URL', [
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
