@extends('layouts.app')
@section('titulo', 'Reportes')
@section('encabezado', 'Reportes')
@section('subtitulo', 'Indicadores operativos y financieros')

@section('contenido')
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold mb-1">Reporte de operaciones</h2>
        <p class="text-secondary mb-0">Resultados calculados desde las órdenes registradas.</p>
    </div>
    <div class="d-flex gap-2">
        <a class="btn btn-danger" href="{{ route('reportes.pdf', request()->only(['desde', 'hasta'])) }}">
            <i class="bi bi-file-earmark-pdf me-1"></i>Descargar PDF
        </a>
        <button class="btn btn-outline-primary" id="print-page">
            <i class="bi bi-printer me-1"></i>Imprimir
        </button>
    </div>
</div>

<div class="card app-card mb-4">
    <div class="card-body">
        <form class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label">Desde</label>
                <input type="date" class="form-control" name="desde" value="{{ request('desde') }}">
            </div>
            <div class="col-md-5">
                <label class="form-label">Hasta</label>
                <input type="date" class="form-control" name="hasta" value="{{ request('hasta') }}">
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100">Aplicar</button>
            </div>
        </form>
    </div>
</div>

<div class="row g-3 mb-4">
    @foreach([
        ['Órdenes', $metricas['ordenes'], 'primary'],
        ['Ingresos', 'Bs '.number_format($metricas['ingresos'], 2), 'success'],
        ['Clientes', $metricas['clientes'], 'info'],
        ['Stock bajo', $metricas['stock_bajo'], 'danger'],
    ] as [$nombre, $valor, $color])
        <div class="col-6 col-xl-3">
            <div class="card app-card h-100">
                <div class="card-body">
                    <small class="text-secondary">{{ $nombre }}</small>
                    <div class="fs-3 fw-bold text-{{ $color }}">{{ $valor }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card app-card h-100">
            <div class="card-body">
                <h5 class="fw-bold">Órdenes por estado</h5>
                @forelse($porEstado as $estado)
                    <div class="d-flex justify-content-between border-bottom py-3">
                        <span>{{ $estado->estado }}</span>
                        <strong>{{ number_format($estado->total, 0) }}</strong>
                    </div>
                @empty
                    <p class="text-secondary mt-4">Sin datos para el periodo.</p>
                @endforelse
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card app-card">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Orden</th>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Vehículo</th>
                            <th>Estado</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ordenes as $orden)
                            <tr>
                                <td>{{ $orden->numero }}</td>
                                <td>{{ $orden->fecha_ingreso->format('d/m/Y') }}</td>
                                <td>{{ $orden->cliente->nombre }}</td>
                                <td>{{ $orden->vehiculo->placa }}</td>
                                <td>{{ $orden->estado }}</td>
                                <td>Bs {{ number_format($orden->total, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center py-4">Sin resultados.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($ordenes->hasPages())
                <div class="card-footer bg-white">{{ $ordenes->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
