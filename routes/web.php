<?php

use App\Http\Controllers\AgendaController;
use App\Http\Controllers\CaracteristicasController;
use App\Http\Controllers\ClientesController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\InmueblesController;
use App\Http\Controllers\TipoViviendaController;
use App\Http\Controllers\VendedoresController;
use App\Http\Controllers\DocumentosController;
use App\Http\Controllers\ContratosController;
use App\Http\Controllers\VisitasController;
use App\Models\Caracteristicas;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;


// use App\Http\Middleware\IsAdmin;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    if (Auth::user()) {
        return Redirect::route('agenda.index');
    } else {
        return Redirect::route('login');
    }
})->name('/');

Route::get('/seleccion', [App\Http\Controllers\HomeController::class, 'index'])->name('seleccion');



Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'home'])->name('home');
Route::get('/cambio', [App\Http\Controllers\HomeController::class, 'cambio'])->name('cambio');

// Route::get('/clients', [App\Http\Controllers\ClientController::class, 'index'])->name('clients.index');

Route::group(['middleware' => 'is.admin', 'prefix' => 'admin'], function () {
    // Tipo de vivienda
    Route::get('tipovivienda', [TipoViviendaController::class, 'index'])->name('tipovivienda.index');

    // Inmuebles
    Route::get('inmuebles', [InmueblesController::class, 'index'])->name('inmuebles.index');
    Route::get('inmuebles/create', [InmueblesController::class, 'create'])->name('inmuebles.create');
    Route::post('inmuebles/store', [InmueblesController::class, 'store'])->name('inmuebles.store');
    Route::get('inmuebles/show/{inmueble}', [InmueblesController::class, 'show'])->name('inmuebles.show');
    Route::get('inmuebles/admin-show/{inmueble}', [InmueblesController::class, 'adminShow'])->name('inmuebles.admin-show');
    Route::get('inmuebles/edit/{inmueble}', [InmueblesController::class, 'edit'])->name('inmuebles.edit');
    Route::put('inmuebles/update/{inmueble}', [InmueblesController::class, 'update'])->name('inmuebles.update');
    Route::delete('inmuebles/destroy/{inmueble}', [InmueblesController::class, 'destroy'])->name('inmuebles.destroy');
    Route::post('inmuebles/import-json', [InmueblesController::class, 'importFromJson'])->name('inmuebles.import-json');
    Route::post('inmuebles/search', [InmueblesController::class, 'search'])->name('inmuebles.search');

    // Nuevas rutas para documentos y contratos
    Route::get('inmuebles/{inmueble}/documentos', [InmueblesController::class, 'documentos'])->name('inmuebles.documentos');
    Route::get('inmuebles/{inmueble}/contratos', [InmueblesController::class, 'contratos'])->name('inmuebles.contratos');
    Route::get('inmuebles/{inmueble}/visitas', [InmueblesController::class, 'visitas'])->name('inmuebles.visitas');
    Route::get('inmuebles/{inmueble}/caracteristicas', [InmueblesController::class, 'caracteristicas'])->name('inmuebles.caracteristicas');

    // Documentos
    Route::get('documentos/{documento}/download', [DocumentosController::class, 'download'])->name('documentos.download');
    Route::post('documentos/store', [DocumentosController::class, 'store'])->name('documentos.store');
    Route::delete('documentos/{documento}', [DocumentosController::class, 'destroy'])->name('documentos.destroy');

    // Contratos
    Route::get('contratos/create', [ContratosController::class, 'create'])->name('contratos.create');
    Route::post('contratos/store', [ContratosController::class, 'store'])->name('contratos.store');
    Route::get('contratos/{contrato}', [ContratosController::class, 'show'])->name('contratos.show');
    Route::get('contratos/{contrato}/edit', [ContratosController::class, 'edit'])->name('contratos.edit');
    Route::put('contratos/{contrato}', [ContratosController::class, 'update'])->name('contratos.update');
    Route::delete('contratos/{contrato}', [ContratosController::class, 'destroy'])->name('contratos.destroy');

    // Visitas
    Route::get('visitas/create', [VisitasController::class, 'create'])->name('visitas.create');
    Route::post('visitas/store', [VisitasController::class, 'store'])->name('visitas.store');
    Route::get('visitas/{visita}', [VisitasController::class, 'show'])->name('visitas.show');
    Route::get('visitas/{visita}/edit', [VisitasController::class, 'edit'])->name('visitas.edit');
    Route::put('visitas/{visita}', [VisitasController::class, 'update'])->name('visitas.update');
    Route::delete('visitas/{visita}', [VisitasController::class, 'destroy'])->name('visitas.destroy');

    // Clientes
    Route::get('clientes', [ClientesController::class, 'index'])->name('clientes.index');
    Route::get('clientes/create', [ClientesController::class, 'create'])->name('clientes.create');
    Route::post('clientes/store', [ClientesController::class, 'store'])->name('clientes.store');
    Route::get('clientes/show/{cliente}', [ClientesController::class, 'show'])->name('clientes.show');
    Route::get('clientes/edit/{cliente}', [ClientesController::class, 'edit'])->name('clientes.edit');
    Route::put('clientes/update/{cliente}', [ClientesController::class, 'update'])->name('clientes.update');
    Route::delete('clientes/destroy/{cliente}', [ClientesController::class, 'destroy'])->name('clientes.destroy');

    Route::post('/clientes/filtrar-inmuebles', [ClientesController::class, 'filtrarInmuebles'])->name('clientes.filtrarInmuebles');

    // Facturacion
    Route::get('facturacion', [FacturaController::class, 'index'])->name('facturacion.index');
    Route::get('facturacion/create', [FacturaController::class, 'create'])->name('facturacion.create');
    Route::post('facturacion/store', [FacturaController::class, 'store'])->name('facturacion.store');
    Route::get('facturacion/show/{factura}', [FacturaController::class, 'show'])->name('facturacion.show');
    Route::get('facturacion/edit/{factura}', [FacturaController::class, 'edit'])->name('facturacion.edit');
    Route::put('facturacion/update/{factura}', [FacturaController::class, 'update'])->name('facturacion.update');
    Route::delete('facturacion/destroy/{factura}', [FacturaController::class, 'destroy'])->name('facturacion.destroy');
    Route::get('facturacion/pdf/{factura}', [FacturaController::class, 'descargarPDF'])->name('facturacion.pdf');

    // Agenda
    Route::get('agenda', [AgendaController::class, 'index'])->name('agenda.index');

    // Vendedores
    Route::get('vendedores', [VendedoresController::class, 'index'])->name('vendedores.index');

    // Caracteristicas
    Route::get('caracteristicas', [CaracteristicasController::class, 'index'])->name('caracteristicas.index');
});

Route::group(['prefix' => 'laravel-filemanager', 'middleware' => ['web', 'auth']], function () {
    \UniSharp\LaravelFilemanager\Lfm::routes();
});

// Ruta para servir imágenes desde storage
Route::get('storage/photos/{folder}/{filename}', function ($folder, $filename) {
    $path = storage_path("app/public/photos/{$folder}/{$filename}");

    if (!file_exists($path)) {
        abort(404);
    }

    $file = file_get_contents($path);
    $type = mime_content_type($path);

    return response($file, 200)
        ->header('Content-Type', $type)
        ->header('Cache-Control', 'public, max-age=31536000');
})->where('filename', '.*');

// Rutas públicas para inmuebles
Route::get('inmueble/{inmueble}', [InmueblesController::class, 'publicShow'])->name('inmueble.public.show');
