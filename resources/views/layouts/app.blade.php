<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('titulo', 'Panel') | {{ config('app.name', 'Grupo Los Jausis') }}</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 64 64%22><rect width=%2264%22 height=%2264%22 rx=%2214%22 fill=%22%232563eb%22/><text x=%2232%22 y=%2241%22 text-anchor=%22middle%22 font-size=%2228%22 font-family=%22Arial%22 font-weight=%22700%22 fill=%22white%22>GJ</text></svg>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root { --navy: #0f172a; --navy-soft: #1e293b; --blue: #2563eb; --page: #f1f5f9; }
        * { box-sizing: border-box; }
        body { margin: 0; background: var(--page); color: #0f172a; font-family: Inter, sans-serif; }
        .app-shell { min-height: 100vh; display: flex; }
        .sidebar { width: 260px; background: var(--navy); color: #cbd5e1; padding: 24px 16px; position: fixed; inset: 0 auto 0 0; }
        .brand { color: white; font-size: 1.25rem; font-weight: 800; text-decoration: none; display: flex; gap: 10px; align-items: center; padding: 0 12px 24px; }
        .brand-mark { width: 38px; height: 38px; display: grid; place-items: center; border-radius: 10px; background: linear-gradient(135deg, #2563eb, #06b6d4); }
        .nav-label { color: #64748b; font-size: .7rem; font-weight: 700; letter-spacing: .12em; padding: 16px 12px 8px; }
        .side-link { color: #94a3b8; text-decoration: none; display: flex; gap: 12px; align-items: center; padding: 11px 12px; border-radius: 9px; margin: 3px 0; font-weight: 500; }
        .side-link:hover, .side-link.active { color: white; background: var(--navy-soft); }
        .side-link.active { box-shadow: inset 3px 0 var(--blue); }
        .main { margin-left: 260px; width: calc(100% - 260px); min-height: 100vh; }
        .topbar { height: 72px; background: white; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; padding: 0 32px; position: sticky; top: 0; z-index: 10; }
        .page-content { padding: 30px 32px; max-width: 1500px; margin: auto; }
        .avatar { width: 38px; height: 38px; border-radius: 50%; background: #dbeafe; color: #1d4ed8; display: grid; place-items: center; font-weight: 700; }
        .app-card { border: 0; border-radius: 14px; box-shadow: 0 2px 12px rgba(15,23,42,.06); }
        .btn-primary { background: var(--blue); border-color: var(--blue); }
        .table > :not(caption) > * > * { padding: .9rem 1rem; vertical-align: middle; }
        .table-responsive { border-radius: inherit; }
        img, canvas, iframe { max-width: 100%; }
        @media (max-width: 1050px) {
            .sidebar { width: 76px; padding-inline: 10px; }
            .brand span:last-child, .side-link span, .nav-label { display: none; }
            .brand { padding-inline: 8px; }
            .side-link { justify-content: center; font-size: 1.2rem; }
            .main { margin-left: 76px; width: calc(100% - 76px); }
            .topbar, .page-content { padding-inline: 18px; }
            .user-name { display: none; }
        }
        @media (max-width: 600px) {
            body { padding-bottom: 72px; }
            .app-shell { display: block; }
            .sidebar {
                width: 100%; height: 68px; padding: 6px 8px; inset: auto 0 0 0;
                z-index: 1050; display: flex; align-items: center; gap: 2px;
                overflow-x: auto; overflow-y: hidden; box-shadow: 0 -4px 16px rgba(15,23,42,.2);
            }
            .sidebar .brand, .sidebar .nav-label { display: none; }
            .side-link {
                min-width: 62px; height: 54px; margin: 0; padding: 7px 5px;
                flex: 1 0 62px; flex-direction: column; justify-content: center;
                gap: 2px; font-size: 1.05rem; border-radius: 9px;
            }
            .side-link span { display: block; font-size: .62rem; white-space: nowrap; }
            .side-link.active { box-shadow: inset 0 -3px var(--blue); }
            .main { margin-left: 0; width: 100%; min-height: calc(100vh - 68px); }
            .topbar { height: 64px; padding: 0 14px; }
            .topbar small { display: none; }
            .topbar .avatar { width: 34px; height: 34px; }
            .page-content { padding: 18px 14px; }
            .page-content > .d-flex { align-items: stretch !important; }
            .page-content h2 { font-size: 1.45rem; }
            .app-card { border-radius: 11px; }
            .card-body { padding: 1rem; }
            .table-responsive .table { min-width: 720px; }
            .alert { font-size: .9rem; }
        }
    </style>
    @stack('estilos')
</head>
<body>
@auth
<div class="app-shell">
    <aside class="sidebar">
        <a class="brand" href="{{ route('dashboard') }}">
            <span class="brand-mark"><i class="bi bi-wrench-adjustable"></i></span>
            <span>{{ config('app.name', 'Grupo Los Jausis') }}</span>
        </a>
        <div class="nav-label">PRINCIPAL</div>
        <a class="side-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
            <i class="bi bi-grid-1x2-fill"></i><span>Dashboard</span>
        </a>
        <a class="side-link {{ request()->routeIs('servicios.*') ? 'active' : '' }}" href="{{ route('servicios.index') }}">
            <i class="bi bi-tools"></i><span>Servicios</span>
        </a>
        <div class="nav-label">GESTIÓN</div>
        @if(Auth::user()->hasRole('admin','recepcionista'))
        <a class="side-link {{ request()->routeIs('clientes.*') ? 'active' : '' }}" href="{{ route('clientes.index') }}"><i class="bi bi-people"></i><span>Clientes</span></a>
        <a class="side-link {{ request()->routeIs('vehiculos.*') ? 'active' : '' }}" href="{{ route('vehiculos.index') }}"><i class="bi bi-car-front"></i><span>Vehículos</span></a>
        <a class="side-link {{ request()->routeIs('ordenes.*') ? 'active' : '' }}" href="{{ route('ordenes.index') }}"><i class="bi bi-clipboard2-check"></i><span>Órdenes</span></a>
        @endif
        @if(Auth::user()->hasRole('admin','almacen'))
        <a class="side-link {{ request()->routeIs('repuestos.*') ? 'active' : '' }}" href="{{ route('repuestos.index') }}"><i class="bi bi-box-seam"></i><span>Repuestos</span></a>
        @endif
        @if(Auth::user()->hasRole('admin'))
        <a class="side-link {{ request()->routeIs('reportes.*') ? 'active' : '' }}" href="{{ route('reportes.index') }}"><i class="bi bi-bar-chart"></i><span>Reportes</span></a>
        <a class="side-link {{ request()->routeIs('auditoria.*') ? 'active' : '' }}" href="{{ route('auditoria.index') }}"><i class="bi bi-shield-check"></i><span>Auditoría</span></a>
        @endif
        <a class="side-link {{ request()->routeIs('ubicacion.*') ? 'active' : '' }}" href="{{ route('ubicacion.index') }}"><i class="bi bi-geo-alt"></i><span>Ubicación</span></a>
    </aside>
    <main class="main">
        <header class="topbar">
            <div>
                <div class="fw-bold">@yield('encabezado', 'Panel de control')</div>
                <small class="text-secondary">@yield('subtitulo', 'Gestión del taller automotriz')</small>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="avatar">{{ mb_strtoupper(mb_substr(Auth::user()->name, 0, 1)) }}</div>
                <span class="user-name"><span class="fw-semibold d-block">{{ Auth::user()->name }}</span><small class="text-secondary text-capitalize">{{ Auth::user()->role }}</small></span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="btn btn-light btn-sm" type="submit" title="Cerrar sesión">
                        <i class="bi bi-box-arrow-right"></i>
                    </button>
                </form>
            </div>
        </header>
        <div class="page-content">
            @if (session('success'))<div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-1"></i>{{ session('success') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>@endif
            @if (session('error'))<div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-exclamation-triangle me-1"></i>{{ session('error') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>@endif
            @yield('contenido')
        </div>
    </main>
</div>
@else
    @yield('contenido')
@endauth
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
<script nonce="{{ $cspNonce }}">
document.addEventListener('submit', (event) => {
    const message = event.target.dataset.confirm;
    if (message && !window.confirm(message)) event.preventDefault();
});
document.getElementById('print-page')?.addEventListener('click', () => window.print());
</script>
</body>
</html>
