<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ReservaController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'nombre' => ['required', 'string', 'min:3', 'max:120'],
            'telefono' => ['required', 'string', 'min:7', 'max:30', 'regex:/^[0-9+()\s-]+$/'],
            'email' => ['nullable', 'email:rfc', 'max:255'],
            'servicio_id' => ['nullable', 'integer', Rule::exists('servicios', 'id')->where('estado', 'Activo')],
            'vehiculo_marca' => ['required', 'string', 'max:80'],
            'vehiculo_modelo' => ['nullable', 'string', 'max:80'],
            'placa' => ['nullable', 'string', 'max:20'],
            'fecha_preferida' => ['required', 'date', 'after_or_equal:today'],
            'hora_preferida' => ['required', 'date_format:H:i', 'after_or_equal:08:00', 'before_or_equal:18:00'],
            'mensaje' => ['nullable', 'string', 'max:1000'],
        ], [
            'telefono.regex' => 'Ingresa un número de teléfono válido.',
            'fecha_preferida.after_or_equal' => 'La fecha de la cita no puede estar en el pasado.',
            'hora_preferida.after_or_equal' => 'El horario de atención comienza a las 08:00.',
            'hora_preferida.before_or_equal' => 'El horario de atención termina a las 18:00.',
            'servicio_id.exists' => 'El servicio seleccionado no está disponible.',
        ]);

        $datos['codigo'] = $this->generarCodigo();
        $datos['placa'] = $datos['placa'] ? mb_strtoupper($datos['placa']) : null;
        $reserva = Reserva::create($datos);

        return redirect(route('inicio').'#reservar')
            ->with('reserva_exitosa', [
                'codigo' => $reserva->codigo,
                'fecha' => $reserva->fecha_preferida->format('d/m/Y'),
                'hora' => $reserva->hora_preferida,
            ]);
    }

    public function index(Request $request): View
    {
        $filtros = $request->validate([
            'buscar' => ['nullable', 'string', 'max:100'],
            'estado' => ['nullable', Rule::in(['Pendiente', 'Confirmada', 'Atendida', 'Cancelada'])],
            'fecha' => ['nullable', 'date'],
        ]);

        $reservas = Reserva::query()
            ->with('servicio')
            ->when($filtros['buscar'] ?? null, function ($query, string $buscar): void {
                $query->where(function ($subquery) use ($buscar): void {
                    $subquery
                        ->where('codigo', 'like', "%{$buscar}%")
                        ->orWhere('nombre', 'like', "%{$buscar}%")
                        ->orWhere('telefono', 'like', "%{$buscar}%")
                        ->orWhere('placa', 'like', "%{$buscar}%");
                });
            })
            ->when($filtros['estado'] ?? null, fn ($query, string $estado) => $query->where('estado', $estado))
            ->when($filtros['fecha'] ?? null, fn ($query, string $fecha) => $query->whereDate('fecha_preferida', $fecha))
            ->orderByRaw("CASE WHEN estado = 'Pendiente' THEN 0 WHEN estado = 'Confirmada' THEN 1 ELSE 2 END")
            ->orderBy('fecha_preferida')
            ->orderBy('hora_preferida')
            ->paginate(12)
            ->withQueryString();

        $resumen = Reserva::query()
            ->selectRaw('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->pluck('total', 'estado');

        return view('reservas.index', compact('reservas', 'resumen'));
    }

    public function update(Request $request, Reserva $reserva): RedirectResponse
    {
        $datos = $request->validate([
            'estado' => ['required', Rule::in(['Pendiente', 'Confirmada', 'Atendida', 'Cancelada'])],
            'nota_interna' => ['nullable', 'string', 'max:1000'],
        ]);

        $reserva->update($datos);

        return back()->with('success', "La reserva {$reserva->codigo} fue actualizada.");
    }

    public function destroy(Reserva $reserva): RedirectResponse
    {
        $codigo = $reserva->codigo;
        $reserva->delete();

        return back()->with('success', "La reserva {$codigo} fue eliminada.");
    }

    private function generarCodigo(): string
    {
        do {
            $codigo = 'CITA-'.now()->format('ymd').'-'.Str::upper(Str::random(5));
        } while (Reserva::where('codigo', $codigo)->exists());

        return $codigo;
    }
}
