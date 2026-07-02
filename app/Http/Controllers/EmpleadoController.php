<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use Illuminate\Http\Request;

class EmpleadoController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $empleados = Empleado::query()
            ->when($search, function ($query, $search) {
                $query->where('nombre', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('departamento', 'like', "%{$search}%")
                      ->orWhere('cargo', 'like', "%{$search}%");
            })
            ->withCount(['prestamos' => function ($query) {
                $query->where('estado', 'Activo');
            }])
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        // Si es una petición AJAX (búsqueda en tiempo real), devolvemos solo la tabla
        if ($request->ajax()) {
            return view('empleados.partials.table', compact('empleados'))->render();
        }

        return view('empleados.index', compact('empleados'));
    }

    public function create()
    {
        return view('empleados.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|unique:empleados,email',
            'telefono' => 'nullable|string|max:50',
            'departamento' => 'required|string|max:255',
            'cargo' => 'required|string|max:255',
            'estado' => 'required|in:Activo,Inactivo',
        ]);

        Empleado::create($validated);

        return redirect()->route('empleados.index')->with('success', 'Empleado registrado correctamente.');
    }

    public function show(Empleado $empleado)
    {
        $empleado->load(['prestamos' => function ($query) {
            $query->with('activo')->orderBy('fecha_prestamo', 'desc');
        }]);

        return view('empleados.show', compact('empleado'));
    }

    public function edit(Empleado $empleado)
    {
        return view('empleados.edit', compact('empleado'));
    }

    public function update(Request $request, Empleado $empleado)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|unique:empleados,email,' . $empleado->id,
            'telefono' => 'nullable|string|max:50',
            'departamento' => 'required|string|max:255',
            'cargo' => 'required|string|max:255',
            'estado' => 'required|in:Activo,Inactivo',
        ]);

        $empleado->update($validated);

        return redirect()->route('empleados.index')->with('success', 'Empleado actualizado correctamente.');
    }

    public function destroy(Empleado $empleado)
    {
        // Verificar si tiene préstamos activos antes de eliminar
        $prestamosActivos = $empleado->prestamos()->where('estado', 'Activo')->count();
        if ($prestamosActivos > 0) {
            return redirect()->route('empleados.index')->with('error', 'No se puede eliminar el empleado porque tiene equipos asignados actualmente.');
        }

        $empleado->delete();
        return redirect()->route('empleados.index')->with('success', 'Empleado eliminado correctamente.');
    }
}
