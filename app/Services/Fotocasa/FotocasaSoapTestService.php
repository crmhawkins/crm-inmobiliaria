<?php

namespace App\Services\Fotocasa;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Output\OutputInterface;

class FotocasaSoapTestService
{
    private const SOAP_NAMESPACE = 'http://ws.fotocasa.es/';
    private const SOAP_URL = 'http://ws.fotocasa.es/mobile/api/v3.asmx';

    private ?OutputInterface $output = null;

    public function setOutput(OutputInterface $output): void
    {
        $this->output = $output;
    }

    private function line(string $message): void
    {
        if ($this->output) {
            $this->output->writeln($message);
        }
    }

    private function info(string $message): void
    {
        if ($this->output) {
            $this->output->writeln('<info>' . $message . '</info>');
        }
    }

    private function warn(string $message): void
    {
        if ($this->output) {
            $this->output->writeln('<comment>' . $message . '</comment>');
        }
    }

    private function error(string $message): void
    {
        if ($this->output) {
            $this->output->writeln('<error>' . $message . '</error>');
        }
    }

    public function testGetWsdl(string $wsdlUrl, ?string $apiKey = null): void
    {
        try {
            $this->line("  URL: {$wsdlUrl}");
            $request = Http::withOptions([
                'verify' => false,
                'timeout' => 30,
            ]);

            if ($apiKey) {
                $request = $request->withHeaders(['api-key' => $apiKey]);
            }

            $response = $request->get($wsdlUrl);

            $status = $response->status();
            $this->line("  Status: {$status}");

            if ($response->successful()) {
                $this->info('  ✅ WSDL obtenido correctamente');
                $wsdl = $response->body();
                $this->line("  Tamaño: " . strlen($wsdl) . " bytes");

                if (strpos($wsdl, 'GetProperty') !== false) {
                    $this->info('  ✅ Método GetProperty encontrado en WSDL');
                }

                if (preg_match('/targetNamespace="([^"]+)"/', $wsdl, $matches)) {
                    $this->line("  Namespace: {$matches[1]}");
                }

                Log::info('Fotocasa SOAP WSDL obtenido', [
                    'url' => $wsdlUrl,
                    'size' => strlen($wsdl),
                ]);
            } else {
                $this->error('  ❌ Error al obtener WSDL: ' . substr($response->body(), 0, 200));
            }
        } catch (\Exception $e) {
            $this->error('  ❌ Excepción: ' . $e->getMessage());
        }
    }

    public function getAvailableExternalIds(string $apiKey): array
    {
        try {
            $client = app(FotocasaClient::class);
            $properties = $client->getProperties();

            if (is_array($properties) && !empty($properties)) {
                $identifiers = [];
                foreach ($properties as $prop) {
                    if (isset($prop['ExternalId'])) {
                        $identifiers[] = [
                            'type' => 'ExternalId',
                            'value' => (string)$prop['ExternalId'],
                            'agency_ref' => $prop['AgencyReference'] ?? null,
                        ];
                    }
                }
                return $identifiers;
            }
        } catch (\Exception $e) {
            $this->warn("  Error obteniendo lista: " . $e->getMessage());
        }

        return [];
    }

    public function testGetProperty(string $url, string $externalId, ?string $apiKey = null): bool
    {
        $namespace = self::SOAP_NAMESPACE;

        $this->line("  Probando: Con TODOS los parámetros requeridos según documentación SOAP");
        $found = $this->testGetPropertyWithAllRequiredParams($url, $externalId, $apiKey, $namespace);
        if ($found) return true;

        $this->line("  Probando: HTTP GET según documentación");
        $found = $this->testGetPropertyHttpGet($url, $externalId, $apiKey);
        if ($found) return true;

        $this->line("  Probando: HTTP POST según documentación");
        $found = $this->testGetPropertyHttpPost($url, $externalId, $apiKey);
        if ($found) return true;

        if ($apiKey) {
            $this->line("  Probando: Solo propertyId con API key en header");
            $found = $this->testGetPropertyWithApiKeyHeaderSpecific($url, $externalId, $apiKey, $namespace, 'propertyId');
            if ($found) return true;
        }

        $this->line("  Probando: propertyId + languageId");
        $found = $this->testGetPropertyWithMinParams($url, $externalId, $apiKey, $namespace);
        if ($found) return true;

        return false;
    }

    public function testGetProductsSoap(string $soapUrl, string $apiKey): bool
    {
        $this->info('📋 Test alternativo: Probando GetProducts SOAP (v2)');
        $getProductsUrl = str_replace('/v3.asmx', '/v2.asmx?op=GetProducts', $soapUrl);
        $namespace = self::SOAP_NAMESPACE;

        $envelope = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ws="' . htmlspecialchars($namespace) . '">
    <soap:Header>
        <ws:ApiKey>' . htmlspecialchars($apiKey) . '</ws:ApiKey>
    </soap:Header>
    <soap:Body>
        <ws:GetProducts/>
    </soap:Body>
</soap:Envelope>';

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'text/xml; charset=utf-8',
                'SOAPAction' => $namespace . 'GetProducts',
            ])->withOptions([
                'verify' => false,
                'timeout' => 30,
            ])->withBody($envelope, 'text/xml')
            ->post($getProductsUrl);

            $this->line("  Status: {$response->status()}");

            if ($response->successful()) {
                $body = $response->body();

                $hasCompleteData = strpos($body, 'PropertyFeature') !== false ||
                                  strpos($body, 'PropertyAddress') !== false ||
                                  strpos($body, 'PropertyTransaction') !== false ||
                                  strpos($body, 'TypeId') !== false ||
                                  (strpos($body, 'Description') !== false && strlen($body) > 1000) ||
                                  (strpos($body, 'Title') !== false && strlen($body) > 1000);

                if ($hasCompleteData) {
                    $this->info("  ✅✅ DATOS COMPLETOS ENCONTRADOS en GetProducts!");
                    $this->displayPropertyDataFromXml($body);

                    if (preg_match('/<Property[^>]*>(.*?)<\/Property>/s', $body, $matches)) {
                        $this->line("  Primera propiedad encontrada:");
                        $this->displayPropertyDataFromXml($matches[0]);
                    }

                    Log::info('Fotocasa SOAP GetProducts - DATOS COMPLETOS', [
                        'response_body' => $body,
                    ]);

                    return true;
                } else {
                    $this->warn("  ⚠️  GetProducts responde pero sin datos completos detectados");
                    $this->line("  Tamaño respuesta: " . strlen($body) . " bytes");
                    if (strlen($body) > 500) {
                        $this->line("  Primeros 500 caracteres: " . substr($body, 0, 500));
                    }
                }
            } else {
                $this->error("  ❌ Error: " . $response->status());
                $fault = $this->extractSoapFault($response->body());
                if ($fault) {
                    $this->error("  SOAP Fault: {$fault}");
                }
            }
        } catch (\Exception $e) {
            $this->error("  ❌ Excepción: " . $e->getMessage());
        }

        return false;
    }

    private function testGetPropertyWithAllRequiredParams(string $url, string $externalId, ?string $apiKey, string $namespace): bool
    {
        $inmueble = \App\Models\Inmuebles::where('external_id', $externalId)->first();
        if (!$inmueble) {
            $inmueble = \App\Models\Inmuebles::find($externalId);
        }
        $latitude = $inmueble->latitude ?? 40.4168;
        $longitude = $inmueble->longitude ?? -3.7038;

        $propertyId = (int)$externalId;
        $languageId = 1;
        $transactionTypeIds = [1, 2];
        $periodicityIds = [1, 2, 3];
        $signatures = [
            '',
            $apiKey ?? '',
            hash('md5', $propertyId . ($apiKey ?? '')),
            hash('sha1', $propertyId . ($apiKey ?? '')),
            hash('sha256', $propertyId . ($apiKey ?? '')),
        ];

        foreach ($transactionTypeIds as $transactionTypeId) {
            foreach ($periodicityIds as $periodicityId) {
                foreach ($signatures as $signature) {
                    $envelope = $this->buildGetPropertyEnvelopeWithAllParams(
                        $propertyId,
                        $longitude,
                        $latitude,
                        $languageId,
                        $transactionTypeId,
                        $periodicityId,
                        $signature,
                        $apiKey,
                        $namespace
                    );

                    try {
                        $response = Http::withHeaders([
                            'Content-Type' => 'text/xml; charset=utf-8',
                            'SOAPAction' => '"' . $namespace . 'GetProperty"',
                            'api-key' => $apiKey ?? '',
                        ])->withOptions([
                            'verify' => false,
                            'timeout' => 30,
                        ])->withBody($envelope, 'text/xml')
                        ->post($url);

                        $sigPreview = $signature ? substr($signature, 0, 10) . '...' : 'vacío';
                        $desc = "Todos los parámetros (transactionTypeId: {$transactionTypeId}, periodicityId: {$periodicityId}, signature: {$sigPreview})";
                        $found = $this->processSoapResponse($response, $desc);
                        if ($found) return true;
                    } catch (\Exception $e) {
                        continue;
                    }
                }
            }
        }

        return false;
    }

    private function buildGetPropertyEnvelopeWithAllParams(
        int $propertyId,
        float $longitude,
        float $latitude,
        int $languageId,
        int $transactionTypeId,
        int $periodicityId,
        string $signature,
        ?string $apiKey,
        string $namespace
    ): string {
        $envelope = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
    <soap:Body>
        <GetProperty xmlns="' . htmlspecialchars($namespace) . '">
            <propertyId>' . $propertyId . '</propertyId>
            <longitude>' . $longitude . '</longitude>
            <latitude>' . $latitude . '</latitude>
            <languageId>' . $languageId . '</languageId>
            <transactionTypeId>' . $transactionTypeId . '</transactionTypeId>
            <periodicityId>' . $periodicityId . '</periodicityId>
            <signature>' . htmlspecialchars($signature) . '</signature>
        </GetProperty>
    </soap:Body>
</soap:Envelope>';

        return $envelope;
    }

    private function testGetPropertyWithMinParams(string $url, string $externalId, ?string $apiKey, string $namespace): bool
    {
        $propertyId = (int)$externalId;
        $languageId = 1;

        $envelope = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ws="' . htmlspecialchars($namespace) . '">
    <soap:Header>';

        if ($apiKey) {
            $envelope .= '
        <ws:ApiKey>' . htmlspecialchars($apiKey) . '</ws:ApiKey>';
        }

        $envelope .= '
    </soap:Header>
    <soap:Body>
        <ws:GetProperty>
            <ws:propertyId>' . $propertyId . '</ws:propertyId>
            <ws:languageId>' . $languageId . '</ws:languageId>
        </ws:GetProperty>
    </soap:Body>
</soap:Envelope>';

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'text/xml; charset=utf-8',
                'SOAPAction' => $namespace . 'GetProperty',
                'api-key' => $apiKey ?? '',
            ])->withOptions([
                'verify' => false,
                'timeout' => 30,
            ])->withBody($envelope, 'text/xml')
            ->post($url);

            return $this->processSoapResponse($response, "propertyId + languageId");
        } catch (\Exception $e) {
            $this->line("    ❌ Error: " . substr($e->getMessage(), 0, 60));
            return false;
        }
    }

    private function testGetPropertyWithApiKeyHeaderSpecific(string $url, string $externalId, string $apiKey, string $namespace, string $paramName): bool
    {
        $envelope = $this->buildGetPropertyEnvelopeWithParam($paramName, $externalId, null, $namespace);

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'text/xml; charset=utf-8',
                'SOAPAction' => $namespace . 'GetProperty',
                'api-key' => $apiKey,
            ])->withOptions([
                'verify' => false,
                'timeout' => 30,
            ])->withBody($envelope, 'text/xml')
            ->post($url);

            return $this->processSoapResponse($response, "{$paramName} con API key en header");
        } catch (\Exception $e) {
            $this->line("    ❌ Error: " . substr($e->getMessage(), 0, 60));
            return false;
        }
    }

    private function buildGetPropertyEnvelopeWithParam(string $paramName, string $paramValue, ?string $apiKey = null, string $namespace = 'http://ws.fotocasa.es/'): string
    {
        $envelope = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ws="' . htmlspecialchars($namespace) . '">
    <soap:Header>';

        if ($apiKey) {
            $envelope .= '
        <ws:ApiKey>' . htmlspecialchars($apiKey) . '</ws:ApiKey>';
        }

        $envelope .= '
    </soap:Header>
    <soap:Body>
        <ws:GetProperty>
            <ws:' . htmlspecialchars($paramName) . '>' . htmlspecialchars($paramValue) . '</ws:' . htmlspecialchars($paramName) . '>
        </ws:GetProperty>
    </soap:Body>
</soap:Envelope>';

        return $envelope;
    }

    private function testGetPropertyHttpGet(string $baseUrl, string $externalId, ?string $apiKey): bool
    {
        $inmueble = \App\Models\Inmuebles::where('external_id', $externalId)->first();
        if (!$inmueble) {
            $inmueble = \App\Models\Inmuebles::find($externalId);
        }
        $latitude = $inmueble->latitude ?? 40.4168;
        $longitude = $inmueble->longitude ?? -3.7038;

        $propertyId = (int)$externalId;
        $languageId = 1;
        $transactionTypeIds = [1, 2];
        $periodicityIds = [1, 2, 3];
        $signatures = [
            '',
            $apiKey ?? '',
            hash('md5', $propertyId . ($apiKey ?? '')),
            hash('sha1', $propertyId . ($apiKey ?? '')),
        ];

        $getPropertyUrl = $baseUrl . '/GetProperty';

        foreach ($transactionTypeIds as $transactionTypeId) {
            foreach ($periodicityIds as $periodicityId) {
                foreach ($signatures as $signature) {
                    try {
                        $queryParams = [
                            'propertyId' => $propertyId,
                            'longitude' => $longitude,
                            'latitude' => $latitude,
                            'languageId' => $languageId,
                            'transactionTypeId' => $transactionTypeId,
                            'periodicityId' => $periodicityId,
                            'signature' => $signature,
                        ];

                        $response = Http::withHeaders([
                            'api-key' => $apiKey ?? '',
                        ])->withOptions([
                            'verify' => false,
                            'timeout' => 30,
                        ])->get($getPropertyUrl, $queryParams);

                        $sigPreview = $signature ? substr($signature, 0, 10) . '...' : 'vacío';
                        $desc = "HTTP GET (transactionTypeId: {$transactionTypeId}, periodicityId: {$periodicityId}, signature: {$sigPreview})";
                        $found = $this->processSoapResponse($response, $desc);
                        if ($found) return true;
                    } catch (\Exception $e) {
                        continue;
                    }
                }
            }
        }

        return false;
    }

    private function testGetPropertyHttpPost(string $baseUrl, string $externalId, ?string $apiKey): bool
    {
        $inmueble = \App\Models\Inmuebles::where('external_id', $externalId)->first();
        if (!$inmueble) {
            $inmueble = \App\Models\Inmuebles::find($externalId);
        }
        $latitude = $inmueble->latitude ?? 40.4168;
        $longitude = $inmueble->longitude ?? -3.7038;

        $propertyId = (int)$externalId;
        $languageId = 1;
        $transactionTypeIds = [1, 2];
        $periodicityIds = [1, 2, 3];
        $signatures = [
            '',
            $apiKey ?? '',
            hash('md5', $propertyId . ($apiKey ?? '')),
            hash('sha1', $propertyId . ($apiKey ?? '')),
        ];

        $getPropertyUrl = $baseUrl . '/GetProperty';

        foreach ($transactionTypeIds as $transactionTypeId) {
            foreach ($periodicityIds as $periodicityId) {
                foreach ($signatures as $signature) {
                    try {
                        $formData = [
                            'propertyId' => (string)$propertyId,
                            'longitude' => (string)$longitude,
                            'latitude' => (string)$latitude,
                            'languageId' => (string)$languageId,
                            'transactionTypeId' => (string)$transactionTypeId,
                            'periodicityId' => (string)$periodicityId,
                            'signature' => $signature,
                        ];

                        $response = Http::asForm()->withHeaders([
                            'api-key' => $apiKey ?? '',
                        ])->withOptions([
                            'verify' => false,
                            'timeout' => 30,
                        ])->post($getPropertyUrl, $formData);

                        $sigPreview = $signature ? substr($signature, 0, 10) . '...' : 'vacío';
                        $desc = "HTTP POST (transactionTypeId: {$transactionTypeId}, periodicityId: {$periodicityId}, signature: {$sigPreview})";
                        $found = $this->processSoapResponse($response, $desc);
                        if ($found) return true;
                    } catch (\Exception $e) {
                        continue;
                    }
                }
            }
        }

        return false;
    }

    private function processSoapResponse($response, string $method): bool
    {
        $status = $response->status();
        $body = $response->body();

        $this->line("      Status: {$status}");

        if ($response->successful()) {
            $isEmpty = trim($body) === '' ||
                       preg_match('/^<\?xml[^>]*>\s*<(soap:Envelope|soap12:Envelope|GetPropertyResponse|Advertisment)[^>]*\s*\/?>\s*$/s', $body) ||
                       preg_match('/<GetPropertyResponse[^>]*>\s*<\s*\/GetPropertyResponse>/s', $body) ||
                       preg_match('/<Advertisment[^>]*>\s*<\s*\/Advertisment>/s', $body);

            $hasCompleteData = !$isEmpty && (
                strpos($body, 'PropertyFeature') !== false ||
                strpos($body, 'PropertyAddress') !== false ||
                strpos($body, 'PropertyTransactionType') !== false ||
                strpos($body, 'GetPropertyResult') !== false ||
                (strpos($body, '<Title>') !== false && strpos($body, '</Title>') !== false) ||
                (strpos($body, '<Description>') !== false && strpos($body, '</Description>') !== false && strlen($body) > 500) ||
                (strpos($body, '<NRooms>') !== false) ||
                (strpos($body, '<NBathrooms>') !== false) ||
                (strpos($body, '<Surface>') !== false) ||
                (strpos($body, '<Price>') !== false && strpos($body, '</Price>') !== false) ||
                (strpos($body, '<ZipCode>') !== false) ||
                (strpos($body, '<Street>') !== false) ||
                (strpos($body, '<CategoryId>') !== false) ||
                (strpos($body, '<SubcategoryId>') !== false) ||
                (strpos($body, '<MediaList>') !== false) ||
                (strpos($body, '<Descriptions>') !== false) ||
                (strpos($body, '<Prices>') !== false) ||
                strpos($body, '<Advertisment') !== false ||
                (preg_match('/<[a-zA-Z]+[^>]*>.*?<\/[a-zA-Z]+>/', $body) && strlen($body) > 500)
            );

            if ($hasCompleteData) {
                $this->info("      ✅✅ DATOS COMPLETOS ENCONTRADOS con método: {$method}!");
                $this->displayPropertyDataFromXml($body);

                Log::info('Fotocasa SOAP GetProperty - DATOS COMPLETOS ENCONTRADOS', [
                    'method' => $method,
                    'status' => $status,
                    'response_body' => $body,
                ]);

                return true;
            } else {
                $this->warn("      ⚠️  Respuesta vacía o sin datos completos");
                $this->line("      Tamaño respuesta: " . strlen($body) . " bytes");
                if (strlen($body) > 200) {
                    $this->line("      Respuesta completa:");
                    $this->line("      " . $body);
                } else {
                    $this->line("      Respuesta: " . $body);
                }

                Log::info('Fotocasa SOAP GetProperty - Respuesta vacía', [
                    'method' => $method,
                    'status' => $status,
                    'response_length' => strlen($body),
                    'response_body' => $body,
                ]);
            }

            Log::info('Fotocasa SOAP GetProperty respuesta', [
                'method' => $method,
                'status' => $status,
                'has_complete_data' => $hasCompleteData,
                'response_length' => strlen($body),
                'response_body' => $body,
            ]);
        } else {
            if (strpos($body, 'soap:Fault') !== false || strpos($body, 'faultstring') !== false) {
                $fault = $this->extractSoapFault($body);
                $this->error("      ❌ SOAP Fault: {$fault}");
            } else {
                $this->error("      ❌ Error HTTP: " . substr($body, 0, 200));
            }
        }

        return false;
    }

    private function extractSoapFault(string $xml): string
    {
        if (preg_match('/<faultstring[^>]*>(.*?)<\/faultstring>/i', $xml, $matches)) {
            return trim($matches[1]);
        }
        if (preg_match('/<soap:Fault[^>]*>.*?<faultstring[^>]*>(.*?)<\/faultstring>/is', $xml, $matches)) {
            return trim($matches[1]);
        }
        return 'Error SOAP desconocido';
    }

    private function displayPropertyDataFromXml(string $xml): void
    {
        $this->line("    📊 Datos encontrados en la respuesta:");

        $fields = [
            'Id' => '/<Id[^>]*>(.*?)<\/Id>/i',
            'ExternalId' => '/<ExternalId[^>]*>(.*?)<\/ExternalId>/i',
            'AgencyReference' => '/<AgencyReference[^>]*>(.*?)<\/AgencyReference>/i',
            'TypeId' => '/<TypeId[^>]*>(.*?)<\/TypeId>/i',
            'CategoryId' => '/<CategoryId[^>]*>(.*?)<\/CategoryId>/i',
            'SubTypeId' => '/<SubTypeId[^>]*>(.*?)<\/SubTypeId>/i',
            'SubcategoryId' => '/<SubcategoryId[^>]*>(.*?)<\/SubcategoryId>/i',
            'Title' => '/<Title[^>]*>(.*?)<\/Title>/i',
            'Description' => '/<Description[^>]*>(.*?)<\/Description>/is',
            'Price' => '/<Price[^>]*>(.*?)<\/Price>/i',
            'Surface' => '/<Surface[^>]*>(.*?)<\/Surface>/i',
            'NRooms' => '/<NRooms[^>]*>(.*?)<\/NRooms>/i',
            'NBathrooms' => '/<NBathrooms[^>]*>(.*?)<\/NBathrooms>/i',
            'Street' => '/<Street[^>]*>(.*?)<\/Street>/i',
            'ZipCode' => '/<ZipCode[^>]*>(.*?)<\/ZipCode>/i',
        ];

        foreach ($fields as $field => $pattern) {
            if (preg_match($pattern, $xml, $matches)) {
                $value = trim($matches[1]);
                $value = preg_replace('/^<!\[CDATA\[(.*?)\]\]>$/', '$1', $value);
                if (strlen($value) > 100) {
                    $value = substr($value, 0, 100) . '...';
                }
                if (!empty($value)) {
                    $this->line("      ✅ {$field}: {$value}");
                }
            }
        }

        $featureCount = preg_match_all('/<PropertyFeature[^>]*>/i', $xml, $matches);
        if ($featureCount > 0) {
            $this->line("      ✅ PropertyFeature: {$featureCount} características encontradas");
        }

        $addressCount = preg_match_all('/<PropertyAddress[^>]*>/i', $xml, $matches);
        if ($addressCount > 0) {
            $this->line("      ✅ PropertyAddress: {$addressCount} direcciones encontradas");
        }

        $transactionCount = preg_match_all('/<PropertyTransaction[^>]*>/i', $xml, $matches);
        if ($transactionCount > 0) {
            $this->line("      ✅ PropertyTransaction: {$transactionCount} transacciones encontradas");
        }

        Log::info('Fotocasa SOAP GetProperty - XML completo', [
            'xml' => $xml,
        ]);
    }
}
