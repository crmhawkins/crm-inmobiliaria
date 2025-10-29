<?php

namespace App\Listeners;

use App\Events\InmuebleCreated;
use App\Mail\NuevaPropuestaAlerta;
use App\Models\Clientes;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EnviarAlertasNuevoInmueble
{
    /**
     * Handle the event.
     *
     * @param  \App\Events\InmuebleCreated  $event
     * @return void
     */
    public function handle(InmuebleCreated $event)
    {
        $inmueble = $event->inmueble;

        try {
            // Obtener todos los clientes con email y intereses definidos
            $clientes = Clientes::whereNotNull('email')
                ->whereNotNull('intereses')
                ->where('email', '!=', '')
                ->get();

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

            // Enviar correos a los clientes interesados
            foreach ($clientesInteresados as $cliente) {
                try {
                    Mail::to($cliente->email)
                        ->send(new NuevaPropuestaAlerta($cliente, $inmueble));
                    
                    Log::info("Alerta enviada a cliente {$cliente->nombre_completo} ({$cliente->email}) por inmueble {$inmueble->titulo}");
                } catch (\Exception $e) {
                    Log::error("Error enviando alerta a cliente {$cliente->email}: " . $e->getMessage());
                }
            }

            if (count($clientesInteresados) > 0) {
                Log::info("Se enviaron " . count($clientesInteresados) . " alertas por el nuevo inmueble: {$inmueble->titulo}");
            }

        } catch (\Exception $e) {
            Log::error("Error en el proceso de alertas de nuevo inmueble: " . $e->getMessage());
        }
    }
}

