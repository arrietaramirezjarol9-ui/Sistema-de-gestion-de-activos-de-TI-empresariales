<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>IT Asset Manager - @yield('title', 'Sistema de Gestión')</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
    
    @yield('styles')
</head>
<body>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="brand">
            <i class="bi bi-cpu text-white me-2 fs-4"></i>
            <h1 class="brand-title">IT Asset Manager</h1>
        </div>
        
        <ul class="sidebar-menu">
            <li class="{{ Request::is('/') || Request::is('dashboard*') ? 'active' : '' }}">
                <a href="{{ route('dashboard') }}">
                    <i class="bi bi-grid-fill"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="{{ Request::is('escanear*') ? 'active' : '' }}">
                <a href="{{ route('escanear') }}">
                    <i class="bi bi-qr-code-scan"></i>
                    <span>Escáner QR</span>
                </a>
            </li>
            <li class="{{ Request::is('activos*') ? 'active' : '' }}">
                <a href="{{ route('activos.index') }}">
                    <i class="bi bi-laptop-fill"></i>
                    <span>Activos TI</span>
                </a>
            </li>
            <li class="{{ Request::is('empleados*') ? 'active' : '' }}">
                <a href="{{ route('empleados.index') }}">
                    <i class="bi bi-people-fill"></i>
                    <span>Empleados</span>
                </a>
            </li>
            <li class="{{ Request::is('prestamos*') ? 'active' : '' }}">
                <a href="{{ route('prestamos.index') }}">
                    <i class="bi bi-arrow-left-right"></i>
                    <span>Asignaciones</span>
                </a>
            </li>
            <li class="{{ Request::is('mantenimientos*') ? 'active' : '' }}">
                <a href="{{ route('mantenimientos.index') }}">
                    <i class="bi bi-tools"></i>
                    <span>Mantenimiento</span>
                </a>
            </li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="main-content" id="main-content">
        
        <!-- Top Navbar -->
        <nav class="top-navbar">
            <button class="btn btn-link text-dark p-0 me-3 d-lg-none" id="sidebar-toggle">
                <i class="bi bi-list fs-3"></i>
            </button>
            
            <div class="d-none d-md-flex align-items-center">
                <span class="text-muted"><i class="bi bi-calendar3 me-2"></i>{{ date('d-m-Y') }}</span>
            </div>
            
            <div class="d-flex align-items-center gap-3">
                <!-- Notifications Dropdown -->
                @php
                    // Obtener mantenimientos pendientes/activos
                    $mantenimientosAlertCount = \App\Models\Mantenimiento::whereIn('estado', ['Programado', 'En Proceso'])->count();
                    $notificacionesMantenimiento = \App\Models\Mantenimiento::with('activo')
                        ->whereIn('estado', ['Programado', 'En Proceso'])
                        ->orderBy('fecha_inicio', 'asc')
                        ->take(5)
                        ->get();
                @endphp
                
                <div class="dropdown">
                    <button class="btn btn-light position-relative p-2 rounded-circle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-bell fs-5"></i>
                        @if($mantenimientosAlertCount > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                {{ $mantenimientosAlertCount }}
                            </span>
                        @endif
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 p-2" style="width: 320px;">
                        <li class="p-2 border-bottom d-flex justify-content-between align-items-center">
                            <span class="fw-bold">Mantenimientos</span>
                            <span class="badge bg-primary-soft text-primary">{{ $mantenimientosAlertCount }} Activos</span>
                        </li>
                        @if($notificacionesMantenimiento->isEmpty())
                            <li class="p-3 text-center text-muted">
                                <i class="bi bi-check-circle text-success fs-3 d-block mb-1"></i>
                                Todo en orden. Sin alertas.
                            </li>
                        @else
                            @foreach($notificacionesMantenimiento as $noti)
                                <li>
                                    <a class="dropdown-item p-2 rounded my-1 d-flex align-items-start gap-2 text-wrap" href="{{ route('mantenimientos.index') }}">
                                        <div class="bg-warning-soft p-1 rounded-circle">
                                            <i class="bi bi-tools text-warning fs-6"></i>
                                        </div>
                                        <div>
                                            <small class="d-block fw-semibold">{{ $noti->activo->nombre }}</small>
                                            <small class="text-muted d-block" style="font-size: 0.75rem;">Inicio: {{ $noti->fecha_inicio->format('d/m/Y') }}</small>
                                            <small class="text-truncate text-muted d-block style-desc" style="max-width: 230px; font-size: 0.75rem;">{{ $noti->descripcion }}</small>
                                        </div>
                                    </a>
                                </li>
                            @endforeach
                            <li class="text-center pt-2 border-top">
                                <a href="{{ route('mantenimientos.index') }}" class="btn btn-sm btn-link text-primary p-0">Ver todo</a>
                            </li>
                        @endif
                    </ul>
                </div>

                <!-- User Profile Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-light d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="border-radius: 30px; padding: 6px 16px;">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 0.85rem;">
                            {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                        </div>
                        <span class="d-none d-sm-inline fw-semibold text-dark fs-6">{{ Auth::user()->name }}</span>
                        <i class="bi bi-chevron-down text-muted" style="font-size: 0.75rem;"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                        <li>
                            <form action="{{ route('logout') }}" method="POST" id="logout-form" class="m-0">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger d-flex align-items-center gap-2">
                                    <i class="bi bi-box-arrow-right"></i>
                                    <span>Cerrar Sesión</span>
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        @yield('content')
        
    </main>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- QR Code Generator (Client Side) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <!-- Sidebar Toggle Script -->
    <script>
        document.getElementById('sidebar-toggle')?.addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
        });
        
        // Alertas SweetAlert2 globales de Laravel
        @if(session('success'))
            Swal.fire({
                title: '¡Operación Exitosa!',
                text: "{{ session('success') }}",
                icon: 'success',
                timer: 3000,
                showConfirmButton: false,
                background: '#ffffff',
                customClass: {
                    popup: 'border-radius-12'
                }
            });
        @endif

        @if(session('error'))
            Swal.fire({
                title: '¡Error!',
                text: "{{ session('error') }}",
                icon: 'error',
                timer: 4000,
                showConfirmButton: false,
                background: '#ffffff'
            });
        @endif
    </script>

    @yield('scripts')
</body>
</html>
