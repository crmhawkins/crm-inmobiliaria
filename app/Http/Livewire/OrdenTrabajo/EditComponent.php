<?php

namespace App\Http\Livewire\OrdenTrabajo;

use App\Models\Presupuesto;
use App\Models\User;
use Carbon\Carbon;
use App\Models\Clients;
use App\Models\OrdenTrabajo;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use App\Models\Productos;
use App\Models\Trabajador;
use Livewire\WithFileUploads;


class EditComponent extends Component
{
    use LivewireAlert;
    use WithFileUploads;

    public $identificador;
    public $tarea;
    public $numero_presupuesto;
    public $users;
    public $fecha_emision;
    public $cliente_id = 0; // 0 por defecto por si no se selecciona ninguna
    public $matricula;
    public $kilometros;
    public $trabajador_id = 0; // 0 por defecto por si no se selecciona ninguna
    public $precio = 0;
    public $origen;
    public $descripcion;
    public $observaciones = "";
    public $tiempo_lista = [];
    public $trabajadores_name = [];


    public $realizables = [];
    public $solicitados = [];
    public $daños = [];
    public $documentosArray = [];
    public $documentos;

    public $documento;

    public $rutasDocumentos = [];
    public $rutasDocumentosMostrar = [];


    public $lista = [];
    public $listaArticulos;

    public $trabajadorSeleccionado;
    public $trabajadores = [];

    public $nuevoRealizar;
    public $nuevoSolicitado;
    public $nuevoDaño;

    public $producto;
    public $productos;

    public $cantidad;
    public $clientes;
    public $fecha;
    public $id_cliente;
    public $id_presupuesto;
    public $trabajos_solicitados;
    public $trabajos_realizar;
    public $operarios;
    public $estado;
    public $operarios_tiempo = [];
    public $danos_localizados;





    public function mount()
    {
        $this->tarea = OrdenTrabajo::find($this->identificador);
        $this->users = User::all();
        $this->productos = Productos::all(); // datos que se envian al select2
        $this->clientes = Clients::all();
        $this->numero_presupuesto = $this->tarea->presupuesto->numero_presupuesto;
        $this->fecha_emision = $this->tarea->presupuesto->fecha_emision;
        $this->cliente_id = $this->tarea->presupuesto->cliente_id;
        $this->trabajador_id = $this->tarea->presupuesto->trabajador_id;
        $this->lista = (array) json_decode($this->tarea->presupuesto->listaArticulos);
        $this->kilometros = $this->tarea->presupuesto->kilometros;
        $this->matricula = $this->tarea->presupuesto->matricula;
        $this->precio = $this->tarea->presupuesto->precio;
        $this->origen = $this->tarea->presupuesto->origen;
        if($this->tarea->operarios){
            $this->fecha = $this->tarea->fecha;
            $this->id_cliente = $this->tarea->id_cliente;
            $this->id_presupuesto = $this->tarea->id_presupuesto;
            $this->observaciones = $this->tarea->observaciones;
            $this->trabajos_solicitados = json_decode($this->tarea->trabajos_solicitados, true);
            $this->solicitados = $this->trabajos_solicitados;
            $this->trabajos_realizar = json_decode($this->tarea->trabajos_realizar, true);
            $this->realizables = $this->trabajos_realizar;
            $this->operarios = json_decode($this->tarea->operarios, true);
            $this->trabajadores = $this->operarios;
            $this->estado = $this->tarea->estado;
            $this->descripcion = $this->tarea->descripcion;
            $this->documentos = json_decode($this->tarea->documentos, true);
            $this->rutasDocumentos = $this->documentos;
            $this->operarios_tiempo = json_decode($this->tarea->operarios_tiempo, true);
            $this->danos_localizados = json_decode($this->tarea->danos_localizados, true);
            $this->daños = $this->danos_localizados;
        } else{
            $this->fecha = $this->tarea->presupuesto->fecha_emision;
            $this->id_cliente = $this->tarea->presupuesto->cliente_id;
            $this->id_presupuesto = $this->tarea->presupuesto->id;
            $this->observaciones = $this->tarea->presupuesto->observaciones;
        }
    }

    public function render()
    {
        return view('livewire.orden-trabajo.edit-component');
    }

    // Al hacer update en el formulario
    public function update()
    {
        $this->estado = ($this->estado != null) ? $this->estado : 'Asignada';
        $this->trabajos_solicitados = json_encode($this->solicitados);
        $this->trabajos_realizar = json_encode($this->realizables);
        $this->danos_localizados = json_encode($this->daños);
        $this->operarios = json_encode($this->trabajadores);
        $this->documentos = json_encode($this->rutasDocumentos);
        $this->operarios_tiempo = json_encode($this->operarios_tiempo);
        $this->tiempo_lista = json_encode($this->tiempo_lista);

        // Validación de datos
        $this->validate(
            [
                'fecha' => 'required',
                'id_cliente' => 'required',
                'id_presupuesto' => 'required',
                'observaciones' => 'required',
                'trabajos_solicitados' => 'required',
                'trabajos_realizar' => 'required',
                'operarios' => 'required',
                'estado' => 'required',
                'descripcion' => 'required',
                'documentos' => 'required',
                'operarios_tiempo' => 'required',
                'danos_localizados' => 'required',

            ],
            // Mensajes de error
            [
                'numero_presupuesto.required' => 'El número de presupuesto es obligatorio.',
                'fecha_emision.required' => 'La fecha de emision es obligatoria.',
                'alumno_id.required' => 'El alumno es obligatorio.',
                'curso_id.required' => 'El curso es obligatorio.',
                'detalles.required' => 'Los detalles son obligatorios',
                'precio.required' => 'El precio es obligaorio',
                'estado.required' => 'El estado es obligatorio',
                'observaciones.required' => 'La observación es obligatoria',
            ]
        );

        // Encuentra el identificador
        $presupuestos = OrdenTrabajo::find($this->identificador);

        // Guardar datos validados
        $presupuestosSave = $presupuestos->update([
            'fecha' => $this->fecha,
            'id_cliente' => $this->id_cliente,
            'id_presupuesto' => $this->id_presupuesto,
            'observaciones' => $this->observaciones,
            'trabajos_solicitados' => $this->trabajos_solicitados,
            'trabajos_realizar' => $this->trabajos_realizar,
            'operarios' => $this->operarios,
            'estado' => $this->estado,
            'descripcion' => $this->descripcion,
            'tiempo_lista' => $this->tiempo_lista,
            'documentos' => $this->documentos,
            'operarios_tiempo' => $this->operarios_tiempo,
            'danos_localizados' => $this->danos_localizados,
        ]);

        $presupuestos->trabajadores()->sync($this->trabajadores);

        $presupuestos->presupuesto->update(['estado' => 'Asignado']);

        if ($presupuestosSave) {
            $this->alert('success', '¡Tarea actualizada correctamente!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar la información de la tarea!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }

        session()->flash('message', '¡Tarea actualizada correctamente!');

        $this->emit('productUpdated');
    }

    // Eliminación
    public function destroy()
    {

        $this->alert('warning', '¿Seguro que desea borrar la tarea? No hay vuelta atrás', [
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

    // Función para cuando se llama a la alerta
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
        return redirect()->route('orden-trabajo.index');
    }

    public function agregarSolicitado()
    {
        array_push($this->solicitados, $this->nuevoSolicitado);
        $this->nuevoSolicitado = '';
    }
    public function agregarRealizar()
    {
        array_push($this->realizables, $this->nuevoRealizar);
        $this->nuevoRealizar = '';
    }

    public function agregarDaño()
    {
        array_push($this->daños, $this->nuevoDaño);
        $this->nuevoDaño = '';
    }

    public function subirArchivo()
    {
        foreach ($this->documentosArray as $documento) {
            $this->documento = $documento;
            $this->validate([
                'documento' => 'file|max:10000',
            ]);

            $nombreDelArchivo = time() . '_' . $this->documento->getClientOriginalName();
            $rutaDocumento = $this->documento->storeAs('documentos', $nombreDelArchivo, 'public');

            // Agrega la ruta del archivo al array de rutas de documentos
            $this->rutasDocumentos[] = $rutaDocumento;

            $this->documento = "";
        }

        $this->documentosArray = [];
    }

    public function agregarTrabajador()
    {
        if (in_array($this->trabajadorSeleccionado, $this->trabajadores)) {
            $this->alert('warning', "Este trabajador ya está asignado");
        } else {
            if ($this->trabajadorSeleccionado == Auth::id()) {
                array_push($this->trabajadores, Auth::id());
                array_push($this->trabajadores_name, Auth::user());
            } else {
                array_push($this->trabajadores, $this->trabajadorSeleccionado);
                array_push($this->trabajadores_name, $this->users->find($this->trabajadorSeleccionado));
            }
        }
        $this->trabajadorSeleccionado = "";
    }

    public function reducir()
    {
        if (isset($this->lista[$this->producto])) {
            if ($this->lista[$this->producto] - $this->cantidad <= 0) {
                $this->precio -= ((Productos::where('id', $this->producto)->first()->precio_venta) * $this->lista[$this->producto]);
                unset($this->lista[$this->producto]);
            } else {
                $this->lista[$this->producto] -= $this->cantidad;
                $this->precio -= ((Productos::where('id', $this->producto)->first()->precio_venta) * $this->cantidad);
            }
        } else {
            $this->alert('warning', "Este producto no está en la lista");
        }
        $this->producto = "";
        $this->cantidad = 0;
    }
}
