<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe de operaciones - Grupo Los Jausis</title>
    <style>
        @page { margin: 28px 32px 38px; }
        * { box-sizing: border-box; }
        body { color: #1f2937; font-family: "DejaVu Sans", sans-serif; font-size: 9px; margin: 0; }
        h1, h2, p { margin: 0; }
        .header { border-bottom: 3px solid #dc2626; margin-bottom: 14px; padding-bottom: 10px; }
        .brand { color: #b91c1c; font-size: 20px; font-weight: bold; }
        .title { font-size: 14px; font-weight: bold; margin-top: 3px; }
        .meta { color: #6b7280; line-height: 1.5; margin-top: 5px; }
        .metrics { margin-bottom: 14px; width: 100%; }
        .metrics td { padding-right: 7px; vertical-align: top; width: 16.66%; }
        .metric { background: #f3f4f6; border-left: 3px solid #dc2626; padding: 8px; }
        .metric-label { color: #6b7280; font-size: 8px; text-transform: uppercase; }
        .metric-value { font-size: 14px; font-weight: bold; margin-top: 3px; }
        .summaries { margin-bottom: 10px; width: 100%; }
        .summaries > tbody > tr > td { vertical-align: top; width: 50%; }
        .summary-left { padding-right: 8px; }
        .summary-right { padding-left: 8px; }
        .section-title { background: #7f1d1d; color: #fff; font-size: 10px; padding: 6px 8px; }
        table.data { border-collapse: collapse; margin-bottom: 12px; width: 100%; }
        table.data thead { display: table-header-group; }
        table.data th { background: #fff1f2; color: #7f1d1d; font-size: 8px; padding: 5px; text-align: left; }
        table.data td { border-bottom: 1px solid #e5e7eb; padding: 5px; vertical-align: top; }
        table.data tr { page-break-inside: avoid; }
        .number { text-align: right; white-space: nowrap; }
        .empty { color: #6b7280; padding: 12px; text-align: center; }
        .note { background: #fff7ed; border: 1px solid #fed7aa; color: #7c2d12; line-height: 1.5; padding: 8px; }
        .status { border-radius: 8px; color: #374151; font-size: 8px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="brand">Grupo Los Jausis</div>
        <div class="title">Informe operativo y financiero del taller</div>
        <div class="meta">
            Periodo:
            {{ $filtros['desde'] ?? 'inicio de registros' }}
            al
            {{ $filtros['hasta'] ?? now()->toDateString() }}
            | Generado: {{ now()->format('d/m/Y H:i') }}
            | Responsable: {{ auth()->user()->name }}
        </div>
    </div>

    <table class="metrics">
        <tr>
            @foreach([
                ['Órdenes', $metricas['ordenes']],
                ['Completadas', $metricas['completadas']],
                ['Ingresos', 'Bs '.number_format($metricas['ingresos'], 2)],
                ['Ticket promedio', 'Bs '.number_format($metricas['ticket_promedio'], 2)],
                ['Clientes', $metricas['clientes']],
                ['Stock bajo', $metricas['stock_bajo']],
            ] as [$etiqueta, $valor])
                <td>
                    <div class="metric">
                        <div class="metric-label">{{ $etiqueta }}</div>
                        <div class="metric-value">{{ $valor }}</div>
                    </div>
                </td>
            @endforeach
        </tr>
    </table>

    <table class="summaries">
        <tr>
            <td class="summary-left">
                <div class="section-title">Órdenes por estado</div>
                <table class="data">
                    <thead><tr><th>Estado</th><th class="number">Cantidad</th></tr></thead>
                    <tbody>
                        @forelse($porEstado as $estado)
                            <tr><td>{{ $estado->estado }}</td><td class="number">{{ number_format($estado->total, 0) }}</td></tr>
                        @empty
                            <tr><td class="empty" colspan="2">Sin datos.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </td>
            <td class="summary-right">
                <div class="section-title">Alertas de inventario</div>
                <table class="data">
                    <thead><tr><th>Código / repuesto</th><th class="number">Stock / mín.</th></tr></thead>
                    <tbody>
                        @forelse($repuestosStockBajo as $repuesto)
                            <tr>
                                <td>{{ $repuesto->codigo }}<br>{{ $repuesto->nombre }}</td>
                                <td class="number">{{ $repuesto->stock }} / {{ $repuesto->stock_minimo }}</td>
                            </tr>
                        @empty
                            <tr><td class="empty" colspan="2">Inventario sin alertas.</td></tr>
                        @endforelse
                    </tbody>
                </table>

            </td>
        </tr>
    </table>

    <div class="note">
        Los ingresos y el ticket promedio consideran únicamente órdenes finalizadas o entregadas.
        Una alerta de inventario se genera cuando el stock es menor o igual al mínimo configurado.
    </div>

    <div class="section-title" style="margin-top: 12px;">Detalle de órdenes</div>
    <table class="data">
        <thead>
            <tr>
                <th>Orden</th>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Vehículo</th>
                <th>Estado</th>
                <th class="number">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ordenes as $orden)
                <tr>
                    <td>{{ $orden->numero }}</td>
                    <td>{{ $orden->fecha_ingreso->format('d/m/Y') }}</td>
                    <td>{{ $orden->cliente->nombre }}</td>
                    <td>{{ $orden->vehiculo->placa }} - {{ $orden->vehiculo->marca }}</td>
                    <td class="status">{{ $orden->estado }}</td>
                    <td class="number">Bs {{ number_format($orden->total, 2) }}</td>
                </tr>
            @empty
                <tr><td class="empty" colspan="6">No existen órdenes en el periodo seleccionado.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
