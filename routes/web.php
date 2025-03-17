<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HorarioController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\MinisterioController;
use App\Http\Controllers\ConfiguracionController;
use App\Http\Controllers\ActividadServicioController;

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
    Route::prefix('admin/configuraciones')->name('admin.configuracion.')->controller(ConfiguracionController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::put('/update/{id}', 'update')->name('update');
        Route::put('/update_logo/{id}', 'update_logo')->name('update_logo');
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

    // 📌 **Grupo de rutas para Reportes**
    Route::prefix('admin/reportes')->name('admin.reportes.')->controller(ReporteController::class)->group(function () {
        Route::match(['get', 'post'], '/multa', 'multa')->name('multa');
        Route::match(['get', 'post'], '/asistencia', 'asistencia')->name('asistencia');
        Route::match(['get', 'post'], '/fidelizacion', 'fidelizacion')->name('fidelizacion');
        Route::get('/exportar-reporte', 'exportarReporte')->name('exportar');
        Route::match(['get', 'post'], '/archivoDB', 'archivoDB')->name('archivoDB');
    });

    // 📌 **Grupo de rutas para Usuarios**
    Route::prefix('admin/usuarios')->name('admin.usuarios.')->controller(UserController::class)->group(function () {
        Route::get('profile', 'profile')->name('profile');
        Route::get('/active', 'active')->name('active');
        Route::get('/inactive', 'inactive')->name('inactive');
        Route::post('/save/{id?}', 'store')->name('save');
        Route::patch('/status/{id}', 'status')->name('status');
        Route::get('/{usuario}/info', 'info')->name('info');
        Route::put('/{usuario}', 'update')->name('update'); // Ruta específica para update
        Route::resource('/', UserController::class)->except(['store'])->parameters(['' => 'usuario']);
    });
});
