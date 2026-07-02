<?php

namespace App\Http\Controllers;

use App\Models\Prestamo;
use App\Models\Activo;
use App\Models\Empleado;
use Illuminate\Http\Request;

class PrestamoController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $estado = $request->input('estado');

        $prestamos = Prestamo::query()
            ->when($search, function ($query, $search) {
                $query->whereHas('activo', function ($q) use ($search) {
                    $q->where('nombre', 'like', "%{$search}%")
                      ->orWhere('codigo_qr', 'like', "%{$search}%")
                      ->orWhere('numero_serie', 'like', "%{$search}%");
                })->orWhereHas('empleado', function ($q) use ($search) {
                    $q->where('nombre', 'like', "%{$search}%");
                });
            })
            ->when($estado, function ($query, $estado) {
                $query->where('estado', $estado);
            })
            ->with(['activo', 'empleado'])
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        if ($request->ajax()) {
            return view('prestamos.partials.table', compact('prestamos'))->render();
        }

        return view('prestamos.index', compact('prestamos'));
    }

    public function create()
    {
        $activosDisponibles = Activo::where('estado', 'Disponible')->orderBy('nombre')->get();
        $empleadosActivos = Empleado::where('estado', 'Activo')->orderBy('nombre')->get();

        return view('prestamos.create', compact('activosDisponibles', 'empleadosActivos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'activo_id' => 'required|exists:activos,id',
            'empleado_id' => 'required|exists:empleados,id',
            'fecha_prestamo' => 'required|date',
            'observaciones' => 'nullable|string',
        ]);

        $activo = Activo::findOrFail($request->input('activo_id'));

        if ($activo->estado !== 'Disponible') {
            return back()->withErrors(['activo_id' => 'El activo seleccionado no está disponible para asignación.'])->withInput();
        }

        // Crear Préstamo
        Prestamo::create([
            'activo_id' => $activo->id,
            'empleado_id' => $request->input('empleado_id'),
            'fecha_prestamo' => $request->input('fecha_prestamo'),
            'estado' => 'Activo',
            'observaciones' => $request->input('observaciones'),
        ]);

        // Cambiar estado del activo
        $activo->update(['estado' => 'Asignado']);

        return redirect()->route('prestamos.index')->with('success', 'Equipo asignado correctamente.');
    }

    public function devolver(Request $request, Prestamo $prestamo)
    {
        if ($prestamo->estado === 'Devuelto') {
            return redirect()->route('prestamos.index')->with('error', 'Este préstamo ya fue devuelto.');
        }

        $request->validate([
            'fecha_devolucion' => 'required|date|after_or_equal:' . $prestamo->fecha_prestamo->toDateString(),
            'observaciones' => 'nullable|string',
        ]);

        // Actualizar Préstamo
        $prestamo->update([
            'fecha_devolucion' => $request->input('fecha_devolucion'),
            'estado' => 'Devuelto',
            'observaciones' => $prestamo->observaciones . "\n[Devolución - " . date('Y-m-d') . "]: " . $request->input('observaciones'),
        ]);

        // Cambiar estado del activo
        $prestamo->activo->update(['estado' => 'Disponible']);

        return redirect()->route('prestamos.index')->with('success', 'Devolución procesada correctamente. El equipo ahora está disponible.');
    }
}
