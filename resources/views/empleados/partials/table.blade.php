<div class="table-responsive">
    <table class="table align-middle custom-table">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Correo Electrónico</th>
                <th>Teléfono</th>
                <th>Departamento</th>
                <th>Cargo</th>
                <th>Estado</th>
                <th class="text-center">Equipos Asignados</th>
                <th class="text-end">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($empleados as $empleado)
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="bg-primary-soft text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold me-3" style="width: 40px; height: 40px; font-size: 0.95rem;">
                                {{ strtoupper(substr($empleado->nombre, 0, 2)) }}
                            </div>
                            <div>
                                <span class="d-block fw-bold text-dark">{{ $empleado->nombre }}</span>
                                <small class="text-muted">ID: #{{ $empleado->id }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <a href="mailto:{{ $empleado->email }}" class="text-secondary text-decoration-none" style="font-size: 0.9rem;">
                            <i class="bi bi-envelope me-1"></i>{{ $empleado->email }}
                        </a>
                    </td>
                    <td style="font-size: 0.9rem;">
                        {{ $empleado->telefono ?? '—' }}
                    </td>
                    <td>
                        <span class="fw-medium text-dark">{{ $empleado->departamento }}</span>
                    </td>
                    <td class="text-secondary" style="font-size: 0.9rem;">
                        {{ $empleado->cargo }}
                    </td>
                    <td>
                        @if($empleado->estado === 'Activo')
                            <span class="badge-custom bg-success-soft text-success"><i class="bi bi-check-circle-fill"></i>Activo</span>
                        @else
                            <span class="badge-custom bg-danger-soft text-danger"><i class="bi bi-dash-circle-fill"></i>Inactivo</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <span class="badge bg-primary rounded-pill px-2.5 py-1.5" style="font-size: 0.85rem;">
                            {{ $empleado->prestamos_count }}
                        </span>
                    </td>
                    <td class="text-end">
                        <div class="dropdown">
                            <button class="btn btn-light btn-sm rounded-circle p-2 border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 p-2">
                                <li>
                                    <a class="dropdown-item rounded py-2 d-flex align-items-center gap-2" href="{{ route('empleados.show', $empleado->id) }}">
                                        <i class="bi bi-eye text-muted"></i> Ver Ficha
                                    </a>
                                </li>
                                @if(Auth::user()->role === 'admin')
                                <li>
                                    <a class="dropdown-item rounded py-2 d-flex align-items-center gap-2" href="{{ route('empleados.edit', $empleado->id) }}">
                                        <i class="bi bi-pencil text-muted"></i> Editar
                                    </a>
                                </li>
                                @if($empleado->prestamos_count == 0)
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <form action="{{ route('empleados.destroy', $empleado->id) }}" method="POST" class="delete-form m-0">
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
                        <i class="bi bi-people-fill fs-2 d-block mb-2"></i>
                        No se encontraron empleados registrados.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="d-flex justify-content-between align-items-center mt-4">
    <div class="text-muted" style="font-size: 0.9rem;">
        Mostrando {{ $empleados->firstItem() ?? 0 }} a {{ $empleados->lastItem() ?? 0 }} de {{ $empleados->total() }} empleados
    </div>
    <div>
        {{ $empleados->links('pagination::bootstrap-5') }}
    </div>
</div>
