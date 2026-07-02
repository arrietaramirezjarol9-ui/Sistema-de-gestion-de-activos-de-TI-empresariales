<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Activos TI</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333333;
            font-size: 11px;
            line-height: 1.4;
        }
        .header {
            margin-bottom: 20px;
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 10px;
        }
        .title {
            font-size: 20px;
            font-weight: bold;
            color: #0f172a;
            margin: 0 0 5px 0;
        }
        .subtitle {
            font-size: 11px;
            color: #64748b;
            margin: 0;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .meta-table td {
            padding: 2px 0;
        }
        .content-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .content-table th {
            background-color: #f1f5f9;
            color: #1e293b;
            font-weight: bold;
            text-align: left;
            padding: 8px 10px;
            border-bottom: 1px solid #cbd5e1;
        }
        .content-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
        }
        .badge {
            display: inline-block;
            padding: 3px 6px;
            font-weight: bold;
            border-radius: 4px;
            font-size: 9px;
        }
        .bg-success { background-color: #dcfce7; color: #15803d; }
        .bg-primary { background-color: #e0e7ff; color: #4338ca; }
        .bg-warning { background-color: #fef3c7; color: #b45309; }
        .bg-danger { background-color: #fee2e2; color: #b91c1c; }
        
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 5px;
        }
    </style>
</head>
<body>

    <div class="header">
        <table style="width: 100%;">
            <tr>
                <td>
                    <h1 class="title">IT Asset Manager</h1>
                    <p class="subtitle">Reporte General de Activos Tecnológicos</p>
                </td>
                <td style="text-align: right; vertical-align: bottom;">
                    <span style="font-weight: bold;">Fecha:</span> {{ date('d/m/Y H:i') }}
                </td>
            </tr>
        </table>
    </div>

    <table class="meta-table">
        <tr>
            <td><strong>Generado por:</strong> Administrador</td>
            <td style="text-align: right;"><strong>Total Activos Encontrados:</strong> {{ $activos->count() }}</td>
        </tr>
    </table>

    <table class="content-table">
        <thead>
            <tr>
                <th style="width: 12%;">Código QR</th>
                <th style="width: 25%;">Nombre Activo</th>
                <th style="width: 12%;">Categoría</th>
                <th style="width: 15%;">Marca / Modelo</th>
                <th style="width: 15%;">N° Serie</th>
                <th style="width: 10%;">Estado</th>
                <th style="width: 11%;">Asignado A</th>
            </tr>
        </thead>
        <tbody>
            @foreach($activos as $activo)
                <tr>
                    <td><strong>{{ $activo->codigo_qr }}</strong></td>
                    <td>{{ $activo->nombre }}</td>
                    <td>{{ $activo->categoria }}</td>
                    <td>{{ $activo->marca }} / {{ $activo->modelo }}</td>
                    <td><code style="color: #64748b;">{{ $activo->numero_serie }}</code></td>
                    <td>
                        @switch($activo->estado)
                            @case('Disponible')
                                <span class="badge bg-success">Disponible</span>
                                @break
                            @case('Asignado')
                                <span class="badge bg-primary">Asignado</span>
                                @break
                            @case('Mantenimiento')
                                <span class="badge bg-warning">Taller</span>
                                @break
                            @default
                                <span class="badge bg-danger">De Baja</span>
                        @endswitch
                    </td>
                    <td>
                        {{ $activo->prestamoActivo ? $activo->prestamoActivo->empleado->nombre : 'N/A' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Documento autogenerado por IT Asset Manager - Sistema de Control de Activos TI. Página 1 de 1
    </div>

</body>
</html>
