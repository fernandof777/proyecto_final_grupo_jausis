<?php

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InicioController;
use App\Http\Controllers\OrdenTrabajoController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\RepuestoController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\VehiculoController;
use Illuminate\Support\Facades\Route;

Route::get('/', [InicioController::class, 'index'])->name('inicio');
Route::post('/reservas', [ReservaController::class, 'store'])
    ->middleware('rate.limit:5,60')
    ->name('reservas.store');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('rate.limit:10,60');
});

Route::middleware(['auth', 'active', 'rate.limit:180,60'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('servicios', ServicioController::class)
        ->except(['show'])
        ->parameters(['servicios' => 'servicio']);
    Route::middleware('role:admin,recepcionista')->group(function () {
        Route::get('/reservas', [ReservaController::class, 'index'])->name('reservas.index');
        Route::patch('/reservas/{reserva}', [ReservaController::class, 'update'])->name('reservas.update');
        Route::resource('clientes', ClienteController::class)->except(['show']);
        Route::resource('vehiculos', VehiculoController::class)->except(['show']);
        Route::resource('ordenes', OrdenTrabajoController::class)
            ->except(['show'])
            ->parameters(['ordenes' => 'orden']);
    });
    Route::resource('repuestos', RepuestoController::class)
        ->except(['show'])->middleware('role:admin,almacen');
    Route::get('/reportes', [ReporteController::class, 'index'])
        ->middleware(['role:admin', 'rate.limit:10,60'])->name('reportes.index');
    Route::get('/reportes/pdf', [ReporteController::class, 'pdf'])
        ->middleware(['role:admin', 'rate.limit:10,60'])->name('reportes.pdf');
    Route::get('/auditoria', [AuditLogController::class, 'index'])
        ->middleware(['role:admin', 'rate.limit:30,60'])->name('auditoria.index');
    Route::delete('/reservas/{reserva}', [ReservaController::class, 'destroy'])
        ->middleware('role:admin')
        ->name('reservas.destroy');
    Route::view('/ubicacion', 'ubicacion.index')->name('ubicacion.index');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
