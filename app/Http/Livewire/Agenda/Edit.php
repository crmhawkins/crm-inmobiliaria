<?php

namespace App\Http\Livewire\Agenda;

use App\Models\Clientes;
use App\Models\Inmuebles;
use Livewire\Component;
use App\Models\Evento;
use App\Models\HojaFirma;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Barryvdh\DomPDF\Facade\Pdf;

class Edit extends Component
{
    use LivewireAlert;

    public $identificador;

    public $eventos;
    public $titulo;
    public $descripcion;
    public $fecha_inicio;
    public $fecha_fin;
    public $tipo_tarea;
    public $cliente_id;
    public $inmueble_id;
    public $clientes;
    public $inmuebles;
    public $inmobiliaria = null;
    
    // Propiedades para hoja de firma
    public $showFirmaModal = false;
    public $hojaFirmaActual = null;
    public $firmaCliente = null;
    public $nombreCliente = '';
    public $observaciones = '';

    public function mount()
    {
        $this->eventos = Evento::with('hojasFirma')->find($this->identificador);
        
        // Filtrar clientes e inmuebles por inmobiliaria
        if (request()->session()->get('inmobiliaria') == 'sayco') {
            $this->clientes = Clientes::where(function($query) {
                $query->where('inmobiliaria', true)->orWhereNull('inmobiliaria');
            })->orderBy('nombre_completo')->get();
            
            $this->inmuebles = Inmuebles::where(function($query) {
                $query->where('inmobiliaria', true)->orWhereNull('inmobiliaria');
            })->orderBy('titulo')->get();
        } else {
            $this->clientes = Clientes::where(function($query) {
                $query->where('inmobiliaria', false)->orWhereNull('inmobiliaria');
            })->orderBy('nombre_completo')->get();
            
            $this->inmuebles = Inmuebles::where(function($query) {
                $query->where('inmobiliaria', false)->orWhereNull('inmobiliaria');
            })->orderBy('titulo')->get();
        }

        $this->titulo =  $this->eventos->titulo ?? '';
        $this->descripcion =  $this->eventos->descripcion ?? '';
        
        // Formatear fechas para datetime-local input (formato: Y-m-d\TH:i)
        if ($this->eventos->fecha_inicio) {
            $this->fecha_inicio = \Carbon\Carbon::parse($this->eventos->fecha_inicio)->format('Y-m-d\TH:i');
        } else {
            $this->fecha_inicio = '';
        }
        
        if ($this->eventos->fecha_fin) {
            $this->fecha_fin = \Carbon\Carbon::parse($this->eventos->fecha_fin)->format('Y-m-d\TH:i');
        } else {
            $this->fecha_fin = '';
        }
        
        $this->tipo_tarea =  $this->eventos->tipo_tarea ?? 'opcion_1';
        $this->cliente_id =  $this->eventos->cliente_id;
        $this->inmueble_id =  $this->eventos->inmueble_id;
        $this->inmobiliaria =  $this->eventos->inmobiliaria;

        \Log::info('Evento cargado para edición', [
            'evento_id' => $this->identificador,
            'cliente_id' => $this->cliente_id,
            'inmueble_id' => $this->inmueble_id,
            'clientes_count' => $this->clientes->count(),
            'inmuebles_count' => $this->inmuebles->count()
        ]);
    }

    public function render()
    {

        return view('livewire.agenda.edit');
    }

    public function update()
    {
        if ($this->tipo_tarea == 'opcion_1') {
            $cliente = Clientes::where('id', $this->cliente_id)->first();
            $inmueble = Inmuebles::where('id', $this->inmueble_id)->first();
            
            if ($cliente) {
                $this->titulo = "Cita con " . $cliente->nombre_completo;
                $this->descripcion = "Cliente citado: " . $cliente->nombre_completo;
                if ($inmueble) {
                    $this->descripcion .= "<br>Inmueble en relación a la cita: " . $inmueble->titulo;
                }
            }
        }

        // Convertir fechas de datetime-local a formato MySQL si es necesario
        if ($this->fecha_inicio && strpos($this->fecha_inicio, 'T') !== false) {
            $this->fecha_inicio = \Carbon\Carbon::parse($this->fecha_inicio)->format('Y-m-d H:i:s');
        }
        
        if ($this->fecha_fin) {
            if (strpos($this->fecha_fin, 'T') !== false) {
                $this->fecha_fin = \Carbon\Carbon::parse($this->fecha_fin)->format('Y-m-d H:i:s');
            }
        } else {
            $this->fecha_fin = $this->fecha_inicio;
        }

        if ($this->inmobiliaria == null) {
            if (request()->session()->get('inmobiliaria') == 'sayco') {
                $this->inmobiliaria = true;
            } else {
                $this->inmobiliaria = false;
            }
        } else {
            $this->inmobiliaria = null;
        }

        $this->validate(
            [
                'titulo' => 'required',
                'descripcion' => 'required',
                'fecha_inicio' => 'required',
                'fecha_fin' => 'required',
                'tipo_tarea' => 'required',
                'cliente_id' => 'nullable',
                'inmueble_id' => 'nullable',
                'inmobiliaria' => 'nullable',
            ],
            // Mensajes de error
            [
                'fecha_inicio.required' => 'Introduce una fecha de inicio.',
            ]
        );


        // Guardar datos validados
        // Encuentra el alumno identificado
        $clientes = Evento::find($this->identificador);

        // Guardar datos validados
        $clientesSave = $clientes->update([
            'titulo' => $this->titulo,
            'descripcion' => $this->descripcion,
            'fecha_inicio' => $this->fecha_inicio,
            'fecha_fin' => $this->fecha_fin,
            'tipo_tarea' => $this->tipo_tarea,
            'cliente_id' => $this->cliente_id,
            'inmueble_id' => $this->inmueble_id,
            'inmobiliaria' => $this->inmobiliaria,
        ]);

        // Alertas de guardado exitoso
        if ($clientesSave) {
            $this->alert('success', '¡Clientes actualizada correctamente!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar la información del clientes!', [
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

        $this->alert('warning', '¿Seguro que desea borrar el clientes? No hay vuelta atrás', [
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
            'confirmDelete',
            'guardarFirmaDesdeJS',
            'cerrarModalDesdeJS' => 'cerrarModalFirma'
        ];
    }

    // Función para cuando se llama a la alerta
    public function confirmed()
    {
        // Do something
        return redirect()->route('agenda.index');
    }
    // Función para cuando se llama a la alerta
    public function confirmDelete()
    {
        $clientes = Evento::find($this->identificador);
        $clientes->delete();
        return redirect()->route('agenda.index');
    }

    // Métodos para hojas de firma
    public function abrirModalFirma()
    {
        try {
            // Recargar el evento con relaciones por si acaso
            $this->eventos = Evento::with(['hojasFirma', 'cliente', 'inmueble'])->find($this->identificador);
            
            if (!$this->eventos) {
                $this->alert('error', 'No se pudo cargar el evento', [
                    'position' => 'top-end',
                    'timer' => 3000,
                    'toast' => true,
                ]);
                return;
            }
            
            // Activar el modal
            $this->showFirmaModal = true;
            
            // Emitir evento para que JavaScript abra el modal
            $this->dispatchBrowserEvent('modal-firma-abierto');
            
            // Cargar hoja de firma existente si existe
            $this->hojaFirmaActual = $this->eventos->hojasFirma()->first();
            
            // Obtener cliente automáticamente de la cita
            if ($this->eventos->cliente) {
                $this->nombreCliente = $this->eventos->cliente->nombre_completo ?? '';
            }
            
            if ($this->hojaFirmaActual) {
                $this->firmaCliente = $this->hojaFirmaActual->firma_cliente;
                // Solo usar nombre de la hoja de firma si no hay cliente en el evento
                if (!$this->eventos->cliente) {
                    $this->nombreCliente = $this->hojaFirmaActual->nombre_cliente ?? '';
                }
                $this->observaciones = $this->hojaFirmaActual->observaciones ?? '';
            } else {
                // Limpiar firma
                $this->firmaCliente = null;
                $this->observaciones = '';
            }
        } catch (\Exception $e) {
            \Log::error('Error al abrir modal de firma: ' . $e->getMessage());
            $this->alert('error', 'Error al abrir el modal de firma: ' . $e->getMessage(), [
                'position' => 'top-end',
                'timer' => 3000,
                'toast' => true,
            ]);
        }
    }

    public function cerrarModalFirma()
    {
        $this->showFirmaModal = false;
        $this->reset(['firmaCliente', 'nombreCliente', 'observaciones']);
        $this->hojaFirmaActual = null;
        $this->dispatchBrowserEvent('modal-firma-cerrado');
    }
    
    public function updatedShowFirmaModal($value)
    {
        // Si se cierra desde Livewire, emitir evento para limpiar backdrop
        if (!$value) {
            $this->dispatchBrowserEvent('limpiar-backdrop-modal');
        }
    }

    public function guardarFirma($firmaData = null, $observacionesData = null)
    {
        // Si se pasan parámetros desde JavaScript, usarlos
        if ($firmaData) {
            $this->firmaCliente = $firmaData;
        }
        if ($observacionesData !== null) {
            $this->observaciones = $observacionesData;
        }

        // Asegurar que el cliente está cargado desde el evento
        if (!$this->nombreCliente && $this->eventos && $this->eventos->cliente) {
            $this->nombreCliente = $this->eventos->cliente->nombre_completo ?? '';
        }

        // Validar
        if (!$this->nombreCliente || strlen($this->nombreCliente) < 2) {
            $this->alert('error', 'El nombre del cliente es requerido', [
                'position' => 'top-end',
                'timer' => 3000,
                'toast' => true,
            ]);
            return;
        }

        if (!$this->firmaCliente) {
            $this->alert('error', 'Por favor, dibuja la firma antes de guardar', [
                'position' => 'top-end',
                'timer' => 3000,
                'toast' => true,
            ]);
            return;
        }

        try {
            $hojaFirma = $this->hojaFirmaActual ?? new HojaFirma();
            
            if (!$hojaFirma->exists) {
                $hojaFirma->evento_id = $this->identificador;
            }

            $hojaFirma->firma_cliente = $this->firmaCliente;
            $hojaFirma->nombre_cliente = $this->nombreCliente;
            $hojaFirma->observaciones = $this->observaciones ?? '';
            
            // Establecer nombre del agente actual
            $user = Auth::user();
            $hojaFirma->nombre_agente = $user->nombre_completo ?? $user->name ?? 'Usuario';
            
            $hojaFirma->save();
            $this->hojaFirmaActual = $hojaFirma;
            
            // Recargar relación
            $this->eventos->refresh();
            
            $this->alert('success', 'Firma guardada correctamente', [
                'position' => 'top-end',
                'timer' => 3000,
                'toast' => true,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error guardando firma: ' . $e->getMessage());
            $this->alert('error', 'Error al guardar la firma: ' . $e->getMessage(), [
                'position' => 'top-end',
                'timer' => 5000,
                'toast' => true,
            ]);
        }
    }

    public function guardarFirmasYGenerarPDF($firmaData = null, $observacionesData = null)
    {
        // Si se pasan parámetros desde JavaScript, usarlos
        if ($firmaData) {
            $this->firmaCliente = $firmaData;
        }
        if ($observacionesData !== null) {
            $this->observaciones = $observacionesData;
        }

        // Asegurar que el cliente está cargado desde el evento
        if (!$this->nombreCliente && $this->eventos && $this->eventos->cliente) {
            $this->nombreCliente = $this->eventos->cliente->nombre_completo ?? '';
        }

        // Validar
        if (!$this->nombreCliente || strlen($this->nombreCliente) < 2) {
            $this->alert('error', 'El nombre del cliente es requerido', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
            return;
        }

        if (!$this->firmaCliente) {
            $this->alert('error', 'Por favor, dibuja la firma del cliente antes de generar el PDF', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
            return;
        }

        try {
            // Guardar la firma si no está guardada
            if (!$this->hojaFirmaActual) {
                $hojaFirma = new HojaFirma();
                $hojaFirma->evento_id = $this->identificador;
                $hojaFirma->firma_cliente = $this->firmaCliente;
                $hojaFirma->nombre_cliente = $this->nombreCliente;
                $hojaFirma->observaciones = $this->observaciones ?? '';
                
                // Establecer nombre del agente actual
                $user = Auth::user();
                $hojaFirma->nombre_agente = $user->nombre_completo ?? $user->name ?? '';
                
                $hojaFirma->fecha_firma = now();
                $hojaFirma->save();
                $this->hojaFirmaActual = $hojaFirma;
            } else {
                $user = Auth::user();
                $this->hojaFirmaActual->update([
                    'firma_cliente' => $this->firmaCliente,
                    'nombre_cliente' => $this->nombreCliente,
                    'nombre_agente' => $user->nombre_completo ?? $user->name ?? '',
                    'observaciones' => $this->observaciones ?? '',
                    'fecha_firma' => now(),
                ]);
            }

            // Generar PDF
            $this->generarPDF($this->hojaFirmaActual->id);
            
            $this->alert('success', 'PDF generado correctamente', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
            
            $this->cerrarModalFirma();
        } catch (\Exception $e) {
            \Log::error('Error generando PDF: ' . $e->getMessage());
            $this->alert('error', 'Error al generar el PDF: ' . $e->getMessage(), [
                'position' => 'center',
                'timer' => 5000,
                'toast' => false,
            ]);
        }
    }

    private function generarPDF($hojaFirmaId)
    {
        $hojaFirma = HojaFirma::find($hojaFirmaId);
        
        if (!$hojaFirma) {
            return;
        }

        $cliente = $this->eventos->cliente;
        $inmueble = $this->eventos->inmueble;

        // Convertir firma base64 a archivo temporal si existe
        $rutaFirmaTemporal = null;
        if ($hojaFirma->firma_cliente) {
            try {
                // Verificar si es una imagen base64
                if (preg_match('/data:image\/(\w+);base64,/', $hojaFirma->firma_cliente, $matches)) {
                    $imageData = $hojaFirma->firma_cliente;
                    $imageType = $matches[1]; // png, jpeg, etc.
                    
                    // Decodificar base64
                    $imageData = str_replace('data:image/' . $imageType . ';base64,', '', $imageData);
                    $imageData = base64_decode($imageData);
                    
                    // Crear directorio temporal en public (accesible para DomPDF)
                    $dirTemp = public_path('temp_firmas');
                    if (!File::exists($dirTemp)) {
                        File::makeDirectory($dirTemp, 0777, true);
                    }
                    
                    // Guardar imagen temporal
                    $nombreArchivoTemp = 'firma_' . $hojaFirmaId . '_' . time() . '.' . $imageType;
                    $rutaFirmaTemporal = $dirTemp . '/' . $nombreArchivoTemp;
                    file_put_contents($rutaFirmaTemporal, $imageData);
                    
                    // Usar solo el nombre del archivo relativo para public_path en la vista
                    $rutaFirmaTemporal = 'temp_firmas/' . $nombreArchivoTemp;
                } else {
                    // Si no es base64, puede ser una ruta de archivo
                    $rutaFirmaTemporal = $hojaFirma->firma_cliente;
                }
            } catch (\Exception $e) {
                \Log::error('Error procesando imagen de firma: ' . $e->getMessage());
                // Continuar sin la imagen si hay error
                $rutaFirmaTemporal = null;
            }
        }

        $datos = [
            'hoja_firma' => $hojaFirma,
            'evento' => $this->eventos,
            'cliente' => $cliente,
            'inmueble' => $inmueble,
            'fecha' => now()->format('d/m/Y'),
            'ruta_firma_temporal' => $rutaFirmaTemporal, // Pasar la ruta del archivo temporal
        ];

        $pdf = Pdf::loadView('agenda.hoja-firma-pdf', $datos);

        // Crear directorio si no existe
        $rutaDirectorio = 'hojas_visita/' . request()->session()->get('inmobiliaria', 'general');
        if (!File::exists(public_path($rutaDirectorio))) {
            File::makeDirectory(public_path($rutaDirectorio), 0777, true);
        }

        // Guardar PDF
        $nombreArchivo = 'hoja_firma_' . $hojaFirmaId . '_' . time() . '.pdf';
        $rutaPdf = $rutaDirectorio . '/' . $nombreArchivo;
        $rutaCompleta = public_path($rutaPdf);

        $pdf->save($rutaCompleta);

        // Actualizar ruta en la base de datos
        $hojaFirma->update(['ruta_pdf' => $rutaPdf]);
        
        // Limpiar archivo temporal de firma si se creó
        if ($rutaFirmaTemporal && strpos($rutaFirmaTemporal, 'temp_firmas/') === 0) {
            try {
                $rutaCompletaTemp = public_path($rutaFirmaTemporal);
                if (File::exists($rutaCompletaTemp)) {
                    File::delete($rutaCompletaTemp);
                }
            } catch (\Exception $e) {
                \Log::warning('No se pudo eliminar archivo temporal de firma: ' . $e->getMessage());
            }
        }
    }

    public function limpiarFirma()
    {
        $this->firmaCliente = null;
    }

    public function guardarFirmaDesdeJS($firmaData, $observaciones = '')
    {
        $this->firmaCliente = $firmaData;
        $this->observaciones = $observaciones;
        $this->guardarFirma();
    }
}
