@extends('layouts.app')

@section('titulo', 'Reservas')
@section('encabezado', 'Reservas de clientes')
@section('subtitulo', 'Solicitudes de citas recibidas desde la página pública')

@push('estilos')
<style>
    .reservation-stat { position: relative; overflow: hidden; }
    .reservation-stat::after { position:absolute; width:80px; height:80px; right:-25px; top:-25px; border:16px solid rgba(220,38,38,.06); border-radius:50%; content:""; }
    .reservation-icon { width:44px; height:44px; display:grid; place-items:center; border-radius:13px; color:#b91c1c; background:#fee2e2; font-size:1.15rem; }
    .reservation-item { border-left: 4px solid #d7c6c8; }
    .reservation-item.pending { border-left-color:#f59e0b; }
    .reservation-item.confirmed { border-left-color:#16a34a; }
    .reservation-item.attended { border-left-color:#2563eb; }
    .data-label { color:#92767a; font-size:.69rem; font-weight:800; letter-spacing:.07em; text-transform:uppercase; }
    .status-badge { display:inline-flex; align-items:center; gap:6px; padding:.42rem .7rem; border-radius:99px; font-size:.76rem; font-weight:800; }
    .status-Pendiente { color:#92400e; background:#fef3c7; }
    .status-Confirmada { color:#166534; background:#dcfce7; }
    .status-Atendida { color:#1e40af; background:#dbeafe; }
    .status-Cancelada { color:#4b5563; background:#f3f4f6; }
</style>
@endpush

@section('contenido')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold mb-1">Agenda del taller</h2>
        <p class="text-secondary mb-0">Revisa y gestiona las citas solicitadas por los clientes.</p>
    </div>
    <a class="btn btn-outline-primary" href="{{ route('inicio') }}#reservar" target="_blank"><i class="bi bi-box-arrow-up-right me-1"></i>Ver formulario público</a>
</div>

<div class="row g-3 mb-4">
    @foreach([['Pendiente','bi-hourglass-split'],['Confirmada','bi-calendar2-check'],['Atendida','bi-check2-circle'],['Cancelada','bi-calendar2-x']] as [$estado,$icono])
        <div class="col-6 col-xl-3">
            <div class="app-card reservation-stat p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="reservation-icon"><i class="bi {{ $icono }}"></i></div>
                    <div><div class="h4 fw-bold mb-0">{{ $resumen[$estado] ?? 0 }}</div><small class="text-secondary">{{ $estado }}</small></div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="app-card p-3 p-md-4 mb-4">
    <form method="GET" action="{{ route('reservas.index') }}">
        <div class="row g-3 align-items-end">
            <div class="col-lg-5">
                <label class="form-label" for="buscar">Buscar</label>
                <div class="input-group"><span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span><input class="form-control border-start-0" id="buscar" name="buscar" value="{{ request('buscar') }}" placeholder="Código, cliente, teléfono o placa"></div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <label class="form-label" for="estado">Estado</label>
                <select class="form-select" id="estado" name="estado">
                    <option value="">Todos</option>
                    @foreach(['Pendiente','Confirmada','Atendida','Cancelada'] as $estado)
                        <option value="{{ $estado }}" @selected(request('estado') === $estado)>{{ $estado }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-6 col-lg-2">
                <label class="form-label" for="fecha">Fecha</label>
                <input class="form-control" id="fecha" name="fecha" type="date" value="{{ request('fecha') }}">
            </div>
            <div class="col-lg-2 d-flex gap-2">
                <button class="btn btn-primary flex-grow-1" type="submit">Filtrar</button>
                <a class="btn btn-light" href="{{ route('reservas.index') }}" title="Limpiar filtros"><i class="bi bi-arrow-counterclockwise"></i></a>
            </div>
        </div>
    </form>
</div>

<div class="d-grid gap-3">
    @forelse($reservas as $reserva)
        @php
            $itemClass = match($reserva->estado) {
                'Pendiente' => 'pending',
                'Confirmada' => 'confirmed',
                'Atendida' => 'attended',
                default => 'cancelled',
            };
        @endphp
        <article class="app-card reservation-item {{ $itemClass }}">
            <div class="p-3 p-md-4">
                <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-4">
                    <div>
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                            <h3 class="h5 fw-bold mb-0">{{ $reserva->nombre }}</h3>
                            <span class="status-badge status-{{ $reserva->estado }}"><i class="bi bi-circle-fill" style="font-size:.42rem"></i>{{ $reserva->estado }}</span>
                        </div>
                        <span class="text-secondary small"><i class="bi bi-hash"></i>{{ $reserva->codigo }} · recibida {{ $reserva->created_at->diffForHumans() }}</span>
                    </div>
                    <div class="text-lg-end">
                        <div class="fw-bold text-danger"><i class="bi bi-calendar3 me-1"></i>{{ $reserva->fecha_preferida->format('d/m/Y') }} · {{ $reserva->hora_preferida }}</div>
                        <small class="text-secondary">Fecha y hora solicitadas</small>
                    </div>
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-sm-6 col-xl-3"><div class="data-label mb-1">Contacto</div><a class="text-dark fw-semibold" href="tel:{{ $reserva->telefono }}">{{ $reserva->telefono }}</a>@if($reserva->email)<div class="small text-secondary text-break">{{ $reserva->email }}</div>@endif</div>
                    <div class="col-sm-6 col-xl-3"><div class="data-label mb-1">Vehículo</div><div class="fw-semibold">{{ $reserva->vehiculo_marca }} {{ $reserva->vehiculo_modelo }}</div><small class="text-secondary">{{ $reserva->placa ?: 'Sin placa indicada' }}</small></div>
                    <div class="col-sm-6 col-xl-3"><div class="data-label mb-1">Servicio</div><div class="fw-semibold">{{ $reserva->servicio?->nombre ?: 'Requiere orientación' }}</div></div>
                    <div class="col-sm-6 col-xl-3"><div class="data-label mb-1">Descripción del cliente</div><div class="small">{{ $reserva->mensaje ?: 'Sin descripción adicional.' }}</div></div>
                </div>
                <form action="{{ route('reservas.update', $reserva) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label" for="estado-{{ $reserva->id }}">Estado de la cita</label>
                            <select class="form-select" id="estado-{{ $reserva->id }}" name="estado" required>
                                @foreach(['Pendiente','Confirmada','Atendida','Cancelada'] as $estado)
                                    <option value="{{ $estado }}" @selected($reserva->estado === $estado)>{{ $estado }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-7">
                            <label class="form-label" for="nota-{{ $reserva->id }}">Nota interna</label>
                            <input class="form-control" id="nota-{{ $reserva->id }}" name="nota_interna" value="{{ $reserva->nota_interna }}" maxlength="1000" placeholder="Ej.: llamar para confirmar disponibilidad">
                        </div>
                        <div class="col-md-2"><button class="btn btn-primary w-100" type="submit"><i class="bi bi-save me-1"></i>Guardar</button></div>
                    </div>
                </form>
                @if(Auth::user()->hasRole('admin'))
                    <div class="text-end mt-3">
                        <form class="d-inline" action="{{ route('reservas.destroy', $reserva) }}" method="POST" data-confirm="¿Eliminar definitivamente la reserva {{ $reserva->codigo }}?">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-light text-danger" type="submit"><i class="bi bi-trash3 me-1"></i>Eliminar</button>
                        </form>
                    </div>
                @endif
            </div>
        </article>
    @empty
        <div class="app-card text-center p-5">
            <div class="reservation-icon mx-auto mb-3"><i class="bi bi-calendar2"></i></div>
            <h3 class="h5 fw-bold">No hay reservas para mostrar</h3>
            <p class="text-secondary mb-0">Las nuevas solicitudes realizadas desde la página pública aparecerán aquí.</p>
        </div>
    @endforelse
</div>

@if($reservas->hasPages())
    <div class="mt-4">{{ $reservas->links() }}</div>
@endif
@endsection
