<div class="table-responsive">
    <table class="table align-middle custom-table">
        <thead>
            <tr>
                <th>Código QR</th>
                <th>Activo</th>
                <th>Categoría</th>
                <th>Marca / Modelo</th>
                <th>N° de Serie</th>
                <th>Estado</th>
                <th>Asignado A</th>
                <th class="text-end">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($activos as $activo)
                <tr>
                    <td>
                        <button class="btn btn-light btn-sm qr-btn border" data-qr="{{ $activo->codigo_qr }}" data-name="{{ $activo->nombre }}" data-bs-toggle="modal" data-bs-target="#qrModal">
                            <i class="bi bi-qr-code me-1"></i>
                            <span class="fw-semibold text-dark">{{ $activo->codigo_qr }}</span>
                        </button>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            @if($activo->imagen)
                                <img src="{{ asset($activo->imagen) }}" class="rounded shadow-sm border me-2" style="width: 42px; height: 42px; object-fit: cover;" alt="Mini">
                            @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center border me-2" style="width: 42px; height: 42px;">
                                    <i class="bi bi-laptop text-secondary" style="font-size: 1.2rem;"></i>
                                </div>
                            @endif
                            <div>
                                <span class="d-block fw-bold text-dark">{{ $activo->nombre }}</span>
                                <small class="text-muted">Adquirido: {{ $activo->fecha_compra ? $activo->fecha_compra->format('d/m/Y') : 'N/A' }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        @switch($activo->categoria)
                            @case('Laptop')
                                <span class="badge bg-primary-soft text-primary"><i class="bi bi-laptop me-1"></i>Laptop</span>
                                @break
                            @case('PC')
                                <span class="badge bg-info-soft text-info"><i class="bi bi-pc-display me-1"></i>PC</span>
                                @break
                            @case('Impresora')
                                <span class="badge bg-success-soft text-success"><i class="bi bi-printer me-1"></i>Impresora</span>
                                @break
                            @case('Celular')
                                <span class="badge bg-warning-soft text-warning"><i class="bi bi-phone me-1"></i>Celular</span>
                                @break
                            @default
                                <span class="badge bg-secondary-soft text-secondary"><i class="bi bi-mouse me-1"></i>Accesorio</span>
                        @endswitch
                    </td>
                    <td>
                        <span class="text-dark fw-medium">{{ $activo->marca }}</span>
                        <small class="text-muted d-block">{{ $activo->modelo }}</small>
                    </td>
                    <td>
                        <code class="text-secondary fw-semibold">{{ $activo->numero_serie }}</code>
                    </td>
                    <td>
                        @switch($activo->estado)
                            @case('Disponible')
                                <span class="badge-custom bg-success-soft text-success"><i class="bi bi-check-circle-fill"></i>Disponible</span>
                                @break
                            @case('Asignado')
                                <span class="badge-custom bg-primary-soft text-primary"><i class="bi bi-person-fill"></i>Asignado</span>
                                @break
                            @case('Mantenimiento')
                                <span class="badge-custom bg-warning-soft text-warning"><i class="bi bi-tools"></i>Mantenimiento</span>
                                @break
                            @default
                                <span class="badge-custom bg-danger-soft text-danger"><i class="bi bi-dash-circle-fill"></i>De Baja</span>
                        @endswitch
                    </td>
                    <td>
                        @if($activo->estado === 'Asignado' && $activo->prestamoActivo)
                            <a href="{{ route('empleados.show', $activo->prestamoActivo->empleado->id) }}" class="text-decoration-none fw-semibold text-primary">
                                {{ $activo->prestamoActivo->empleado->nombre }}
                            </a>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <div class="dropdown">
                            <button class="btn btn-light btn-sm rounded-circle p-2 border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 p-2">
                                <li>
                                    <a class="dropdown-item rounded py-2 d-flex align-items-center gap-2" href="{{ route('activos.show', $activo->id) }}">
                                        <i class="bi bi-eye text-muted"></i> Ver Detalle
                                    </a>
                                </li>
                                @if(Auth::user()->role === 'admin')
                                <li>
                                    <a class="dropdown-item rounded py-2 d-flex align-items-center gap-2" href="{{ route('activos.edit', $activo->id) }}">
                                        <i class="bi bi-pencil text-muted"></i> Editar
                                    </a>
                                </li>
                                @if($activo->estado !== 'Asignado')
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <form action="{{ route('activos.destroy', $activo->id) }}" method="POST" class="delete-form m-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item rounded py-2 text-danger d-flex align-items-center gap-2">
                                            <i class="bi bi-trash"></i> Eliminar
                                        </button>
                                    </form>
                                </li>
                                @endif
                                @endif
                            </ul>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center py-5 text-muted">
                        <i class="bi bi-folder-x fs-2 d-block mb-2"></i>
                        No se encontraron activos de TI en el inventario.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="d-flex justify-content-between align-items-center mt-4">
    <div class="text-muted" style="font-size: 0.9rem;">
        Mostrando {{ $activos->firstItem() ?? 0 }} a {{ $activos->lastItem() ?? 0 }} de {{ $activos->total() }} activos
    </div>
    <div>
        {{ $activos->links('pagination::bootstrap-5') }}
    </div>
</div>
