<?php

namespace App\Console\Commands;

use App\Services\Fotocasa\FotocasaSoapTestService;
use Illuminate\Console\Command;

class TestFotocasaGetProperty extends Command
{
    protected $signature = 'fotocasa:test-get-property
                            {--external-id=270 : ExternalId de la propiedad a obtener}
                            {--wsdl : Obtener el WSDL del servicio}
                            {--apikey= : API Key específica para probar}';

    protected $description = 'Prueba el servicio SOAP de Fotocasa GetProperty v3 para obtener una propiedad completa';

    public function handle(): int
    {
        $this->info('🧪 Probando servicio SOAP GetProperty de Fotocasa v3 exhaustivamente...');
        $this->newLine();

        $soapUrl = 'http://ws.fotocasa.es/mobile/api/v3.asmx';
        $wsdlUrl = $soapUrl . '?WSDL';

        $externalId = $this->option('external-id');
        $apiKey = $this->option('apikey') ?? env('API_KEY');

        if (!$apiKey) {
            $this->error('⚠️  API_KEY no configurada');
            return self::FAILURE;
        }

        $service = app(FotocasaSoapTestService::class);
        $service->setOutput($this->output);

        // Test 1: Obtener WSDL
        if ($this->option('wsdl')) {
            $this->info('📋 Test 1: Obtener WSDL del servicio');
            $service->testGetWsdl($wsdlUrl, $apiKey);
            $this->newLine();
        }

        // Obtener lista de identificadores reales para probar
        $this->info('📋 Obteniendo lista de propiedades disponibles...');
        $allIdentifiers = $service->getAvailableExternalIds($apiKey);

        if (empty($allIdentifiers)) {
            $this->warn('⚠️  No se encontraron identificadores disponibles');
            $identifiersToTest = [[
                'type' => 'ExternalId',
                'value' => $externalId,
                'agency_ref' => null,
            ]];
        } else {
            $total = count($allIdentifiers);
            $this->info("✅ Encontrados {$total} identificadores. Probando con los primeros 3 (ExternalId y AgencyReference)...");
            $identifiersToTest = array_slice($allIdentifiers, 0, 3);
        }

        $this->newLine();

        // Test 2: Probar GetProperty con diferentes identificadores
        $foundCompleteData = false;
        foreach ($identifiersToTest as $identifier) {
            $testId = $identifier['value'];
            $agencyRef = $identifier['agency_ref'];
            $type = $identifier['type'];

            $this->info("📋 Probando {$type}: {$testId}" . ($agencyRef ? " (AgencyReference: {$agencyRef})" : ""));

            $found = $service->testGetProperty($soapUrl, $testId, $apiKey);
            if ($found) {
                $foundCompleteData = true;
                $this->newLine();
                $this->info("✅✅ ÉXITO: Se encontraron datos completos para {$type}: {$testId}");
                break;
            }

            if ($agencyRef && $agencyRef !== $testId) {
                $this->line("  Probando también con AgencyReference: {$agencyRef}");
                $found = $service->testGetProperty($soapUrl, (string)$agencyRef, $apiKey);
                if ($found) {
                    $foundCompleteData = true;
                    $this->newLine();
                    $this->info("✅✅ ÉXITO: Se encontraron datos completos para AgencyReference: {$agencyRef}");
                    break;
                }
            }

            $this->newLine();
        }

        // Si no encontramos datos, probar con más identificadores
        if (!$foundCompleteData && !empty($allIdentifiers) && count($allIdentifiers) > 3) {
            $this->warn('⚠️  No se encontraron datos con los primeros 3. Probando con más identificadores...');
            $this->newLine();

            $offset = 3;
            $batchSize = 3;
            $maxAttempts = 20;

            for ($attempt = 0; $attempt < $maxAttempts && !$foundCompleteData; $attempt++) {
                $nextBatch = array_slice($allIdentifiers, $offset, $batchSize);
                if (empty($nextBatch)) break;

                $idsList = array_map(fn($id) => $id['value'], $nextBatch);
                $this->info("📋 Lote " . ($attempt + 2) . ": Probando " . implode(', ', $idsList));

                foreach ($nextBatch as $identifier) {
                    $testId = $identifier['value'];
                    $agencyRef = $identifier['agency_ref'];
                    $type = $identifier['type'];

                    $found = $service->testGetProperty($soapUrl, $testId, $apiKey);
                    if ($found) {
                        $foundCompleteData = true;
                        $this->newLine();
                        $this->info("✅✅ ÉXITO: Se encontraron datos completos para {$type}: {$testId}");
                        break 2;
                    }

                    if ($agencyRef && $agencyRef !== $testId) {
                        $found = $service->testGetProperty($soapUrl, (string)$agencyRef, $apiKey);
                        if ($found) {
                            $foundCompleteData = true;
                            $this->newLine();
                            $this->info("✅✅ ÉXITO: Se encontraron datos completos para AgencyReference: {$agencyRef}");
                            break 2;
                        }
                    }
                }
                $offset += $batchSize;
                $this->newLine();
            }
        }

        if (!$foundCompleteData) {
            $this->warn('⚠️  GetProperty no devolvió datos. Probando GetProducts SOAP...');
            $this->newLine();

            $foundCompleteData = $service->testGetProductsSoap($soapUrl, $apiKey);
        }

        if (!$foundCompleteData) {
            $this->warn('⚠️  No se encontraron datos completos con ningún método probado');
            $this->newLine();
            $this->info('💡 Posibles razones:');
            $this->line('   1. Los ExternalIds no existen en Fotocasa o no tienen datos completos');
            $this->line('   2. El método GetProperty requiere permisos específicos o parámetros adicionales');
            $this->line('   3. Las propiedades pueden no estar publicadas o activas');
            $this->line('   4. Contactar con soporte de Fotocasa para verificar acceso a GetProperty');
        }

        return self::SUCCESS;
    }
}
