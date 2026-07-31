<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\OrdenTrabajo;
use App\Models\Repuesto;
use App\Models\Vehiculo;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class OrdenTrabajoController extends Controller
{
    public function index(Request $request): View
    {
        $ordenes = OrdenTrabajo::with(['cliente', 'vehiculo', 'usuario'])
            ->when($request->buscar, fn ($q, $v) => $q->where(fn ($s) => $s->where('numero', 'like', "%{$v}%")->orWhereHas('cliente', fn ($c) => $c->where('nombre', 'like', "%{$v}%"))))
            ->when($request->estado, fn ($q, $v) => $q->where('estado', $v))
            ->latest('fecha_ingreso')->paginate(12)->withQueryString();

        return view('ordenes.index', compact('ordenes'));
    }

    public function create(): View
    {
        return $this->form(new OrdenTrabajo);
    }

    public function store(Request $request): RedirectResponse
    {
        try {
            $data = $this->validated($request);
            $repuestos = $this->repuestosSolicitados($request);
            $numero = 'OT-'.now()->format('ymd').'-'.Str::upper(Str::random(5));

            DB::transaction(function () use ($request, $data, $numero, $repuestos) {
                $orden = $request->user()->ordenesTrabajo()->create($data + ['numero' => $numero]);
                $this->aplicarRepuestos($orden, $repuestos);
            });
        } catch (QueryException $exception) {
            report($exception);

            return back()
                ->withInput()
                ->with('error', 'No se pudo guardar la orden. Verifica que los datos sean válidos e inténtalo nuevamente.');
        }

        return to_route('ordenes.index')->with('success', 'Orden de trabajo creada correctamente.');
    }

    public function edit(OrdenTrabajo $orden): View
    {
        return $this->form($orden);
    }

    public function update(Request $request, OrdenTrabajo $orden): RedirectResponse
    {
        try {
            $data = $this->validated($request, $orden);
            $repuestos = $this->repuestosSolicitados($request);

            DB::transaction(function () use ($orden, $data, $repuestos) {
                $this->liberarRepuestos($orden);
                $orden->update($data);
                $this->aplicarRepuestos($orden, $repuestos);
            });
        } catch (QueryException $exception) {
            report($exception);

            return back()
                ->withInput()
                ->with('error', 'No se pudo actualizar la orden. Verifica que los datos sean válidos e inténtalo nuevamente.');
        }

        return to_route('ordenes.index')->with('success', 'Orden actualizada correctamente.');
    }

    public function destroy(OrdenTrabajo $orden): RedirectResponse
    {
        DB::transaction(function () use ($orden) {
            $this->liberarRepuestos($orden);
            $orden->delete();
        });

        return back()->with('success', 'Orden eliminada.');
    }

    private function form(OrdenTrabajo $orden): View
    {
        $orden->loadMissing('repuestos');

        // Cantidades que la propia orden ya tiene reservadas (0 si es una orden nueva).
        // Se suman al stock actual para mostrar el "disponible real" al editar,
        // ya que esa cantidad se libera y se vuelve a validar al guardar.
        $cantidadesActuales = $orden->repuestos->pluck('pivot.cantidad', 'id');

        $repuestosDisponibles = Repuesto::where('activo', true)->orderBy('nombre')->get()
            ->each(function (Repuesto $repuesto) use ($cantidadesActuales) {
                $repuesto->disponible = $repuesto->stock + (int) ($cantidadesActuales[$repuesto->id] ?? 0);
            });

        return view('ordenes.form', [
            'orden' => $orden,
            'clientes' => Cliente::where('activo', true)->orderBy('nombre')->get(),
            'vehiculos' => Vehiculo::with('cliente')->orderBy('placa')->get(),
            'repuestosDisponibles' => $repuestosDisponibles,
        ]);
    }

    /**
     * Extrae y normaliza la lista de repuestos enviada desde el formulario.
     */
    private function repuestosSolicitados(Request $request): array
    {
        $request->validate([
            'repuestos' => ['nullable', 'array'],
            'repuestos.*.id' => [
                'required_with:repuestos',
                'integer',
                'distinct',
                Rule::exists('repuestos', 'id')->where('activo', true),
            ],
            'repuestos.*.cantidad' => ['required_with:repuestos', 'integer', 'min:1', 'max:100000'],
        ], [
            'repuestos.*.id.exists' => 'Uno de los repuestos seleccionados no existe o está inactivo.',
            'repuestos.*.id.distinct' => 'No puedes seleccionar el mismo repuesto más de una vez.',
            'repuestos.*.cantidad.min' => 'La cantidad de cada repuesto debe ser mayor a cero.',
        ]);

        return collect($request->input('repuestos', []))
            ->filter(fn ($item) => filled($item['id'] ?? null) && (int) ($item['cantidad'] ?? 0) > 0)
            ->map(fn ($item) => ['id' => (int) $item['id'], 'cantidad' => (int) $item['cantidad']])
            ->unique('id')
            ->values()
            ->all();
    }

    /**
     * Descuenta del stock los repuestos solicitados y los asocia a la orden.
     * Bloquea las filas de repuestos para evitar condiciones de carrera entre
     * órdenes concurrentes que soliciten el mismo repuesto.
     */
    private function aplicarRepuestos(OrdenTrabajo $orden, array $items): void
    {
        if (empty($items)) {
            return;
        }

        $ids = collect($items)->pluck('id');

        $repuestos = Repuesto::whereIn('id', $ids)
            ->where('activo', true)
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        foreach ($items as $item) {
            $repuesto = $repuestos->get($item['id']);

            if (! $repuesto) {
                throw ValidationException::withMessages([
                    'repuestos' => 'Uno de los repuestos seleccionados no existe o está inactivo.',
                ]);
            }

            if ($item['cantidad'] > $repuesto->stock) {
                throw ValidationException::withMessages([
                    'repuestos' => "No hay stock suficiente de \"{$repuesto->nombre}\". Disponible: {$repuesto->stock}, solicitado: {$item['cantidad']}.",
                ]);
            }

            $repuesto->decrement('stock', $item['cantidad']);

            $orden->repuestos()->attach($repuesto->id, [
                'cantidad' => $item['cantidad'],
                'precio_unitario' => $repuesto->precio,
            ]);
        }
    }

    /**
     * Devuelve al stock las cantidades previamente asignadas a la orden y
     * elimina las asociaciones, dejando el inventario como si la orden no
     * hubiese consumido repuestos.
     */
    private function liberarRepuestos(OrdenTrabajo $orden): void
    {
        $asignados = $orden->repuestos()->get();

        if ($asignados->isEmpty()) {
            return;
        }

        $repuestos = Repuesto::whereIn('id', $asignados->pluck('id'))
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        foreach ($asignados as $asignado) {
            $repuestos->get($asignado->id)?->increment('stock', (int) $asignado->pivot->cantidad);
        }

        $orden->repuestos()->detach();
    }

    private function validated(Request $request, ?OrdenTrabajo $orden = null): array
    {
        $fechaIngresoRules = ['required', 'date_format:Y-m-d'];
        $fechaOriginal = $orden?->fecha_ingreso?->format('Y-m-d');

        if (! $orden || $request->input('fecha_ingreso') !== $fechaOriginal) {
            $fechaIngresoRules[] = 'after_or_equal:today';
        }

        $data = $request->validate([
            'cliente_id' => ['required', 'integer', Rule::exists('clientes', 'id')->where('activo', true)],
            'vehiculo_id' => [
                'required',
                'integer',
                Rule::exists('vehiculos', 'id')
                    ->where(fn ($query) => $query->where('cliente_id', $request->integer('cliente_id'))),
            ],
            'problema' => ['required', 'string', 'max:2000'],
            'diagnostico' => ['nullable', 'string', 'max:3000'],
            'estado' => ['required', Rule::in(['Pendiente', 'En diagnóstico', 'En reparación', 'Finalizada', 'Entregada', 'Cancelada'])],
            'fecha_ingreso' => $fechaIngresoRules,
            'fecha_entrega_estimada' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:fecha_ingreso'],
            'fecha_entrega' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:fecha_ingreso'],
            'total' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
        ], [
            'cliente_id.exists' => 'El cliente seleccionado no existe o está inactivo.',
            'vehiculo_id.exists' => 'El vehículo seleccionado no pertenece al cliente.',
            'fecha_ingreso.after_or_equal' => 'La fecha de ingreso de una orden nueva no puede estar en el pasado.',
            'fecha_ingreso.date_format' => 'La fecha de ingreso no tiene un formato válido.',
            'fecha_entrega_estimada.after_or_equal' => 'La entrega estimada debe ser igual o posterior a la fecha de ingreso.',
            'fecha_entrega.after_or_equal' => 'La entrega real debe ser igual o posterior a la fecha de ingreso.',
            'fecha_entrega_estimada.date_format' => 'La fecha de entrega estimada no tiene un formato válido.',
            'fecha_entrega.date_format' => 'La fecha de entrega real no tiene un formato válido.',
        ]);

        if ($data['estado'] === 'Entregada' && empty($data['fecha_entrega'])) {
            throw ValidationException::withMessages([
                'fecha_entrega' => 'Indica la fecha de entrega real cuando la orden está Entregada.',
            ]);
        }

        return $data;
    }
}
