<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TestFotocasaSoap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fotocasa:test-soap
                            {--wsdl : Obtener el WSDL del servicio}
                            {--username= : Usuario para autenticación SOAP}
                            {--password= : Contraseña para autenticación SOAP}
                            {--apikey= : API Key específica para probar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prueba el servicio SOAP de Fotocasa GetProducts';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🧪 Probando servicio SOAP de Fotocasa...');
        $this->newLine();

        $soapUrl = 'https://ws.fotocasa.es/mobile/api/v2.asmx';
        $wsdlUrl = $soapUrl . '?WSDL';
        $getProductsUrl = $soapUrl . '?op=GetProducts';

        // Test 1: Obtener WSDL
        if ($this->option('wsdl')) {
            $this->info('📋 Test 1: Obtener WSDL del servicio');
            $this->testGetWsdl($wsdlUrl);
            $this->newLine();
        }

        // Test 2: Probar GetProducts
        $this->info('📋 Test 2: Llamar a GetProducts');
        $apiKey = $this->option('apikey') ?? env('API_KEY');
        $username = $this->option('username') ?? env('FOTOCASA_SOAP_USERNAME');
        $password = $this->option('password') ?? env('FOTOCASA_SOAP_PASSWORD');
        $this->testGetProducts($getProductsUrl, $apiKey, $username, $password);
        $this->newLine();

        // Test 3: Probar con diferentes métodos de autenticación
        $this->info('📋 Test 3: Probar diferentes métodos de autenticación');
        $this->testDifferentAuthMethods($soapUrl);
        $this->newLine();

        return self::SUCCESS;
    }

    /**
     * Obtiene el WSDL del servicio
     */
    private function testGetWsdl(string $wsdlUrl): void
    {
        try {
            $this->line("  URL: {$wsdlUrl}");
            $response = Http::withOptions([
                'verify' => false,
                'timeout' => 30,
            ])->get($wsdlUrl);

            $this->line("  Status: {$response->status()}");

            if ($response->successful()) {
                $this->info('  ✅ WSDL obtenido correctamente');
                $wsdl = $response->body();
                $this->line("  Tamaño: " . strlen($wsdl) . " bytes");

                // Mostrar las primeras líneas del WSDL
                $lines = explode("\n", $wsdl);
                $this->line("  Primeras líneas del WSDL:");
                foreach (array_slice($lines, 0, 20) as $line) {
                    $this->line("    " . trim($line));
                }
            } else {
                $this->error('  ❌ Error al obtener WSDL: ' . $response->body());
            }
        } catch (\Exception $e) {
            $this->error('  ❌ Excepción: ' . $e->getMessage());
        }
    }

    /**
     * Prueba el método GetProducts
     */
    private function testGetProducts(string $url, ?string $apiKey = null, ?string $username = null, ?string $password = null): void
    {
        if (!$apiKey) {
            $apiKey = env('API_KEY');
        }

        // Primero, obtener el WSDL para encontrar el namespace correcto
        $wsdlUrl = str_replace('?op=GetProducts', '?WSDL', $url);
        $wsdl = Http::withOptions(['verify' => false, 'timeout' => 30])->get($wsdlUrl)->body();

        // Buscar el namespace correcto en el WSDL
        $namespace = 'http://ws.fotocasa.es/'; // Namespace por defecto según el WSDL
        if (preg_match('/targetNamespace="([^"]+)"/', $wsdl, $matches)) {
            $namespace = $matches[1];
        }

        // Construir el SOAP envelope para GetProducts con el namespace correcto
        // Intentar diferentes métodos de autenticación
        $authMethods = [];

        if ($apiKey) {
            $authMethods[] = ['type' => 'apikey', 'value' => $apiKey];
        }
        if ($username && $password) {
            $authMethods[] = ['type' => 'username_password', 'username' => $username, 'password' => $password];
        }

        if (empty($authMethods)) {
            $this->warn('  ⚠️  No se proporcionaron credenciales. Usando solo API_KEY del .env');
            $authMethods[] = ['type' => 'apikey', 'value' => $apiKey];
        }

        $soapAction = $namespace . 'GetProducts';

        $this->line("  URL: {$url}");
        $this->line("  Namespace: {$namespace}");
        $this->line("  SOAP Action: {$soapAction}");

        // Probar cada método de autenticación
        foreach ($authMethods as $authMethod) {
            if ($authMethod['type'] === 'apikey') {
                $this->line("  Probando con API Key...");
                $soapEnvelope = $this->buildSoapEnvelope($authMethod['value'], $namespace);
            } elseif ($authMethod['type'] === 'username_password') {
                $this->line("  Probando con Username/Password...");
                $soapEnvelope = $this->buildSoapEnvelopeWithUserPass($authMethod['username'], $authMethod['password'], $namespace);
            }

            $this->line("  Envelope:");
            $this->line("    " . str_replace("\n", "\n    ", $soapEnvelope));

            try {
                $request = Http::withHeaders([
                    'Content-Type' => 'text/xml; charset=utf-8',
                    'SOAPAction' => '"' . $soapAction . '"',
                ]);

                // Si es username/password, usar Basic Auth
                if ($authMethod['type'] === 'username_password') {
                    $request = $request->withBasicAuth($authMethod['username'], $authMethod['password']);
                }

                $response = $request->withOptions([
                    'verify' => false,
                    'timeout' => 30,
                ])->withBody($soapEnvelope, 'text/xml')->post($url);

                $this->line("  Status: {$response->status()}");

                if ($response->successful()) {
                    $this->info('  ✅ Respuesta exitosa');
                    $body = $response->body();
                    $this->line("  Tamaño de respuesta: " . strlen($body) . " bytes");

                    // Intentar parsear como XML
                    $xml = @simplexml_load_string($body);
                    if ($xml) {
                        $this->info('  ✅ Respuesta XML válida');
                        $this->line("  Estructura:");
                        $this->displayXmlStructure($xml, 2);
                        return; // Éxito, salir del método
                    } else {
                        $this->warn('  ⚠️  Respuesta no es XML válido');
                        $this->line("  Primeros 500 caracteres:");
                        $this->line("    " . substr($body, 0, 500));
                        return; // Aunque no sea XML válido, tenemos respuesta
                    }
                } else {
                    $errorBody = $response->body();
                    $this->error('  ❌ Error: ' . $response->status());

                    // Intentar parsear el error SOAP
                    $xml = @simplexml_load_string($errorBody);
                    if ($xml) {
                        $faultString = (string)($xml->xpath('//soap:Fault/faultstring')[0] ?? '');
                        $faultCode = (string)($xml->xpath('//soap:Fault/faultcode')[0] ?? '');
                        $this->line("  Fault Code: {$faultCode}");
                        $this->line("  Fault String: {$faultString}");
                    } else {
                        $this->line("  Respuesta: " . substr($errorBody, 0, 500));
                    }

                    // Si este método falló, continuar con el siguiente
                    continue;
                }
            } catch (\Exception $e) {
                $this->error('  ❌ Excepción: ' . $e->getMessage());
                Log::error('Error en test SOAP GetProducts', [
                    'error' => $e->getMessage(),
                    'url' => $url,
                ]);
                continue;
            }
        }

        // Si llegamos aquí, ningún método funcionó
        $this->warn('  ⚠️  Ningún método de autenticación funcionó. Revisa las credenciales.');
    }

    /**
     * Prueba diferentes métodos de autenticación
     */
    private function testDifferentAuthMethods(string $baseUrl): void
    {
        $apiKey = env('API_KEY');
        $getProductsUrl = $baseUrl . '?op=GetProducts';

        // Obtener el namespace correcto del WSDL
        $wsdlUrl = $baseUrl . '?WSDL';
        $wsdl = Http::withOptions(['verify' => false, 'timeout' => 10])->get($wsdlUrl)->body();
        $namespace = 'http://ws.fotocasa.es/';
        if (preg_match('/targetNamespace="([^"]+)"/', $wsdl, $matches)) {
            $namespace = $matches[1];
        }
        $soapAction = '"' . $namespace . 'GetProducts"';

        $authMethods = [
            [
                'name' => 'API Key en header',
                'headers' => [
                    'Content-Type' => 'text/xml; charset=utf-8',
                    'SOAPAction' => $soapAction,
                    'Api-Key' => $apiKey,
                ],
            ],
            [
                'name' => 'API Key en SOAP header',
                'headers' => [
                    'Content-Type' => 'text/xml; charset=utf-8',
                    'SOAPAction' => $soapAction,
                ],
                'envelope' => $this->buildSoapEnvelopeWithAuth($apiKey, $namespace),
            ],
            [
                'name' => 'Basic Auth',
                'headers' => [
                    'Content-Type' => 'text/xml; charset=utf-8',
                    'SOAPAction' => $soapAction,
                ],
                'auth' => ['basic', $apiKey, ''],
            ],
        ];

        foreach ($authMethods as $method) {
            $this->line("  Probando: {$method['name']}");
            try {
                $request = Http::withHeaders($method['headers'] ?? []);

                if (isset($method['auth'])) {
                    $request = $request->withBasicAuth($method['auth'][1], $method['auth'][2]);
                }

                $envelope = $method['envelope'] ?? $this->buildSoapEnvelope($apiKey, $namespace);
                $response = $request->withOptions([
                    'verify' => false,
                    'timeout' => 10,
                ])->withBody($envelope, 'text/xml')->post($getProductsUrl);

                $this->line("    Status: {$response->status()}");
                if ($response->successful()) {
                    $this->info("    ✅ Éxito con {$method['name']}");
                    $body = $response->body();
                    if (strlen($body) > 0) {
                        $this->line("    Respuesta: " . substr($body, 0, 200) . "...");
                    }
                    break; // Si funciona, no probar los demás
                } else {
                    $this->warn("    ⚠️  Error {$response->status()}: " . substr($response->body(), 0, 100));
                }
            } catch (\Exception $e) {
                $this->error("    ❌ Excepción: " . $e->getMessage());
            }
            $this->newLine();
        }
    }

    /**
     * Construye el SOAP envelope básico
     */
    private function buildSoapEnvelope(?string $apiKey = null, string $namespace = 'http://ws.fotocasa.es/'): string
    {
        $prefix = 'ws';
        $envelope = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:' . $prefix . '="' . $namespace . '">
    <soap:Header>';

        if ($apiKey) {
            $envelope .= '
        <' . $prefix . ':ApiKey>' . htmlspecialchars($apiKey) . '</' . $prefix . ':ApiKey>';
        }

        $envelope .= '
    </soap:Header>
    <soap:Body>
        <' . $prefix . ':GetProducts/>
    </soap:Body>
</soap:Envelope>';

        return $envelope;
    }

    /**
     * Construye el SOAP envelope con autenticación en el header
     */
    private function buildSoapEnvelopeWithAuth(string $apiKey, string $namespace = 'http://ws.fotocasa.es/'): string
    {
        $prefix = 'ws';
        return '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:' . $prefix . '="' . $namespace . '">
    <soap:Header>
        <' . $prefix . ':ApiKey>' . htmlspecialchars($apiKey) . '</' . $prefix . ':ApiKey>
    </soap:Header>
    <soap:Body>
        <' . $prefix . ':GetProducts/>
    </soap:Body>
</soap:Envelope>';
    }

    /**
     * Construye el SOAP envelope con usuario y contraseña
     */
    private function buildSoapEnvelopeWithUserPass(string $username, string $password, string $namespace = 'http://ws.fotocasa.es/'): string
    {
        $prefix = 'ws';
        return '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:' . $prefix . '="' . $namespace . '">
    <soap:Header>
        <' . $prefix . ':Username>' . htmlspecialchars($username) . '</' . $prefix . ':Username>
        <' . $prefix . ':Password>' . htmlspecialchars($password) . '</' . $prefix . ':Password>
    </soap:Header>
    <soap:Body>
        <' . $prefix . ':GetProducts/>
    </soap:Body>
</soap:Envelope>';
    }

    /**
     * Muestra la estructura de un XML de forma legible
     */
    private function displayXmlStructure(\SimpleXMLElement $xml, int $indent = 0): void
    {
        $spaces = str_repeat('  ', $indent);

        foreach ($xml->children() as $child) {
            $name = $child->getName();
            $value = trim((string) $child);

            if (count($child->children()) > 0) {
                $this->line("{$spaces}{$name}:");
                $this->displayXmlStructure($child, $indent + 1);
            } else {
                $displayValue = strlen($value) > 100 ? substr($value, 0, 100) . '...' : $value;
                $this->line("{$spaces}{$name}: {$displayValue}");
            }
        }
    }
}
