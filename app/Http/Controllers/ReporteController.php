<?php

namespace App\Http\Controllers;

use App\Models\Activo;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReporteController extends Controller
{
    public function exportarExcel(Request $request)
    {
        $headers = [
            'Content-type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename=inventario_activos_' . date('Y-m-d') . '.csv',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0'
        ];

        $activos = Activo::with(['prestamoActivo.empleado'])->orderBy('codigo_qr')->get();

        $callback = function() use($activos) {
            $file = fopen('php://output', 'w');
            
            // UTF-8 BOM para Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Encabezados
            fputcsv($file, [
                'Código QR',
                'Nombre',
                'Categoría',
                'Marca',
                'Modelo',
                'Número de Serie',
                'Estado',
                'Precio (S/)',
                'Fecha de Compra',
                'Asignado A',
                'Descripción'
            ], ';');

            foreach ($activos as $activo) {
                $empleado = $activo->prestamoActivo ? $activo->prestamoActivo->empleado->nombre : 'N/A';
                fputcsv($file, [
                    $activo->codigo_qr,
                    $activo->nombre,
                    $activo->categoria,
                    $activo->marca,
                    $activo->modelo,
                    $activo->numero_serie,
                    $activo->estado,
                    number_format($activo->precio, 2),
                    $activo->fecha_compra ? $activo->fecha_compra->format('Y-m-d') : 'N/A',
                    $empleado,
                    $activo->descripcion
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function pdfActivos(Request $request)
    {
        $search = $request->input('search');
        $categoria = $request->input('categoria');
        $estado = $request->input('estado');

        $activos = Activo::query()
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nombre', 'like', "%{$search}%")
                      ->orWhere('codigo_qr', 'like', "%{$search}%")
                      ->orWhere('numero_serie', 'like', "%{$search}%");
                });
            })
            ->when($categoria, function ($query, $categoria) {
                $query->where('categoria', $categoria);
            })
            ->when($estado, function ($query, $estado) {
                $query->where('estado', $estado);
            })
            ->with(['prestamoActivo.empleado'])
            ->orderBy('codigo_qr')
            ->get();

        $pdf = Pdf::loadView('reportes.pdf_activos', compact('activos'));
        return $pdf->download('reporte_activos_' . date('Y-m-d') . '.pdf');
    }

    public function pdfActivo(Activo $activo)
    {
        $activo->load([
            'prestamos.empleado',
            'mantenimientos'
        ]);

        $pdf = Pdf::loadView('reportes.pdf_activo_detalle', compact('activo'));
        return $pdf->download('detalle_activo_' . $activo->codigo_qr . '.pdf');
    }
}
