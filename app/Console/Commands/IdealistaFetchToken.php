<?php

namespace App\Console\Commands;

use App\Services\Idealista\IdealistaClient;
use Illuminate\Console\Command;
use Throwable;

class IdealistaFetchToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'idealista:token {--refresh : Fuerza ignorar el token cacheado}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Obtiene un access token de Idealista usando client_credentials';

    public function __construct(private readonly IdealistaClient $client)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        try {
            $token = $this->client->getAccessToken($this->option('refresh'));
        } catch (Throwable $exception) {
            $this->error('No se pudo obtener el token: '.$exception->getMessage());

            return self::FAILURE;
        }

        $this->info('Token obtenido correctamente:');
        $this->line($token);

        return self::SUCCESS;
    }
}

