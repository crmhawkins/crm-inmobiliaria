<?php

namespace App\Mail;

use App\Models\Clientes;
use App\Models\Inmuebles;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NuevaPropuestaAlerta extends Mailable
{
    use Queueable, SerializesModels;

    public $cliente;
    public $inmueble;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Clientes $cliente, Inmuebles $inmueble)
    {
        $this->cliente = $cliente;
        $this->inmueble = $inmueble;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $inmobiliaria = $this->determinarNombreInmobiliaria();
        $fromEmail = env('MAIL_FROM_ADDRESS', 'admin@grupocerban.com');
        
        return $this->from($fromEmail, $inmobiliaria)
                    ->subject($inmobiliaria . ' - Nueva propiedad que puede interesarle')
                    ->view('emails.nueva-propuesta-alerta')
                    ->with([
                        'inmobiliaria' => $inmobiliaria
                    ]);
    }
    
    private function determinarNombreInmobiliaria()
    {
        if ($this->inmueble->inmobiliaria === true) {
            return 'INMOBILIARIA SAYCO';
        } elseif ($this->inmueble->inmobiliaria === false) {
            return 'INMOBILIARIA SANCER';
        } elseif ($this->cliente->inmobiliaria === true) {
            return 'INMOBILIARIA SAYCO';
        } elseif ($this->cliente->inmobiliaria === false) {
            return 'INMOBILIARIA SANCER';
        }
        
        // Por defecto, intentar desde la sesión si está disponible
        if (request()->hasSession() && request()->session()->get('inmobiliaria') == 'sayco') {
            return 'INMOBILIARIA SAYCO';
        } elseif (request()->hasSession() && request()->session()->get('inmobiliaria') == 'sancer') {
            return 'INMOBILIARIA SANCER';
        }
        
        return 'INMOBILIARIA';
    }
}

