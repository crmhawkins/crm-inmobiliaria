<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Inmuebles;

class ListProperties extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'properties:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all properties in the database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $properties = Inmuebles::select('id', 'titulo')->get();

        $this->info("Propiedades en la base de datos:");
        $this->info("ID | Título");
        $this->info("---|-------");

        foreach ($properties as $property) {
            $this->line("{$property->id} | {$property->titulo}");
        }

        return 0;
    }
}
