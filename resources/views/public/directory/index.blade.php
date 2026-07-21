@extends('layouts.directory')

@section('content')
<div class="directory-header">
    <div class="container">
        <h1 class="directory-title">
            <i class="bi bi-building me-2"></i>Directorio de Negocios
        </h1>
        <p class="directory-subtitle">Encuentra los mejores negocios locales cerca de ti</p>
    </div>
</div>

<div class="container my-4">
    <form method="GET" action="{{ route('directory.index') }}" class="directory-filters" id="filterForm">
        <input type="hidden" name="lat" id="lat" value="{{ $filters['lat'] }}">
        <input type="hidden" name="lng" id="lng" value="{{ $filters['lng'] }}">
        <input type="hidden" name="radius" id="radius" value="{{ $filters['radius'] }}">

        <div class="row g-3 align-items-end">
            <div class="col-12 col-md-4">
                <label for="search" class="form-label fw-semibold">Buscar</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" id="search" class="form-control" placeholder="Nombre del negocio..." value="{{ $filters['search'] }}">
                </div>
            </div>

            <div class="col-6 col-md-3">
                <label for="type" class="form-label fw-semibold">Categoría</label>
                <select name="type" id="type" class="form-select">
                    <option value="">Todas</option>
                    @foreach($businessTypes as $type)
                        <option value="{{ $type->value }}" {{ $filters['type'] == $type->value ? 'selected' : '' }}>
                            {{ $type->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-6 col-md-3">
                <label for="location" class="form-label fw-semibold">Ubicación</label>
                <div class="input-group">
                    <input type="text" name="location" id="location" class="form-control" placeholder="Ciudad, CP..." value="{{ $filters['location'] }}">
                    <button type="button" class="btn btn-outline-secondary" id="geolocationBtn" title="Usar mi ubicación">
                        <i class="bi bi-crosshair"></i>
                    </button>
                </div>
            </div>

            <div class="col-12 col-md-2">
                <button type="submit" class="btn btn-primary w-100 h-100">
                    <i class="bi bi-search me-1"></i> Buscar
                </button>
            </div>
        </div>

        @if($filters['lat'] && $filters['lng'])
            <div class="row mt-3">
                <div class="col-12">
                    <span class="badge bg-success py-2 px-3">
                        <i class="bi bi-geo-alt-fill me-1"></i>
                        Radio: {{ $filters['radius'] }} km
                        <button type="button" class="btn-close btn-close-white ms-2" aria-label="Cerrar" onclick="clearGeolocation()"></button>
                    </span>
                </div>
            </div>
        @endif
    </form>
</div>

<div class="container mb-4">
    <div class="row g-4">
        <div class="col-12 order-2 order-lg-1 col-lg-5">
            <div class="directory-map-container rounded shadow-sm overflow-hidden">
                <div id="directory-map"></div>
            </div>
        </div>

        <div class="col-12 order-1 order-lg-2 col-lg-7">
            <div class="directory-results">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">
                        <i class="bi bi-shop me-1"></i>
                        {{ $businesses->total() }} negocios encontrados
                    </span>
                </div>

                @forelse($businesses as $business)
                    <a href="{{ route('directory.show', $business->slug) }}" class="text-decoration-none">
                        <div class="business-card">
                            <div class="row g-0">
                                <div class="col-4">
                                    <div class="business-card-image h-100">
                                        @if($business->cover_image_path)
                                            <img src="{{ $business->cover_image_path }}" alt="{{ $business->name }}">
                                        @elseif($business->logo_path)
                                            <img src="{{ $business->logo_path }}" alt="{{ $business->name }}">
                                        @else
                                            <div class="d-flex align-items-center justify-content-center h-100 bg-light">
                                                <i class="bi bi-building text-muted" style="font-size: 2.5rem;"></i>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-8">
                                    <div class="business-card-body">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <span class="badge bg-primary bg-opacity-10 text-primary mb-1">{{ $business->business_type->label() }}</span>
                                                <h5 class="business-card-name mb-1">{{ $business->name }}</h5>
                                            </div>
                                            @if($business->reviews_count > 0)
                                                <div class="text-warning small">
                                                    <i class="bi bi-star-fill"></i>
                                                    {{ number_format($business->reviews_avg_rating ?? 0, 1) }}
                                                    <span class="text-muted">({{ $business->reviews_count }})</span>
                                                </div>
                                            @endif
                                        </div>

                                        @if($business->locations->isNotEmpty())
                                            <p class="business-card-address mb-1">
                                                <i class="bi bi-geo-alt text-muted me-1"></i>
                                                {{ $business->locations->first()->city }}
                                                @if($business->locations->count() > 1)
                                                    <span class="text-muted">(+{{ $business->locations->count() - 1 }} más)</span>
                                                @endif
                                            </p>
                                        @endif

                                        @if($business->description)
                                            <p class="text-muted small mb-0" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                                {{ $business->description }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="alert alert-light border text-center py-5">
                        <i class="bi bi-search text-muted" style="font-size: 3rem;"></i>
                        <p class="mt-2 mb-0 text-muted">No se encontraron negocios.</p>
                        <small class="text-muted">Intenta con otros filtros.</small>
                    </div>
                @endforelse

                @if($businesses->hasPages())
                    <nav class="directory-pagination mt-4">
                        {{ $businesses->appends(request()->query())->links() }}
                    </nav>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var geolocationBtn = document.getElementById('geolocationBtn');
    if (geolocationBtn) {
        geolocationBtn.addEventListener('click', function() {
            if (navigator.geolocation) {
                geolocationBtn.disabled = true;
                geolocationBtn.innerHTML = '<i class="bi bi-hourglass-split"></i>';
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        document.getElementById('lat').value = position.coords.latitude;
                        document.getElementById('lng').value = position.coords.longitude;
                        document.getElementById('radius').value = 10;
                        document.getElementById('filterForm').submit();
                    },
                    function(error) {
                        alert('No se pudo obtener tu ubicación.');
                        geolocationBtn.disabled = false;
                        geolocationBtn.innerHTML = '<i class="bi bi-crosshair"></i>';
                    }
                );
            } else {
                alert('Tu navegador no soporta geolocalización.');
            }
        });
    }
});

function clearGeolocation() {
    document.getElementById('lat').value = '';
    document.getElementById('lng').value = '';
    document.getElementById('radius').value = '';
    document.getElementById('filterForm').submit();
}

document.addEventListener('DOMContentLoaded', function() {
    @if($mapMarkers->isNotEmpty())
        var map = L.map('directory-map', { scrollWheelZoom: false }).setView([{{ $mapMarkers->first()->latitude }}, {{ $mapMarkers->first()->longitude }}], 12);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        @foreach($mapMarkers as $marker)
            var popupContent = `
                <div class="map-popup">
                    <div class="popup-name">{{ $marker['name'] }}</div>
                    <div class="popup-address">{{ $marker['address'] }}</div>
                    <a href="{{ route('directory.show', $marker['slug']) }}" class="popup-link">Ver negocio →</a>
                </div>
            `;
            L.marker([{{ $marker['latitude'] }}, {{ $marker['longitude'] }}])
                .addTo(map)
                .bindPopup(popupContent);
        @endforeach

        @if($mapMarkers->count() > 1)
            var group = new L.featureGroup([
                @foreach($mapMarkers as $marker)
                    L.marker([{{ $marker['latitude'] }}, {{ $marker['longitude'] }}]),
                @endforeach
            ]);
            map.fitBounds(group.getBounds(), {padding: [50, 50]});
        @endif
    @else
        var map = L.map('directory-map', { scrollWheelZoom: false }).setView([19.4326, -99.1332], 10);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);
    @endif
});
</script>
@endpush
