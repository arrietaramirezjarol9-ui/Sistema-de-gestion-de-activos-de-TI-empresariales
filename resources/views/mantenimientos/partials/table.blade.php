<div class="table-responsive">
    <table class="table align-middle custom-table">
        <thead>
            <tr>
                <th>Activo</th>
                <th>Código QR</th>
                <th>Fecha Inicio</th>
                <th>Fecha Fin</th>
                <th>Costo ($)</th>
                <th>Estado</th>
                <th>Descripción / Trabajo Realizado</th>
                <th class="text-end">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($mantenimientos as $mantenimiento)
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="bg-light p-2 rounded me-3">
                                <i class="bi bi-tools text-warning"></i>
                            </div>
                            <div>
                                <a href="{{ route('activos.show', $mantenimiento->activo->id) }}" class="d-block fw-bold text-dark text-decoration-none">{{ $mantenimiento->activo->nombre }}</a>
                                <small class="text-muted">Serie: {{ $mantenimiento->activo->numero_serie }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge bg-light text-dark border font-monospace fw-semibold">{{ $mantenimiento->activo->codigo_qr }}</span>
                    </td>
                    <td style="font-size: 0.9rem;">
                        {{ $mantenimiento->fecha_inicio->format('d/m/Y') }}
                    </td>
                    <td style="font-size: 0.9rem;">
                        {{ $mantenimiento->fecha_fin ? $mantenimiento->fecha_fin->format('d/m/Y') : '—' }}
                    </td>
                    <td class="fw-semibold" style="font-size: 0.9rem;">
                        {{ $mantenimiento->costo ? 'S/ ' . number_format($mantenimiento->costo, 2) : '—' }}
                    </td>
                    <td>
                        @switch($mantenimiento->estado)
                            @case('Programado')
                                <span class="badge-custom bg-info-soft text-info"><i class="bi bi-calendar-event"></i>Programado</span>
                                @break
                            @case('En Proceso')
                                <span class="badge-custom bg-warning-soft text-warning"><i class="bi bi-arrow-repeat spin"></i>En Proceso</span>
                                @break
                            @default
                                <span class="badge-custom bg-success-soft text-success"><i class="bi bi-check-circle-fill"></i>Completado</span>
                        @endswitch
                    </td>
                    <td style="font-size: 0.85rem; max-width: 250px;" class="text-wrap">
                        {{ $mantenimiento->descripcion }}
                    </td>
                    <td class="text-end">
                        @if($mantenimiento->estado !== 'Completado')
                            <button class="btn btn-sm btn-warning complete-btn px-3 d-inline-flex align-items-center gap-1 text-dark" 
                                    style="border-radius: 6px; font-weight: 500;" 
                                    data-id="{{ $mantenimiento->id }}" 
                                    data-asset="{{ $mantenimiento->activo->nombre }}" 
                                    data-date="{{ $mantenimiento->fecha_inicio->format('Y-m-d') }}"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#completarModal">
                                <i class="bi bi-check-circle"></i> Finalizar
                            </button>
                        @else
                            <button class="btn btn-sm btn-light border px-3 text-muted d-inline-flex align-items-center gap-1" style="border-radius: 6px;" disabled>
                                <i class="bi bi-shield-check text-success"></i> Completado
                            </button>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center py-5 text-muted">
                        <i class="bi bi-tools fs-2 d-block mb-2"></i>
                        No se encontraron registros de mantenimientos.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="d-flex justify-content-between align-items-center mt-4">
    <div class="text-muted" style="font-size: 0.9rem;">
        Mostrando {{ $mantenimientos->firstItem() ?? 0 }} a {{ $mantenimientos->lastItem() ?? 0 }} de {{ $mantenimientos->total() }} registros
    </div>
    <div>
        {{ $mantenimientos->links('pagination::bootstrap-5') }}
    </div>
</div>
