<?php

namespace App\Http\Livewire\Inmuebles;

use App\Models\ContratoArras;
use App\Models\Inmuebles;
use App\Models\Clientes;
use Livewire\Component;
use Illuminate\Support\Facades\Mail;
class ContratoCreate extends Component
{
    public $inmueble_id;
    public $contrato;
    public $contratoArras;
	public $clientes;
	public $cliente_id = 1;

    public function mount($inmueble_id = null){
		if (request()->session()->get('inmobiliaria') == 'sayco') {
            $this->clientes = Clientes::where('inmobiliaria', true)->orWhere('inmobiliaria', null)->get();
        } else {
            $this->clientes = Clientes::where('inmobiliaria', false)->orWhere('inmobiliaria', null)->get();
        }      
		
        $this->inmueble_id = $inmueble_id;
        if($inmueble_id != null){
            if (ContratoArras::where('inmueble_id', $this->inmueble_id)->exists()) {
                $this->contratoArras = ContratoArras::where('inmueble_id', $this->inmueble_id)->first();
            }
        }
    }
    public function render()
    {
        if($this->inmueble_id != null){
            if (ContratoArras::where('inmueble_id', $this->inmueble_id)->exists()) {
                $this->contratoArras = ContratoArras::where('inmueble_id', $this->inmueble_id)->first();
            }
        }
        return view('livewire.inmuebles.contrato-create');
    }

    public function addContrato()
    {
        if (ContratoArras::where('inmueble_id', $this->inmueble_id)->exists()) {

            // Obtener el ContratoArras
            $contratoArras = ContratoArras::where('inmueble_id', $this->inmueble_id)->first();

            // Actualiza la ruta del ContratoArras
            $contratoArras->ruta = $this->contrato; // Sobrescribe cualquier ruta existente
            $contratoArras->save();
        } else {
            // Si el ContratoArras no existe, se crea uno nuevo
            $contratoArras = new ContratoArras;
            $contratoArras->inmueble_id = $this->inmueble_id;
            $contratoArras->ruta = $this->contrato; // Asume que $rutaNueva contiene la nueva ruta
            $contratoArras->save();
        }
    }
	
	public function enviarCorreo($documento){
		
    $inmueble = Inmuebles::where('id', $this->inmueble_id)->first();
    $cliente = Clientes::where('id', $this->cliente_id)->first();
    
    if (request()->session()->get('inmobiliaria') == 'sayco') {
        $nombre_inmobiliaria = "INMOBILIARIA SAYCO";
    } else {
        $nombre_inmobiliaria = "INMOBILIARIA SANCER";
    }
		
    
    $texto = 'Buenas, ' . $cliente->nombre_completo .'. Te enviamos el contrato de arras perteneciente al inmueble  ' . $inmueble->titulo;

Mail::raw($texto, function ($message) use ($cliente, $nombre_inmobiliaria, $inmueble, $documento) {
	$archivo = json_decode($documento, true);
    $message->from('admin@grupocerban.com', $nombre_inmobiliaria);
    $message->to($cliente->email, $cliente->nombre_completo);
	$message->to(env('MAIL_USERNAME'));
    $message->subject($nombre_inmobiliaria . " - Contrato de arrás del inmueble " . $inmueble->titulo);
	$message->attach($archivo['ruta']);

});
}
}
