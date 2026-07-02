@extends('layouts.app')

@section('title', 'Directorio de Empleados')

@section('content')
<div class="container-fluid">

    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold mb-1">Directorio de Empleados</h2>
            <p class="text-muted mb-0">Registre personal de la organización y consulte los equipos tecnológicos que tienen a su cargo.</p>
        </div>
        @if(Auth::user()->role === 'admin')
        <div>
            <a href="{{ route('empleados.create') }}" class="btn btn-custom btn-custom-primary d-flex align-items-center gap-2">
                <i class="bi bi-person-plus-fill"></i> Registrar Empleado
            </a>
        </div>
        @endif
    </div>

    <!-- Filters and Real-time Search Box -->
    <div class="custom-card mb-4">
        <form id="filter-form" action="{{ route('empleados.index') }}" method="GET" class="row g-3">
            <div class="col-12">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="search" id="search-input" class="form-control border-start-0" placeholder="Buscar por nombre, correo, departamento, cargo..." value="{{ request('search') }}">
                </div>
            </div>
        </form>
    </div>

    <!-- Table Container (Replaced dynamically during AJAX search) -->
    <div id="table-container">
        @include('empleados.partials.table')
    </div>

</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('search-input');
        const tableContainer = document.getElementById('table-container');

        // Búsqueda en tiempo real (AJAX)
        function fetchEmpleados() {
            const query = searchInput.value;
            const url = new URL(window.location.href);
            url.searchParams.set('search', query);
            url.searchParams.set('page', 1); // Resetear a la primera página

            fetch(url.toString(), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                tableContainer.innerHTML = html;
                initializeDeleteConfirm();
                bindPagination();
            })
            .catch(err => console.error("Error al buscar empleados: ", err));
        }

        // Evento de búsqueda con debounce
        let debounceTimer;
        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(fetchEmpleados, 400);
        });

        // Paginación en AJAX
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

        // Confirmación de eliminación con SweetAlert2
        function initializeDeleteConfirm() {
            const deleteForms = document.querySelectorAll('.delete-form');
            deleteForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: '¿Está seguro de eliminar el empleado?',
                        text: "Se borrará del registro permanente de la empresa.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar',
                        background: '#ffffff'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        }
        
        initializeDeleteConfirm();
    });
</script>
@endsection
