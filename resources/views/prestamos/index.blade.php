@extends('layouts.app')

@section('title', 'Asignaciones de Equipos')

@section('content')
<div class="container-fluid">

    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold mb-1">Control de Asignaciones</h2>
            <p class="text-muted mb-0">Registre la entrega de equipos tecnológicos a los empleados y gestione sus devoluciones.</p>
        </div>
        @if(Auth::user()->role === 'admin')
        <div>
            <a href="{{ route('prestamos.create') }}" class="btn btn-custom btn-custom-primary d-flex align-items-center gap-2">
                <i class="bi bi-plus-circle"></i> Asignar Equipo
            </a>
        </div>
        @endif
    </div>

    <!-- Filters and Real-time Search Box -->
    <div class="custom-card mb-4">
        <form id="filter-form" action="{{ route('prestamos.index') }}" method="GET" class="row g-3">
            <div class="col-12 col-md-8">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="search" id="search-input" class="form-control border-start-0" placeholder="Buscar por activo, serie, código QR o empleado..." value="{{ request('search') }}">
                </div>
            </div>
            
            <div class="col-12 col-md-4">
                <select name="estado" id="estado-select" class="form-select">
                    <option value="">Todos los Estados</option>
                    <option value="Activo" {{ request('estado') == 'Activo' ? 'selected' : '' }}>Vigentes</option>
                    <option value="Devuelto" {{ request('estado') == 'Devuelto' ? 'selected' : '' }}>Devueltos</option>
                </select>
            </div>
        </form>
    </div>

    <!-- Table Container -->
    <div id="table-container">
        @include('prestamos.partials.table')
    </div>

</div>

<!-- Devolución Modal -->
<div class="modal fade" id="devolucionModal" tabindex="-1" aria-labelledby="devolucionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="devolucionModalLabel">Procesar Devolución</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="devolucion-form" method="POST">
                @csrf
                <div class="modal-body pt-3">
                    <div class="alert alert-info border-0 p-3 mb-3 bg-light" style="border-radius: 8px;">
                        <div class="mb-1" style="font-size: 0.9rem;">
                            <strong>Activo:</strong> <span id="modal-asset-name"></span>
                        </div>
                        <div style="font-size: 0.9rem;">
                            <strong>Asignado a:</strong> <span id="modal-employee-name"></span>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="fecha_devolucion" class="form-label fw-semibold">Fecha de Devolución *</label>
                        <input type="date" name="fecha_devolucion" id="fecha_devolucion" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="observaciones" class="form-label fw-semibold">Observaciones / Comentarios de Devolución</label>
                        <textarea name="observaciones" id="observaciones" rows="3" class="form-control" placeholder="Describa el estado en el que se devuelve el equipo (Ej: Excelente estado, pantalla con rasguño menor, etc.)"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4 justify-content-end gap-2">
                    <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success px-4">Confirmar Devolución</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('search-input');
        const estadoSelect = document.getElementById('estado-select');
        const tableContainer = document.getElementById('table-container');

        // Búsqueda en tiempo real (AJAX)
        function fetchPrestamos() {
            const query = searchInput.value;
            const est = estadoSelect.value;
            const url = new URL(window.location.href);
            url.searchParams.set('search', query);
            url.searchParams.set('estado', est);
            url.searchParams.set('page', 1);

            fetch(url.toString(), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                tableContainer.innerHTML = html;
                bindPagination();
            })
            .catch(err => console.error("Error al buscar asignaciones: ", err));
        }

        let debounceTimer;
        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(fetchPrestamos, 400);
        });

        estadoSelect.addEventListener('change', fetchPrestamos);

        // Paginación AJAX
        function bindPagination() {
            const links = tableContainer.querySelectorAll('.pagination a');
            links.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const url = this.getAttribute('href');
                    
                    fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.text())
                    .then(html => {
                        tableContainer.innerHTML = html;
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                        bindPagination();
                    });
                });
            });
        }
        
        bindPagination();

        // Controlar envío de datos al Modal de Devolución
        tableContainer.addEventListener('click', function(e) {
            const returnBtn = e.target.closest('.return-btn');
            if (!returnBtn) return;

            const loanId = returnBtn.getAttribute('data-id');
            const assetName = returnBtn.getAttribute('data-asset');
            const employeeName = returnBtn.getAttribute('data-employee');
            const minDate = returnBtn.getAttribute('data-date');

            // Configurar los campos del modal
            document.getElementById('modal-asset-name').innerText = assetName;
            document.getElementById('modal-employee-name').innerText = employeeName;
            
            const dateInput = document.getElementById('fecha_devolucion');
            dateInput.value = new Date().toISOString().split('T')[0]; // Hoy
            dateInput.min = minDate; // No puede ser devuelto antes de la fecha del préstamo

            // Configurar la acción del formulario
            const form = document.getElementById('devolucion-form');
            form.action = `/prestamos/${loanId}/devolver`;
        });
    });
</script>
@endsection
