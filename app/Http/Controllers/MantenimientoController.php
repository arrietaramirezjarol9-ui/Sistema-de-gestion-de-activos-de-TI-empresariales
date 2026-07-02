<?php

namespace App\Http\Controllers;

use App\Models\Mantenimiento;
use App\Models\Activo;
use Illuminate\Http\Request;

class MantenimientoController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $estado = $request->input('estado');

        $mantenimientos = Mantenimiento::query()
            ->when($search, function ($query, $search) {
                $query->whereHas('activo', function ($q) use ($search) {
                    $q->where('nombre', 'like', "%{$search}%")
                      ->orWhere('codigo_qr', 'like', "%{$search}%")
                      ->orWhere('numero_serie', 'like', "%{$search}%");
                });
            })
            ->when($estado, function ($query, $estado) {
                $query->where('estado', $estado);
            })
            ->with('activo')
            ->orderBy('fecha_inicio', 'desc')
            ->paginate(10)
            ->withQueryString();

        if ($request->ajax()) {
            return view('mantenimientos.partials.table', compact('mantenimientos'))->render();
        }

        return view('mantenimientos.index', compact('mantenimientos'));
    }

    public function create()
    {
        // Activos disponibles para ingresar a mantenimiento (no deben estar asignados)
        $activosDisponibles = Activo::where('estado', '!=', 'Asignado')
            ->where('estado', '!=', 'De Baja')
            ->orderBy('nombre')
            ->get();

        return view('mantenimientos.create', compact('activosDisponibles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'activo_id' => 'required|exists:activos,id',
            'fecha_inicio' => 'required|date',
            'descripcion' => 'required|string',
            'estado' => 'required|in:Programado,En Proceso',
        ]);

        $activo = Activo::findOrFail($request->input('activo_id'));

        if ($activo->estado === 'Asignado') {
            return back()->withErrors(['activo_id' => 'El activo seleccionado está asignado a un empleado. Primero procese la devolución.'])->withInput();
        }

        // Crear Mantenimiento
        Mantenimiento::create([
            'activo_id' => $activo->id,
            'fecha_inicio' => $request->input('fecha_inicio'),
            'descripcion' => $request->input('descripcion'),
            'estado' => $request->input('estado'),
        ]);

        // Cambiar estado del activo
        $activo->update(['estado' => 'Mantenimiento']);

        return redirect()->route('mantenimientos.index')->with('success', 'Mantenimiento registrado y programado correctamente.');
    }

    public function completar(Request $request, Mantenimiento $mantenimiento)
    {
        if ($mantenimiento->estado === 'Completado') {
            return redirect()->route('mantenimientos.index')->with('error', 'Este mantenimiento ya fue completado.');
        }

        $request->validate([
            'fecha_fin' => 'required|date|after_or_equal:' . $mantenimiento->fecha_inicio->toDateString(),
            'costo' => 'nullable|numeric|min:0',
            'descripcion' => 'nullable|string',
        ]);

        // Completar Mantenimiento
        $mantenimiento->update([
            'fecha_fin' => $request->input('fecha_fin'),
            'costo' => $request->input('costo'),
            'estado' => 'Completado',
            'descripcion' => $mantenimiento->descripcion . "\n[Completado - " . date('Y-m-d') . "]: " . $request->input('descripcion'),
        ]);

        // Devolver activo a Disponible
        $mantenimiento->activo->update(['estado' => 'Disponible']);

        return redirect()->route('mantenimientos.index')->with('success', 'Mantenimiento finalizado con éxito. El equipo ahora está disponible.');
    }
}
