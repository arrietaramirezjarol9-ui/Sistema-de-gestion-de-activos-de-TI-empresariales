<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ficha de Activo - {{ $activo->codigo_qr }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333333;
            font-size: 11px;
            line-height: 1.4;
        }
        .header {
            margin-bottom: 25px;
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 10px;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            color: #0f172a;
            margin: 0 0 5px 0;
        }
        .subtitle {
            font-size: 11px;
            color: #64748b;
            margin: 0;
        }
        .section-title {
            font-size: 13px;
            font-weight: bold;
            color: #1e293b;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 5px;
            margin: 20px 0 10px 0;
        }
        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .detail-table td {
            padding: 6px 10px;
            border-bottom: 1px solid #f1f5f9;
        }
        .detail-table td.label {
            font-weight: bold;
            color: #64748b;
            width: 30%;
        }
        .history-table {
            width: 100%;
            border-collapse: collapse;
        }
        .history-table th {
            background-color: #f8fafc;
            font-weight: bold;
            text-align: left;
            padding: 6px 8px;
            border-bottom: 1px solid #cbd5e1;
        }
        .history-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #e2e8f0;
        }
        .badge {
            display: inline-block;
            padding: 2px 5px;
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
                    <h1 class="title">Ficha Técnica de Activo</h1>
                    <p class="subtitle">Detalle Completo del Equipo de TI</p>
                </td>
                <td style="text-align: right; vertical-align: bottom;">
                    <span style="font-size: 14px; font-weight: bold; color: #4f46e5;">{{ $activo->codigo_qr }}</span>
                </td>
            </tr>
        </table>
    </div>

    <div class="section-title">Información del Activo</div>
    <table class="detail-table">
        <tr>
            <td class="label">Nombre del Activo</td>
            <td><strong>{{ $activo->nombre }}</strong></td>
        </tr>
        <tr>
            <td class="label">Categoría</td>
            <td>{{ $activo->categoria }}</td>
        </tr>
        <tr>
            <td class="label">Marca / Modelo</td>
            <td>{{ $activo->marca }} / {{ $activo->modelo }}</td>
        </tr>
        <tr>
            <td class="label">Número de Serie</td>
            <td><code>{{ $activo->numero_serie }}</code></td>
        </tr>
        <tr>
            <td class="label">Estado Actual</td>
            <td>
                @switch($activo->estado)
                    @case('Disponible')
                        <span class="badge bg-success">Disponible</span>
                        @break
                    @case('Asignado')
                        <span class="badge bg-primary">Asignado</span>
                        @break
                    @case('Mantenimiento')
                        <span class="badge bg-warning">En Mantenimiento</span>
                        @break
                    @default
                        <span class="badge bg-danger">De Baja</span>
                @endswitch
            </td>
        </tr>
        <tr>
            <td class="label">Costo de Adquisición</td>
            <td>S/ {{ number_format($activo->precio, 2) }}</td>
        </tr>
        <tr>
            <td class="label">Fecha de Compra</td>
            <td>{{ $activo->fecha_compra ? $activo->fecha_compra->format('d/m/Y') : 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Especificaciones</td>
            <td>{{ $activo->descripcion ?? 'Ninguna especificación detallada.' }}</td>
        </tr>
    </table>

    @if($activo->estado === 'Asignado' && $activo->prestamoActivo)
        <div class="section-title">Asignación Vigente</div>
        <table class="detail-table">
            <tr>
                <td class="label">Empleado Responsable</td>
                <td><strong>{{ $activo->prestamoActivo->empleado->nombre }}</strong></td>
            </tr>
            <tr>
                <td class="label">Cargo / Departamento</td>
                <td>{{ $activo->prestamoActivo->empleado->cargo }} / {{ $activo->prestamoActivo->empleado->departamento }}</td>
            </tr>
            <tr>
                <td class="label">Fecha de Entrega</td>
                <td>{{ $activo->prestamoActivo->fecha_prestamo->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td class="label">Observaciones de Entrega</td>
                <td>{{ $activo->prestamoActivo->observaciones ?? 'Ninguna.' }}</td>
            </tr>
        </table>
    @endif

    <div class="section-title">Historial de Mantenimientos</div>
    <table class="history-table">
        <thead>
            <tr>
                <th style="width: 18%;">Fecha Inicio</th>
                <th style="width: 18%;">Fecha Fin</th>
                <th style="width: 15%;">Costo (S/)</th>
                <th style="width: 15%;">Estado</th>
                <th style="width: 34%;">Detalle / Trabajo Realizado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($activo->mantenimientos as $maint)
                <tr>
                    <td>{{ $maint->fecha_inicio->format('d/m/Y') }}</td>
                    <td>{{ $maint->fecha_fin ? $maint->fecha_fin->format('d/m/Y') : '—' }}</td>
                    <td>{{ $maint->costo ? 'S/ ' . number_format($maint->costo, 2) : '—' }}</td>
                    <td>
                        @if($maint->estado === 'Programado')
                            <span class="badge bg-warning">Programado</span>
                        @elseif($maint->estado === 'En Proceso')
                            <span class="badge bg-warning">En Taller</span>
                        @else
                            <span class="badge bg-success">Completado</span>
                        @endif
                    </td>
                    <td>{{ $maint->descripcion }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: #94a3b8; padding: 15px 0;">Este equipo no registra mantenimientos.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Ficha técnica autogenerada el {{ date('d/m/Y H:i') }} por IT Asset Manager.
    </div>

</body>
</html>
