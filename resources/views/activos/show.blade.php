@extends('layouts.app')

@section('title', 'Detalle de Activo')

@section('content')
<div class="container-fluid">

    <!-- Header & Action Buttons -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <a href="{{ route('activos.index') }}" class="btn btn-sm btn-link text-muted text-decoration-none p-0 mb-1 d-inline-flex align-items-center gap-1">
                <i class="bi bi-arrow-left"></i> Volver al Inventario
            </a>
            <h2 class="fw-bold mb-1">{{ $activo->nombre }}</h2>
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted fw-semibold">{{ $activo->codigo_qr }}</span>
                <span>•</span>
                @switch($activo->estado)
                    @case('Disponible')
                        <span class="badge bg-success-soft text-success px-3 py-2 fs-7" style="border-radius: 30px;"><i class="bi bi-check-circle-fill me-1"></i>Disponible</span>
                        @break
                    @case('Asignado')
                        <span class="badge bg-primary-soft text-primary px-3 py-2 fs-7" style="border-radius: 30px;"><i class="bi bi-person-fill me-1"></i>Asignado</span>
                        @break
                    @case('Mantenimiento')
                        <span class="badge bg-warning-soft text-warning px-3 py-2 fs-7" style="border-radius: 30px;"><i class="bi bi-tools me-1"></i>Mantenimiento</span>
                        @break
                    @default
                        <span class="badge bg-danger-soft text-danger px-3 py-2 fs-7" style="border-radius: 30px;"><i class="bi bi-dash-circle-fill me-1"></i>De Baja</span>
                @endswitch
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('reportes.pdf-activo', $activo->id) }}" class="btn btn-custom btn-light border d-flex align-items-center gap-2">
                <i class="bi bi-file-earmark-pdf text-danger"></i> Exportar Ficha (PDF)
            </a>
            @if(Auth::user()->role === 'admin')
            <a href="{{ route('activos.edit', $activo->id) }}" class="btn btn-custom btn-light border d-flex align-items-center gap-2">
                <i class="bi bi-pencil"></i> Editar Activo
            </a>
            @endif
        </div>
    </div>

    <!-- Main Layout -->
    <div class="row">
        
        <!-- Left Side: Detail & QR Code Card -->
        <div class="col-12 col-lg-5 mb-4">
            @if($activo->imagen)
                <!-- Image Card -->
                <div class="custom-card mb-4 p-0 overflow-hidden text-center border-0 shadow-sm" style="border-radius: var(--border-radius);">
                    <img src="{{ asset($activo->imagen) }}" class="img-fluid w-100" style="max-height: 280px; object-fit: cover;" alt="Foto del activo">
                </div>
            @endif

            <!-- Details Card -->
            <div class="custom-card mb-4">
                <h5 class="fw-bold border-bottom pb-3 mb-3">Información General</h5>
                
                <div class="row g-3">
                    <div class="col-6">
                        <small class="text-muted d-block" style="font-size: 0.75rem; text-transform: uppercase;">Categoría</small>
                        <span class="fw-semibold text-dark">{{ $activo->categoria }}</span>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block" style="font-size: 0.75rem; text-transform: uppercase;">Número de Serie</small>
                        <code class="text-secondary fw-bold" style="font-size: 0.9rem;">{{ $activo->numero_serie }}</code>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block" style="font-size: 0.75rem; text-transform: uppercase;">Marca</small>
                        <span class="fw-semibold text-dark">{{ $activo->marca }}</span>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block" style="font-size: 0.75rem; text-transform: uppercase;">Modelo</small>
                        <span class="fw-semibold text-dark">{{ $activo->modelo }}</span>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block" style="font-size: 0.75rem; text-transform: uppercase;">Precio de Adquisición</small>
                        <span class="fw-semibold text-dark">S/ {{ number_format($activo->precio, 2) }}</span>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block" style="font-size: 0.75rem; text-transform: uppercase;">Fecha de Compra</small>
                        <span class="fw-semibold text-dark">{{ $activo->fecha_compra ? $activo->fecha_compra->format('d/m/Y') : 'N/A' }}</span>
                    </div>
                    <div class="col-12">
                        <small class="text-muted d-block" style="font-size: 0.75rem; text-transform: uppercase;">Descripción</small>
                        <p class="text-dark mb-0 style-desc mt-1">{{ $activo->descripcion ?? 'Sin especificaciones registradas.' }}</p>
                    </div>
                </div>
            </div>

            <!-- QR Code Card -->
            <div class="custom-card text-center">
                <h5 class="fw-bold mb-3">Identificador QR</h5>
                
                <div class="qr-container mx-auto mb-3" style="width: 200px; height: 200px;">
                    <div id="show-qrcode-canvas" class="qr-code-img"></div>
                </div>
                
                <p class="text-muted mb-4" style="font-size: 0.85rem;">Enlace codificado:<br><code style="word-break: break-all;" id="qr-target-url"></code></p>
                
                <div class="d-flex gap-2 justify-content-center">
                    <button type="button" class="btn btn-sm btn-outline-secondary px-3" onclick="window.print()">
                        <i class="bi bi-printer me-1"></i> Imprimir
                    </button>
                    <a id="show-btn-download-qr" href="#" class="btn btn-sm btn-primary px-3" download="qr_{{ $activo->codigo_qr }}.png">
                        <i class="bi bi-download me-1"></i> Descargar
                    </a>
                </div>
            </div>
        </div>

        <!-- Right Side: Assignments & Maintenance History -->
        <div class="col-12 col-lg-7">
            <!-- Assignment History Card -->
            <div class="custom-card mb-4">
                <h5 class="fw-bold mb-3 border-bottom pb-3"><i class="bi bi-arrow-left-right me-2 text-primary"></i>Historial de Asignaciones</h5>
                
                @if($activo->prestamos->isEmpty())
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-slash-circle fs-3 d-block mb-2"></i>
                        Este equipo no ha sido asignado a ningún empleado todavía.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr class="text-muted" style="font-size: 0.8rem;">
                                    <th>Empleado</th>
                                    <th>Préstamo</th>
                                    <th>Devolución</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($activo->prestamos as $prestamo)
                                    <tr>
                                        <td>
                                            <a href="{{ route('empleados.show', $prestamo->empleado->id) }}" class="fw-bold text-dark text-decoration-none">
                                                {{ $prestamo->empleado->nombre }}
                                            </a>
                                            <small class="text-muted d-block" style="font-size: 0.75rem;">{{ $prestamo->empleado->cargo }}</small>
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

            <!-- Maintenance History Card -->
            <div class="custom-card">
                <h5 class="fw-bold mb-3 border-bottom pb-3"><i class="bi bi-tools me-2 text-warning"></i>Historial de Mantenimientos</h5>
                
                @if($activo->mantenimientos->isEmpty())
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-shield-check fs-3 d-block mb-2"></i>
                        Este equipo no tiene registro de mantenimientos realizados.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr class="text-muted" style="font-size: 0.8rem;">
                                    <th>Fecha Inicio</th>
                                    <th>Fecha Fin</th>
                                    <th>Costo ($)</th>
                                    <th>Estado</th>
                                    <th>Descripción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($activo->mantenimientos as $mantenimiento)
                                    <tr>
                                        <td style="font-size: 0.85rem;">
                                            {{ $mantenimiento->fecha_inicio->format('d/m/Y') }}
                                        </td>
                                        <td style="font-size: 0.85rem;">
                                            {{ $mantenimiento->fecha_fin ? $mantenimiento->fecha_fin->format('d/m/Y') : 'N/A' }}
                                        </td>
                                        <td class="fw-semibold" style="font-size: 0.85rem;">
                                            {{ $mantenimiento->costo ? 'S/ ' . number_format($mantenimiento->costo, 2) : '—' }}
                                        </td>
                                        <td>
                                            @if($mantenimiento->estado === 'Programado')
                                                <span class="badge bg-info-soft text-info rounded-pill">Programado</span>
                                            @elseif($mantenimiento->estado === 'En Proceso')
                                                <span class="badge bg-warning-soft text-warning rounded-pill">En Proceso</span>
                                            @else
                                                <span class="badge bg-success-soft text-success rounded-pill">Completado</span>
                                            @endif
                                        </td>
                                        <td style="font-size: 0.85rem; max-width: 200px;" class="text-truncate" title="{{ $mantenimiento->descripcion }}">
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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const qrCanvasContainer = document.getElementById('show-qrcode-canvas');
        const codeQrValue = "{{ $activo->codigo_qr }}";
        
        // El QR apunta a la búsqueda de este activo o directamente a la URL de consulta rápida
        const detailUrl = window.location.origin + '/activos?search=' + codeQrValue;
        document.getElementById('qr-target-url').innerText = detailUrl;

        const qrInstance = new QRCode(qrCanvasContainer, {
            text: detailUrl,
            width: 180,
            height: 180,
            colorDark: "#0f172a",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });
        
        // Cargar enlace de descarga de imagen
        setTimeout(function() {
            const img = qrCanvasContainer.querySelector('img');
            if (img) {
                document.getElementById('show-btn-download-qr').href = img.src;
            }
        }, 300);
    });
</script>
@endsection
