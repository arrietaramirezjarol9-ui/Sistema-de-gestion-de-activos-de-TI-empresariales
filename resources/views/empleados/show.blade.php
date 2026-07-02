@extends('layouts.app')

@section('title', 'Detalle del Empleado')

@section('content')
<div class="container-fluid">

    <!-- Header & Action Buttons -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <a href="{{ route('empleados.index') }}" class="btn btn-sm btn-link text-muted text-decoration-none p-0 mb-1 d-inline-flex align-items-center gap-1">
                <i class="bi bi-arrow-left"></i> Volver al Directorio
            </a>
            <h2 class="fw-bold mb-1">{{ $empleado->nombre }}</h2>
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted fw-semibold">ID: #{{ $empleado->id }}</span>
                <span>•</span>
                @if($empleado->estado === 'Activo')
                    <span class="badge bg-success-soft text-success px-3 py-1.5 fs-7" style="border-radius: 30px;"><i class="bi bi-check-circle-fill me-1"></i>Activo</span>
                @else
                    <span class="badge bg-danger-soft text-danger px-3 py-1.5 fs-7" style="border-radius: 30px;"><i class="bi bi-dash-circle-fill me-1"></i>Inactivo</span>
                @endif
            </div>
        </div>
        <div class="d-flex gap-2">
            @if(Auth::user()->role === 'admin')
            <a href="{{ route('empleados.edit', $empleado->id) }}" class="btn btn-custom btn-light border d-flex align-items-center gap-2">
                <i class="bi bi-pencil"></i> Editar Datos
            </a>
            @endif
        </div>
    </div>

    <!-- Main Layout -->
    <div class="row">
        
        <!-- Left Side: Detail Card -->
        <div class="col-12 col-lg-4 mb-4">
            <div class="custom-card">
                <div class="text-center mb-4">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold mx-auto mb-3" style="width: 80px; height: 80px; font-size: 2.2rem;">
                        {{ strtoupper(substr($empleado->nombre, 0, 2)) }}
                    </div>
                    <h5 class="fw-bold mb-1">{{ $empleado->nombre }}</h5>
                    <span class="text-muted fs-7 d-block">{{ $empleado->cargo }}</span>
                </div>
                
                <h6 class="fw-bold border-bottom pb-2 mb-3 text-secondary" style="font-size: 0.85rem; text-transform: uppercase;">Información Laboral</h6>
                
                <div class="row g-3">
                    <div class="col-12">
                        <small class="text-muted d-block" style="font-size: 0.75rem; text-transform: uppercase;">Correo Electrónico</small>
                        <a href="mailto:{{ $empleado->email }}" class="text-dark fw-medium text-decoration-none">{{ $empleado->email }}</a>
                    </div>
                    <div class="col-12">
                        <small class="text-muted d-block" style="font-size: 0.75rem; text-transform: uppercase;">Teléfono de Contacto</small>
                        <span class="fw-medium text-dark">{{ $empleado->telefono ?? '—' }}</span>
                    </div>
                    <div class="col-12">
                        <small class="text-muted d-block" style="font-size: 0.75rem; text-transform: uppercase;">Departamento</small>
                        <span class="fw-medium text-dark">{{ $empleado->departamento }}</span>
                    </div>
                    <div class="col-12">
                        <small class="text-muted d-block" style="font-size: 0.75rem; text-transform: uppercase;">Equipos Asignados Actualmente</small>
                        <span class="badge bg-primary px-2.5 py-1.5" style="font-size: 0.85rem;">
                            {{ $empleado->prestamos->where('estado', 'Activo')->count() }} Equipos
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side: Assigned Assets History -->
        <div class="col-12 col-lg-8">
            <div class="custom-card h-100">
                <h5 class="fw-bold mb-3 border-bottom pb-3"><i class="bi bi-laptop me-2 text-primary"></i>Historial de Equipos Entregados</h5>
                
                @if($empleado->prestamos->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-arrow-left-right fs-2 d-block mb-2"></i>
                        Este empleado no registra préstamos ni asignaciones de equipos tecnológicos.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr class="text-muted" style="font-size: 0.8rem;">
                                    <th>Código</th>
                                    <th>Activo</th>
                                    <th>Categoría</th>
                                    <th>Fecha Préstamo</th>
                                    <th>Fecha Devolución</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($empleado->prestamos as $prestamo)
                                    <tr>
                                        <td>
                                            <a href="{{ route('activos.show', $prestamo->activo->id) }}" class="fw-bold text-primary text-decoration-none">
                                                {{ $prestamo->activo->codigo_qr }}
                                            </a>
                                        </td>
                                        <td>
                                            <div>
                                                <span class="d-block fw-semibold text-dark">{{ $prestamo->activo->nombre }}</span>
                                                <small class="text-muted">SN: {{ $prestamo->activo->numero_serie }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <small class="text-dark font-monospace">{{ $prestamo->activo->categoria }}</small>
                                        </td>
                                        <td style="font-size: 0.85rem;">
                                            {{ $prestamo->fecha_prestamo->format('d/m/Y') }}
                                        </td>
                                        <td style="font-size: 0.85rem;">
                                            {{ $prestamo->fecha_devolucion ? $prestamo->fecha_devolucion->format('d/m/Y') : 'N/A' }}
                                        </td>
                                        <td>
                                            @if($prestamo->estado === 'Activo')
                                                <span class="badge bg-primary-soft text-primary rounded-pill">Vigente</span>
                                            @else
                                                <span class="badge bg-success-soft text-success rounded-pill">Devuelto</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

    </div>

</div>
@endsection
