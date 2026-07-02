@extends('layouts.app')

@section('title', 'Mantenimiento de Equipos')

@section('content')
<div class="container-fluid">

    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold mb-1">Mantenimiento de Activos</h2>
            <p class="text-muted mb-0">Gestione mantenimientos preventivos y correctivos para asegurar la operatividad de los equipos de la empresa.</p>
        </div>
        <div>
            <a href="{{ route('mantenimientos.create') }}" class="btn btn-custom btn-custom-primary d-flex align-items-center gap-2">
                <i class="bi bi-calendar-plus"></i> Programar Mantenimiento
            </a>
        </div>
    </div>

    <!-- Filters and Real-time Search Box -->
    <div class="custom-card mb-4">
        <form id="filter-form" action="{{ route('mantenimientos.index') }}" method="GET" class="row g-3">
            <div class="col-12 col-md-8">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="search" id="search-input" class="form-control border-start-0" placeholder="Buscar por equipo, serie o código QR..." value="{{ request('search') }}">
                </div>
            </div>
            
            <div class="col-12 col-md-4">
                <select name="estado" id="estado-select" class="form-select">
                    <option value="">Todos los Estados</option>
                    <option value="Programado" {{ request('estado') == 'Programado' ? 'selected' : '' }}>Programado</option>
                    <option value="En Proceso" {{ request('estado') == 'En Proceso' ? 'selected' : '' }}>En Proceso</option>
                    <option value="Completado" {{ request('estado') == 'Completado' ? 'selected' : '' }}>Completados</option>
                </select>
            </div>
        </form>
    </div>

    <!-- Table Container -->
    <div id="table-container">
        @include('mantenimientos.partials.table')
    </div>

</div>

<!-- Completar Mantenimiento Modal -->
<div class="modal fade" id="completarModal" tabindex="-1" aria-labelledby="completarModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="completarModalLabel">Finalizar Mantenimiento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="completar-form" method="POST">
                @csrf
                <div class="modal-body pt-3">
                    <div class="alert alert-warning border-0 p-3 mb-3 bg-light" style="border-radius: 8px;">
                        <div style="font-size: 0.9rem;">
                            <strong>Activo:</strong> <span id="modal-asset-name"></span>
                        </div>
                    </div>
                    
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label for="fecha_fin" class="form-label fw-semibold">Fecha de Finalización *</label>
                            <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label for="costo" class="form-label fw-semibold">Costo del Mantenimiento ($)</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" min="0" name="costo" id="costo" class="form-control" placeholder="0.00">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label fw-semibold">Detalle del Trabajo Realizado *</label>
                        <textarea name="descripcion" id="descripcion" rows="3" class="form-control" placeholder="Describa el trabajo técnico realizado (Ej: Cambio de memoria RAM de 8GB a 16GB, limpieza física de cooler, reinstalación de SO, etc.)" required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4 justify-content-end gap-2">
                    <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning px-4 text-dark" style="font-weight: 550;">Completar Mantenimiento</button>
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
        function fetchMantenimientos() {
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
            .catch(err => console.error("Error al buscar mantenimientos: ", err));
        }

        let debounceTimer;
        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(fetchMantenimientos, 400);
        });

        estadoSelect.addEventListener('change', fetchMantenimientos);

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

        // Controlar envío de datos al Modal de Completar Mantenimiento
        tableContainer.addEventListener('click', function(e) {
            const completeBtn = e.target.closest('.complete-btn');
            if (!completeBtn) return;

            const maintId = completeBtn.getAttribute('data-id');
            const assetName = completeBtn.getAttribute('data-asset');
            const minDate = completeBtn.getAttribute('data-date');

            // Configurar los campos del modal
            document.getElementById('modal-asset-name').innerText = assetName;
            
            const dateInput = document.getElementById('fecha_fin');
            dateInput.value = new Date().toISOString().split('T')[0]; // Hoy
            dateInput.min = minDate; // No puede finalizar antes de iniciar

            // Configurar la acción del formulario
            const form = document.getElementById('completar-form');
            form.action = `/mantenimientos/${maintId}/completar`;
        });
    });
</script>
@endsection
