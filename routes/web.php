<?php

use App\Http\Controllers\ActividadServicioController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MinisterioController;
use App\Http\Controllers\HorarioController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Aquí registramos las rutas web del sistema.
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

// Página de inicio después de autenticación
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Rutas protegidas por autenticación
Route::middleware('auth')->group(function () {

    // 📌 **Grupo de rutas para Configuraciones**
    Route::prefix('admin/configuraciones')->name('admin.configuracion.')->controller(App\Http\Controllers\ConfiguracionController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::put('/update/{id}', 'update')->name('update');
    });

    // 📌 **Grupo de rutas para Ministerios**
    Route::prefix('admin/ministerios')->name('admin.ministerios.')->controller(MinisterioController::class)->group(function () {
        Route::get('/active', 'active')->name('active');
        Route::get('/inactive', 'inactive')->name('inactive');
        Route::post('/save/{id?}', 'store')->name('save');
        Route::patch('/status/{id}', 'status')->name('status');
        Route::get('/{ministerio}/horarios', 'horarios')->name('horarios');
        Route::resource('/', MinisterioController::class)->except(['store', 'update'])->parameters(['' => 'ministerio']);
    });

    // 📌 **Grupo de rutas para Actividades y servicios**
    Route::prefix('admin/actividad_servicios')->name('admin.actividad_servicios.')->controller(ActividadServicioController::class)->group(function () {
        Route::get('/active', 'active')->name('active');
        Route::get('/inactive', 'inactive')->name('inactive');
        Route::post('/save/{id?}', 'store')->name('save');
        Route::patch('/status/{id}', 'status')->name('status');
        Route::resource('/', ActividadServicioController::class)->except(['store', 'update'])->parameters(['' => 'actividad_servicio']);
    });

    // 📌 **Grupo de rutas para Horarios**
    Route::prefix('admin/horarios')->name('admin.horarios.')->controller(HorarioController::class)->group(function () {
        Route::get('/active', 'active')->name('active');
        Route::get('/inactive', 'inactive')->name('inactive');
        Route::post('/save/{id?}', 'store')->name('save');
        Route::patch('/status/{id}', 'status')->name('status');
        Route::resource('/', HorarioController::class)->except(['store', 'update'])->parameters(['' => 'horario']);
    });
});
