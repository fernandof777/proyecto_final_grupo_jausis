<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Grupo Los Jausis: servicios de mantenimiento y reparación automotriz en Santa Cruz. Reserva tu cita en línea.">
    <title>@yield('titulo', 'Taller automotriz') | {{ config('app.name', 'Grupo Los Jausis') }}</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 64 64%22><rect width=%2264%22 height=%2264%22 rx=%2216%22 fill=%22%23b91c1c%22/><text x=%2232%22 y=%2241%22 text-anchor=%22middle%22 font-size=%2228%22 font-family=%22Arial%22 font-weight=%22700%22 fill=%22white%22>GJ</text></svg>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --wine: #550b12;
            --wine-dark: #260306;
            --red: #dc2626;
            --red-dark: #a41118;
            --cream: #fff8f7;
            --ink: #281518;
            --muted: #755f62;
        }
        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; scroll-padding-top: 86px; }
        body { margin: 0; color: var(--ink); background: var(--cream); font-family: Inter, sans-serif; overflow-x: hidden; }
        a { text-decoration: none; }
        img, iframe { max-width: 100%; }
        .public-nav {
            min-height: 76px; background: rgba(255,255,255,.94); border-bottom: 1px solid rgba(85,11,18,.09);
            box-shadow: 0 8px 30px rgba(52,5,10,.06); backdrop-filter: blur(16px);
        }
        .logo-mark {
            width: 45px; height: 45px; display: grid; place-items: center; border-radius: 14px; color: white;
            background: linear-gradient(135deg, #f04444, #8f1017); box-shadow: 0 10px 24px rgba(185,28,28,.25);
        }
        .navbar-brand { color: var(--wine) !important; font-weight: 900; letter-spacing: -.04em; }
        .nav-link { color: #5d4246; font-weight: 650; font-size: .92rem; }
        .nav-link:hover { color: var(--red-dark); }
        .btn-red {
            color: white; border: 0; border-radius: 12px; padding: .72rem 1.15rem; font-weight: 750;
            background: linear-gradient(135deg, #ef4444, #a51118); box-shadow: 0 10px 23px rgba(185,28,28,.22);
        }
        .btn-red:hover { color: white; transform: translateY(-1px); background: linear-gradient(135deg, #dc2626, #7f1015); }
        .btn-soft { border: 1px solid #efc9cd; border-radius: 12px; color: var(--wine); background: white; font-weight: 700; }
        .btn-soft:hover { color: white; border-color: var(--wine); background: var(--wine); }
        .section-pad { padding: 92px 0; }
        .section-kicker { color: var(--red-dark); font-size: .73rem; font-weight: 850; letter-spacing: .16em; text-transform: uppercase; }
        .section-title { color: var(--wine-dark); font-size: clamp(2rem, 4vw, 3.1rem); line-height: 1.08; font-weight: 900; letter-spacing: -.055em; }
        .section-copy { color: var(--muted); line-height: 1.75; }
        .public-card {
            height: 100%; border: 1px solid rgba(112,19,28,.1); border-radius: 22px; background: white;
            box-shadow: 0 15px 44px rgba(85,11,18,.07);
        }
        .field-label { color: #5c3439; font-size: .84rem; font-weight: 700; }
        .form-control, .form-select {
            min-height: 48px; border: 1px solid #ead7d9; border-radius: 12px; background: #fffdfd;
        }
        .form-control:focus, .form-select:focus { border-color: #ef6a72; box-shadow: 0 0 0 .22rem rgba(220,38,38,.11); }
        .form-control.is-invalid, .form-select.is-invalid { border-color: #dc3545; }
        footer { color: #dab8bc; background: var(--wine-dark); }
        footer a { color: white; }
        @media (max-width: 991.98px) {
            .navbar-collapse { padding: 15px 0 5px; border-top: 1px solid #f3e3e5; margin-top: 12px; }
            .section-pad { padding: 68px 0; }
        }
        @media (max-width: 575.98px) {
            .section-pad { padding: 54px 0; }
            .section-title { font-size: 2rem; }
            .public-card { border-radius: 17px; }
        }
        @media (prefers-reduced-motion: reduce) { html { scroll-behavior: auto; } * { transition: none !important; } }
    </style>
    @stack('estilos')
</head>
<body>
    <nav class="navbar navbar-expand-lg public-nav sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('inicio') }}">
                <span class="logo-mark"><i class="bi bi-wrench-adjustable"></i></span>
                <span>Grupo Los Jausis</span>
            </a>
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#publicNav" aria-controls="publicNav" aria-expanded="false" aria-label="Abrir menú">
                <i class="bi bi-list fs-2"></i>
            </button>
            <div class="collapse navbar-collapse" id="publicNav">
                <div class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                    <a class="nav-link" href="{{ route('inicio') }}#inicio">Inicio</a>
                    <a class="nav-link" href="{{ route('inicio') }}#servicios">Servicios</a>
                    <a class="nav-link" href="{{ route('inicio') }}#nosotros">Nosotros</a>
                    <a class="nav-link" href="{{ route('inicio') }}#ubicacion">Ubicación</a>
                    <a class="nav-link" href="{{ route('inicio') }}#reservar">Reservar</a>
                    <a class="btn btn-soft ms-lg-2 px-3 py-2" href="{{ auth()->check() ? route('dashboard') : route('login') }}">
                        <i class="bi bi-person-badge me-1"></i>{{ auth()->check() ? 'Ir al panel' : 'Acceso trabajadores' }}
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main>@yield('contenido')</main>

    <footer class="py-5">
        <div class="container">
            <div class="row gy-4 align-items-center">
                <div class="col-lg-6">
                    <div class="d-flex align-items-center gap-2 text-white fw-bold fs-5 mb-2">
                        <span class="logo-mark"><i class="bi bi-wrench-adjustable"></i></span>Grupo Los Jausis
                    </div>
                    <div class="small">Cuidamos tu vehículo con atención profesional y transparente.</div>
                </div>
                <div class="col-lg-6 text-lg-end small">
                    <a class="me-3" href="#servicios">Servicios</a>
                    <a class="me-3" href="#ubicacion">Ubicación</a>
                    <a href="{{ route('login') }}">Trabajadores</a>
                    <div class="mt-2">© {{ date('Y') }} Grupo Los Jausis</div>
                </div>
            </div>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
