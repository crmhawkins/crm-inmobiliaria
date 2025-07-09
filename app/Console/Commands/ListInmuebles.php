<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Inmuebles;

class ListInmuebles extends Command
{
    protected $signature = 'list:inmuebles';
    protected $description = 'Listar todos los inmuebles con sus referencias';

    public function handle()
    {
        $inmuebles = Inmuebles::select('id', 'titulo', 'valor_referencia', 'external_id')->get();

        $this->info('Inmuebles en la base de datos:');
        $this->table(['ID', 'Título', 'Valor Referencia', 'External ID'], $inmuebles->map(function($inmueble) {
            return [$inmueble->id, $inmueble->titulo, $inmueble->valor_referencia, $inmueble->external_id];
        })->toArray());
    }
}
