<?php

namespace App\Http\Livewire\Inmuebles;

use App\Models\DocInmueble;
use App\Models\Inmuebles;
use App\Models\Clientes;
use Livewire\Component;
use Illuminate\Support\Facades\Mail;

class DocumentosCreate extends Component
{
    public $inmueble_id;
	public $cliente_id = 1;
    public $documento;
    public $docs;

    public function mount($inmueble_id = null){
 		if (request()->session()->get('inmobiliaria') == 'sayco') {
            $this->clientes = Clientes::where('inmobiliaria', true)->orWhere('inmobiliaria', null)->get();
        } else {
            $this->clientes = Clientes::where('inmobiliaria', false)->orWhere('inmobiliaria', null)->get();
        }        
		
		$this->inmueble_id = $inmueble_id;
		
        if($inmueble_id != null){
            if (DocInmueble::where('inmueble_id', $this->inmueble_id)->exists()) {
                $this->docs = DocInmueble::where('inmueble_id', $this->inmueble_id)->first();
            }
        }
    }
    public function render()
    {
        if($this->inmueble_id != null){
            if (DocInmueble::where('inmueble_id', $this->inmueble_id)->exists()) {
                $this->docs = DocInmueble::where('inmueble_id', $this->inmueble_id)->first();
            }
        }

        return view('livewire.inmuebles.documentos-create');
    }

    public function addDocumento()
    {
        // Comprobar si existe un DocInmueble con el inmueble_id especificado
        if (DocInmueble::where('inmueble_id', $this->inmueble_id)->exists()) {

            // Obtener el DocInmueble
            $docInmueble = DocInmueble::where('inmueble_id', $this->inmueble_id)->first();

            // Obtener las rutas del DocInmueble
            $rutas = json_decode($docInmueble->rutas, true);

            // Comprobar si las rutas están vacías
            if (empty($rutas)) {
                // Si las rutas están vacías, crea un nuevo array con la nueva ruta
                $rutas = [$this->documento];
            } else {
                // Si las rutas no están vacías, añade la nueva ruta al array existente
                $rutas[] = $this->documento;
            }

            // Actualiza las rutas del DocInmueble
            $docInmueble->rutas = json_encode($rutas);
            $docInmueble->save();
        } else {
            // Si el DocInmueble no existe, se crea uno nuevo
            $docInmueble = new DocInmueble;
            $docInmueble->inmueble_id = $this->inmueble_id;
            $docInmueble->rutas = json_encode([$this->documento]); // Asume que $this->documento contiene la nueva ruta
            $docInmueble->save();
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
		
    
    $texto = 'Buenas, ' . $cliente->nombre_completo .'. Te enviamos un documento perteneciente al inmueble  ' . $inmueble->titulo;

Mail::raw($texto, function ($message) use ($cliente, $nombre_inmobiliaria, $inmueble, $documento) {
    $message->from('admin@grupocerban.com', $nombre_inmobiliaria);
    $message->to($cliente->email, $cliente->nombre_completo);
	$message->to(env('MAIL_USERNAME'));
    $message->subject($nombre_inmobiliaria . " - Documento del inmueble " . $inmueble->titulo);
	$message->attach($documento);

});
}
}
