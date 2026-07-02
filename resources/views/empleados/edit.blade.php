@extends('layouts.app')

@section('title', 'Editar Empleado')

@section('content')
<div class="container-fluid">

    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12 col-md-8 mx-auto">
            <a href="{{ route('empleados.index') }}" class="btn btn-sm btn-link text-muted text-decoration-none p-0 mb-2 d-inline-flex align-items-center gap-1">
                <i class="bi bi-arrow-left"></i> Volver al Directorio
            </a>
            <h2 class="fw-bold mb-1">Editar Empleado</h2>
            <p class="text-muted">Modifique los datos personales y de trabajo de: <strong>{{ $empleado->nombre }}</strong></p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="row">
        <div class="col-12 col-md-8 mx-auto">
            <div class="custom-card">
                <form action="{{ route('empleados.update', $empleado->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-3 mb-4">
                        <div class="col-12 col-sm-6">
                            <label for="nombre" class="form-label fw-semibold">Nombre Completo *</label>
                            <input type="text" name="nombre" id="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $empleado->nombre) }}" required>
                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12 col-sm-6">
                            <label for="email" class="form-label fw-semibold">Correo Institucional *</label>
                            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $empleado->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-12 col-sm-6">
                            <label for="telefono" class="form-label fw-semibold">Número de Teléfono</label>
                            <input type="text" name="telefono" id="telefono" class="form-control @error('telefono') is-invalid @enderror" value="{{ old('telefono', $empleado->telefono) }}">
                            @error('telefono')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12 col-sm-6">
                            <label for="estado" class="form-label fw-semibold">Estado Laboral *</label>
                            <select name="estado" id="estado" class="form-select @error('estado') is-invalid @enderror" required>
                                <option value="Activo" {{ old('estado', $empleado->estado) == 'Activo' ? 'selected' : '' }}>Activo</option>
                                <option value="Inactivo" {{ old('estado', $empleado->estado) == 'Inactivo' ? 'selected' : '' }}>Inactivo</option>
                            </select>
                            @error('estado')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-12 col-sm-6">
                            <label for="departamento" class="form-label fw-semibold">Departamento / Área *</label>
                            <select name="departamento" id="departamento" class="form-select @error('departamento') is-invalid @enderror" required>
                                <option value="TI" {{ old('departamento', $empleado->departamento) == 'TI' ? 'selected' : '' }}>Tecnología de Información (TI)</option>
                                <option value="Recursos Humanos" {{ old('departamento', $empleado->departamento) == 'Recursos Humanos' ? 'selected' : '' }}>Recursos Humanos (RRHH)</option>
                                <option value="Ventas" {{ old('departamento', $empleado->departamento) == 'Ventas' ? 'selected' : '' }}>Ventas</option>
                                <option value="Finanzas" {{ old('departamento', $empleado->departamento) == 'Finanzas' ? 'selected' : '' }}>Finanzas</option>
                                <option value="Marketing" {{ old('departamento', $empleado->departamento) == 'Marketing' ? 'selected' : '' }}>Marketing</option>
                                <option value="Operaciones" {{ old('departamento', $empleado->departamento) == 'Operaciones' ? 'selected' : '' }}>Operaciones</option>
                            </select>
                            @error('departamento')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12 col-sm-6">
                            <label for="cargo" class="form-label fw-semibold">Cargo / Puesto *</label>
                            <input type="text" name="cargo" id="cargo" class="form-control @error('cargo') is-invalid @enderror" value="{{ old('cargo', $empleado->cargo) }}" required>
                            @error('cargo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex gap-2 justify-content-end pt-3 border-top">
                        <a href="{{ route('empleados.index') }}" class="btn btn-light border px-4">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-4">Guardar Cambios</button>
                    </div>

                </form>
            </div>
        </div>
    </div>

</div>
@endsection
