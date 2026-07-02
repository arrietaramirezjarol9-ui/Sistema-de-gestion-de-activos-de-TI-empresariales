@extends('layouts.app')

@section('title', 'Asignar Equipo')

@section('content')
<div class="container-fluid">

    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12 col-md-8 mx-auto">
            <a href="{{ route('prestamos.index') }}" class="btn btn-sm btn-link text-muted text-decoration-none p-0 mb-2 d-inline-flex align-items-center gap-1">
                <i class="bi bi-arrow-left"></i> Volver a Asignaciones
            </a>
            <h2 class="fw-bold mb-1">Asignar Equipo Tecnológico</h2>
            <p class="text-muted">Entregue un activo tecnológico disponible a un empleado activo de la organización.</p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="row">
        <div class="col-12 col-md-8 mx-auto">
            <div class="custom-card">
                <form action="{{ route('prestamos.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="activo_id" class="form-label fw-semibold">Seleccionar Activo Disponible *</label>
                        <select name="activo_id" id="activo_id" class="form-select @error('activo_id') is-invalid @enderror" required>
                            <option value="" disabled selected>Seleccione un equipo disponible...</option>
                            @foreach($activosDisponibles as $activo)
                                <option value="{{ $activo->id }}" {{ old('activo_id') == $activo->id ? 'selected' : '' }}>
                                    [{{ $activo->codigo_qr }}] {{ $activo->nombre }} — Serie: {{ $activo->numero_serie }} ({{ $activo->categoria }})
                                </option>
                            @endforeach
                        </select>
                        @error('activo_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if($activosDisponibles->isEmpty())
                            <small class="text-danger mt-1 d-block"><i class="bi bi-exclamation-circle me-1"></i>No hay activos disponibles en este momento. Todos los equipos están asignados o en mantenimiento.</small>
                        @endif
                    </div>

                    <div class="mb-4">
                        <label for="empleado_id" class="form-label fw-semibold">Seleccionar Empleado Receptor *</label>
                        <select name="empleado_id" id="empleado_id" class="form-select @error('empleado_id') is-invalid @enderror" required>
                            <option value="" disabled selected>Seleccione un empleado activo...</option>
                            @foreach($empleadosActivos as $empleado)
                                <option value="{{ $empleado->id }}" {{ old('empleado_id') == $empleado->id ? 'selected' : '' }}>
                                    {{ $empleado->nombre }} — {{ $empleado->cargo }} ({{ $empleado->departamento }})
                                </option>
                            @endforeach
                        </select>
                        @error('empleado_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="fecha_prestamo" class="form-label fw-semibold">Fecha de Entrega / Préstamo *</label>
                        <input type="date" name="fecha_prestamo" id="fecha_prestamo" class="form-control @error('fecha_prestamo') is-invalid @enderror" value="{{ old('fecha_prestamo', date('Y-m-d')) }}" required>
                        @error('fecha_prestamo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="observaciones" class="form-label fw-semibold">Observaciones de Entrega</label>
                        <textarea name="observaciones" id="observaciones" rows="3" class="form-control @error('observaciones') is-invalid @enderror" placeholder="Comentarios sobre el estado del equipo entregado (Ej: Cargador original incluido, funda de laptop, sin rayones, etc.)">{{ old('observaciones') }}</textarea>
                        @error('observaciones')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2 justify-content-end pt-3 border-top">
                        <a href="{{ route('prestamos.index') }}" class="btn btn-light border px-4">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-4" {{ $activosDisponibles->isEmpty() ? 'disabled' : '' }}>Asignar Equipo</button>
                    </div>

                </form>
            </div>
        </div>
    </div>

</div>
@endsection
