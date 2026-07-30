<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ClienteController extends Controller
{
    public function index(Request $request): View
    {
        $clientes = Cliente::withCount(['vehiculos', 'ordenes'])
            ->when($request->buscar, fn ($q, $v) => $q->where(fn ($s) => $s->where('nombre', 'like', "%{$v}%")->orWhere('ci_nit', 'like', "%{$v}%")))
            ->latest()->paginate(12)->withQueryString();

        return view('clientes.index', compact('clientes'));
    }

    public function create(): View
    {
        return view('clientes.form', ['cliente' => new Cliente]);
    }

    public function store(Request $request): RedirectResponse
    {
        try {
            Cliente::create($this->validated($request));
        } catch (QueryException $exception) {
            report($exception);

            return back()
                ->withInput()
                ->with('error', 'No se pudo registrar el cliente. Verifica que los datos sean válidos e inténtalo nuevamente.');
        }

        return to_route('clientes.index')->with('success', 'Cliente registrado correctamente.');
    }

    public function edit(Cliente $cliente): View
    {
        return view('clientes.form', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente): RedirectResponse
    {
        try {
            $cliente->update($this->validated($request, $cliente));
        } catch (QueryException $exception) {
            report($exception);

            return back()
                ->withInput()
                ->with('error', 'No se pudo actualizar el cliente. Verifica que los datos sean válidos e inténtalo nuevamente.');
        }

        return to_route('clientes.index')->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy(Cliente $cliente): RedirectResponse
    {
        if ($cliente->vehiculos()->exists() || $cliente->ordenes()->exists()) {
            return back()->with('error', 'No se puede eliminar un cliente con vehículos u órdenes asociadas.');
        }
        $cliente->delete();

        return back()->with('success', 'Cliente eliminado.');
    }

    private function validated(Request $request, ?Cliente $cliente = null): array
    {
        $request->merge([
            'nombre' => trim((string) $request->input('nombre')),
            'ci_nit' => mb_strtoupper(trim((string) $request->input('ci_nit'))),
            'telefono' => trim((string) $request->input('telefono')),
            'email' => $request->filled('email') ? mb_strtolower(trim((string) $request->input('email'))) : null,
            'ciudad' => trim((string) $request->input('ciudad')),
            'direccion' => $request->filled('direccion') ? trim((string) $request->input('direccion')) : null,
        ]);

        return $request->validate([
            'nombre' => ['required', 'string', 'max:120'],
            'ci_nit' => ['required', 'string', 'max:30', Rule::unique('clientes')->ignore($cliente)],
            'telefono' => ['required', 'string', 'min:7', 'max:30', 'regex:/^[0-9+()\s-]+$/'],
            'email' => ['nullable', 'email:rfc', 'max:255'],
            'ciudad' => ['required', 'string', 'max:80'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'activo' => ['required', 'boolean'],
        ], [
            'ci_nit.unique' => 'Ya existe un cliente con ese CI o NIT.',
            'telefono.regex' => 'Ingresa un número de teléfono válido.',
            'telefono.min' => 'El teléfono debe tener al menos 7 caracteres.',
            'email.email' => 'Ingresa un correo electrónico válido.',
        ]);
    }
}
