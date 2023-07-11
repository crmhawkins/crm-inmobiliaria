<?php

namespace App\Http\Livewire\Productoscategories;

use Livewire\Component;
use App\Models\ProductosCategories;
use App\Models\TipoProducto;
use Livewire\WithPagination;


class IndexComponent extends Component

{

    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $tipos_producto;
    protected $productosCategories;

    public function mount()
    {
        $this->tipos_producto = TipoProducto::all();
    }
    public function render()
    {
        $this->productosCategories = ProductosCategories::paginate(6);
        return view('livewire.productos_categories.index-component', [
            'productosCategories' => $this->productosCategories,
        ]);
        // return view('livewire.productos-component');
    }
}
