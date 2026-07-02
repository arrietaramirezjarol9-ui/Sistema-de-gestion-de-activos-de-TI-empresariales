<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - IT Asset Manager</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #4f46e5;
            --primary-hover: #4338ca;
            --background-color: #0f172a;
            --card-background: rgba(30, 41, 59, 0.7);
            --border-color: rgba(255, 255, 255, 0.1);
        }

        body {
            background-color: var(--background-color);
            background-image: radial-gradient(circle at 10% 20%, rgba(79, 70, 229, 0.15) 0%, transparent 40%),
                              radial-gradient(circle at 90% 80%, rgba(6, 182, 212, 0.1) 0%, transparent 40%);
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-card {
            background: var(--card-background);
            backdrop-filter: blur(16px);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 40px;
            width: 100%;
            max-width: 440px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
        }

        .brand-icon {
            width: 60px;
            height: 60px;
            background: rgba(79, 70, 229, 0.2);
            color: #818cf8;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin: 0 auto 24px;
        }

        .form-control {
            background-color: rgba(15, 23, 42, 0.6);
            border: 1px solid var(--border-color);
            color: #f8fafc;
            padding: 12px 16px;
            border-radius: 10px;
        }

        .form-control:focus {
            background-color: rgba(15, 23, 42, 0.8);
            border-color: var(--primary-color);
            color: #ffffff;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.25);
        }

        .form-label {
            color: #cbd5e1;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .btn-login {
            background-color: var(--primary-color);
            color: #ffffff;
            border: none;
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .btn-login:hover {
            background-color: var(--primary-hover);
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(79, 70, 229, 0.4);
        }

        .login-footer {
            color: #94a3b8;
            font-size: 0.85rem;
            text-align: center;
            margin-top: 24px;
        }

        .text-danger-custom {
            color: #f87171;
            font-size: 0.8rem;
            margin-top: 4px;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="brand-icon">
            <i class="bi bi-cpu"></i>
        </div>
        
        <h2 class="text-white text-center fw-bold mb-2" style="font-size: 1.5rem;">Bienvenido de nuevo</h2>
        <p class="text-center mb-4" style="color: #94a3b8; font-size: 0.9rem;">IT Asset Management System</p>
        
        <form method="POST" action="{{ route('login.post') }}">
            @csrf
            
            <div class="mb-3">
                <label for="email" class="form-label">Correo Electrónico</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="admin@admin.com">
                @error('email')
                    <div class="text-danger-custom"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <label for="password" class="form-label m-0">Contraseña</label>
                </div>
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required placeholder="••••••••">
                @error('password')
                    <div class="text-danger-custom"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-4 form-check">
                <input type="checkbox" class="form-check-input" id="remember" name="remember" style="background-color: rgba(15, 23, 42, 0.6); border-color: var(--border-color);">
                <label class="form-check-label text-muted" for="remember" style="font-size: 0.85rem;">Recordar sesión</label>
            </div>
            
            <button type="submit" class="btn btn-login w-100 mb-3">Iniciar Sesión</button>
        </form>
        
        <div class="login-footer">
            <span>&copy; {{ date('Y') }} IT Asset Manager. Todos los derechos reservados.</span>
        </div>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
