<?php

namespace App\Http\Livewire\Productoscategories;

use App\Models\Productos;
use App\Models\TipoProducto;
use App\Models\ProductosCategories;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class CreateComponent extends Component
{

    use LivewireAlert;

    public $tipos_producto;
    public $tipo_producto;
    public $nombre;

    public function mount()
    {
        $this->tipos_producto = TipoProducto::all();
    }

    public function render()
    {
        return view('livewire.productos_categories.create-component');
    }

    // Al hacer submit en el formulario
    public function submit()
    {
        // Validación de datos
        $validatedData = $this->validate([
            'nombre' => 'required',
            'tipo_producto' => 'required|min:1|max:5',
        ],
            // Mensajes de error
            [
                'tipo_producto.required' => 'El tipo de producto es necesario.',
                'tipo_producto.min' => "Error de tipo de producto.",
                'tipo_producto.max' => "Error de tipo de producto.",
                'nombre.required' => 'El nombre es obligatorio.',
            ]);

        // Guardar datos validados
        $productosSave = ProductosCategories::create($validatedData);

        // Alertas de guardado exitoso
        if ($productosSave) {
            $this->alert('success', 'Categoría registrada correctamente!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar la información del producto!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }
    }

    // Función para cuando se llama a la alerta
    public function getListeners()
    {
        return [
            'confirmed',
        ];
    }

    // Función para cuando se llama a la alerta
    public function confirmed()
    {
        // Do something
        return redirect()->route('productos-categories.index');

    }
}
