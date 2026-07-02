<div class="table-responsive">
    <table class="table align-middle custom-table">
        <thead>
            <tr>
                <th>Activo</th>
                <th>Código QR</th>
                <th>Empleado</th>
                <th>Fecha Asignación</th>
                <th>Fecha Devolución</th>
                <th>Estado</th>
                <th class="text-end">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($prestamos as $prestamo)
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="bg-light p-2 rounded me-3">
                                <i class="bi bi-laptop text-primary"></i>
                            </div>
                            <div>
                                <a href="{{ route('activos.show', $prestamo->activo->id) }}" class="d-block fw-bold text-dark text-decoration-none">{{ $prestamo->activo->nombre }}</a>
                                <small class="text-muted">Serie: {{ $prestamo->activo->numero_serie }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge bg-light text-dark border font-monospace fw-semibold">{{ $prestamo->activo->codigo_qr }}</span>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="bg-primary-soft text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold me-2" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                {{ strtoupper(substr($prestamo->empleado->nombre, 0, 2)) }}
                            </div>
                            <a href="{{ route('empleados.show', $prestamo->empleado->id) }}" class="fw-semibold text-dark text-decoration-none">{{ $prestamo->empleado->nombre }}</a>
                        </div>
                    </td>
                    <td style="font-size: 0.9rem;">
                        {{ $prestamo->fecha_prestamo->format('d/m/Y') }}
                    </td>
                    <td style="font-size: 0.9rem;">
                        {{ $prestamo->fecha_devolucion ? $prestamo->fecha_devolucion->format('d/m/Y') : '—' }}
                    </td>
                    <td>
                        @if($prestamo->estado === 'Activo')
                            <span class="badge-custom bg-primary-soft text-primary"><i class="bi bi-arrow-left-right"></i>Vigente</span>
                        @else
                            <span class="badge-custom bg-success-soft text-success"><i class="bi bi-check-circle-fill"></i>Devuelto</span>
                        @endif
                    </td>
                    <td class="text-end">
                        @if($prestamo->estado === 'Activo')
                            @if(Auth::user()->role === 'admin')
                                <button class="btn btn-sm btn-success return-btn px-3 d-inline-flex align-items-center gap-1" 
                                        style="border-radius: 6px;" 
                                        data-id="{{ $prestamo->id }}" 
                                        data-asset="{{ $prestamo->activo->nombre }}" 
                                        data-employee="{{ $prestamo->empleado->nombre }}" 
                                        data-date="{{ $prestamo->fecha_prestamo->format('Y-m-d') }}"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#devolucionModal">
                                    <i class="bi bi-box-arrow-in-left"></i> Devolver
                                </button>
                            @else
                                <span class="badge bg-light text-muted border px-3 py-1.5"><i class="bi bi-lock-fill text-secondary me-1"></i>Vigente</span>
                            @endif
                        @else
                            <button class="btn btn-sm btn-light border px-3 text-muted d-inline-flex align-items-center gap-1" style="border-radius: 6px;" disabled>
                                <i class="bi bi-check2-all text-success"></i> Finalizado
                            </button>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <i class="bi bi-arrow-left-right fs-2 d-block mb-2"></i>
                        No se encontraron registros de asignaciones.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="d-flex justify-content-between align-items-center mt-4">
    <div class="text-muted" style="font-size: 0.9rem;">
        Mostrando {{ $prestamos->firstItem() ?? 0 }} a {{ $prestamos->lastItem() ?? 0 }} de {{ $prestamos->total() }} registros
    </div>
    <div>
        {{ $prestamos->links('pagination::bootstrap-5') }}
    </div>
</div>
