@extends('layouts.app')

@section('title', 'Programar Mantenimiento')

@section('content')
<div class="container-fluid">

    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12 col-md-8 mx-auto">
            <a href="{{ route('mantenimientos.index') }}" class="btn btn-sm btn-link text-muted text-decoration-none p-0 mb-2 d-inline-flex align-items-center gap-1">
                <i class="bi bi-arrow-left"></i> Volver a Mantenimientos
            </a>
            <h2 class="fw-bold mb-1">Programar Mantenimiento</h2>
            <p class="text-muted">Envíe un equipo al taller de soporte o programe una revisión periódica.</p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="row">
        <div class="col-12 col-md-8 mx-auto">
            <div class="custom-card">
                <form action="{{ route('mantenimientos.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="activo_id" class="form-label fw-semibold">Seleccionar Equipo *</label>
                        <select name="activo_id" id="activo_id" class="form-select @error('activo_id') is-invalid @enderror" required>
                            <option value="" disabled selected>Seleccione un equipo disponible para mantenimiento...</option>
                            @foreach($activosDisponibles as $activo)
                                <option value="{{ $activo->id }}" {{ old('activo_id') == $activo->id ? 'selected' : '' }}>
                                    [{{ $activo->codigo_qr }}] {{ $activo->nombre }} — Serie: {{ $activo->numero_serie }} ({{ $activo->estado }})
                                </option>
                            @endforeach
                        </select>
                        @error('activo_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if($activosDisponibles->isEmpty())
                            <small class="text-danger mt-1 d-block"><i class="bi bi-exclamation-circle me-1"></i>No hay equipos disponibles en el taller. Todos los activos están asignados o ya están en mantenimiento.</small>
                        @endif
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-12 col-sm-6">
                            <label for="fecha_inicio" class="form-label fw-semibold">Fecha de Inicio *</label>
                            <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control @error('fecha_inicio') is-invalid @enderror" value="{{ old('fecha_inicio', date('Y-m-d')) }}" required>
                            @error('fecha_inicio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12 col-sm-6">
                            <label for="estado" class="form-label fw-semibold">Estado de Ingreso *</label>
                            <select name="estado" id="estado" class="form-select @error('estado') is-invalid @enderror" required>
                                <option value="Programado" {{ old('estado') == 'Programado' ? 'selected' : '' }}>Programado (Planificado a futuro)</option>
                                <option value="En Proceso" {{ old('estado') == 'En Proceso' ? 'selected' : '' }}>En Proceso (Taller activo)</option>
                            </select>
                            @error('estado')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="descripcion" class="form-label fw-semibold">Descripción del Problema / Motivo de Revisión *</label>
                        <textarea name="descripcion" id="descripcion" rows="4" class="form-control @error('descripcion') is-invalid @enderror" placeholder="Describa a detalle la falla reportada o el plan de mantenimiento preventivo (Ej: Cambio preventivo de pasta térmica y limpieza de ventilador por recalentamiento)" required>{{ old('descripcion') }}</textarea>
                        @error('descripcion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2 justify-content-end pt-3 border-top">
                        <a href="{{ route('mantenimientos.index') }}" class="btn btn-light border px-4">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-4" {{ $activosDisponibles->isEmpty() ? 'disabled' : '' }}>Programar Mantenimiento</button>
                    </div>

                </form>
            </div>
        </div>
    </div>

</div>
@endsection
