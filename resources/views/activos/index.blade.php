@extends('layouts.app')

@section('title', 'Inventario de Activos')

@section('content')
<div class="container-fluid">

    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold mb-1">Inventario de Activos TI</h2>
            <p class="text-muted mb-0">Gestione computadores, impresoras, celulares y accesorios de la organización.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('reportes.excel') }}" class="btn btn-custom btn-light border d-flex align-items-center gap-2">
                <i class="bi bi-file-earmark-excel text-success"></i> Exportar Excel
            </a>
            <a href="{{ route('reportes.pdf-activos') }}" id="btn-pdf-export" class="btn btn-custom btn-light border d-flex align-items-center gap-2">
                <i class="bi bi-file-earmark-pdf text-danger"></i> Exportar PDF
            </a>
            @if(Auth::user()->role === 'admin')
            <a href="{{ route('activos.create') }}" class="btn btn-custom btn-custom-primary d-flex align-items-center gap-2">
                <i class="bi bi-plus-circle"></i> Registrar Activo
            </a>
            @endif
        </div>
    </div>

    <!-- Filters and Real-time Search Box -->
    <div class="custom-card mb-4">
        <form id="filter-form" action="{{ route('activos.index') }}" method="GET" class="row g-3">
            <div class="col-12 col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="search" id="search-input" class="form-control border-start-0" placeholder="Buscar por código, nombre, marca, serie..." value="{{ request('search') }}">
                </div>
            </div>
            
            <div class="col-6 col-md-3">
                <select name="categoria" id="categoria-select" class="form-select">
                    <option value="">Todas las Categorías</option>
                    <option value="Laptop" {{ request('categoria') == 'Laptop' ? 'selected' : '' }}>Laptops</option>
                    <option value="PC" {{ request('categoria') == 'PC' ? 'selected' : '' }}>PCs</option>
                    <option value="Impresora" {{ request('categoria') == 'Impresora' ? 'selected' : '' }}>Impresoras</option>
                    <option value="Celular" {{ request('categoria') == 'Celular' ? 'selected' : '' }}>Celulares</option>
                    <option value="Accesorio" {{ request('categoria') == 'Accesorio' ? 'selected' : '' }}>Accesorios</option>
                </select>
            </div>
            
            <div class="col-6 col-md-3">
                <select name="estado" id="estado-select" class="form-select">
                    <option value="">Todos los Estados</option>
                    <option value="Disponible" {{ request('estado') == 'Disponible' ? 'selected' : '' }}>Disponible</option>
                    <option value="Asignado" {{ request('estado') == 'Asignado' ? 'selected' : '' }}>Asignado</option>
                    <option value="Mantenimiento" {{ request('estado') == 'Mantenimiento' ? 'selected' : '' }}>Mantenimiento</option>
                    <option value="De Baja" {{ request('estado') == 'De Baja' ? 'selected' : '' }}>De Baja</option>
                </select>
            </div>
            
            <div class="col-12 col-md-1 d-grid">
                <a href="{{ route('activos.index') }}" class="btn btn-outline-secondary" title="Limpiar Filtros">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Table Container (Replaced dynamically during AJAX search) -->
    <div id="table-container">
        @include('activos.partials.table')
    </div>

</div>

<!-- QR Code Display Modal -->
<div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 380px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="qrModalLabel">Código QR del Activo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center pt-3 pb-4">
                <p id="qr-activo-name" class="fw-semibold text-dark mb-3" style="font-size: 1.05rem;"></p>
                
                <div class="qr-container mx-auto mb-4" style="width: 200px; height: 200px;">
                    <div id="qrcode-canvas" class="qr-code-img"></div>
                </div>
                
                <p class="text-muted px-2" style="font-size: 0.85rem;">Escanee este código con una cámara para consultar la ficha técnica y el historial del equipo.</p>
                
                <div class="d-flex gap-2 justify-content-center">
                    <button type="button" class="btn btn-light border px-3" onclick="window.print()">
                        <i class="bi bi-printer me-1"></i> Imprimir
                    </button>
                    <a id="btn-download-qr" href="#" class="btn btn-primary px-3" download="qr_code.png">
                        <i class="bi bi-download me-1"></i> Descargar PNG
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('search-input');
        const categoriaSelect = document.getElementById('categoria-select');
        const estadoSelect = document.getElementById('estado-select');
        const tableContainer = document.getElementById('table-container');
        const btnPdfExport = document.getElementById('btn-pdf-export');

        // Búsqueda en tiempo real (AJAX)
        function fetchActivos() {
            const query = searchInput.value;
            const cat = categoriaSelect.value;
            const est = estadoSelect.value;
            
            // Actualizar enlace de exportación de PDF
            const pdfUrl = new URL("{{ route('reportes.pdf-activos') }}", window.location.origin);
            if (query) pdfUrl.searchParams.append('search', query);
            if (cat) pdfUrl.searchParams.append('categoria', cat);
            if (est) pdfUrl.searchParams.append('estado', est);
            btnPdfExport.href = pdfUrl.toString();

            const url = new URL(window.location.href);
            url.searchParams.set('search', query);
            url.searchParams.set('categoria', cat);
            url.searchParams.set('estado', est);
            url.searchParams.set('page', 1); // Resetear a la primera página

            fetch(url.toString(), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                tableContainer.innerHTML = html;
                initializeTooltips();
                bindPagination();
            })
            .catch(err => console.error("Error al buscar activos: ", err));
        }

        // Eventos para filtros
        let debounceTimer;
        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(fetchActivos, 400); // Debounce de 400ms
        });

        categoriaSelect.addEventListener('change', fetchActivos);
        estadoSelect.addEventListener('change', fetchActivos);

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

        // Inicializar tooltips o alertas de confirmación
        function initializeTooltips() {
            // Confirmación de eliminación con SweetAlert2
            const deleteForms = document.querySelectorAll('.delete-form');
            deleteForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: '¿Está seguro de eliminar?',
                        text: "Esta acción no se puede deshacer.",
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
        
        initializeTooltips();

        // Generar QR Dinámico en el Modal
        let qrInstance = null;
        
        tableContainer.addEventListener('click', function(e) {
            const qrBtn = e.target.closest('.qr-btn');
            if (!qrBtn) return;
            
            const qrText = qrBtn.getAttribute('data-qr');
            const assetName = qrBtn.getAttribute('data-name');
            
            document.getElementById('qr-activo-name').innerText = assetName;
            
            const qrCanvasContainer = document.getElementById('qrcode-canvas');
            qrCanvasContainer.innerHTML = ""; // Limpiar anterior
            
            // Generar el código QR apuntando a la ruta del activo
            // e.g. http://localhost:8000/activos/{id}
            // Para fines de escaneo rápido, usamos el código único o el enlace de detalle
            const detailUrl = window.location.origin + '/activos?search=' + qrText;
            
            qrInstance = new QRCode(qrCanvasContainer, {
                text: detailUrl,
                width: 180,
                height: 180,
                colorDark: "#0f172a",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.H
            });
            
            // Esperar un instante para obtener la imagen en base64 generada por qrcode.js
            setTimeout(function() {
                const img = qrCanvasContainer.querySelector('img');
                if (img) {
                    document.getElementById('btn-download-qr').href = img.src;
                }
            }, 300);
        });
    });
</script>
@endsection
