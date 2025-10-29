<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Inmuebles;

class SetInmueblesInmobiliariaNull extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inmuebles:set-null 
                            {--dry-run : Ejecutar sin hacer cambios reales}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Establece todos los inmuebles con inmobiliaria = null (aparecen en ambas)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        $totalInmuebles = Inmuebles::count();
        $inmueblesTrue = Inmuebles::where('inmobiliaria', true)->count();
        $inmueblesFalse = Inmuebles::where('inmobiliaria', false)->count();
        $inmueblesNull = Inmuebles::whereNull('inmobiliaria')->count();
        
        $this->info('=== Información actual ===');
        $this->line("Total de inmuebles: {$totalInmuebles}");
        $this->line("Inmobiliaria = true (Sayco): {$inmueblesTrue}");
        $this->line("Inmobiliaria = false (Sancer): {$inmueblesFalse}");
        $this->line("Inmobiliaria = null (Ambas): {$inmueblesNull}");
        $this->newLine();
        
        if ($dryRun) {
            $this->warn('MODO DRY-RUN: No se realizarán cambios');
            $this->line("Se actualizarían " . ($inmueblesTrue + $inmueblesFalse) . " inmuebles");
            return Command::SUCCESS;
        }
        
        if (!$this->confirm('¿Estás seguro de que deseas establecer todos los inmuebles en null?', true)) {
            $this->info('Operación cancelada.');
            return Command::SUCCESS;
        }
        
        $this->info('Actualizando inmuebles...');
        
        $updated = Inmuebles::query()
            ->whereNotNull('inmobiliaria')
            ->update(['inmobiliaria' => null]);
        
        $this->newLine();
        $this->info("✓ Se actualizaron {$updated} inmuebles correctamente.");
        
        // Mostrar información final
        $this->newLine();
        $this->info('=== Información final ===');
        $inmueblesNullFinal = Inmuebles::whereNull('inmobiliaria')->count();
        $this->line("Total de inmuebles: {$totalInmuebles}");
        $this->line("Inmobiliaria = null (Ambas): {$inmueblesNullFinal}");
        
        return Command::SUCCESS;
    }
}
