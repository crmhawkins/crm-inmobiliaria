<?php

namespace App\Console\Commands;

use App\Services\Idealista\IdealistaApiService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Throwable;

class IdealistaApiCall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'idealista:api-call
        {method : Verbo HTTP (GET, POST, etc.)}
        {endpoint : Ruta absoluta del endpoint, ej. /v1/properties}
        {--query= : Query string, ej. page=1&size=10}
        {--json= : Payload en formato JSON (solo para POST/PUT/PATCH)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Realiza una llamada arbitraria a la API de Idealista (sandbox)';

    public function __construct(private readonly IdealistaApiService $api)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $method = Str::upper($this->argument('method'));
        $endpoint = $this->argument('endpoint');

        $query = $this->parseQueryString($this->option('query'));
        $payload = $this->parseJson($this->option('json'));

        try {
            $response = $this->api->call($method, $endpoint, $query, $payload);
        } catch (Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->line(json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        return self::SUCCESS;
    }

    private function parseQueryString(?string $query): array
    {
        if (! $query) {
            return [];
        }

        parse_str($query, $parsed);

        return array_filter($parsed, static fn ($value) => $value !== '' && $value !== null);
    }

    private function parseJson(?string $json): ?array
    {
        if (! $json) {
            return null;
        }

        $decoded = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE || ! is_array($decoded)) {
            $this->error('El payload JSON es inválido: '.json_last_error_msg());
            exit(self::FAILURE);
        }

        return $decoded;
    }
}

