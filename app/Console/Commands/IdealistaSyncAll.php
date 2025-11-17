<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class IdealistaSyncAll extends Command
{
    protected $signature = 'idealista:sync-all {--size=50 : Registros por página}';

    protected $description = 'Sincroniza contactos e inmuebles de Idealista en una sola ejecución';

    public function handle(): int
    {
        $size = max(1, min(100, (int) $this->option('size')));

        $this->info('Sincronizando contactos...');
        $exitContacts = Artisan::call('idealista:sync-contacts', [
            '--all' => true,
            '--size' => $size,
        ], $this->output);

        if ($exitContacts !== 0) {
            $this->error('Sincronización de contactos falló. Aborto.');

            return $exitContacts;
        }

        $this->info('Sincronizando inmuebles...');
        $exitProperties = Artisan::call('idealista:sync-properties', [
            '--all' => true,
            '--size' => $size,
        ], $this->output);

        return $exitProperties;
    }
}

