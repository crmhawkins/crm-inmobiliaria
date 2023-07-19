<?php

namespace App\Http\Livewire\Inmuebles;

use App\Models\Caracteristicas;
use App\Models\Inmuebles;
use App\Models\TipoVivienda;
use App\Models\User;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Illuminate\Http\Request;

class Create extends Component
{
    use LivewireAlert;

    public $caracteristicas;

    public $titulo;
    public $descripcion;
    public $m2;
    public $m2_construidos;
    public $valor_referencia;
    public $habitaciones;
    public $banos;
    public $cod_postal;
    public $tipo_vivienda_id;
    public $ubicacion;
    public $cert_energetico;
    public $cert_energetico_elegido;
    public $inmobiliaria = null;
    public $estado;
    public $disponibilidad;
    public $otras_caracteristicasArray = [];

    public $otras_caracteristicas;
    public $referencia_catastral;

    public $vendedores;
    public $vendedor_id;
    public $vendedor_nombre;
    public $vendedor_dni;
    public $vendedor_ubicacion;
    public $vendedor_telefono;
    public $vendedor_correo;
    public $tipos_vivienda;
    public $ruta_imagenes;

    public $galeriaArray = [];
    public $galeria;

    protected $listeners = ['fileSelected'];



    public function mount()
    {
        $this->tipos_vivienda = TipoVivienda::all();
        $this->vendedores = User::all();
        $this->caracteristicas = Caracteristicas::all();
    }

    // Renderizado del Componente
    public function render()
    {
        return view('livewire.inmuebles.create');
    }

    public function submit()
    {
        if ($this->inmobiliaria == null) {
            if (request()->session()->get('inmobiliaria') == 'sayco') {
                $this->inmobiliaria = true;
            } else {
                $this->inmobiliaria = false;
            }
        } else {
            $this->inmobiliaria = null;
        }

        $this->otras_caracteristicas = json_encode($this->otras_caracteristicasArray);
        $this->galeria = json_encode($this->galeriaArray);

        $validatedData = $this->validate(
            [
                'titulo' => 'required',
                'descripcion' => 'required',
                'm2' => 'required',
                'm2_construidos' => 'required',
                'valor_referencia' => 'required',
                'habitaciones' => 'required',
                'banos' => 'required',
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
            ],
            // Mensajes de error
            [
                'titulo.required' => 'El título es obligatorio.',
                'descripcion.required' => 'Se requiere añadir descripción.',
                'm2.required' => 'Indica los m2 del inmueble.',
                'm2_construidos.required' => 'Indica los m2 construidos del inmueble.',
                'valor_referencia.required' => 'Indica el valor de referencia del inmueble.',
                'habitaciones.required' => 'Indica las habitaciones del inmueble.',
                'banos.required' => 'Indica los baños del inmueble.',
                'tipo_vivienda_id.required' => 'Indica el tipo de vivienda del inmueble.',
                'vendedor_id.required' => 'Indica al vendedor del inmueble.',
                'ubicacion.required' => 'Indica la ubicación del inmueble.',
                'cod_postal.required' => 'El código postal es obligatorio.',
                'cert_energetico.required' => 'Indica si existe un certificado energético o no.',
            ]
        );

        // Guardar datos validados
        $inmueblesSave = Inmuebles::create($validatedData);

        // Alertas de guardado exitoso
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
        return [
            'confirmed'
        ];
    }

    public function confirmed()
    {
        return redirect()->route('inmuebles.index');
    }

    public function fileSelected($url)
    {

        $this->ruta_imagenes = $url;

        // puedes realizar acciones aquí, como almacenar la URL en la base de datos
    }

    public function addGaleria()
    {
        if (!in_array($this->ruta_imagenes, $this->galeriaArray)) {
            $this->galeriaArray[count($this->galeriaArray) + 1] = $this->ruta_imagenes;
            $this->emit('refreshGalleryComponent', $this->galeriaArray);
        }
    }

    public function eliminarImagen($id)
    {
        unset($this->galeriaArray[$id]);
    }

    public function updated()
    {
        if ($this->vendedor_id == "") {
            $this->vendedor_nombre = "";
            $this->vendedor_dni = "";
            $this->vendedor_ubicacion = "";
            $this->vendedor_telefono = "";
            $this->vendedor_correo = "";
        } else {
            $vendedor = User::where('id', $this->vendedor_id)->first();
            $this->vendedor_nombre = $vendedor->nombre_completo;
            $this->vendedor_dni = $vendedor->dni;
            $this->vendedor_ubicacion  = $vendedor->ubicacion;
            $this->vendedor_telefono = $vendedor->telefono;
            $this->vendedor_correo = $vendedor->email;
        }
    }
}
