@extends('layouts.app')

@section('titulo', 'Acceso para trabajadores')

@section('contenido')
<div class="login-page d-flex align-items-center justify-content-center p-3">
    <div class="login-card card text-white">
        <div class="card-body p-4 p-md-5">
            <div class="text-center mb-4">
                <div class="brand-mark mx-auto mb-3"><i class="bi bi-wrench-adjustable"></i></div>
                <h2 class="fw-bold">{{ config('app.name', 'Grupo Los Jausis') }}</h2>
                <p class="text-white-50 mb-1">Acceso para trabajadores</p>
                <small class="text-white-50">Panel interno de gestión del taller</small>
            </div>
            @if ($errors->any())
                <div class="alert alert-danger py-2">{{ $errors->first() }}</div>
            @endif
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label">Correo electrónico</label>
                    <input class="form-control form-control-lg" id="email" name="email" type="email" value="{{ old('email') }}" required autofocus placeholder="nombre@taller.com">
                </div>
                <div class="mb-4">
                    <label for="password" class="form-label">Contraseña</label>
                    <input class="form-control form-control-lg" id="password" name="password" type="password" required placeholder="••••••••">
                </div>
                <button class="btn btn-primary btn-lg w-100" type="submit">Iniciar sesión <i class="bi bi-arrow-right ms-2"></i></button>
            </form>
            <div class="text-center mt-4">
                <a class="text-white-50 small" href="{{ route('inicio') }}"><i class="bi bi-arrow-left me-1"></i>Volver al sitio del taller</a>
            </div>
            @env('local')
                <div class="border-top border-secondary mt-4 pt-3 text-center text-white-50 small">
                    Usuario inicial: luis@taller.com / password123
                </div>
            @endenv
        </div>
    </div>
</div>
@endsection
