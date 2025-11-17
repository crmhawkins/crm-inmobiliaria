<?php

namespace App\Console\Commands;

use App\Services\Idealista\IdealistaApiService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class IdealistaDumpEndpoints extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'idealista:dump-endpoints
        {--dir=idealista : Carpeta relativa en storage/app donde guardar los XML}
        {--propertyId= : Opcional, propertyId a consultar en detalle}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Consume los endpoints clave de Idealista y almacena las respuestas en XML';

    public function __construct(private readonly IdealistaApiService $api)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $directory = trim($this->option('dir'));
        $propertyId = $this->option('propertyId');

        $dataset = [
            'properties_list' => ['method' => 'GET', 'endpoint' => '/v1/properties', 'query' => ['page' => 1, 'size' => 5]],
            'contacts_list' => ['method' => 'GET', 'endpoint' => '/v1/contacts', 'query' => ['page' => 1, 'size' => 5]],
            'publish_info' => ['method' => 'GET', 'endpoint' => '/v1/customer/publishinfo'],
        ];

        if ($propertyId) {
            $dataset['property_detail'] = [
                'method' => 'GET',
                'endpoint' => "/v1/properties/{$propertyId}",
            ];
            $dataset['property_images'] = [
                'method' => 'GET',
                'endpoint' => "/v1/properties/{$propertyId}/images",
            ];
            $dataset['property_videos'] = [
                'method' => 'GET',
                'endpoint' => "/v1/properties/{$propertyId}/videos",
            ];
            $dataset['property_virtual_tours'] = [
                'method' => 'GET',
                'endpoint' => "/v1/properties/{$propertyId}/virtualtours",
            ];
        }

        $success = true;
        foreach ($dataset as $label => $config) {
            $this->line("→ Consultando {$config['endpoint']} ({$label})");

            try {
                $response = $this->api->call(
                    $config['method'],
                    $config['endpoint'],
                    $config['query'] ?? [],
                    $config['body'] ?? null
                );
            } catch (Throwable $exception) {
                $this->error("   ✗ Error: ".$exception->getMessage());
                $success = false;
                continue;
            }

            $xml = $this->toXml($response, rootName: Str::snake($label));
            $path = $directory.'/'.Str::snake($label).'.xml';

            Storage::put($path, $xml);
            $this->info("   ✓ Guardado en storage/app/{$path}");
        }

        return $success ? self::SUCCESS : self::FAILURE;
    }

    private function toXml(array $data, string $rootName = 'idealista'): string
    {
        $xml = new \SimpleXMLElement(sprintf('<?xml version="1.0" encoding="UTF-8"?><%s/>', $rootName));
        $this->appendArray($xml, $data);

        $dom = dom_import_simplexml($xml)->ownerDocument;
        $dom->formatOutput = true;

        return $dom->saveXML();
    }

    private function appendArray(\SimpleXMLElement $xml, array $data): void
    {
        foreach ($data as $key => $value) {
            $nodeName = $this->sanitizeNodeName($key);

            if (is_array($value)) {
                $child = $xml->addChild($nodeName);
                $this->appendArray($child, $value);
                continue;
            }

            $xml->addChild($nodeName, htmlspecialchars((string) $value));
        }
    }

    private function sanitizeNodeName(string|int $key): string
    {
        if (is_numeric($key)) {
            return 'item';
        }

        $sanitized = preg_replace('/[^A-Za-z0-9_-]+/', '_', $key) ?: 'item';

        if (preg_match('/^[0-9]/', $sanitized)) {
            $sanitized = 'n_'.$sanitized;
        }

        return strtolower($sanitized);
    }
}

