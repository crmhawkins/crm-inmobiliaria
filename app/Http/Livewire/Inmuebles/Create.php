<?php

namespace App\Http\Livewire\Inmuebles;

use App\Models\Caracteristicas;
use App\Models\Inmuebles;
use App\Models\TipoVivienda;
use App\Models\User;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Create extends Component
{
    use LivewireAlert;

    // Campos base
    public $titulo, $descripcion, $m2, $m2_construidos, $valor_referencia, $habitaciones, $banos,
           $cod_postal, $tipo_vivienda_id, $ubicacion, $cert_energetico, $cert_energetico_elegido,
           $inmobiliaria = null, $estado, $disponibilidad, $otras_caracteristicasArray = [],
           $otras_caracteristicas, $referencia_catastral, $galeriaArray = [], $galeria;

    public $vendedor_id, $vendedor_nombre, $vendedor_dni, $vendedor_ubicacion, $vendedor_telefono, $vendedor_correo;

    // Selects
    public $tipos_vivienda, $vendedores, $caracteristicas;

    // Imagen
    public $ruta_imagenes;

    // Datos API Fotocasa
    public $external_id, $agency_reference, $type_id, $subtype_id, $contact_type_id;
    public $zip_code, $floor_id, $x, $y, $visibility_mode_id, $street, $number;

    // Arrays JSON
    public $featuresArray = [], $contact_infoArray = [], $transactionsArray = [], $publicationsArray = [], $documentosArray = [];
    public $features, $contact_info, $transactions, $publications, $documentos;

    protected $listeners = ['fileSelected'];

    public function mount()
    {
        $this->tipos_vivienda = TipoVivienda::all();
        $this->vendedores = User::all();
        $this->caracteristicas = Caracteristicas::all();
    }

    public function render()
    {
        return view('livewire.inmuebles.create');
    }

    public function submit()
    {
        $this->otras_caracteristicas = json_encode($this->otras_caracteristicasArray);
        $this->galeria = json_encode($this->galeriaArray);

        $this->features = json_encode($this->featuresArray);
        $this->contact_info = json_encode($this->contact_infoArray);
        $this->transactions = json_encode($this->transactionsArray);
        $this->publications = json_encode($this->publicationsArray);
        $this->documentos = json_encode($this->documentosArray);

        if ($this->inmobiliaria == null) {
            $this->inmobiliaria = request()->session()->get('inmobiliaria') === 'sayco';
        }

        $validatedData = $this->validate([
            'titulo' => 'required',
            'descripcion' => 'required',
            'm2' => 'required|numeric',
            'm2_construidos' => 'required|numeric',
            'valor_referencia' => 'required|numeric',
            'habitaciones' => 'required|integer',
            'banos' => 'required|integer',
            'tipo_vivienda_id' => 'required',
            'vendedor_id' => 'required',
            'ubicacion' => 'required',
            'cod_postal' => 'required',
            'cert_energetico' => 'required',
            'cert_energetico_elegido' => 'nullable',
            'estado' => 'required',
            'galeria' => 'nullable',
            'disponibilidad' => 'required',
            'otras_caracteristicas' => 'nullable',
            'referencia_catastral' => 'required',
            'inmobiliaria' => 'nullable',

            // API Fotocasa
            'external_id' => 'nullable|string',
            'agency_reference' => 'nullable|string',
            'type_id' => 'nullable|integer',
            'subtype_id' => 'nullable|integer',
            'contact_type_id' => 'nullable|integer',
            'zip_code' => 'nullable|string',
            'floor_id' => 'nullable|integer',
            'x' => 'nullable|numeric',
            'y' => 'nullable|numeric',
            'visibility_mode_id' => 'nullable|integer',
            'street' => 'nullable|string',
            'number' => 'nullable|string',

            'features' => 'nullable|json',
            'contact_info' => 'nullable|json',
            'transactions' => 'nullable|json',
            'publications' => 'nullable|json',
            'documentos' => 'nullable|json',
        ]);

        $inmueblesSave = Inmuebles::create($validatedData);

        if ($inmueblesSave) {
            $this->alert('success', '¡Inmueble registrado correctamente!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar la información del inmueble!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }
    }

    public function getListeners()
    {
        return ['confirmed'];
    }

    public function confirmed()
    {
        return redirect()->route('inmuebles.index');
    }

    public function fileSelected($url)
    {
        $this->ruta_imagenes = $url;
    }

    public function addGaleria()
    {
        if (!in_array($this->ruta_imagenes, $this->galeriaArray)) {
            $this->galeriaArray[] = $this->ruta_imagenes;
            $this->emit('refreshGalleryComponent', $this->galeriaArray);
        }
    }

    public function eliminarImagen($id)
    {
        unset($this->galeriaArray[$id]);
    }

    public function updated()
    {
        if (empty($this->vendedor_id)) {
            $this->vendedor_nombre = $this->vendedor_dni = $this->vendedor_ubicacion = $this->vendedor_telefono = $this->vendedor_correo = '';
        } else {
            $vendedor = User::find($this->vendedor_id);
            if ($vendedor) {
                $this->vendedor_nombre = $vendedor->nombre_completo;
                $this->vendedor_dni = $vendedor->dni;
                $this->vendedor_ubicacion  = $vendedor->ubicacion;
                $this->vendedor_telefono = $vendedor->telefono;
                $this->vendedor_correo = $vendedor->email;
            }
        }
    }
}
