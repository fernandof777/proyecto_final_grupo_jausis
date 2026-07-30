@extends('layouts.publico')

@section('titulo', 'Taller automotriz y reservas')

@push('estilos')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
    .hero {
        position: relative; min-height: 690px; display: flex; align-items: center; overflow: hidden; color: white;
        background:
            radial-gradient(circle at 77% 24%, rgba(239,68,68,.38), transparent 23rem),
            linear-gradient(118deg, rgba(36,3,6,.98) 0%, rgba(86,9,16,.95) 53%, rgba(151,18,27,.88) 100%);
    }
    .hero::before {
        position: absolute; inset: 0; opacity: .17; content: "";
        background-image: linear-gradient(rgba(255,255,255,.1) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.1) 1px, transparent 1px);
        background-size: 48px 48px; transform: perspective(500px) rotateX(62deg) scale(1.5); transform-origin: bottom;
    }
    .hero .container { position: relative; z-index: 1; }
    .hero h1 { max-width: 760px; font-size: clamp(2.65rem, 7vw, 5.2rem); line-height: .98; font-weight: 900; letter-spacing: -.075em; }
    .hero-copy { max-width: 650px; color: #f2cdd1; font-size: clamp(1rem, 2vw, 1.18rem); line-height: 1.7; }
    .hero-badge { display: inline-flex; gap: 8px; align-items: center; padding: .48rem .75rem; border: 1px solid rgba(255,255,255,.18); border-radius: 99px; color: #ffe4e6; background: rgba(255,255,255,.08); font-size: .78rem; font-weight: 750; }
    .hero-panel { border: 1px solid rgba(255,255,255,.15); border-radius: 28px; background: rgba(255,255,255,.09); box-shadow: 0 28px 70px rgba(15,0,2,.35); backdrop-filter: blur(14px); }
    .hero-wheel { width: 104px; height: 104px; display: grid; place-items: center; border: 8px solid rgba(255,255,255,.15); border-radius: 50%; background: #f04444; font-size: 2.6rem; box-shadow: 0 0 0 13px rgba(255,255,255,.05); }
    .trust-strip { position: relative; z-index: 2; margin-top: -44px; }
    .trust-item { padding: 1.1rem; border-right: 1px solid #f1dde0; }
    .trust-item:last-child { border-right: 0; }
    .service-icon { width: 54px; height: 54px; display: grid; place-items: center; color: #b2141c; border-radius: 16px; background: #fff0f1; font-size: 1.35rem; }
    .service-price { color: #a51118; font-weight: 850; }
    .about-panel { min-height: 480px; position: relative; overflow: hidden; border-radius: 30px; color: white; background: linear-gradient(145deg,#2e0508,#84121b); box-shadow: 0 28px 70px rgba(85,11,18,.19); }
    .about-panel::after { position: absolute; width: 300px; height: 300px; right: -80px; bottom: -90px; content: ""; border: 55px solid rgba(255,255,255,.06); border-radius: 50%; }
    .step-number { width: 42px; height: 42px; flex: 0 0 42px; display: grid; place-items: center; color: white; border-radius: 13px; background: linear-gradient(135deg,#ef4444,#9d1219); font-weight: 850; }
    .booking-section { background: linear-gradient(180deg,#fff7f7,#f9eeee); }
    .booking-aside { color: white; background: linear-gradient(160deg,#77101a,#290407); }
    .booking-list i { color: #fda4af; }
    #public-map { height: 390px; border-radius: 22px; z-index: 1; }
    .location-chip { display: inline-flex; align-items: center; gap: 7px; padding: .5rem .75rem; border-radius: 10px; color: #77101a; background: #fff0f1; font-size: .82rem; font-weight: 700; }
    @media(max-width: 991.98px) {
        .hero { min-height: auto; padding: 94px 0 105px; }
        .hero-panel { margin-top: 40px; }
        .trust-item:nth-child(2) { border-right: 0; }
        .trust-item:nth-child(-n+2) { border-bottom: 1px solid #f1dde0; }
    }
    @media(max-width: 575.98px) {
        .hero { padding: 74px 0 91px; }
        .hero h1 { font-size: 2.7rem; }
        .trust-item { border-right: 0 !important; border-bottom: 1px solid #f1dde0; }
        .trust-item:last-child { border-bottom: 0; }
        #public-map { height: 300px; }
    }
</style>
@endpush

@section('contenido')
<section class="hero" id="inicio">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <span class="hero-badge mb-4"><i class="bi bi-shield-check"></i>Atención responsable y transparente</span>
                <h1>Tu auto en manos expertas.</h1>
                <p class="hero-copy mt-4 mb-4">En Grupo Los Jausis diagnosticamos, mantenemos y reparamos tu vehículo con atención cercana. Agenda tu visita en pocos pasos y llega con tu cita programada.</p>
                <div class="d-flex flex-wrap gap-3">
                    <a class="btn btn-red btn-lg px-4" href="#reservar"><i class="bi bi-calendar2-check me-2"></i>Reservar una cita</a>
                    <a class="btn btn-outline-light btn-lg rounded-3 px-4 fw-bold" href="#servicios">Conocer servicios</a>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="hero-panel p-4 p-xl-5">
                    <div class="hero-wheel mb-4"><i class="bi bi-wrench-adjustable"></i></div>
                    <div class="text-uppercase small fw-bold text-white-50 mb-2">Taller automotriz</div>
                    <h2 class="fw-bold">Grupo Los Jausis</h2>
                    <p class="text-white-50 mb-4">Mantenimiento, diagnóstico y reparación para que vuelvas a la ruta con confianza.</p>
                    <div class="d-flex align-items-center gap-3"><i class="bi bi-geo-alt-fill fs-4 text-danger"></i><span class="small">Av. Beni, Santa Cruz de la Sierra</span></div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="trust-strip">
    <div class="container">
        <div class="public-card">
            <div class="row g-0 text-center">
                <div class="col-sm-6 col-lg-3 trust-item"><i class="bi bi-calendar2-check text-danger fs-3"></i><div class="fw-bold mt-1">Reserva sencilla</div><small class="text-secondary">Agenda desde cualquier dispositivo</small></div>
                <div class="col-sm-6 col-lg-3 trust-item"><i class="bi bi-search text-danger fs-3"></i><div class="fw-bold mt-1">Diagnóstico claro</div><small class="text-secondary">Información antes de intervenir</small></div>
                <div class="col-sm-6 col-lg-3 trust-item"><i class="bi bi-tools text-danger fs-3"></i><div class="fw-bold mt-1">Servicio profesional</div><small class="text-secondary">Trabajo organizado y responsable</small></div>
                <div class="col-sm-6 col-lg-3 trust-item"><i class="bi bi-geo-alt text-danger fs-3"></i><div class="fw-bold mt-1">Fácil de encontrar</div><small class="text-secondary">Ubicación y ruta disponibles</small></div>
            </div>
        </div>
    </div>
</div>

<section class="section-pad" id="servicios">
    <div class="container">
        <div class="row align-items-end mb-5">
            <div class="col-lg-7">
                <div class="section-kicker mb-2">Lo que hacemos</div>
                <h2 class="section-title mb-3">Servicios para cuidar cada recorrido</h2>
                <p class="section-copy mb-0">Selecciona el servicio que necesita tu vehículo y envíanos tu solicitud de cita. Nuestro equipo revisará la disponibilidad.</p>
            </div>
        </div>
        <div class="row g-4">
            @forelse($servicios as $servicio)
                <div class="col-md-6 col-xl-4">
                    <article class="public-card p-4">
                        <div class="service-icon mb-4"><i class="bi bi-gear-wide-connected"></i></div>
                        <h3 class="h5 fw-bold">{{ $servicio->nombre }}</h3>
                        <p class="section-copy small">{{ $servicio->descripcion ?: 'Atención profesional adaptada a las necesidades de tu vehículo.' }}</p>
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <span class="service-price">Bs {{ number_format((float) $servicio->precio, 2) }}</span>
                            <a class="fw-bold text-danger small" href="#reservar">Reservar <i class="bi bi-arrow-right"></i></a>
                        </div>
                    </article>
                </div>
            @empty
                @foreach([['Diagnóstico automotriz','Revisión del vehículo para identificar fallas y orientar la reparación.','bi-search'],['Mantenimiento preventivo','Cambio de aceite, filtros y controles esenciales para evitar averías.','bi-droplet-half'],['Reparación mecánica','Atención de sistemas mecánicos con seguimiento organizado del trabajo.','bi-wrench']] as [$nombre,$descripcion,$icono])
                    <div class="col-md-6 col-xl-4">
                        <article class="public-card p-4">
                            <div class="service-icon mb-4"><i class="bi {{ $icono }}"></i></div>
                            <h3 class="h5 fw-bold">{{ $nombre }}</h3>
                            <p class="section-copy small">{{ $descripcion }}</p>
                            <a class="fw-bold text-danger small" href="#reservar">Consultar y reservar <i class="bi bi-arrow-right"></i></a>
                        </article>
                    </div>
                @endforeach
            @endforelse
        </div>
    </div>
</section>

<section class="section-pad pt-0" id="nosotros">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6">
                <div class="about-panel p-4 p-md-5 d-flex flex-column justify-content-end">
                    <div class="position-relative" style="z-index:1">
                        <div class="hero-wheel mb-4"><i class="bi bi-car-front-fill"></i></div>
                        <div class="section-kicker text-danger-subtle mb-2">Nuestro compromiso</div>
                        <h2 class="display-6 fw-bold">Que entiendas qué necesita tu vehículo.</h2>
                        <p class="text-white-50 mb-0">Una experiencia directa, ordenada y cercana desde la reserva hasta la entrega.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="section-kicker mb-2">Una visita más simple</div>
                <h2 class="section-title mb-4">Así funciona tu cita</h2>
                @foreach([['Elige el servicio','Cuéntanos qué atención necesita tu vehículo.'],['Selecciona fecha y hora','Indica el momento que prefieres para visitarnos.'],['Espera la confirmación','El personal del taller revisará y confirmará tu solicitud.']] as $i => [$titulo,$texto])
                    <div class="d-flex gap-3 mb-4">
                        <div class="step-number">{{ $i + 1 }}</div>
                        <div><h3 class="h6 fw-bold mb-1">{{ $titulo }}</h3><p class="section-copy small mb-0">{{ $texto }}</p></div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<section class="section-pad booking-section" id="reservar">
    <div class="container">
        <div class="text-center mx-auto mb-5" style="max-width:720px">
            <div class="section-kicker mb-2">Agenda tu visita</div>
            <h2 class="section-title mb-3">Reserva una cita para tu vehículo</h2>
            <p class="section-copy">Completa tus datos y el taller recibirá tu solicitud. La cita quedará pendiente hasta que nuestro equipo la confirme.</p>
        </div>
        @if(session('reserva_exitosa'))
            <div class="alert alert-success border-0 shadow-sm rounded-4 p-4 mb-4" role="alert">
                <div class="d-flex gap-3">
                    <i class="bi bi-check-circle-fill fs-2"></i>
                    <div><strong>¡Solicitud registrada correctamente!</strong><div>Tu código es <strong>{{ session('reserva_exitosa.codigo') }}</strong>. Fecha solicitada: {{ session('reserva_exitosa.fecha') }} a las {{ session('reserva_exitosa.hora') }}.</div><small>Guarda este código como referencia. El taller revisará la disponibilidad.</small></div>
                </div>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4"><strong>Revisa los datos del formulario:</strong> {{ $errors->first() }}</div>
        @endif
        <div class="public-card overflow-hidden">
            <div class="row g-0">
                <div class="col-lg-4 booking-aside p-4 p-md-5">
                    <i class="bi bi-calendar2-week display-4 text-danger"></i>
                    <h3 class="h3 fw-bold mt-4">Tu solicitud en minutos</h3>
                    <p class="text-white-50">Usaremos tus datos únicamente para coordinar la atención solicitada.</p>
                    <div class="booking-list small mt-4">
                        <div class="d-flex gap-2 mb-3"><i class="bi bi-check-circle-fill"></i><span>Sin necesidad de crear una cuenta</span></div>
                        <div class="d-flex gap-2 mb-3"><i class="bi bi-check-circle-fill"></i><span>Código único de seguimiento</span></div>
                        <div class="d-flex gap-2"><i class="bi bi-check-circle-fill"></i><span>Gestión directa por el taller</span></div>
                    </div>
                </div>
                <div class="col-lg-8 p-4 p-md-5">
                    <form action="{{ route('reservas.store') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6"><label class="field-label mb-1" for="nombre">Nombre completo *</label><input class="form-control @error('nombre') is-invalid @enderror" id="nombre" name="nombre" value="{{ old('nombre') }}" required maxlength="120" autocomplete="name">@error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                            <div class="col-md-6"><label class="field-label mb-1" for="telefono">Teléfono o WhatsApp *</label><input class="form-control @error('telefono') is-invalid @enderror" id="telefono" name="telefono" value="{{ old('telefono') }}" required maxlength="30" inputmode="tel" autocomplete="tel">@error('telefono')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                            <div class="col-md-6"><label class="field-label mb-1" for="email">Correo electrónico</label><input class="form-control @error('email') is-invalid @enderror" id="email" name="email" type="email" value="{{ old('email') }}" maxlength="255" autocomplete="email">@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                            <div class="col-md-6"><label class="field-label mb-1" for="servicio_id">Servicio</label><select class="form-select @error('servicio_id') is-invalid @enderror" id="servicio_id" name="servicio_id"><option value="">Necesito orientación</option>@foreach($servicios as $servicio)<option value="{{ $servicio->id }}" @selected(old('servicio_id') == $servicio->id)>{{ $servicio->nombre }}</option>@endforeach</select>@error('servicio_id')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                            <div class="col-md-4"><label class="field-label mb-1" for="vehiculo_marca">Marca del vehículo *</label><input class="form-control @error('vehiculo_marca') is-invalid @enderror" id="vehiculo_marca" name="vehiculo_marca" value="{{ old('vehiculo_marca') }}" required maxlength="80" placeholder="Ej.: Toyota">@error('vehiculo_marca')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                            <div class="col-md-4"><label class="field-label mb-1" for="vehiculo_modelo">Modelo</label><input class="form-control" id="vehiculo_modelo" name="vehiculo_modelo" value="{{ old('vehiculo_modelo') }}" maxlength="80" placeholder="Ej.: Corolla"></div>
                            <div class="col-md-4"><label class="field-label mb-1" for="placa">Placa</label><input class="form-control" id="placa" name="placa" value="{{ old('placa') }}" maxlength="20" placeholder="Ej.: 1234ABC"></div>
                            <div class="col-md-6"><label class="field-label mb-1" for="fecha_preferida">Fecha preferida *</label><input class="form-control @error('fecha_preferida') is-invalid @enderror" id="fecha_preferida" name="fecha_preferida" type="date" min="{{ now()->format('Y-m-d') }}" value="{{ old('fecha_preferida') }}" required>@error('fecha_preferida')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                            <div class="col-md-6"><label class="field-label mb-1" for="hora_preferida">Hora preferida *</label><input class="form-control @error('hora_preferida') is-invalid @enderror" id="hora_preferida" name="hora_preferida" type="time" min="08:00" max="18:00" value="{{ old('hora_preferida') }}" required>@error('hora_preferida')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                            <div class="col-12"><label class="field-label mb-1" for="mensaje">¿Qué problema presenta el vehículo?</label><textarea class="form-control" id="mensaje" name="mensaje" rows="4" maxlength="1000" placeholder="Describe brevemente los síntomas o la atención que necesitas.">{{ old('mensaje') }}</textarea></div>
                            <div class="col-12 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mt-4">
                                <small class="text-secondary"><i class="bi bi-shield-lock me-1"></i>Tus datos se envían de forma segura.</small>
                                <button class="btn btn-red btn-lg px-4" type="submit">Enviar solicitud <i class="bi bi-arrow-right ms-1"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section-pad" id="ubicacion">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-5">
                <div class="section-kicker mb-2">Encuéntranos</div>
                <h2 class="section-title mb-3">Visita nuestro taller</h2>
                <p class="section-copy">Consulta la ubicación en el mapa y abre la ruta en Google Maps para llegar desde donde estés.</p>
                <div class="location-chip mb-3"><i class="bi bi-geo-alt-fill"></i>6RRG+77G, Av. Beni, Santa Cruz</div>
                <div class="d-flex gap-3 mb-4"><i class="bi bi-clock text-danger fs-4"></i><div><strong class="d-block">Horario de atención</strong><span class="text-secondary small">Lunes a sábado · 08:00 a 18:00</span></div></div>
                <a class="btn btn-red" href="https://maps.app.goo.gl/Te5Pba1yJDVpEE7Z7" target="_blank" rel="noopener noreferrer"><i class="bi bi-sign-turn-right me-2"></i>Cómo llegar</a>
            </div>
            <div class="col-lg-7">
                <div class="public-card p-2"><div id="public-map" aria-label="Mapa de ubicación del taller"></div></div>
            </div>
        </div>
    </div>
</section>

<section class="pb-5">
    <div class="container">
        <div class="public-card p-4 p-md-5 d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-4">
            <div><div class="section-kicker mb-2">Área privada</div><h2 class="h3 fw-bold mb-2">¿Formas parte del equipo?</h2><p class="section-copy mb-0">Ingresa al sistema interno para gestionar citas, clientes, vehículos y órdenes según tu rol.</p></div>
            <a class="btn btn-soft btn-lg px-4 flex-shrink-0" href="{{ auth()->check() ? route('dashboard') : route('login') }}"><i class="bi bi-person-badge me-2"></i>{{ auth()->check() ? 'Ir a mi panel' : 'Acceso trabajadores' }}</a>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script nonce="{{ $cspNonce }}">
const publicMap = L.map('public-map', { scrollWheelZoom: false }).setView([-17.7592893, -63.1743331], 16);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; OpenStreetMap'
}).addTo(publicMap);
L.marker([-17.7592893, -63.1743331]).addTo(publicMap)
    .bindPopup('<strong>Grupo Los Jausis</strong><br>Taller automotriz')
    .openPopup();
</script>
@endpush
