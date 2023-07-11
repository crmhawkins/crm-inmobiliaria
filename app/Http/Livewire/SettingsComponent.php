<?php

namespace App\Http\Livewire;
use Illuminate\Support\Facades\Route;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Models\Settings;
use Illuminate\Support\Facades\Storage;

class SettingsComponent extends Component
{   
    use LivewireAlert;

    public $empresa;
    public $file;


    public function render()
    {
        $this->empresa = Settings::whereNull('deleted_at')->first();

        
        // $this->file= Storage::disk('public_local')->get($this->empresa->photo);
        // dd($this->file);
        return view('livewire.settings.settings-component');
    }
    
}
