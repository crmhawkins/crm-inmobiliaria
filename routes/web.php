<?php

use App\Http\Controllers\FabricantesController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\BudgetReferenceAutopincrementsController;
use App\Http\Controllers\BudgetStatuController;
use App\Http\Controllers\ClientsEmailController;
use App\Http\Controllers\ProjectsController;
use App\Http\Controllers\ProjectPriorityController;
use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\CursoController;
use App\Http\Controllers\PresupuestoController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\ProductosController;
use App\Http\Controllers\ProveedoresController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\OrdenTrabajoController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ClientsController;
use App\Http\Controllers\EcotasaController;
use App\Http\Controllers\TrabajadorController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\IvaController;
use App\Http\Controllers\CajaController;
use App\Http\Controllers\InformesController;
use App\Http\Controllers\ProductosCategoriesController;
use App\Http\Livewire\Facturas\EditComponent;
use App\Http\Livewire\Facturas\IndexComponent as FacturasIndexComponent;
use App\Http\Livewire\Productos\IndexComponent;


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

Route::name('inicio')->get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
// Route::get('/clients', [App\Http\Controllers\ClientController::class, 'index'])->name('clients.index');

Route::group(['middleware' => 'is.admin', 'prefix' => 'admin'], function () {

    /* --------------------------------------- */
    // Budgets
    Route::get('budgets', [BudgetController::class, 'index'])->name('budget.index');
    Route::get('budget-create', [BudgetController::class, 'create'])->name('budget.create');
    Route::post('budget-store', [BudgetController::class, 'store'])->name('budget.store');
    Route::get('budget-edit', [BudgetController::class, 'edit'])->name('budget.edit');
    Route::post('budget-update', [BudgetController::class, 'update'])->name('budget.update');
    Route::delete('budget-delete', [BudgetController::class, 'delete'])->name('budget.delete');

    // Budgets Reference Autoincremental
    Route::get('budgets-reference', [BudgetReferenceAutopincrementsController::class, 'index'])->name('budgetReference.index');
    Route::get('budget-reference-create', [BudgetReferenceAutopincrementsController::class, 'create'])->name('budgetReference.create');
    Route::post('budget-reference-store', [BudgetReferenceAutopincrementsController::class, 'store'])->name('budgetReference.store');
    Route::get('budget-reference-edit', [BudgetReferenceAutopincrementsController::class, 'edit'])->name('budgetReference.edit');
    Route::post('budget-reference-update', [BudgetReferenceAutopincrementsController::class, 'update'])->name('budgetReference.update');
    Route::delete('budget-reference-delete', [BudgetReferenceAutopincrementsController::class, 'delete'])->name('budgetReference.delete');

    // Budgets Status
    Route::get('budgets-status', [BudgetStatuController::class, 'index'])->name('budgetStatus.index');
    Route::get('budget-status-create', [BudgetStatuController::class, 'create'])->name('budgetStatus.create');
    Route::post('budget-status-store', [BudgetStatuController::class, 'store'])->name('budgetStatus.store');
    Route::get('budget-status-edit', [BudgetStatuController::class, 'edit'])->name('budgetStatus.edit');
    Route::post('budget-status-update', [BudgetStatuController::class, 'update'])->name('budgetStatus.update');
    Route::delete('budget-status-delete', [BudgetStatuController::class, 'delete'])->name('budgetStatus.delete');

    /* --------------------------------------- */

    // RECORDATORIO: IMPORTAR CONTROLADORES NUEVOS

    Route::get('caja', [CajaController::class, 'index'])->name('caja.index');



    Route::get('proveedores', [ProveedoresController::class, 'index'])->name('proveedores.index');
    Route::get('proveedores/create', [ProveedoresController::class, 'create'])->name('proveedores.create');
    Route::get('proveedores/edit/{id}', [ProveedoresController::class, 'edit'])->name('proveedores.edit');

    Route::get('ecotasa', [EcotasaController::class, 'index'])->name('ecotasa.index');
    Route::get('ecotasa/create', [EcotasaController::class, 'create'])->name('ecotasa.create');
    Route::get('ecotasa/edit/{id}', [EcotasaController::class, 'edit'])->name('ecotasa.edit');

    // Alumnos
    Route::get('alumnos', [AlumnoController::class, 'index'])->name('alumnos.index');
    Route::get('alumnos-create', [AlumnoController::class, 'create'])->name('alumnos.create');
    Route::get('alumnos-edit/{id}', [AlumnoController::class, 'edit'])->name('alumnos.edit');

    // Empresas
    Route::get('empresas', [EmpresaController::class, 'index'])->name('empresas.index');
    Route::get('empresas-create', [EmpresaController::class, 'create'])->name('empresas.create');
    Route::get('empresas-edit/{id}', [EmpresaController::class, 'edit'])->name('empresas.edit');

    // Registrar usuarios
    Route::get('usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
    Route::get('usuarios-create', [UsuarioController::class, 'create'])->name('usuarios.create');
    Route::get('usuarios-edit/{id}', [UsuarioController::class, 'edit'])->name('usuarios.edit');

    // Registrar usuarios
    Route::get('trabajadores', [TrabajadorController::class, 'index'])->name('trabajadores.index');
    Route::get('trabajadores-create', [TrabajadorController::class, 'create'])->name('trabajadores.create');
    Route::get('trabajadores-edit/{id}', [TrabajadorController::class, 'edit'])->name('trabajadores.edit');


    // Cursos
    Route::get('cursos', [CursoController::class, 'index'])->name('cursos.index');
    Route::get('cursos-create', [CursoController::class, 'create'])->name('cursos.create');
    Route::get('cursos-edit/{id}', [CursoController::class, 'edit'])->name('cursos.edit');

    // Presupuestos
    Route::get('presupuestos', [PresupuestoController::class, 'index'])->name('presupuestos.index');
    Route::get('presupuestos-create', [PresupuestoController::class, 'create'])->name('presupuestos.create');
    Route::get('presupuestos-edit/{id}', [PresupuestoController::class, 'edit'])->name('presupuestos.edit');

    // Orden trabajo

    Route::get('orden-trabajo', [OrdenTrabajoController::class, 'index'])->name('orden-trabajo.index');
    Route::get('orden-trabajo-create/{id}', [OrdenTrabajoController::class, 'create'])->name('orden-trabajo.create');
    Route::get('orden-trabajo-edit/{id}', [OrdenTrabajoController::class, 'edit'])->name('orden-trabajo.edit');

    // Facturas
    Route::get('facturas', [FacturaController::class, 'index'])->name('facturas.index');
    Route::get('facturas-create', [FacturaController::class, 'create'])->name('facturas.create');
    Route::get('facturas-edit/{id}', [FacturaController::class, 'edit'])->name('facturas.edit');
    Route::get('factura/pdf/{id}', [FacturaController::class, 'pdf'])->name('facturas.pdf');
    Route::get('certificado/{id}', [FacturaController::class, 'certificado'])->name('certificado.pdf');

    // Productos
    Route::get('productos', [ProductosController::class, 'index'])->name('productos.index');
    Route::get('productos-create', [ProductosController::class, 'create'])->name('productos.create');
    Route::get('productos-edit/{id}', [ProductosController::class, 'edit'])->name('productos.edit');
    Route::get('productos/pdf', [IndexComponent::class, 'pdf'])->name('productos.pdf');

    // Productos Categories
    Route::get('productos-categories', [ProductosCategoriesController::class, 'index'])->name('productos-categories.index');
    Route::get('productos-categories-create', [ProductosCategoriesController::class, 'create'])->name('productos-categories.create');
    Route::post('productos-categories-store', [ProductosCategoriesController::class, 'store'])->name('productos-categories.store');
    Route::get('productos-categories-edit/{id}', [ProductosCategoriesController::class, 'edit'])->name('productos-categories.edit');
    Route::post('productos-categories-updated', [ProductosCategoriesController::class, 'updated'])->name('productos-categories.update');
    Route::delete('productos-categories-delete', [ProductosCategoriesController::class, 'delete'])->name('productos-categories.delete');

    // Iva de Productos Categories
    Route::get('iva', [IvaController::class, 'index'])->name('iva.index');
    Route::get('iva/create', [IvaController::class, 'create'])->name('iva.create');
    Route::get('iva/edit/{id}', [IvaController::class, 'edit'])->name('iva.edit');

    // Clients
    Route::get('clients', [ClientController::class, 'index'])->name('client.index');
    Route::get('clients-create', [ClientController::class, 'create'])->name('client.create');
    Route::post('clients-store', [ClientController::class, 'store'])->name('client.store');
    Route::get('clients-edit', [ClientController::class, 'edit'])->name('client.edit');
    Route::post('clients-updated', [ClientController::class, 'updated'])->name('client.updated');
    Route::delete('clients-delete', [ClientController::class, 'delete'])->name('client.delete');

    // Clients
    Route::get('clients-emails', [ClientsEmailController::class, 'index'])->name('clientEmail.index');
    Route::get('client-email-create', [ClientsEmailController::class, 'create'])->name('clientEmail.create');
    Route::post('client-email-store', [ClientsEmailController::class, 'store'])->name('clientEmail.store');
    Route::get('client-email-edit', [ClientsEmailController::class, 'edit'])->name('clientEmail.edit');
    Route::post('client-email-updated', [ClientsEmailController::class, 'updated'])->name('clientEmail.updated');
    Route::delete('client-email-delete', [ClientsEmailController::class, 'delete'])->name('clientEmail.delete');

    /* --------------------------------------- */
    // Payment_method
    Route::get('payments-method', [PaymentMethodController::class, 'index'])->name('paymentsMethod.index');
    Route::get('payment-method-create', [PaymentMethodController::class, 'create'])->name('paymentMethod.create');
    Route::post('payment-method-store', [PaymentMethodController::class, 'store'])->name('paymentMethod.store');
    Route::get('payment-method-edit', [PaymentMethodController::class, 'edit'])->name('paymentMethod.edit');
    Route::post('payment-method-updated', [PaymentMethodController::class, 'updated'])->name('paymentMethod.updated');
    Route::delete('payment-method-delete', [PaymentMethodController::class, 'delete'])->name('paymentMethod.delete');

    /* --------------------------------------- */
    // Projects
    Route::get('projects', [ProjectsController::class, 'index'])->name('projects.index');
    Route::get('project-create', [ProjectsController::class, 'create'])->name('project.create');
    Route::post('project-store', [ProjectsController::class, 'store'])->name('project.store');
    Route::get('project-edit', [ProjectsController::class, 'edit'])->name('project.edit');
    Route::post('project-updated', [ProjectsController::class, 'updated'])->name('project.updated');
    Route::delete('project-delete', [ProjectsController::class, 'delete'])->name('project.delete');

    // Projects Priority
    Route::get('projects-priority', [ProjectPriorityController::class, 'index'])->name('projectsPriority.index');
    Route::get('project-priority-create', [ProjectPriorityController::class, 'create'])->name('projectPriority.create');
    Route::post('project-priority-store', [ProjectPriorityController::class, 'store'])->name('projectPriority.store');
    Route::get('project-priority-edit', [ProjectPriorityController::class, 'edit'])->name('projectPriority.edit');
    Route::post('project-priority-updated', [ProjectPriorityController::class, 'updated'])->name('projectPriority.updated');
    Route::delete('project-priority-delete', [ProjectPriorityController::class, 'delete'])->name('projectPriority.delete');

    // // Facturas
    // Route::get('generar-factura', [FacturasController::class, 'generar'])->name('generarFactura.generar');
    // Route::get('factura', [FacturasController::class, 'index'])->name('factura.index');
    // Route::get('factura/create', [FacturasController::class, 'create'])->name('factura.create');
    // Route::get('factura/edit/{id}', [FacturasController::class, 'edit'])->name('factura.edit');
    // Route::get('factura/electronica/{id}', [FacturasController::class, 'electronica'])->name('factura.electronica');
    // Route::get('factura/pdf/{id}', [FacturasController::class, 'pdf'])->name('facturas.pdf');


    // Settings
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::get('settings/create', [SettingsController::class, 'create'])->name('settings.create');
    Route::get('settings/edit', [SettingsController::class, 'edit'])->name('settings.edit');

    // Settings
    Route::get('clients', [ClientsController::class, 'index'])->name('clients.index');
    Route::get('clients/create', [ClientsController::class, 'create'])->name('clients.create');
    Route::get('clients/edit/{id}', [ClientsController::class, 'edit'])->name('clients.edit');

    Route::get('informes', [InformesController::class, 'index'])->name('informes.index');
    Route::get('fabricantes', [FabricantesController::class, 'index'])->name('fabricantes.index');



});
