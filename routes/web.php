<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\ActivoController;
use App\Http\Controllers\PrestamoController;
use App\Http\Controllers\MantenimientoController;
use App\Http\Controllers\ReporteController;

// Autenticación
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rutas Protegidas por Login
Route::middleware(['auth'])->group(function () {
    
    // Dashboard
    Route::get('/', [DashboardController::class, 'index']);
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Escáner QR de Cámara
    Route::get('/escanear', function () {
        return view('escanear');
    })->name('escanear');

    // Rutas exclusivas para Administrador
    Route::middleware(['admin'])->group(function () {
        Route::resource('activos', ActivoController::class)->except(['index', 'show']);
        Route::resource('empleados', EmpleadoController::class)->except(['index', 'show']);
        Route::resource('prestamos', PrestamoController::class)->except(['index']);
        Route::post('/prestamos/{prestamo}/devolver', [PrestamoController::class, 'devolver'])->name('prestamos.devolver');
    });

    // Acceso general a lecturas
    Route::resource('activos', ActivoController::class)->only(['index', 'show']);
    Route::resource('empleados', EmpleadoController::class)->only(['index', 'show']);
    Route::resource('prestamos', PrestamoController::class)->only(['index']);

    // Mantenimientos
    Route::resource('mantenimientos', MantenimientoController::class)->only(['index', 'create', 'store']);
    Route::post('/mantenimientos/{mantenimiento}/completar', [MantenimientoController::class, 'completar'])->name('mantenimientos.completar');

    // Reportes
    Route::get('/reportes/exportar-excel', [ReporteController::class, 'exportarExcel'])->name('reportes.excel');
    Route::get('/reportes/pdf-activos', [ReporteController::class, 'pdfActivos'])->name('reportes.pdf-activos');
    Route::get('/reportes/pdf-activo/{activo}', [ReporteController::class, 'pdfActivo'])->name('reportes.pdf-activo');

});
