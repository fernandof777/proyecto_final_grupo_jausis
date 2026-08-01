<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\OrdenTrabajo;
use App\Models\Vehiculo;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
            $data['numero'] = 'OT-'.now()->format('ymd').'-'.Str::upper(Str::random(5));
            $request->user()->ordenesTrabajo()->create($data);
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
            $orden->update($this->validated($request, $orden));
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
        $orden->delete();

        return back()->with('success', 'Orden eliminada.');
    }

    private function form(OrdenTrabajo $orden): View
    {
        return view('ordenes.form', [
            'orden' => $orden,
            'clientes' => Cliente::where('activo', true)->orderBy('nombre')->get(),
            'vehiculos' => Vehiculo::with('cliente')->orderBy('placa')->get(),
        ]);
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
