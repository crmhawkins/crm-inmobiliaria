<?php

namespace App\Http\Livewire;

use App\Models\Clientes;
use App\Models\Inmuebles;
use App\Models\Evento;
use App\Models\Factura;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Dashboard extends Component
{
    public $stats = [];

    public function mount()
    {
        $inmobiliaria = session('inmobiliaria');

        // Estadísticas de clientes
        $this->stats['total_clientes'] = Clientes::when($inmobiliaria, function($query) use ($inmobiliaria) {
            if ($inmobiliaria == 'sayco' || $inmobiliaria == 'sancer') {
                $query->where(function($q) use ($inmobiliaria) {
                    $q->where('inmobiliaria', $inmobiliaria)
                      ->orWhereNull('inmobiliaria');
                });
            }
        })->count();

        $this->stats['clientes_nuevos_mes'] = Clientes::when($inmobiliaria, function($query) use ($inmobiliaria) {
            if ($inmobiliaria == 'sayco' || $inmobiliaria == 'sancer') {
                $query->where(function($q) use ($inmobiliaria) {
                    $q->where('inmobiliaria', $inmobiliaria)
                      ->orWhereNull('inmobiliaria');
                });
            }
        })
        ->whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->count();

        // Estadísticas de inmuebles
        $this->stats['total_inmuebles'] = Inmuebles::when($inmobiliaria, function($query) use ($inmobiliaria) {
            if ($inmobiliaria == 'sayco' || $inmobiliaria == 'sancer') {
                $query->where(function($q) use ($inmobiliaria) {
                    $q->where('inmobiliaria', $inmobiliaria)
                      ->orWhereNull('inmobiliaria');
                });
            }
        })->count();

        $this->stats['inmuebles_disponibles'] = Inmuebles::when($inmobiliaria, function($query) use ($inmobiliaria) {
            if ($inmobiliaria == 'sayco' || $inmobiliaria == 'sancer') {
                $query->where(function($q) use ($inmobiliaria) {
                    $q->where('inmobiliaria', $inmobiliaria)
                      ->orWhereNull('inmobiliaria');
                });
            }
        })
        ->where('disponibilidad', 'Disponible')
        ->count();

        // Estadísticas de eventos/citas
        $this->stats['total_eventos'] = Evento::when($inmobiliaria, function($query) use ($inmobiliaria) {
            if ($inmobiliaria == 'sayco' || $inmobiliaria == 'sancer') {
                $query->where(function($q) use ($inmobiliaria) {
                    $q->where('inmobiliaria', $inmobiliaria)
                      ->orWhereNull('inmobiliaria');
                });
            }
        })->count();

        $this->stats['eventos_hoy'] = Evento::when($inmobiliaria, function($query) use ($inmobiliaria) {
            if ($inmobiliaria == 'sayco' || $inmobiliaria == 'sancer') {
                $query->where(function($q) use ($inmobiliaria) {
                    $q->where('inmobiliaria', $inmobiliaria)
                      ->orWhereNull('inmobiliaria');
                });
            }
        })
        ->whereDate('fecha_inicio', today())
        ->count();

        $this->stats['eventos_proximos'] = Evento::with(['cliente', 'inmueble'])
            ->when($inmobiliaria, function($query) use ($inmobiliaria) {
                if ($inmobiliaria == 'sayco' || $inmobiliaria == 'sancer') {
                    $query->where(function($q) use ($inmobiliaria) {
                        $q->where('inmobiliaria', $inmobiliaria)
                          ->orWhereNull('inmobiliaria');
                    });
                }
            })
            ->whereBetween('fecha_inicio', [now(), now()->addDays(7)])
            ->orderBy('fecha_inicio', 'asc')
            ->limit(5)
            ->get();

        // Estadísticas de facturación
        $this->stats['total_facturas'] = Factura::when($inmobiliaria, function($query) use ($inmobiliaria) {
            if ($inmobiliaria == 'sayco' || $inmobiliaria == 'sancer') {
                $query->whereHas('cliente', function($q) use ($inmobiliaria) {
                    $q->where(function($subQ) use ($inmobiliaria) {
                        $subQ->where('inmobiliaria', $inmobiliaria)
                             ->orWhereNull('inmobiliaria');
                    });
                });
            }
        })->count();

        $this->stats['facturacion_mes'] = Factura::when($inmobiliaria, function($query) use ($inmobiliaria) {
            if ($inmobiliaria == 'sayco' || $inmobiliaria == 'sancer') {
                $query->whereHas('cliente', function($q) use ($inmobiliaria) {
                    $q->where(function($subQ) use ($inmobiliaria) {
                        $subQ->where('inmobiliaria', $inmobiliaria)
                             ->orWhereNull('inmobiliaria');
                    });
                });
            }
        })
        ->whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->sum('total');
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
