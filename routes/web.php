<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MinisterioController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route :: get ('/new', function(){
    return 'new page';
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

//rutas para configuraciones
Route::get('admin/configuraciones', [App\Http\Controllers\ConfiguracionController::class, 'index'])->name('admin.configuracion.index')->middleware('auth');

// Route::get('/configuracion/create', [App\Http\Controllers\ConfiguracionController::class, 'create'])->name('configuracion.create');
// Route::post('/configuracion', [App\Http\Controllers\ConfiguracionController::class, 'store'])->name('configuracion.store');
// Route::get('/configuracion/{configuracion}', [App\Http\Controllers\ConfiguracionController::class, 'show'])->name('configuracion.show');
// Route::get('/configuracion/{configuracion}/edit', [App\Http\Controllers\ConfiguracionController::class, 'edit'])->name('configuracion.edit');
// Route::put('/configuracion/{configuracion}', [App\Http\Controllers\ConfiguracionController::class, 'update'])->name('configuracion.update');
// Route::delete('/configuracion/{configuracion}', [App\Http\Controllers\ConfiguracionController::class, 'destroy'])->name('configuracion.destroy');



// Rutas para ministerios con CRUD completo
Route::middleware('auth')->group(function () {

    Route::get('admin/ministerios/active', [MinisterioController::class, 'active'])->name('admin.ministerios.active');
    Route::get('admin/ministerios/inactive', [MinisterioController::class, 'inactive'])->name('admin.ministerios.inactive');
    Route::resource('admin/ministerios', MinisterioController::class)->except(['store', 'update'])->names([
        'index' => 'admin.ministerios.index',
        'create' => 'admin.ministerios.create',
        'edit' => 'admin.ministerios.edit',
        'destroy' => 'admin.ministerios.destroy',
    ]);

    Route::post('admin/ministerios/save/{id?}', [MinisterioController::class, 'store'])->name('admin.ministerios.save');
    Route::patch('admin/ministerios/status/{id}', [MinisterioController::class, 'status'])->name('admin.ministerios.status');
});
