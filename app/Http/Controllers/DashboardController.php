<?php

namespace App\Http\Controllers;

use App\Models\Activo;
use App\Models\Empleado;
use App\Models\Prestamo;
use App\Models\Mantenimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Estadísticas generales
        $totalActivos = Activo::count();
        $totalEmpleados = Empleado::count();
        $prestamosActivos = Prestamo::where('estado', 'Activo')->count();
        $mantenimientosActivos = Mantenimiento::whereIn('estado', ['Programado', 'En Proceso'])->count();

        // Datos para Gráfico 1: Activos por Categoría
        $activosPorCategoria = Activo::select('categoria', DB::raw('count(*) as total'))
            ->groupBy('categoria')
            ->get()
            ->pluck('total', 'categoria')
            ->toArray();

        $categoriasPredefinidas = ['Laptop', 'PC', 'Impresora', 'Celular', 'Accesorio'];
        $chartCategoriasData = [];
        foreach ($categoriasPredefinidas as $cat) {
            $chartCategoriasData[$cat] = $activosPorCategoria[$cat] ?? 0;
        }

        // Datos para Gráfico 2: Activos por Estado
        $activosPorEstado = Activo::select('estado', DB::raw('count(*) as total'))
            ->groupBy('estado')
            ->get()
            ->pluck('total', 'estado')
            ->toArray();

        $estadosPredefinidos = ['Disponible', 'Asignado', 'Mantenimiento', 'De Baja'];
        $chartEstadosData = [];
        foreach ($estadosPredefinidos as $est) {
            $chartEstadosData[$est] = $activosPorEstado[$est] ?? 0;
        }

        // Préstamos recientes
        $prestamosRecientes = Prestamo::with(['activo', 'empleado'])
            ->orderBy('fecha_prestamo', 'desc')
            ->take(5)
            ->get();

        // Mantenimientos programados o activos
        $mantenimientosRecientes = Mantenimiento::with('activo')
            ->whereIn('estado', ['Programado', 'En Proceso'])
            ->orderBy('fecha_inicio', 'desc')
            ->take(5)
            ->get();

        // Alertas de Mantenimiento (Mantenimientos vencidos o para hoy)
        $alertasMantenimiento = Mantenimiento::with('activo')
            ->whereIn('estado', ['Programado', 'En Proceso'])
            ->where('fecha_inicio', '<=', now()->toDateString())
            ->orderBy('fecha_inicio', 'asc')
            ->get();

        return view('dashboard', compact(
            'totalActivos',
            'totalEmpleados',
            'prestamosActivos',
            'mantenimientosActivos',
            'chartCategoriasData',
            'chartEstadosData',
            'prestamosRecientes',
            'mantenimientosRecientes',
            'alertasMantenimiento'
        ));
    }
}
