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
        return Redirect::route('dashboard.index');
    } else {
        return Redirect::route('login');
    }
})->name('/');

Route::get('/seleccion', [App\Http\Controllers\HomeController::class, 'index'])->name('seleccion');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'home'])->name('home');
Route::get('/cambio', [App\Http\Controllers\HomeController::class, 'cambio'])->name('cambio');

// Dashboard moderno
Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard.index')->middleware('is.admin');

// Proxy para Nominatim (evita problemas de CORS) - Accesible sin autenticación
Route::get('api/nominatim/search', [InmueblesController::class, 'searchNominatim'])->name('nominatim.search');
Route::get('api/nominatim/reverse', [InmueblesController::class, 'reverseNominatim'])->name('nominatim.reverse');

// Endpoint público para servir imágenes (necesario para Idealista)
Route::get('storage/images/{path}', [InmueblesController::class, 'servePublicImage'])
    ->where('path', '.*')
    ->name('images.public');

// Route::get('/clients', [App\Http\Controllers\ClientController::class, 'index'])->name('clients.index');

Route::group(['middleware' => 'is.admin', 'prefix' => 'admin'], function () {
    // Tipo de vivienda
    Route::get('tipovivienda', [TipoViviendaController::class, 'index'])->name('tipovivienda.index');

    // Inmuebles
    Route::get('inmuebles', [InmueblesController::class, 'index'])->name('inmuebles.index');
    Route::get('inmuebles/idealista', function () {
        return view('inmuebles.idealista-management');
    })->name('inmuebles.idealista');
    Route::get('inmuebles/idealista/recent', [InmueblesController::class, 'idealistaRecent'])->name('inmuebles.idealista-recent');
    Route::get('inmuebles/{inmueble}/idealista-preview', [InmueblesController::class, 'idealistaPreview'])->name('inmuebles.idealista-preview');

    // Rutas para gestión completa de Idealista
    Route::post('inmuebles/{inmueble}/idealista/update', [InmueblesController::class, 'updateIdealistaProperty'])->name('inmuebles.idealista-update');
    Route::post('inmuebles/{inmueble}/idealista/deactivate', [InmueblesController::class, 'deactivateIdealistaProperty'])->name('inmuebles.idealista-deactivate');
    Route::post('inmuebles/{inmueble}/idealista/reactivate', [InmueblesController::class, 'reactivateIdealistaProperty'])->name('inmuebles.idealista-reactivate');
    Route::post('inmuebles/{inmueble}/idealista/clone', [InmueblesController::class, 'cloneIdealistaProperty'])->name('inmuebles.idealista-clone');
    Route::get('inmuebles/idealista/list', [InmueblesController::class, 'listIdealistaProperties'])->name('inmuebles.idealista-list');

    // Rutas para contactos de Idealista
    Route::get('inmuebles/idealista/contacts', [InmueblesController::class, 'listIdealistaContacts'])->name('inmuebles.idealista-contacts');
    Route::post('inmuebles/idealista/contacts', [InmueblesController::class, 'createIdealistaContact'])->name('inmuebles.idealista-contacts-create');

    // Rutas para videos de Idealista
    Route::get('inmuebles/idealista/videos', function () {
        return view('inmuebles.idealista-videos');
    })->name('inmuebles.idealista-videos');
    Route::get('inmuebles/{inmueble}/idealista/videos', [InmueblesController::class, 'listIdealistaVideos'])->name('inmuebles.idealista-videos-list');
    Route::post('inmuebles/{inmueble}/idealista/videos', [InmueblesController::class, 'createIdealistaVideo'])->name('inmuebles.idealista-videos-create');
    Route::delete('inmuebles/{inmueble}/idealista/videos', [InmueblesController::class, 'deleteIdealistaVideo'])->name('inmuebles.idealista-videos-delete');

    // Rutas para tours virtuales de Idealista
    Route::get('inmuebles/idealista/virtual-tours', function () {
        return view('inmuebles.idealista-virtual-tours');
    })->name('inmuebles.idealista-virtual-tours');
    Route::get('inmuebles/{inmueble}/idealista/virtual-tours', [InmueblesController::class, 'listIdealistaVirtualTours'])->name('inmuebles.idealista-virtual-tours-list');
    Route::post('inmuebles/{inmueble}/idealista/virtual-tours', [InmueblesController::class, 'createIdealistaVirtualTour'])->name('inmuebles.idealista-virtual-tours-create');
    Route::post('inmuebles/{inmueble}/idealista/virtual-tours/deactivate', [InmueblesController::class, 'deactivateIdealistaVirtualTour'])->name('inmuebles.idealista-virtual-tours-deactivate');

    // Ruta para información de publicación del cliente
    Route::get('inmuebles/idealista/publication-info', [InmueblesController::class, 'getIdealistaPublicationInfo'])->name('inmuebles.idealista-publication-info');
    Route::get('inmuebles/create', [InmueblesController::class, 'create'])->name('inmuebles.create');
    Route::post('inmuebles/store', [InmueblesController::class, 'store'])->name('inmuebles.store');
    Route::get('inmuebles/show/{inmueble}', [InmueblesController::class, 'show'])->name('inmuebles.show');
    Route::get('inmuebles/admin-show/{inmueble}', [InmueblesController::class, 'adminShow'])->name('inmuebles.admin-show');
    Route::get('inmuebles/edit/{inmueble}', [InmueblesController::class, 'edit'])->name('inmuebles.edit');
    Route::put('inmuebles/update/{inmueble}', [InmueblesController::class, 'update'])->name('inmuebles.update');
    Route::delete('inmuebles/destroy/{inmueble}', [InmueblesController::class, 'destroy'])->name('inmuebles.destroy');
    Route::post('inmuebles/import-json', [InmueblesController::class, 'importFromJson'])->name('inmuebles.import-json');
    Route::post('inmuebles/search', [InmueblesController::class, 'search'])->name('inmuebles.search');

    // Nuevas rutas para documentos, contratos, visitas y características
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
    Route::get('agenda/hoja-firma/pdf/{hojaFirma}', [AgendaController::class, 'descargarPDFHojaFirma'])->name('agenda.hoja-firma.pdf');

    // Vendedores
    Route::get('vendedores', [VendedoresController::class, 'index'])->name('vendedores.index');

    // Caracteristicas
    Route::get('caracteristicas', [CaracteristicasController::class, 'index'])->name('caracteristicas.index');
});

Route::group(['prefix' => 'laravel-filemanager', 'middleware' => ['web', 'auth']], function () {
    \UniSharp\LaravelFilemanager\Lfm::routes();
});

// Ruta para servir imágenes desde public/storage CON marca de agua
Route::get('/storage/photos/{folder}/{filename}', function ($folder, $filename) {
    $path = storage_path("app/public/photos/{$folder}/{$filename}");

    if (!file_exists($path)) {
        abort(404);
    }

    // Detectar tipo de imagen
    $imageInfo = getimagesize($path);
    if (!$imageInfo) {
        abort(404);
    }

    $type = $imageInfo[2];
    $width = $imageInfo[0];
    $height = $imageInfo[1];

    // Crear imagen según el tipo
    switch ($type) {
        case IMAGETYPE_JPEG:
            $image = imagecreatefromjpeg($path);
            $contentType = 'image/jpeg';
            break;
        case IMAGETYPE_PNG:
            $image = imagecreatefrompng($path);
            $contentType = 'image/png';
            break;
        case IMAGETYPE_GIF:
            $image = imagecreatefromgif($path);
            $contentType = 'image/gif';
            break;
        default:
            abort(404);
    }

    if (!$image) {
        abort(404);
    }

    // Configuración de la marca de agua
    $text = 'SAYCO';

    // Preservar transparencia si es PNG
    if ($type == IMAGETYPE_PNG) {
        imagealphablending($image, true);
        imagesavealpha($image, true);
    }

    // Calcular tamaño de fuente - hacerlo GRANDE
    $fontSize = max(150, min($width, $height) / 6); // Tamaño mucho más grande
    $angle = -45; // Diagonal
    $opacity = 30; // Opacidad más baja para que sea muy visible

    // Color blanco con opacidad reducida
    $textColor = imagecolorallocatealpha($image, 255, 255, 255, $opacity);

    // Intentar usar fuente TTF si está disponible
    $ttfPath = public_path('fonts/arial.ttf');
    $useTTF = function_exists('imagettftext') && file_exists($ttfPath);

    if ($useTTF) {
        // Calcular bounding box del texto rotado
        $bbox = imagettfbbox($fontSize, $angle, $ttfPath, $text);
        $textWidth = abs($bbox[4] - $bbox[0]);
        $textHeight = abs($bbox[5] - $bbox[1]);

        // Centrar exactamente
        $x = ($width - $textWidth) / 2;
        $y = ($height - $textHeight) / 2 + $textHeight;

        // Dibujar el texto con rotación
        imagettftext($image, $fontSize, $angle, $x, $y, $textColor, $ttfPath, $text);
    } else {
        // Fallback: crear texto más grande usando fuentes built-in
        // Crear texto superpuesto varias veces para simular tamaño grande
        $fontSizeInt = 5; // Fuente máxima built-in
        $charWidth = imagefontwidth($fontSizeInt);
        $charHeight = imagefontheight($fontSizeInt);
        $textWidth = $charWidth * strlen($text);
        $textHeight = $charHeight;

        $centerX = $width / 2;
        $centerY = $height / 2;

        // Ajustar posición para texto centrado (sin rotación en built-in)
        $x = $centerX - ($textWidth / 2);
        $y = $centerY - ($textHeight / 2);

        // Dibujar texto múltiples veces para efecto bold y tamaño
        for ($offset = -3; $offset <= 3; $offset++) {
            imagestring($image, $fontSizeInt, $x + $offset, $y, $text, $textColor);
            imagestring($image, $fontSizeInt, $x, $y + $offset, $text, $textColor);
        }
    }

    // Guardar en memoria
    ob_start();
    switch ($type) {
        case IMAGETYPE_JPEG:
            imagejpeg($image, null, 90);
            break;
        case IMAGETYPE_PNG:
            imagepng($image);
            break;
        case IMAGETYPE_GIF:
            imagegif($image);
            break;
    }
    $watermarkedImage = ob_get_clean();
    imagedestroy($image);

    return response($watermarkedImage, 200)
        ->header('Content-Type', $contentType)
        ->header('Cache-Control', 'public, max-age=31536000');
})->where('filename', '.*');

// Endpoint para servir imágenes con marca de agua
Route::get('/images/watermark/{path}', function ($path) {
    $fullPath = storage_path('app/public/photos/' . $path);

    if (!file_exists($fullPath)) {
        abort(404);
    }

    // Detectar tipo de imagen
    $imageInfo = getimagesize($fullPath);
    if (!$imageInfo) {
        $file = file_get_contents($fullPath);
        $type = mime_content_type($fullPath);
        return response($file, 200)
            ->header('Content-Type', $type)
            ->header('Cache-Control', 'public, max-age=31536000');
    }

    $type = $imageInfo[2];
    $width = $imageInfo[0];
    $height = $imageInfo[1];

    // Crear imagen según el tipo
    switch ($type) {
        case IMAGETYPE_JPEG:
            $image = imagecreatefromjpeg($fullPath);
            $contentType = 'image/jpeg';
            break;
        case IMAGETYPE_PNG:
            $image = imagecreatefrompng($fullPath);
            $contentType = 'image/png';
            break;
        case IMAGETYPE_GIF:
            $image = imagecreatefromgif($fullPath);
            $contentType = 'image/gif';
            break;
        default:
            $file = file_get_contents($fullPath);
            return response($file, 200)
                ->header('Content-Type', mime_content_type($fullPath));
    }

    if (!$image) {
        $file = file_get_contents($fullPath);
        return response($file, 200)
            ->header('Content-Type', mime_content_type($fullPath));
    }

    // Configuración de la marca de agua
    $text = 'SAYCO';

    if ($type == IMAGETYPE_PNG) {
        imagealphablending($image, true);
        imagesavealpha($image, true);
    }

    $fontSize = max(120, min($width, $height) / 7);
    $opacity = 40;
    $textColor = imagecolorallocatealpha($image, 255, 255, 255, $opacity);

    // Calcular posición centrada
    $fontSizeInt = 5;
    $charWidth = imagefontwidth($fontSizeInt) * strlen($text);
    $charHeight = imagefontheight($fontSizeInt);
    $x = ($width / 2) - ($charWidth / 2);
    $y = ($height / 2) - ($charHeight / 2);

    // Dibujar texto múltiples veces para efecto bold
    for ($offset = -2; $offset <= 2; $offset++) {
        imagestring($image, $fontSizeInt, $x + $offset, $y, $text, $textColor);
        imagestring($image, $fontSizeInt, $x, $y + $offset, $text, $textColor);
    }

    ob_start();
    switch ($type) {
        case IMAGETYPE_JPEG:
            imagejpeg($image, null, 90);
            break;
        case IMAGETYPE_PNG:
            imagepng($image);
            break;
        case IMAGETYPE_GIF:
            imagegif($image);
            break;
    }
    $watermarkedImage = ob_get_clean();
    imagedestroy($image);

    return response($watermarkedImage, 200)
        ->header('Content-Type', $contentType)
        ->header('Cache-Control', 'public, max-age=3600');
})->where('path', '.*');

// Rutas públicas para inmuebles
Route::get('inmueble/{inmueble}', [InmueblesController::class, 'publicShow'])->name('inmueble.public.show');
