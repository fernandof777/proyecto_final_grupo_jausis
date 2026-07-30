@extends('layouts.app')

@section('titulo', 'Iniciar sesión')

@section('contenido')
<div class="min-vh-100 d-flex align-items-center justify-content-center p-3" style="background: radial-gradient(circle at 20% 20%, #1e3a8a 0, #0f172a 40%, #020617 100%);">
    <div class="card border-0 shadow-lg text-white" style="width: 100%; max-width: 440px; border-radius: 20px; background: rgba(15,23,42,.88); backdrop-filter: blur(14px);">
        <div class="card-body p-4 p-md-5">
            <div class="text-center mb-4">
                <div class="brand-mark mx-auto mb-3"><i class="bi bi-wrench-adjustable"></i></div>
                <h2 class="fw-bold">{{ config('app.name', 'Grupo Los Jausis') }}</h2>
                <p class="text-white-50">Gestión inteligente para tu taller</p>
            </div>
            @if ($errors->any())
                <div class="alert alert-danger py-2">{{ $errors->first() }}</div>
            @endif
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label">Correo electrónico</label>
                    <input class="form-control form-control-lg bg-dark text-white border-secondary" id="email" name="email" type="email" value="{{ old('email') }}" required autofocus placeholder="nombre@taller.com">
                </div>
                <div class="mb-4">
                    <label for="password" class="form-label">Contraseña</label>
                    <input class="form-control form-control-lg bg-dark text-white border-secondary" id="password" name="password" type="password" required placeholder="••••••••">
                </div>
                <button class="btn btn-primary btn-lg w-100" type="submit">Iniciar sesión <i class="bi bi-arrow-right ms-2"></i></button>
            </form>
            <div class="border-top border-secondary mt-4 pt-3 text-center text-white-50 small">
                Usuario inicial: luis@taller.com / password123
            </div>
        </div>
    </div>
</div>
@endsection
