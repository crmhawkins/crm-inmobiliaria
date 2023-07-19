<?php

namespace App\Http\Livewire\Inmuebles;

use App\Models\Caracteristicas;
use App\Models\TipoVivienda;
use App\Models\User;
use Livewire\Component;
use App\Models\Inmuebles;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Edit extends Component
{
    use LivewireAlert;

    public $identificador;

    public $inmuebles;
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
        $this->inmuebles = Inmuebles::find($this->identificador);
        $this->tipos_vivienda = TipoVivienda::all();
        $this->vendedores = User::all();
        $this->caracteristicas = Caracteristicas::all();

        $this->titulo = $this->inmuebles->titulo;
        $this->descripcion = $this->inmuebles->descripcion;
        $this->m2 = $this->inmuebles->m2;
        $this->m2_construidos = $this->inmuebles->m2_construidos;
        $this->valor_referencia = $this->inmuebles->valor_referencia;
        $this->habitaciones = $this->inmuebles->habitaciones;
        $this->banos = $this->inmuebles->banos;
        $this->cod_postal = $this->inmuebles->cod_postal;
        $this->tipo_vivienda_id = $this->inmuebles->tipo_vivienda_id;
        $this->ubicacion = $this->inmuebles->ubicacion;
        $this->cert_energetico = $this->inmuebles->cert_energetico;
        $this->cert_energetico_elegido = $this->inmuebles->cert_energetico_elegido;
        if ($this->inmuebles->inmobiliaria != null) {
            $this->inmobiliaria = null;
        } else {
            $this->inmobiliaria = true;
        };
        $this->estado = $this->inmuebles->estado;
        $this->disponibilidad = $this->inmuebles->disponibilidad;
        $this->otras_caracteristicasArray = json_decode($this->inmuebles->otras_caracteristicas, true);
        $this->referencia_catastral = $this->inmuebles->referencia_catastral;
        if ($this->inmuebles->galeria != null) {
            $this->galeriaArray = json_decode($this->inmuebles->galeria, true);
        } else {
            $this->galeriaArray = [];
        }
        $this->vendedor_id = $this->inmuebles->vendedor_id;

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

    public function render()
    {
        return view('livewire.inmuebles.edit');
    }

    public function update()
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
        // Encuentra el alumno identificado
        $inmuebles = Inmuebles::find($this->identificador);

        // Guardar datos validados
        $inmueblesSave = $inmuebles->update([
            'titulo' => $this->titulo,
            'descripcion' => $this->descripcion,
            'm2' => $this->m2,
            'm2_construidos' => $this->m2_construidos,
            'valor_referencia' => $this->valor_referencia,
            'habitaciones' => $this->habitaciones,
            'banos' => $this->banos,
            'tipo_vivienda_id' => $this->tipo_vivienda_id,
            'vendedor_id' => $this->vendedor_id,
            'ubicacion' => $this->ubicacion,
            'cod_postal' => $this->cod_postal,
            'cert_energetico' => $this->cert_energetico,
            'cert_energetico_elegido' => $this->cert_energetico_elegido,
            'estado' => $this->estado,
            'galeria' => $this->galeria,
            'disponibilidad' => $this->disponibilidad,
            'otras_caracteristicas' => $this->otras_caracteristicas,
            'referencia_catastral' => $this->referencia_catastral,
            'inmobiliaria' => $this->inmobiliaria

        ]);

        // Alertas de guardado exitoso
        if ($inmueblesSave) {
            $this->alert('success', '¡Inmuebles actualizada correctamente!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar la información del inmuebles!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }
    }

    public function destroy()
    {
        // $product = Productos::find($this->identificador);
        // $product->delete();

        $this->alert('warning', '¿Seguro que desea borrar el inmuebles? No hay vuelta atrás', [
            'position' => 'center',
            'timer' => 3000,
            'toast' => false,
            'showConfirmButton' => true,
            'onConfirmed' => 'confirmDelete',
            'confirmButtonText' => 'Sí',
            'showDenyButton' => true,
            'denyButtonText' => 'No',
            'timerProgressBar' => true,
        ]);
    }

    public function getListeners()
    {
        return [
            'confirmed',
            'confirmDelete'
        ];
    }

    // Función para cuando se llama a la alerta
    public function confirmed()
    {
        // Do something
        return redirect()->route('inmuebles.index');
    }
    // Función para cuando se llama a la alerta
    public function confirmDelete()
    {
        $inmuebles = Inmuebles::find($this->identificador);
        $inmuebles->delete();
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
