@extends('layouts.app')
@php
    $edit = $orden->exists;
    $fechaIngreso = old('fecha_ingreso', $orden->fecha_ingreso?->format('Y-m-d') ?? now()->format('Y-m-d'));
    $fechaIngresoMinima = $edit && $orden->fecha_ingreso?->isPast()
        ? $orden->fecha_ingreso->format('Y-m-d')
        : now()->format('Y-m-d');
@endphp

@section('titulo', $edit ? 'Editar orden' : 'Nueva orden')
@section('encabezado', $edit ? 'Editar orden' : 'Nueva orden')
@section('subtitulo', 'Recepción, diagnóstico y seguimiento del trabajo')

@section('contenido')
<div class="card app-card mx-auto" style="max-width:1000px">
    <div class="card-body p-4">
        @if($errors->any())
            <div class="alert alert-danger">
                <strong>Revisa el formulario:</strong> {{ $errors->first() }}
            </div>
        @endif

        <form id="orden-form" method="POST" action="{{ $edit ? route('ordenes.update', $orden) : route('ordenes.store') }}">
            @csrf
            @if($edit) @method('PUT') @endif

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label" for="cliente_id">Cliente *</label>
                    <select class="form-select @error('cliente_id') is-invalid @enderror" id="cliente_id" name="cliente_id" required>
                        <option value="">Seleccionar cliente</option>
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}" @selected(old('cliente_id', $orden->cliente_id) == $cliente->id)>
                                {{ $cliente->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('cliente_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label" for="vehiculo_id">Vehículo *</label>
                    <select class="form-select @error('vehiculo_id') is-invalid @enderror" id="vehiculo_id" name="vehiculo_id" required>
                        <option value="">Primero selecciona un cliente</option>
                        @foreach($vehiculos as $vehiculo)
                            <option
                                value="{{ $vehiculo->id }}"
                                data-cliente="{{ $vehiculo->cliente_id }}"
                                @selected(old('vehiculo_id', $orden->vehiculo_id) == $vehiculo->id)
                            >
                                {{ $vehiculo->placa }} · {{ $vehiculo->marca }} {{ $vehiculo->modelo }}
                            </option>
                        @endforeach
                    </select>
                    <div class="form-text">Solo se muestran los vehículos pertenecientes al cliente seleccionado.</div>
                    @error('vehiculo_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label" for="fecha_ingreso">Fecha de ingreso *</label>
                    <input
                        type="date"
                        class="form-control @error('fecha_ingreso') is-invalid @enderror"
                        id="fecha_ingreso"
                        name="fecha_ingreso"
                        min="{{ $fechaIngresoMinima }}"
                        value="{{ $fechaIngreso }}"
                        required
                    >
                    <div class="form-text">{{ $edit ? 'Una fecha histórica existente puede conservarse.' : 'No puede ser anterior a hoy.' }}</div>
                    @error('fecha_ingreso')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label" for="fecha_entrega_estimada">Entrega estimada</label>
                    <input
                        type="date"
                        class="form-control @error('fecha_entrega_estimada') is-invalid @enderror"
                        id="fecha_entrega_estimada"
                        name="fecha_entrega_estimada"
                        min="{{ $fechaIngreso }}"
                        value="{{ old('fecha_entrega_estimada', $orden->fecha_entrega_estimada?->format('Y-m-d')) }}"
                    >
                    <div class="form-text">Puede ser el mismo día o una fecha posterior.</div>
                    @error('fecha_entrega_estimada')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label" for="fecha_entrega">Entrega real</label>
                    <input
                        type="date"
                        class="form-control @error('fecha_entrega') is-invalid @enderror"
                        id="fecha_entrega"
                        name="fecha_entrega"
                        min="{{ $fechaIngreso }}"
                        value="{{ old('fecha_entrega', $orden->fecha_entrega?->format('Y-m-d')) }}"
                    >
                    <div class="form-text">Es obligatoria cuando el estado sea Entregada.</div>
                    @error('fecha_entrega')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-8">
                    <label class="form-label" for="estado">Estado *</label>
                    <select class="form-select @error('estado') is-invalid @enderror" id="estado" name="estado" required>
                        @foreach(['Pendiente', 'En diagnóstico', 'En reparación', 'Finalizada', 'Entregada', 'Cancelada'] as $estado)
                            <option value="{{ $estado }}" @selected(old('estado', $orden->estado ?: 'Pendiente') === $estado)>{{ $estado }}</option>
                        @endforeach
                    </select>
                    @error('estado')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label" for="total">Total (Bs) *</label>
                    <input type="number" step=".01" min="0" class="form-control @error('total') is-invalid @enderror" id="total" name="total" value="{{ old('total', $orden->total ?? 0) }}" required>
                    @error('total')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label class="form-label" for="problema">Problema reportado *</label>
                    <textarea class="form-control @error('problema') is-invalid @enderror" id="problema" rows="3" name="problema" required maxlength="2000">{{ old('problema', $orden->problema) }}</textarea>
                    @error('problema')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label class="form-label" for="diagnostico">Diagnóstico técnico</label>
                    <textarea class="form-control @error('diagnostico') is-invalid @enderror" id="diagnostico" rows="4" name="diagnostico" maxlength="3000">{{ old('diagnostico', $orden->diagnostico) }}</textarea>
                    @error('diagnostico')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <hr class="my-4">

            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="fw-bold mb-0">Repuestos de stock utilizados</h5>
                <button type="button" class="btn btn-sm btn-outline-primary" id="btn-agregar-repuesto">
                    <i class="bi bi-plus-lg"></i> Agregar repuesto
                </button>
            </div>
            <div class="form-text mb-2">Solo puedes usar la cantidad disponible en inventario. El stock se descuenta al guardar la orden.</div>

            @error('repuestos')<div class="alert alert-danger">{{ $message }}</div>@enderror

            <div class="table-responsive">
                <table class="table table-sm align-middle" id="tabla-repuestos">
                    <thead class="table-light">
                        <tr>
                            <th style="min-width:260px">Repuesto</th>
                            <th style="width:140px">Disponible</th>
                            <th style="width:140px">Cantidad</th>
                            <th style="width:120px">Subtotal (Bs)</th>
                            <th style="width:50px"></th>
                        </tr>
                    </thead>
                    <tbody id="repuestos-filas"></tbody>
                    <tfoot>
                        <tr id="repuestos-vacio">
                            <td colspan="5" class="text-center text-secondary py-3">No se han agregado repuestos a esta orden.</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="text-end text-secondary" id="repuestos-total">Subtotal repuestos: Bs 0.00</div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a class="btn btn-light" href="{{ route('ordenes.index') }}">Cancelar</a>
                <button class="btn btn-primary" type="submit">Guardar orden</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script nonce="{{ $cspNonce }}">
const clienteSelect = document.getElementById('cliente_id');
const vehiculoSelect = document.getElementById('vehiculo_id');
const fechaIngresoInput = document.getElementById('fecha_ingreso');
const fechaEstimadaInput = document.getElementById('fecha_entrega_estimada');
const fechaEntregaInput = document.getElementById('fecha_entrega');
const estadoSelect = document.getElementById('estado');

function filtrarVehiculos() {
    const clienteId = clienteSelect.value;
    const seleccionActual = vehiculoSelect.value;
    let seleccionValida = false;

    Array.from(vehiculoSelect.options).forEach((option, index) => {
        if (index === 0) return;
        const pertenece = clienteId !== '' && option.dataset.cliente === clienteId;
        option.hidden = !pertenece;
        option.disabled = !pertenece;
        if (pertenece && option.value === seleccionActual) seleccionValida = true;
    });

    vehiculoSelect.options[0].textContent = clienteId
        ? 'Seleccionar vehículo del cliente'
        : 'Primero selecciona un cliente';
    vehiculoSelect.disabled = clienteId === '';

    if (!seleccionValida) vehiculoSelect.value = '';
}

function sincronizarFechas() {
    const fechaIngreso = fechaIngresoInput.value;
    fechaEstimadaInput.min = fechaIngreso;
    fechaEntregaInput.min = fechaIngreso;

    if (fechaEstimadaInput.value && fechaEstimadaInput.value < fechaIngreso) fechaEstimadaInput.value = '';
    if (fechaEntregaInput.value && fechaEntregaInput.value < fechaIngreso) fechaEntregaInput.value = '';
}

function sincronizarEstado() {
    fechaEntregaInput.required = estadoSelect.value === 'Entregada';
}

clienteSelect.addEventListener('change', filtrarVehiculos);
fechaIngresoInput.addEventListener('change', sincronizarFechas);
estadoSelect.addEventListener('change', sincronizarEstado);
filtrarVehiculos();
sincronizarFechas();
sincronizarEstado();

(function () {
    const repuestos = @json($repuestosDisponibles->map(fn ($r) => [
        'id' => $r->id,
        'nombre' => $r->nombre,
        'codigo' => $r->codigo,
        'disponible' => (int) $r->disponible,
        'precio' => (float) $r->precio,
    ])->values());

    const seleccionInicial = @json(
        collect(old('repuestos', $orden->repuestos->map(fn ($r) => ['id' => $r->id, 'cantidad' => $r->pivot->cantidad])->all()))
            ->map(fn ($item) => ['id' => (int) ($item['id'] ?? 0), 'cantidad' => max(1, (int) ($item['cantidad'] ?? 1))])
            ->filter(fn ($item) => $item['id'] > 0)
            ->values()
    );

    const cuerpo = document.getElementById('repuestos-filas');
    const filaVacia = document.getElementById('repuestos-vacio');
    const totalTexto = document.getElementById('repuestos-total');
    const btnAgregar = document.getElementById('btn-agregar-repuesto');

    let filas = [];
    let contador = 0;

    const escapar = (texto) => String(texto).replace(/[&<>"']/g, (c) => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;',
    }[c]));

    const repuestoPorId = (id) => repuestos.find((r) => r.id === Number(id));
    const idsUsados = (exceptoRowId) => filas.filter((f) => f.rowId !== exceptoRowId).map((f) => f.repuestoId);

    function agregarFila(repuestoId, cantidad) {
        contador += 1;
        filas.push({ rowId: contador, repuestoId: repuestoId ? Number(repuestoId) : null, cantidad: cantidad || 1 });
        render();
    }

    function eliminarFila(rowId) {
        filas = filas.filter((f) => f.rowId !== rowId);
        render();
    }

    function render() {
        cuerpo.innerHTML = '';
        filaVacia.classList.toggle('d-none', filas.length > 0);

        let total = 0;

        filas.forEach((fila) => {
            const usados = idsUsados(fila.rowId);
            const repuesto = repuestoPorId(fila.repuestoId);

            const opciones = repuestos
                .filter((r) => (r.disponible > 0 || r.id === fila.repuestoId) && !usados.includes(r.id))
                .map((r) => `<option value="${r.id}" ${r.id === fila.repuestoId ? 'selected' : ''}>${escapar(r.codigo)} · ${escapar(r.nombre)} (disp. ${r.disponible})</option>`)
                .join('');

            const disponible = repuesto ? repuesto.disponible : 0;
            const cantidad = Math.min(fila.cantidad, disponible || fila.cantidad);
            const excedeStock = repuesto && fila.cantidad > disponible;
            const subtotal = repuesto ? repuesto.precio * fila.cantidad : 0;
            total += subtotal;

            const nombreId = repuesto ? `name="repuestos[${fila.rowId}][id]"` : '';
            const nombreCantidad = repuesto ? `name="repuestos[${fila.rowId}][cantidad]"` : '';

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>
                    <select class="form-select form-select-sm" data-role="repuesto" ${nombreId}>
                        <option value="">Seleccionar repuesto</option>
                        ${opciones}
                    </select>
                </td>
                <td class="text-center" data-role="disponible">${repuesto ? disponible : '—'}</td>
                <td>
                    <input type="number" class="form-control form-control-sm ${excedeStock ? 'is-invalid' : ''}" data-role="cantidad" min="1" max="${disponible || 1}" value="${fila.cantidad}" ${repuesto ? '' : 'disabled'} ${nombreCantidad}>
                    ${excedeStock ? `<div class="invalid-feedback d-block">Máximo disponible: ${disponible}</div>` : ''}
                </td>
                <td class="text-end" data-role="subtotal">${subtotal.toFixed(2)}</td>
                <td class="text-end"><button type="button" class="btn btn-sm btn-outline-danger" data-role="eliminar"><i class="bi bi-x-lg"></i></button></td>
            `;

            tr.querySelector('[data-role="repuesto"]').addEventListener('change', (e) => {
                fila.repuestoId = e.target.value ? Number(e.target.value) : null;
                fila.cantidad = 1;
                render();
            });

            const inputCantidad = tr.querySelector('[data-role="cantidad"]');
            if (inputCantidad) {
                inputCantidad.addEventListener('input', (e) => {
                    fila.cantidad = Math.max(1, parseInt(e.target.value || '1', 10));
                    render();
                });
            }

            tr.querySelector('[data-role="eliminar"]').addEventListener('click', () => eliminarFila(fila.rowId));

            cuerpo.appendChild(tr);
        });

        totalTexto.textContent = `Subtotal repuestos: Bs ${total.toFixed(2)}`;
    }

    btnAgregar.addEventListener('click', () => agregarFila(null, 1));

    document.getElementById('orden-form').addEventListener('submit', (e) => {
        const invalida = filas.find((fila) => {
            const repuesto = repuestoPorId(fila.repuestoId);
            return repuesto && fila.cantidad > repuesto.disponible;
        });

        if (invalida) {
            e.preventDefault();
            alert('Hay repuestos con una cantidad mayor al stock disponible. Corrígelos antes de guardar.');
        }
    });

    seleccionInicial.forEach((item) => agregarFila(item.id, item.cantidad));
    render();
})();
</script>
@endpush
