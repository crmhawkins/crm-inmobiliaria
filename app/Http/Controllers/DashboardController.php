<?php

namespace App\Http\Controllers;

use App\Models\Inmuebles;
use App\Models\Clientes;
use App\Models\Evento;
use App\Models\Factura;
use App\Services\Idealista\IdealistaCustomerService;
use App\Services\Idealista\IdealistaPropertiesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $inmobiliaria = session('inmobiliaria');

        // Estadísticas de Inmuebles
        $totalInmuebles = Inmuebles::count();
        $inmueblesActivos = Inmuebles::where('estado', 'activo')->count();
        $inmueblesIdealista = Inmuebles::whereNotNull('idealista_property_id')->count();
        $inmueblesRecientes = Inmuebles::orderBy('created_at', 'desc')->limit(5)->get();

        // Estadísticas de Clientes
        $totalClientes = Clientes::count();
        $clientesRecientes = Clientes::orderBy('created_at', 'desc')->limit(5)->get();

        // Estadísticas de Eventos/Agenda
        $eventosHoy = Evento::whereDate('fecha_inicio', today())->count();
        $eventosProximos = Evento::whereDate('fecha_inicio', '>', today())
            ->orderBy('fecha_inicio', 'asc')
            ->limit(5)
            ->get();

        // Estadísticas de Facturación
        $facturasMes = Factura::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $ingresosMes = Factura::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total');

        // Estadísticas de Idealista
        $idealistaStats = null;
        try {
            $customerService = app(IdealistaCustomerService::class);
            $idealistaStats = $customerService->getPublicationInfo();
        } catch (\Exception $e) {
            Log::warning('No se pudieron obtener estadísticas de Idealista', ['error' => $e->getMessage()]);
        }

        // Gráfico de inmuebles por tipo
        $inmueblesPorTipo = Inmuebles::selectRaw('tipo_vivienda_id, COUNT(*) as total')
            ->with('tipoVivienda')
            ->groupBy('tipo_vivienda_id')
            ->get();

        // Gráfico de inmuebles por estado
        $inmueblesPorEstado = Inmuebles::selectRaw('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->get();

        // Inmuebles por mes (últimos 6 meses)
        $inmueblesPorMes = Inmuebles::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as mes, COUNT(*) as total')
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('mes')
            ->orderBy('mes', 'asc')
            ->get();

        return view('dashboard.index', compact(
            'totalInmuebles',
            'inmueblesActivos',
            'inmueblesIdealista',
            'inmueblesRecientes',
            'totalClientes',
            'clientesRecientes',
            'eventosHoy',
            'eventosProximos',
            'facturasMes',
            'ingresosMes',
            'idealistaStats',
            'inmueblesPorTipo',
            'inmueblesPorEstado',
            'inmueblesPorMes'
        ));
    }
}

