<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('titulo', 'Panel') | {{ config('app.name', 'Grupo Los Jausis') }}</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 64 64%22><defs><linearGradient id=%22g%22 x1=%220%22 y1=%220%22 x2=%221%22 y2=%221%22><stop stop-color=%22%23ef4444%22/><stop offset=%221%22 stop-color=%22%23991b1b%22/></linearGradient></defs><rect width=%2264%22 height=%2264%22 rx=%2216%22 fill=%22url(%23g)%22/><text x=%2232%22 y=%2241%22 text-anchor=%22middle%22 font-size=%2228%22 font-family=%22Arial%22 font-weight=%22700%22 fill=%22white%22>GJ</text></svg>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --wine-950: #240507;
            --wine-900: #450a0a;
            --wine-800: #6f1016;
            --red-700: #b91c1c;
            --red-600: #dc2626;
            --red-500: #ef4444;
            --red-100: #fee2e2;
            --red-50: #fff1f2;
            --ink: #291518;
            --muted: #7f676a;
            --page: #fdf7f7;
            --surface: rgba(255, 255, 255, .94);
            --blue: var(--red-600);
            --bs-primary: #dc2626;
            --bs-primary-rgb: 220, 38, 38;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            background:
                radial-gradient(circle at 82% 3%, rgba(239, 68, 68, .08), transparent 24rem),
                linear-gradient(180deg, #fffafa 0, var(--page) 36rem);
            color: var(--ink);
            font-family: Inter, sans-serif;
        }
        ::selection { background: #fecaca; color: var(--wine-950); }
        .app-shell { min-height: 100vh; display: flex; }
        .sidebar {
            width: 272px;
            background:
                radial-gradient(circle at 15% 0, rgba(239, 68, 68, .24), transparent 18rem),
                linear-gradient(165deg, var(--wine-800) 0, var(--wine-950) 64%);
            border-right: 1px solid rgba(248, 113, 113, .18);
            box-shadow: 16px 0 40px rgba(69, 10, 10, .12);
            color: #f8d8dc;
            padding: 24px 16px;
            position: fixed;
            inset: 0 auto 0 0;
            z-index: 20;
        }
        .brand {
            color: white;
            font-size: 1.18rem;
            font-weight: 800;
            letter-spacing: -.025em;
            text-decoration: none;
            display: flex;
            gap: 12px;
            align-items: center;
            padding: 0 10px 25px;
        }
        .brand-mark {
            width: 42px;
            height: 42px;
            flex: 0 0 42px;
            display: grid;
            place-items: center;
            border: 1px solid rgba(255, 255, 255, .2);
            border-radius: 13px;
            background: linear-gradient(135deg, #fb7185, var(--red-700));
            box-shadow: 0 8px 24px rgba(220, 38, 38, .36), inset 0 1px rgba(255, 255, 255, .35);
        }
        .nav-label { color: #d7959e; font-size: .66rem; font-weight: 800; letter-spacing: .15em; padding: 18px 13px 7px; }
        .side-link {
            color: #e9bfc5;
            text-decoration: none;
            display: flex;
            gap: 11px;
            align-items: center;
            padding: 10px 11px;
            border: 1px solid transparent;
            border-radius: 12px;
            margin: 4px 0;
            font-size: .92rem;
            font-weight: 600;
            transition: background .2s ease, color .2s ease, transform .2s ease, border-color .2s ease;
        }
        .side-link i {
            width: 32px;
            height: 32px;
            display: grid;
            flex: 0 0 32px;
            place-items: center;
            border-radius: 9px;
            background: rgba(255, 255, 255, .06);
            transition: background .2s ease, transform .2s ease;
        }
        .side-link:hover {
            color: white;
            background: rgba(255, 255, 255, .09);
            border-color: rgba(255, 255, 255, .08);
            transform: translateX(2px);
        }
        .side-link.active {
            color: white;
            background: linear-gradient(90deg, rgba(239, 68, 68, .42), rgba(255, 255, 255, .08));
            border-color: rgba(248, 113, 113, .26);
            box-shadow: 0 10px 24px rgba(24, 2, 5, .18);
        }
        .side-link.active i { background: var(--red-600); box-shadow: 0 5px 12px rgba(0, 0, 0, .2); }
        .main { margin-left: 272px; width: calc(100% - 272px); min-height: 100vh; }
        .topbar {
            height: 76px;
            background: rgba(255, 255, 255, .88);
            border-bottom: 1px solid rgba(185, 28, 28, .1);
            box-shadow: 0 5px 24px rgba(69, 10, 10, .035);
            backdrop-filter: blur(14px);
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 34px;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        .topbar .fw-bold { color: var(--wine-900); letter-spacing: -.015em; }
        .text-secondary { color: var(--muted) !important; }
        .page-content { padding: 32px 34px 44px; max-width: 1500px; margin: auto; }
        .page-content h2, .page-content h3, .page-content h5 { color: var(--wine-900); letter-spacing: -.025em; }
        .avatar {
            width: 40px;
            height: 40px;
            border: 1px solid #fecaca;
            border-radius: 12px;
            background: linear-gradient(135deg, #fee2e2, #fecaca);
            color: var(--red-700);
            display: grid;
            place-items: center;
            font-weight: 800;
            box-shadow: 0 5px 14px rgba(185, 28, 28, .1);
        }
        .app-card {
            border: 1px solid rgba(185, 28, 28, .09);
            border-radius: 17px;
            background: var(--surface);
            box-shadow: 0 10px 32px rgba(69, 10, 10, .065);
            overflow: hidden;
            transition: box-shadow .2s ease, border-color .2s ease, transform .2s ease;
        }
        .app-card:hover { border-color: rgba(185, 28, 28, .15); box-shadow: 0 14px 38px rgba(69, 10, 10, .09); }
        .btn {
            border-radius: 10px;
            font-weight: 700;
            transition: transform .18s ease, box-shadow .18s ease, background .18s ease;
        }
        .btn:hover { transform: translateY(-1px); }
        .btn-primary {
            background: linear-gradient(135deg, var(--red-500), var(--red-700));
            border-color: var(--red-700);
            box-shadow: 0 7px 16px rgba(220, 38, 38, .2);
        }
        .btn-primary:hover, .btn-primary:focus {
            background: linear-gradient(135deg, var(--red-600), #991b1b);
            border-color: #991b1b;
            box-shadow: 0 9px 20px rgba(185, 28, 28, .28);
        }
        .btn-outline-primary { color: var(--red-700); border-color: #fca5a5; }
        .btn-outline-primary:hover { background: var(--red-700); border-color: var(--red-700); }
        .btn-danger { background: linear-gradient(135deg, #ef4444, #991b1b); border-color: #991b1b; box-shadow: 0 7px 16px rgba(153, 27, 27, .18); }
        .btn-light { background: #fff7f7; border-color: #f2d9dc; color: var(--wine-800); }
        .form-control, .form-select {
            min-height: 43px;
            border-color: #ead7d9;
            border-radius: 10px;
            background-color: #fffdfd;
        }
        .form-control:focus, .form-select:focus {
            border-color: #f87171;
            box-shadow: 0 0 0 .22rem rgba(220, 38, 38, .12);
        }
        .form-label { color: #5f363b; font-size: .88rem; font-weight: 650; }
        .table > :not(caption) > * > * { padding: .9rem 1rem; vertical-align: middle; }
        .table { --bs-table-hover-bg: #fff1f2; color: var(--ink); }
        .table-light {
            --bs-table-bg: #fff1f2;
            --bs-table-color: #68151d;
            border-color: #f6d9dc;
        }
        .table thead th { font-size: .76rem; font-weight: 800; letter-spacing: .035em; text-transform: uppercase; }
        .table tbody td { border-color: #f2e5e6; }
        .table-responsive { border-radius: inherit; }
        .pagination { --bs-pagination-color: var(--red-700); --bs-pagination-active-bg: var(--red-700); --bs-pagination-active-border-color: var(--red-700); }
        .alert { border: 0; border-left: 4px solid currentColor; border-radius: 12px; box-shadow: 0 8px 20px rgba(69, 10, 10, .05); }
        .badge.bg-primary { background: var(--red-600) !important; }
        .text-primary { color: var(--red-700) !important; }
        .card-footer { border-color: #f2e5e6; }
        .login-page {
            position: relative;
            min-height: 100vh;
            overflow: hidden;
            background:
                radial-gradient(circle at 18% 18%, rgba(248, 113, 113, .34), transparent 22rem),
                radial-gradient(circle at 86% 82%, rgba(153, 27, 27, .4), transparent 25rem),
                linear-gradient(145deg, #2a0508 0, #570b12 50%, #170205 100%);
        }
        .login-page::before {
            position: absolute;
            inset: 0;
            background-image: linear-gradient(rgba(255,255,255,.025) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.025) 1px, transparent 1px);
            background-size: 34px 34px;
            content: "";
            mask-image: linear-gradient(to bottom, black, transparent);
        }
        .login-card {
            width: 100%;
            max-width: 450px;
            border: 1px solid rgba(255, 255, 255, .14) !important;
            border-radius: 24px;
            background: rgba(52, 7, 12, .82);
            box-shadow: 0 32px 80px rgba(10, 0, 2, .42) !important;
            backdrop-filter: blur(18px);
        }
        .login-card .brand-mark { width: 52px; height: 52px; margin-inline: auto; border-radius: 16px; }
        .login-card .form-label { color: #ffe4e6; }
        .login-card .form-control {
            color: white;
            border-color: rgba(255,255,255,.16);
            background: rgba(255,255,255,.075);
        }
        .login-card .form-control::placeholder { color: #d8a9af; }
        .login-card .form-control:focus { border-color: #fb7185; background: rgba(255,255,255,.1); box-shadow: 0 0 0 .22rem rgba(251, 113, 133, .16); }
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
                overflow-x: auto; overflow-y: hidden; box-shadow: 0 -8px 24px rgba(69,10,10,.28);
            }
            .sidebar .brand, .sidebar .nav-label { display: none; }
            .side-link {
                min-width: 62px; height: 54px; margin: 0; padding: 7px 5px;
                flex: 1 0 62px; flex-direction: column; justify-content: center;
                gap: 2px; font-size: 1.05rem; border-radius: 9px;
            }
            .side-link span { display: block; font-size: .62rem; white-space: nowrap; }
            .side-link i { width: 26px; height: 26px; flex-basis: 26px; background: transparent; }
            .side-link:hover { transform: none; }
            .side-link.active { background: rgba(239,68,68,.32); box-shadow: inset 0 -3px var(--red-500); }
            .side-link.active i { background: transparent; box-shadow: none; }
            .main { margin-left: 0; width: 100%; min-height: calc(100vh - 68px); }
            .topbar { height: 64px; padding: 0 14px; }
            .topbar small { display: none; }
            .topbar .avatar { width: 34px; height: 34px; }
            .page-content { padding: 18px 14px; }
            .page-content > .d-flex { align-items: stretch !important; }
            .page-content h2 { font-size: 1.45rem; }
            .app-card { border-radius: 13px; }
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
        <a class="side-link {{ request()->routeIs('reservas.*') ? 'active' : '' }}" href="{{ route('reservas.index') }}"><i class="bi bi-calendar2-check"></i><span>Reservas</span></a>
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
        <a class="side-link" href="{{ route('inicio') }}" target="_blank"><i class="bi bi-globe2"></i><span>Sitio público</span></a>
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
