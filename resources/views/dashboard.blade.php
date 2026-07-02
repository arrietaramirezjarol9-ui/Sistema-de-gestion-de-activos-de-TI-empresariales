@extends('layouts.app')

@section('title', 'Panel de Control')

@section('content')
<div class="container-fluid">
    
    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold mb-1">Panel de Control</h2>
            <p class="text-muted">Resumen del estado actual de los activos e inventario de TI.</p>
        </div>
    </div>

    <!-- Maintenance Warning Alerts -->
    @if($alertasMantenimiento->isNotEmpty())
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center justify-content-between p-3" role="alert" style="border-radius: 12px; background-color: rgba(245, 158, 11, 0.15); border-left: 5px solid #f59e0b !important;">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle-fill text-warning fs-4 me-3"></i>
                        <div>
                            <strong class="d-block text-warning-emphasis">Mantenimientos Pendientes/En Curso</strong>
                            <span class="text-dark-emphasis" style="font-size: 0.9rem;">Hay {{ $alertasMantenimiento->count() }} equipos que requieren atención inmediata o están en mantenimiento programado.</span>
                        </div>
                    </div>
                    <a href="{{ route('mantenimientos.index') }}" class="btn btn-warning btn-sm fw-semibold px-3 py-2" style="border-radius: 8px;">Gestionar</a>
                </div>
            </div>
        </div>
    @endif

    <!-- Metrics row -->
    <div class="row">
        <!-- Card 1 -->
        <div class="col-12 col-sm-6 col-xl-3 mb-4">
            <div class="custom-card metric-card">
                <div class="metric-info">
                    <h6>Total Activos</h6>
                    <h3>{{ $totalActivos }}</h3>
                </div>
                <div class="metric-icon bg-primary-soft">
                    <i class="bi bi-laptop"></i>
                </div>
            </div>
        </div>
        
        <!-- Card 2 -->
        <div class="col-12 col-sm-6 col-xl-3 mb-4">
            <div class="custom-card metric-card">
                <div class="metric-info">
                    <h6>Empleados</h6>
                    <h3>{{ $totalEmpleados }}</h3>
                </div>
                <div class="metric-icon bg-success-soft">
                    <i class="bi bi-people"></i>
                </div>
            </div>
        </div>
        
        <!-- Card 3 -->
        <div class="col-12 col-sm-6 col-xl-3 mb-4">
            <div class="custom-card metric-card">
                <div class="metric-info">
                    <h6>Equipos Asignados</h6>
                    <h3>{{ $prestamosActivos }}</h3>
                </div>
                <div class="metric-icon bg-info-soft">
                    <i class="bi bi-arrow-left-right"></i>
                </div>
            </div>
        </div>
        
        <!-- Card 4 -->
        <div class="col-12 col-sm-6 col-xl-3 mb-4">
            <div class="custom-card metric-card">
                <div class="metric-info">
                    <h6>En Mantenimiento</h6>
                    <h3>{{ $mantenimientosActivos }}</h3>
                </div>
                <div class="metric-icon bg-warning-soft">
                    <i class="bi bi-tools"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts row -->
    <div class="row mb-4">
        <!-- Category Chart -->
        <div class="col-12 col-lg-6 mb-4">
            <div class="custom-card h-100">
                <h5 class="fw-bold mb-4">Activos por Categoría</h5>
                <div style="position: relative; height: 300px;">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Status Chart -->
        <div class="col-12 col-lg-6 mb-4">
            <div class="custom-card h-100">
                <h5 class="fw-bold mb-4">Estado de los Equipos</h5>
                <div style="position: relative; height: 300px;">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Lists row -->
    <div class="row">
        <!-- Recent Loans -->
        <div class="col-12 col-xl-6 mb-4">
            <div class="custom-card h-100">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold m-0">Asignaciones Recientes</h5>
                    <a href="{{ route('prestamos.index') }}" class="btn btn-sm btn-link text-primary text-decoration-none">Ver todas</a>
                </div>
                
                @if($prestamosRecientes->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-clock-history fs-2 d-block mb-2"></i>
                        No hay asignaciones registradas recientemente.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr class="text-muted" style="font-size: 0.8rem;">
                                    <th>Activo</th>
                                    <th>Empleado</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($prestamosRecientes as $prestamo)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-light p-2 rounded me-2">
                                                    <i class="bi bi-laptop text-primary"></i>
                                                </div>
                                                <div>
                                                    <span class="d-block fw-semibold" style="font-size: 0.9rem;">{{ $prestamo->activo->nombre }}</span>
                                                    <small class="text-muted">{{ $prestamo->activo->codigo_qr }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-medium" style="font-size: 0.9rem;">{{ $prestamo->empleado->nombre }}</span>
                                        </td>
                                        <td style="font-size: 0.85rem;">
                                            {{ $prestamo->fecha_prestamo->format('d/m/Y') }}
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

        <!-- Recent Maintenances -->
        <div class="col-12 col-xl-6 mb-4">
            <div class="custom-card h-100">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold m-0">Mantenimientos Vigentes</h5>
                    <a href="{{ route('mantenimientos.index') }}" class="btn btn-sm btn-link text-primary text-decoration-none">Ver todos</a>
                </div>

                @if($mantenimientosRecientes->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-shield-check fs-2 d-block mb-2"></i>
                        No hay mantenimientos activos o en proceso.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr class="text-muted" style="font-size: 0.8rem;">
                                    <th>Activo</th>
                                    <th>Fecha Inicio</th>
                                    <th>Estado</th>
                                    <th>Descripción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($mantenimientosRecientes as $mantenimiento)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-light p-2 rounded me-2">
                                                    <i class="bi bi-tools text-warning"></i>
                                                </div>
                                                <div>
                                                    <span class="d-block fw-semibold" style="font-size: 0.9rem;">{{ $mantenimiento->activo->nombre }}</span>
                                                    <small class="text-muted">{{ $mantenimiento->activo->codigo_qr }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="font-size: 0.85rem;">
                                            {{ $mantenimiento->fecha_inicio->format('d/m/Y') }}
                                        </td>
                                        <td>
                                            @if($mantenimiento->estado === 'Programado')
                                                <span class="badge bg-info-soft text-info rounded-pill">Programado</span>
                                            @else
                                                <span class="badge bg-warning-soft text-warning rounded-pill">En Proceso</span>
                                            @endif
                                        </td>
                                        <td style="font-size: 0.85rem; max-width: 180px;" class="text-truncate">
                                            {{ $mantenimiento->descripcion }}
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

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Gráfico de Categorías (Doughnut)
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    const categoriesData = @json(array_values($chartCategoriasData));
    const categoriesLabels = @json(array_keys($chartCategoriasData));
    
    new Chart(categoryCtx, {
        type: 'doughnut',
        data: {
            labels: categoriesLabels,
            datasets: [{
                data: categoriesData,
                backgroundColor: [
                    '#4f46e5', // Laptop (Indigo)
                    '#06b6d4', // PC (Cyan)
                    '#10b981', // Impresora (Emerald)
                    '#f59e0b', // Celular (Amber)
                    '#ec4899'  // Accesorio (Pink)
                ],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        padding: 15,
                        font: {
                            family: 'Plus Jakarta Sans',
                            weight: '500'
                        }
                    }
                }
            },
            cutout: '70%'
        }
    });

    // Gráfico de Estados (Bar)
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    const statusData = @json(array_values($chartEstadosData));
    const statusLabels = @json(array_keys($chartEstadosData));

    new Chart(statusCtx, {
        type: 'bar',
        data: {
            labels: statusLabels,
            datasets: [{
                label: 'Equipos',
                data: statusData,
                backgroundColor: [
                    '#10b981', // Disponible (Green)
                    '#4f46e5', // Asignado (Indigo)
                    '#f59e0b', // Mantenimiento (Orange)
                    '#ef4444'  // De Baja (Red)
                ],
                borderRadius: 6,
                maxBarThickness: 40
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    },
                    grid: {
                        color: '#f1f5f9'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
</script>
@endsection
