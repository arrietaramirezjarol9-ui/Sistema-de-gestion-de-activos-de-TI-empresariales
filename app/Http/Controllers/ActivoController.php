<?php

namespace App\Http\Controllers;

use App\Models\Activo;
use Illuminate\Http\Request;

class ActivoController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $categoria = $request->input('categoria');
        $estado = $request->input('estado');

        $activos = Activo::query()
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nombre', 'like', "%{$search}%")
                      ->orWhere('codigo_qr', 'like', "%{$search}%")
                      ->orWhere('marca', 'like', "%{$search}%")
                      ->orWhere('modelo', 'like', "%{$search}%")
                      ->orWhere('numero_serie', 'like', "%{$search}%");
                });
            })
            ->when($categoria, function ($query, $categoria) {
                $query->where('categoria', $categoria);
            })
            ->when($estado, function ($query, $estado) {
                $query->where('estado', $estado);
            })
            ->with(['prestamoActivo.empleado'])
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        if ($request->ajax()) {
            return view('activos.partials.table', compact('activos'))->render();
        }

        return view('activos.index', compact('activos'));
    }

    public function create()
    {
        return view('activos.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'categoria' => 'required|in:Laptop,PC,Impresora,Celular,Accesorio',
            'marca' => 'required|string|max:255',
            'modelo' => 'required|string|max:255',
            'numero_serie' => 'required|string|unique:activos,numero_serie|max:255',
            'estado' => 'required|in:Disponible,Asignado,Mantenimiento,De Baja',
            'precio' => 'required|numeric|min:0',
            'fecha_compra' => 'required|date',
            'descripcion' => 'nullable|string',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // Generar un código QR único
        $codigoQr = 'ACT-' . str_pad(random_int(1, 99999), 5, '0', STR_PAD_LEFT);
        while (Activo::where('codigo_qr', $codigoQr)->exists()) {
            $codigoQr = 'ACT-' . str_pad(random_int(1, 99999), 5, '0', STR_PAD_LEFT);
        }

        $validated['codigo_qr'] = $codigoQr;

        // Guardar imagen en disco público
        if ($request->hasFile('imagen')) {
            $imageName = time() . '_' . uniqid() . '.' . $request->file('imagen')->getClientOriginalExtension();
            $request->file('imagen')->move(public_path('uploads/activos'), $imageName);
            $validated['imagen'] = 'uploads/activos/' . $imageName;
        }

        Activo::create($validated);

        return redirect()->route('activos.index')->with('success', 'Activo registrado correctamente con el código: ' . $codigoQr);
    }

    public function show(Activo $activo)
    {
        $activo->load([
            'prestamos' => function ($query) {
                $query->with('empleado')->orderBy('fecha_prestamo', 'desc');
            },
            'mantenimientos' => function ($query) {
                $query->orderBy('fecha_inicio', 'desc');
            }
        ]);

        return view('activos.show', compact('activo'));
    }

    public function edit(Activo $activo)
    {
        return view('activos.edit', compact('activo'));
    }

    public function update(Request $request, Activo $activo)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'categoria' => 'required|in:Laptop,PC,Impresora,Celular,Accesorio',
            'marca' => 'required|string|max:255',
            'modelo' => 'required|string|max:255',
            'numero_serie' => 'required|string|max:255|unique:activos,numero_serie,' . $activo->id,
            'estado' => 'required|in:Disponible,Asignado,Mantenimiento,De Baja',
            'precio' => 'required|numeric|min:0',
            'fecha_compra' => 'required|date',
            'descripcion' => 'nullable|string',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // Si el estado cambia a asignado de manera manual sin un préstamo, arrojamos un error de validación o alertamos
        // Para consistencia, el estado 'Asignado' debe manejarse a través de Préstamos. Sin embargo, si lo cambian aquí,
        // nos aseguramos que el flujo se mantenga.
        if ($validated['estado'] === 'Asignado' && !$activo->prestamoActivo()->exists()) {
            return back()->withErrors(['estado' => 'Para asignar un equipo, use la sección de Préstamos o Asignación.'])->withInput();
        }

        // Guardar imagen en disco público
        if ($request->hasFile('imagen')) {
            // Eliminar imagen anterior si existe
            if ($activo->imagen && file_exists(public_path($activo->imagen))) {
                @unlink(public_path($activo->imagen));
            }
            $imageName = time() . '_' . uniqid() . '.' . $request->file('imagen')->getClientOriginalExtension();
            $request->file('imagen')->move(public_path('uploads/activos'), $imageName);
            $validated['imagen'] = 'uploads/activos/' . $imageName;
        }

        $activo->update($validated);

        return redirect()->route('activos.index')->with('success', 'Activo actualizado correctamente.');
    }

    public function destroy(Activo $activo)
    {
        // Verificar si está prestado actualmente
        if ($activo->estado === 'Asignado') {
            return redirect()->route('activos.index')->with('error', 'No se puede eliminar un activo que está asignado a un empleado.');
        }

        // Eliminar imagen si existe
        if ($activo->imagen && file_exists(public_path($activo->imagen))) {
            @unlink(public_path($activo->imagen));
        }

        $activo->delete();
        return redirect()->route('activos.index')->with('success', 'Activo eliminado correctamente.');
    }
}
