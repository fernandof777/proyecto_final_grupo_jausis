@extends('layouts.app')

@section('titulo', 'Ubicación')
@section('encabezado', 'Ubicación del taller')
@section('subtitulo', 'Encuéntranos en la avenida Beni')

@push('estilos')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIINfQ3ynhRdOZtD/miZyoHS5obTRR9BMY=" crossorigin="">
<style>
    #mapa-taller { height: 420px; border-radius: 0 0 14px 14px; z-index: 1; }
    .location-icon { width: 52px; height: 52px; display: grid; place-items: center; border-radius: 14px; background: #dbeafe; color: #2563eb; font-size: 1.5rem; }
    .leaflet-popup-content { font-family: Inter, sans-serif; line-height: 1.5; }
    @media (max-width: 900px) { #mapa-taller { height: 360px; } }
    @media (max-width: 600px) {
        #mapa-taller { height: 300px; border-radius: 0 0 11px 11px; }
        .location-icon { width: 44px; height: 44px; font-size: 1.2rem; flex: 0 0 44px; }
    }
</style>
@endpush

@section('contenido')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold mb-1">Visítanos en Grupo Los Jausis</h2>
        <p class="text-secondary mb-0">Consulta el mapa, acerca la vista o abre las indicaciones en Google Maps.</p>
    </div>
    <a class="btn btn-primary px-4"
       href="https://maps.app.goo.gl/Te5Pba1yJDVpEE7Z7"
       target="_blank" rel="noopener noreferrer">
        <i class="bi bi-sign-turn-right me-2"></i>Cómo llegar
    </a>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6 col-xl-4">
        <div class="card app-card h-100"><div class="card-body d-flex gap-3 align-items-center">
            <div class="location-icon"><i class="bi bi-geo-alt-fill"></i></div>
            <div><small class="text-secondary">Dirección</small><div class="fw-semibold">6RRG+77G, Av. Beni</div><div class="small text-secondary">Santa Cruz de la Sierra</div></div>
        </div></div>
    </div>
    <div class="col-md-6 col-xl-4">
        <div class="card app-card h-100"><div class="card-body d-flex gap-3 align-items-center">
            <div class="location-icon"><i class="bi bi-clock-fill"></i></div>
            <div><small class="text-secondary">Horario de atención</small><div class="fw-semibold">Lunes a sábado</div><div class="small text-secondary">08:00 a 18:00</div></div>
        </div></div>
    </div>
    <div class="col-md-6 col-xl-4">
        <div class="card app-card h-100"><div class="card-body d-flex gap-3 align-items-center">
            <div class="location-icon"><i class="bi bi-pin-map-fill"></i></div>
            <div><small class="text-secondary">Coordenadas</small><div class="fw-semibold">-17.7592893, -63.1743331</div><div class="small text-secondary">Ubicación exacta del enlace</div></div>
        </div></div>
    </div>
</div>

<div class="card app-card overflow-hidden">
    <div class="card-header bg-white border-0 p-4">
        <h5 class="fw-bold mb-1">Mapa interactivo</h5>
        <small class="text-secondary">Arrastra el mapa y utiliza los controles para explorar los alrededores.</small>
    </div>
    <div id="mapa-taller" role="application" aria-label="Mapa interactivo con la ubicación de Grupo Los Jausis"></div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script nonce="{{ $cspNonce }}">
document.addEventListener('DOMContentLoaded', () => {
    const coordenadas = [-17.7592893, -63.1743331];
    const mapa = L.map('mapa-taller', {
        scrollWheelZoom: false,
        zoomControl: true
    }).setView(coordenadas, 17);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(mapa);

    L.marker(coordenadas)
        .addTo(mapa)
        .bindPopup('<strong>Grupo Los Jausis</strong><br>6RRG+77G, Av. Beni<br>Santa Cruz de la Sierra')
        .openPopup();

    mapa.on('focus', () => mapa.scrollWheelZoom.enable());
    mapa.on('blur', () => mapa.scrollWheelZoom.disable());
});
</script>
@endpush
