<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Clientes;
use App\Models\Inmuebles;
use App\Mail\NuevaPropuestaAlerta;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ReenviarAlertasInmuebles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alertas:reenviar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reenvía alertas por email a todos los clientes con intereses según los inmuebles existentes';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Iniciando proceso de reenvío de alertas...');
        $this->newLine();

        // Obtener todos los clientes con email e intereses definidos
        $clientes = Clientes::whereNotNull('email')
            ->whereNotNull('intereses')
            ->where('email', '!=', '')
            ->get();

        $this->info("Clientes encontrados con intereses: " . $clientes->count());
        $this->newLine();

        if ($clientes->isEmpty()) {
            $this->warn('No se encontraron clientes con intereses definidos.');
            return Command::SUCCESS;
        }

        // Obtener todos los inmuebles
        $inmuebles = Inmuebles::all();
        $this->info("Inmuebles totales: " . $inmuebles->count());
        $this->newLine();

        if ($inmuebles->isEmpty()) {
            $this->warn('No se encontraron inmuebles.');
            return Command::SUCCESS;
        }

        $totalEnviados = 0;
        $totalErrores = 0;
        $bar = $this->output->createProgressBar($inmuebles->count());
        $bar->start();

        // Para cada inmueble, buscar clientes interesados
        foreach ($inmuebles as $inmueble) {
            $bar->advance();
            
            $clientesInteresados = [];
            
            foreach ($clientes as $cliente) {
                // Verificar que el cliente tiene interés en este inmueble
                if ($cliente->interesaInmueble($inmueble)) {
                    // Verificar también que sean de la misma inmobiliaria si aplica
                    $mismaInmobiliaria = true;
                    
                    if ($inmueble->inmobiliaria !== null && $cliente->inmobiliaria !== null) {
                        // Si el inmueble es exclusivo de una inmobiliaria
                        if ($inmueble->inmobiliaria == 1 && $cliente->inmobiliaria == 0) {
                            $mismaInmobiliaria = false;
                        } elseif ($inmueble->inmobiliaria == 0 && $cliente->inmobiliaria == 1) {
                            $mismaInmobiliaria = false;
                        }
                    }
                    
                    // Si comparten inmobiliaria o el cliente es de ambas
                    if ($mismaInmobiliaria && ($cliente->inmobiliaria === null || $inmueble->inmobiliaria === null)) {
                        $mismaInmobiliaria = true;
                    }
                    
                    if ($mismaInmobiliaria) {
                        $clientesInteresados[] = $cliente;
                    }
                }
            }

            // Enviar correos a los clientes interesados en este inmueble
            foreach ($clientesInteresados as $cliente) {
                try {
                    Mail::to($cliente->email)
                        ->send(new NuevaPropuestaAlerta($cliente, $inmueble));
                    
                    $totalEnviados++;
                    $this->line("\n✓ Enviado a {$cliente->nombre_completo} ({$cliente->email}) - Inmueble: {$inmueble->titulo}");
                    
                    Log::info("Alerta reenviada a cliente {$cliente->nombre_completo} ({$cliente->email}) por inmueble {$inmueble->titulo} (ID: {$inmueble->id})");
                } catch (\Exception $e) {
                    $totalErrores++;
                    $this->error("\n✗ Error enviando a {$cliente->email}: " . $e->getMessage());
                    
                    Log::error("Error reenviando alerta a cliente {$cliente->email} (Inmueble ID: {$inmueble->id}): " . $e->getMessage());
                }
            }
        }

        $bar->finish();
        $this->newLine(2);

        // Resumen
        $this->info("=== RESUMEN ===");
        $this->info("Total de inmuebles procesados: " . $inmuebles->count());
        $this->info("Total de clientes evaluados: " . $clientes->count());
        $this->info("Correos enviados correctamente: " . $totalEnviados);
        
        if ($totalErrores > 0) {
            $this->error("Errores al enviar: " . $totalErrores);
        } else {
            $this->info("Sin errores.");
        }

        $this->newLine();
        $this->info("Revisa los logs en storage/logs/laravel.log para más detalles.");

        Log::info("Comando reenviar alertas completado: {$totalEnviados} correos enviados, {$totalErrores} errores");

        return Command::SUCCESS;
    }
}
