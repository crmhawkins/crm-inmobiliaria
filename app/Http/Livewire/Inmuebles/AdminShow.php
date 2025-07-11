<?php

namespace App\Http\Livewire\Inmuebles;

use App\Models\Caracteristicas;
use App\Models\TipoVivienda;
use App\Models\User;
use Livewire\Component;
use App\Models\Inmuebles;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Models\Clientes;

class AdminShow extends Component
{
    use LivewireAlert;

    public $identificador;
    public $inmuebles;
    public $caracteristicas;
    public $tipos_vivienda;
    public $vendedores;
    public $clientes;

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
    public $vendedor_id;
    public $galeriaArray = [];
    public $galeria;

    public function mount()
    {
        $this->inmuebles = Inmuebles::with(['tipoVivienda', 'vendedor'])->find($this->identificador);
        $this->tipos_vivienda = TipoVivienda::all();
        $this->vendedores = User::all();
        $this->caracteristicas = Caracteristicas::all();
        $this->clientes = Clientes::all();

        if ($this->inmuebles) {
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
            $this->estado = $this->inmuebles->estado;
            $this->disponibilidad = $this->inmuebles->disponibilidad;
            $this->otras_caracteristicasArray = json_decode($this->inmuebles->otras_caracteristicas, true) ?? [];
            $this->referencia_catastral = $this->inmuebles->referencia_catastral;
            $this->vendedor_id = $this->inmuebles->vendedor_id;

            if ($this->inmuebles->galeria != null) {
                $this->galeriaArray = json_decode($this->inmuebles->galeria, true);
            } else {
                $this->galeriaArray = [];
            }
        }
    }

    public function render()
    {
        return view('livewire.inmuebles.admin-show');
    }

    public function update()
    {
        $this->otras_caracteristicas = json_encode($this->otras_caracteristicasArray);
        $this->galeria = json_encode($this->galeriaArray);

        $inmuebles = Inmuebles::find($this->identificador);

        $inmuebles->update([
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
        ]);

        $this->alert('success', '¡Inmueble actualizado correctamente!', [
            'position' => 'center',
            'timer' => 3000,
            'toast' => false,
            'showConfirmButton' => true,
            'confirmButtonText' => 'OK',
            'timerProgressBar' => true,
        ]);
    }

    public function destroy()
    {
        $this->alert('warning', '¿Seguro que desea eliminar este inmueble? Esta acción no se puede deshacer.', [
            'position' => 'center',
            'timer' => null,
            'toast' => false,
            'showConfirmButton' => true,
            'onConfirmed' => 'confirmDelete',
            'confirmButtonText' => 'Sí, eliminar',
            'showDenyButton' => true,
            'denyButtonText' => 'Cancelar',
            'timerProgressBar' => false,
        ]);
    }

    public function confirmDelete()
    {
        $inmuebles = Inmuebles::find($this->identificador);
        $inmuebles->delete();

        $this->alert('success', 'Inmueble eliminado correctamente.', [
            'position' => 'center',
            'timer' => 2000,
            'toast' => false,
        ]);

        return redirect()->route('inmuebles.index');
    }

    public function getListeners()
    {
        return [
            'confirmDelete'
        ];
    }
}
