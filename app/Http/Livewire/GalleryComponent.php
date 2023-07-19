<?php

namespace App\Http\Livewire;

use Livewire\Component;

class GalleryComponent extends Component
{

    public $galeria;
    protected $listeners = ['refreshGalleryComponent' => 'refrescarGaleria'];

    public function render()
    {
        return view('livewire.gallery-component');
    }

    public function refrescarGaleria($galeria)
    {
        $this->galeria = $galeria;
    }
}
