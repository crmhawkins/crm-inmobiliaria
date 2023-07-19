<?php

namespace App\Http\Livewire\Factura;

use App\Mail\MailFactura;
use App\Models\Clientes;
use App\Models\Factura;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    protected $facturas;
    public $clientes;


    public function render()
    {

        if (request()->session()->get('inmobiliaria') == 'sayco') {
            $ids = Clientes::where('inmobiliaria', true)->orWhere('inmobiliaria', null)->get();
        } else {
            $ids = Clientes::where('inmobiliaria', false)->orWhere('inmobiliaria', null)->get();
        }
        $this->clientes = Clientes::all();
        $this->facturas = Factura::whereIn('cliente', $ids->pluck('id'))->get();

        return view('livewire.factura.index', [
            'facturas' => $this->facturas
        ]);
    }
    public function enviarCorreo($factura_id)
    {
        if (request()->session()->get('inmobiliaria') == 'sayco') {
            $inmobiliaria = 'sayco';
        } else if (request()->session()->get('inmobiliaria') == 'sancer') {
            $inmobiliaria = 'sancer';
        }

        $factura = Factura::where('id', $factura_id)->first();
        $documento = $factura->ruta_pdf;
        $fromEmail = Auth::user()->email;
        $toEmail = $this->clientes->where('id', $factura->cliente)->first()->email;
        $cliente = $this->clientes->where('id', $factura->cliente)->first()->nombre_completo;

        Mail::to($toEmail)->send(new MailFactura($inmobiliaria, $cliente, $fromEmail, $documento));

        session()->flash('message', 'Documento enviado correctamente!');
    }
}
