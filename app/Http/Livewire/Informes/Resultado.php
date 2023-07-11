<?php

namespace App\Http\Livewire\Informes;

use App\Models\Clients;
use App\Models\TipoInforme;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Component;

class Resultado extends Component
{
    public $tipo_informe;
    public $datos;
    public $ruta;
    public $fecha_inicio;
    public $fecha_fin;
    public $servicio;

    public function mount($datos, $tipo_informe, $fecha_inicio, $fecha_fin, $servicio)
    {
        $this->datos = $datos;
        $this->tipo_informe = $tipo_informe;
        $this->fecha_inicio = $fecha_inicio;
        $this->fecha_fin = $fecha_fin;
        $this->servicio = $servicio;

        switch ($this->tipo_informe) {
            case '1':

                $nombreInforme = TipoInforme::find($this->tipo_informe)->nombre;

                // Crea el PDF
                $pdf = PDF::loadView('informes.informe', ['datos' => $datos, 'tipo_informe' => $tipo_informe, 'nombreInforme' => $nombreInforme, 'fecha_inicio' => $fecha_inicio, 'fecha_fin' => $fecha_fin, 'servicio' => $servicio]);

                // Genera un nombre de archivo único
                $nombreArchivo = 'informe_' . time() . '.pdf';

                // Guarda el PDF en el directorio storage/app/public
                $pdf->save($nombreArchivo, 'public');
                // Devuelve la ruta del archivo
                $this->ruta = $nombreArchivo;
                break;

            case '2':
                $nombreInforme = TipoInforme::find($this->tipo_informe)->nombre;

                // Crea el PDF
                $pdf = PDF::loadView('informes.informe', ['datos' => $datos, 'tipo_informe' => $tipo_informe, 'nombreInforme' => $nombreInforme, 'fecha_inicio' => $fecha_inicio, 'fecha_fin' => $fecha_fin, 'servicio' => $servicio]);

                // Genera un nombre de archivo único
                $nombreArchivo = 'informe_' . time() . '.pdf';

                // Guarda el PDF en el directorio storage/app/public
                $pdf->save($nombreArchivo, 'public');
                // Devuelve la ruta del archivo
                $this->ruta = $nombreArchivo;
                break;
            case '3':

                break;
            case '4':
                # code...
                break;
            case '5':

                $nombreInforme = TipoInforme::find($this->tipo_informe)->nombre;
                $total = 0;
                foreach($datos as $dato){
                    $total += $dato['total'];
                    $cliente = $dato['cliente'];
                }
                $clienteName = Clients::find($cliente)->nombre;
                $pdf = PDF::loadView('informes.informe', ['datos' => $datos, 'cliente' => $cliente, 'clienteName' => $clienteName, 'tipo_informe' => $tipo_informe, 'nombreInforme' => $nombreInforme, 'fecha_inicio' => $fecha_inicio, 'fecha_fin' => $fecha_fin, 'servicio' => $servicio, 'total' => $total]);

                // Genera un nombre de archivo único
                $nombreArchivo = 'informe_' . time() . '.pdf';

                // Guarda el PDF en el directorio storage/app/public
                $pdf->save($nombreArchivo, 'public');
                // Devuelve la ruta del archivo
                $this->ruta = $nombreArchivo;

                break;
            case '6':
                # code...
                break;
            case '7':
                # code...
                break;
            case '8':
                # code...
                break;
            case '9':
                # code...
                break;
            case '10':
                # code...
                break;
            case '11':
                # code...
                break;
            case '12':
                # code...
                break;
            case '13':
                # code...
                break;
            case '14':
                # code...
                break;
            case '15':
                # code...
                break;
            case '16':
                # code...
                break;
            case '17':
                # code...
                break;
            case '18':
                # code...
                break;
            case '19':
                # code...
                break;

            default:
                # code...
                break;
        }
    }
    public function render()
    {
        return view('livewire.informes.resultado');
    }
}
