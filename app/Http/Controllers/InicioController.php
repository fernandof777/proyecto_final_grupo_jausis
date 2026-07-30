<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use Illuminate\View\View;

class InicioController extends Controller
{
    public function index(): View
    {
        $servicios = Servicio::query()
            ->where('estado', 'Activo')
            ->orderBy('nombre')
            ->limit(8)
            ->get();

        return view('inicio', compact('servicios'));
    }
}
