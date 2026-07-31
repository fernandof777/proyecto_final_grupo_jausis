<?php

namespace App\Http\Controllers;

use App\Models\Repuesto;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RepuestoController extends Controller
{
    public function index(Request $request): View
    {
        $repuestos = Repuesto::when($request->buscar, fn ($q, $v) => $q->where(fn ($s) => $s->where('codigo', 'like', "%{$v}%")->orWhere('nombre', 'like', "%{$v}%")))
            ->when($request->alerta === 'stock', fn ($q) => $q->whereColumn('stock', '<=', 'stock_minimo'))
            ->orderBy('nombre')->paginate(12)->withQueryString();

        return view('repuestos.index', compact('repuestos'));
    }

    public function create(): View
    {
        return view('repuestos.form', ['repuesto' => new Repuesto]);
    }

    public function store(Request $request): RedirectResponse
    {
        Repuesto::create($this->validated($request));

        return to_route('repuestos.index')->with('success', 'Repuesto registrado correctamente.');
    }

    public function edit(Repuesto $repuesto): View
    {
        return view('repuestos.form', compact('repuesto'));
    }

    public function update(Request $request, Repuesto $repuesto): RedirectResponse
    {
        $repuesto->update($this->validated($request, $repuesto));

        return to_route('repuestos.index')->with('success', 'Inventario actualizado correctamente.');
    }

    public function destroy(Repuesto $repuesto): RedirectResponse
    {
        try {
            $repuesto->delete();
        } catch (QueryException $exception) {
            report($exception);

            return back()->with('error', 'No se puede eliminar este repuesto porque ya está asociado a una o más órdenes de trabajo. Puedes marcarlo como inactivo en su lugar.');
        }

        return back()->with('success', 'Repuesto eliminado.');
    }

    private function validated(Request $request, ?Repuesto $repuesto = null): array
    {
        return $request->validate([
            'codigo' => ['required', 'string', 'max:40', Rule::unique('repuestos')->ignore($repuesto)],
            'nombre' => ['required', 'string', 'max:120'],
            'proveedor' => ['nullable', 'string', 'max:120'],
            'stock' => ['required', 'integer', 'min:0'],
            'stock_minimo' => ['required', 'integer', 'min:0'],
            'precio' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'activo' => ['required', 'boolean'],
        ]);
    }
}
