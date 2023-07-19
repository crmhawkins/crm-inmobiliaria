<?php

namespace App\Http\Livewire\Factura;

use App\Models\Clientes;
use App\Models\Factura;
use App\Models\Facturas;
use App\Models\Inmuebles;
use App\Models\Intereses;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Create extends Component
{
    use LivewireAlert;
    public $cliente;
    public $clientes;

    public $numero_factura;
    public $fecha;
    public $fecha_vencimiento;
    public $articulos;
    public $articulosArray = [];
    public $subtotal = 0;
    public $total = 0;
    public $ruta_pdf = null;
    public $condiciones;
    public $inmobiliaria = null;

    public function mount()
    {
        if (request()->session()->get('inmobiliaria') == 'sayco') {
            $this->clientes = Clientes::where('inmobiliaria', true)->orWhere('inmobiliaria', null)->get();
        } else {
            $this->clientes = Clientes::where('inmobiliaria', false)->orWhere('inmobiliaria', null)->get();
        }

        $this->articulosArray = [
            ['descripcion' => '', 'importe' => 0, 'impuesto' => 0]
        ];
    }

    // Renderizado del Componente
    public function render()
    {
        $this->numero_factura = $this->getNumeroFactura();
        return view('livewire.factura.create');
    }

    public function getNumeroFactura()
    {
        $year = date('Y');

        if (request()->session()->get('inmobiliaria') == 'sayco') {
            $lastFacturaThisYear = Factura::where('inmobiliaria', true)->whereYear('fecha', $year)
                ->orderBy('numero_factura', 'desc')
                ->first();
        } else {
            $lastFacturaThisYear = Factura::where('inmobiliaria', false)->whereYear('fecha', $year)
                ->orderBy('numero_factura', 'desc')
                ->first();
        }

        // Si hay una factura este año, incrementa el número de factura, sino empieza en 1.
        return $lastFacturaThisYear ? $lastFacturaThisYear->numero_factura + 1 : 1;
    }

    public function updatedArticulosArray()
    {
        $this->subtotal = 0;
        $this->total = 0;

        foreach ($this->articulosArray as $articulo) {
            $this->subtotal += (int) $articulo['importe'];
            $this->total += ((int) $articulo['importe'] + ((int) $articulo['importe'] * ((int) $articulo['impuesto'] / 100)));
        }
    }

    public function addArticulo()
    {
        $this->articulosArray[] = ['descripcion' => '', 'importe' => 0, 'impuesto' => 0];
    }

    public function removeArticulo($index)
    {
        unset($this->articulosArray[$index]);
        $this->articulosArray = array_values($this->articulosArray);
        $this->updatedArticulos();
    }


    public function submit()
    {
        if (request()->session()->get('inmobiliaria') == 'sayco') {
            $this->inmobiliaria = true;
        } else {
            $this->inmobiliaria = false;
        }

        $this->articulos = json_encode($this->articulosArray);

        $validatedData = $this->validate(
            [
                'cliente' => 'required',
                'numero_factura' => 'required',
                'fecha' => 'required',
                'fecha_vencimiento' => 'nullable',
                'condiciones' => 'required',
                'articulos' => 'nullable',
                'subtotal' => 'nullable',
                'total' => 'nullable',
                'ruta_pdf' => 'nullable',
                'inmobiliaria' => 'nullable',

            ],
            // Mensajes de error
            [
                'cliente.required' => 'Selecciona un cliente.',
                'fecha.required' => 'Escoge una fecha de emisión.',
                'condiciones.required' => 'Indica las condiciones y métodos de pago.',
            ]
        );

        $clientePDF = Clientes::where('id', $this->cliente)->first();
        // Genera el PDF a partir de la vista 'factura' y los datos de la factura.
        $pdf = Pdf::loadView('facturacion.generar', ['factura' => [
            'cliente_nombre' => $clientePDF->nombre_completo,
            'cliente_dni' => $clientePDF->dni,
            'numero_factura' => $this->numero_factura,
            'fecha' => $this->fecha,
            'fecha_vencimiento' => $this->fecha_vencimiento,
            'articulos' => $this->articulos,
            'subtotal' => $this->subtotal,
            'total' => $this->total,
            'condiciones' => $this->condiciones,
            'inmobiliaria' => $this->inmobiliaria,
        ]]);

        // Ruta del directorio donde se guardará el archivo
        $rutaDirectorio = 'facturas/' . request()->session()->get('inmobiliaria');

        // Crea el directorio si no existe
        if (!File::exists(public_path($rutaDirectorio))) {
            File::makeDirectory(public_path($rutaDirectorio), 0777, true);
        }

        // Ruta del archivo PDF
        $rutaPdf = $rutaDirectorio . '/factura_' . $this->numero_factura . '.pdf';
        $rutaCompleta = public_path($rutaPdf);

        // Guarda el PDF en un archivo en el servidor.
        $pdf->save($rutaCompleta);

        // Añade la ruta del PDF a los datos validados.
        $validatedData['ruta_pdf'] = $rutaPdf;

        // Guardar datos validados
        $facturasSave = Factura::create($validatedData);

        // Alertas de guardado exitoso
        if ($facturasSave) {

            $this->alert('success', '¡Factura generada correctamente!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar la factura!', [
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
        return redirect()->route('facturacion.index');
    }
}
