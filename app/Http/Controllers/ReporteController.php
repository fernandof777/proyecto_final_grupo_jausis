<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\OrdenTrabajo;
use App\Models\Repuesto;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class ReporteController extends Controller
{
    public function index(Request $request): View
    {
        return view('reportes.index', $this->reportData($request, true));
    }

    public function pdf(Request $request): Response
    {
        $data = $this->reportData($request, false);
        $options = new Options;
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isRemoteEnabled', false);

        $pdf = new Dompdf($options);
        $pdf->loadHtml(view('reportes.pdf', $data)->render(), 'UTF-8');
        $pdf->setPaper('A4', 'landscape');
        $pdf->render();

        $font = $pdf->getFontMetrics()->getFont('DejaVu Sans');
        $pdf->getCanvas()->page_text(
            720,
            570,
            'Página {PAGE_NUM} de {PAGE_COUNT}',
            $font,
            8,
            [0.38, 0.42, 0.48]
        );

        $sufijo = collect([$data['filtros']['desde'], $data['filtros']['hasta']])
            ->filter()
            ->implode('_');
        $nombre = 'informe-taller'.($sufijo ? "-{$sufijo}" : '').'.pdf';

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$nombre.'"',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    private function reportData(Request $request, bool $paginar): array
    {
        $filtros = $request->validate([
            'desde' => ['nullable', 'date'],
            'hasta' => ['nullable', 'date', 'after_or_equal:desde'],
        ]);
        $filtros += ['desde' => null, 'hasta' => null];

        $base = OrdenTrabajo::query()
            ->when($filtros['desde'] ?? null, fn ($q, $v) => $q->whereDate('fecha_ingreso', '>=', $v))
            ->when($filtros['hasta'] ?? null, fn ($q, $v) => $q->whereDate('fecha_ingreso', '<=', $v));

        $ordenesCompletadas = (clone $base)->whereIn('estado', ['Finalizada', 'Entregada'])->count();
        $ingresos = (float) (clone $base)->whereIn('estado', ['Finalizada', 'Entregada'])->sum('total');
        $ordenesQuery = (clone $base)->with(['cliente', 'vehiculo'])->latest('fecha_ingreso');

        return [
            'filtros' => $filtros,
            'metricas' => [
                'ordenes' => (clone $base)->count(),
                'completadas' => $ordenesCompletadas,
                'ingresos' => $ingresos,
                'ticket_promedio' => $ordenesCompletadas > 0 ? $ingresos / $ordenesCompletadas : 0,
                'clientes' => Cliente::count(),
                'stock_bajo' => Repuesto::whereColumn('stock', '<=', 'stock_minimo')->count(),
            ],
            'porEstado' => (clone $base)->selectRaw('estado, count(*) total')->groupBy('estado')->orderByDesc('total')->get(),
            'ordenes' => $paginar
                ? $ordenesQuery->paginate(15)->withQueryString()
                : $ordenesQuery->get(),
            'repuestosStockBajo' => Repuesto::query()
                ->where('activo', true)
                ->whereColumn('stock', '<=', 'stock_minimo')
                ->orderBy('stock')
                ->orderBy('nombre')
                ->get(),
        ];
    }
}
