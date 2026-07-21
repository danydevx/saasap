@extends('layouts.directory')

@section('content')
<div class="business-detail-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-auto">
                <div class="business-detail-logo">
                    @if($business->logo_path)
                        <img src="{{ $business->logo_path }}" alt="{{ $business->name }}">
                    @else
                        <i class="bi bi-building"></i>
                    @endif
                </div>
            </div>
            <div class="col">
                <div class="business-detail-type">{{ $business->business_type->label() }}</div>
                <h1 class="business-detail-name">{{ $business->name }}</h1>
                @if($avgRating > 0)
                    <div class="mt-2">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= round($avgRating))
                                <i class="bi bi-star-fill text-warning"></i>
                            @else
                                <i class="bi bi-star text-muted"></i>
                            @endif
                        @endfor
                        <span class="text-white-50 ms-2">({{ number_format($avgRating, 1) }})</span>
                    </div>
                @endif
            </div>
        </div>
        @if($business->description)
            <p class="business-detail-description">{{ $business->description }}</p>
        @endif
    </div>
</div>

<div class="container my-4">
    <div class="row g-4">
        <div class="col-lg-8">
            @if($locations->isNotEmpty())
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="bi bi-geo-alt-fill text-primary me-2"></i>Ubicaciones</h5>
                    </div>
                    <div class="card-body p-0">
                        @foreach($locations as $location)
                            <div class="p-3 border-bottom {{ !$loop->last ? 'border-light' : '' }}">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">{{ $location->name }}</h6>
                                        <p class="text-muted mb-1 small">
                                            <i class="bi bi-map-pin me-1"></i>
                                            {{ $location->address_line_1 }}
                                            @if($location->city), {{ $location->city }}@endif
                                            @if($location->postal_code) {{ $location->postal_code }}@endif
                                        </p>
                                        @if($location->phone)
                                            <p class="mb-0 small">
                                                <i class="bi bi-telephone me-1"></i>
                                                <a href="tel:{{ $location->phone }}" class="text-decoration-none">{{ $location->phone }}</a>
                                            </p>
                                        @endif
                                    </div>
                                    @if($location->directions_url)
                                        <a href="{{ $location->directions_url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-box-arrow-up-right"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($galleryImages->isNotEmpty())
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="bi bi-images text-primary me-2"></i>Galería</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            @foreach($galleryImages as $image)
                                <div class="col-4 col-md-3">
                                    <a href="{{ $image->path }}" class="d-block rounded overflow-hidden" style="aspect-ratio: 1; background: #f3f4f6;">
                                        <img src="{{ $image->path }}" alt="{{ $image->title ?? '' }}" class="w-100 h-100 object-fit-cover">
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            @if($services->isNotEmpty())
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="bi bi-briefcase text-primary me-2"></i>Servicios</h5>
                    </div>
                    <div class="card-body p-0">
                        @foreach($services as $service)
                            <div class="p-3 border-bottom {{ !$loop->last ? 'border-light' : '' }}">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">{{ $service->name }}</h6>
                                        @if($service->description)
                                            <p class="text-muted small mb-0">{{ $service->description }}</p>
                                        @endif
                                    </div>
                                    <div class="text-end">
                                        @if($service->price)
                                            <span class="badge bg-primary">{{ $business->currency }} {{ number_format($service->price, 2) }}</span>
                                        @endif
                                        @if($service->duration_minutes)
                                            <span class="badge bg-light text-dark">{{ $service->duration_minutes }} min</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($reviews->isNotEmpty())
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">
                            <i class="bi bi-chat-quote text-primary me-2"></i>Reseñas
                            @if($avgRating > 0)
                                <span class="badge bg-warning text-dark ms-2">
                                    <i class="bi bi-star-fill me-1"></i>{{ number_format($avgRating, 1) }}
                                </span>
                            @endif
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        @foreach($reviews as $review)
                            <div class="p-3 border-bottom {{ !$loop->last ? 'border-light' : '' }}">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <strong>{{ $review->client_name }}</strong>
                                        @if($review->company)
                                            <span class="text-muted"> - {{ $review->company }}</span>
                                        @endif
                                    </div>
                                    <div class="text-warning">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="bi {{ $i <= $review->rating ? 'bi-star-fill' : 'bi-star' }}"></i>
                                        @endfor
                                    </div>
                                </div>
                                @if($review->comment)
                                    <p class="text-muted mb-0 small">{{ $review->comment }}</p>
                                @endif
                                <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($services->isNotEmpty())
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="bi bi-calendar-check text-primary me-2"></i>Solicitar Cita</h5>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <form method="POST" action="{{ route('directory.appointment.store', $business->slug) }}">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="service_id" class="form-label">Servicio</label>
                                    <select name="service_id" id="service_id" class="form-select" required>
                                        <option value="">Selecciona...</option>
                                        @foreach($services as $service)
                                            <option value="{{ $service->id }}">{{ $service->name }}
                                                @if($service->price) - {{ $business->currency }} {{ number_format($service->price, 2) }}@endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="location_id" class="form-label">Sucursal</label>
                                    <select name="location_id" id="location_id" class="form-select" required>
                                        <option value="">Selecciona...</option>
                                        @foreach($locations as $location)
                                            <option value="{{ $location->id }}">{{ $location->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="appointment_date" class="form-label">Fecha</label>
                                    <input type="date" name="appointment_date" id="appointment_date" class="form-control" min="{{ date('Y-m-d') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="start_time" class="form-label">Hora</label>
                                    <select name="start_time" id="start_time" class="form-select" required>
                                        <option value="">Selecciona...</option>
                                        @foreach(['09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00'] as $time)
                                            <option value="{{ $time }}">{{ $time }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="customer_name" class="form-label">Nombre</label>
                                    <input type="text" name="customer_name" id="customer_name" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="customer_email" class="form-label">Correo</label>
                                    <input type="email" name="customer_email" id="customer_email" class="form-control" required>
                                </div>
                                <div class="col-12">
                                    <label for="customer_phone" class="form-label">Teléfono (opcional)</label>
                                    <input type="tel" name="customer_phone" id="customer_phone" class="form-control">
                                </div>
                                <div class="col-12">
                                    <label for="notes" class="form-label">Notas (opcional)</label>
                                    <textarea name="notes" id="notes" class="form-control" rows="2"></textarea>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-calendar-plus me-2"></i>Solicitar Cita
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-envelope text-primary me-2"></i>Enviar Mensaje</h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <form method="POST" action="{{ route('directory.contact.store', $business->slug) }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="contact_name" class="form-label">Nombre</label>
                                <input type="text" name="name" id="contact_name" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label for="contact_email" class="form-label">Correo</label>
                                <input type="email" name="email" id="contact_email" class="form-control" required>
                            </div>
                            <div class="col-12">
                                <label for="contact_phone" class="form-label">Teléfono (opcional)</label>
                                <input type="tel" name="phone" id="contact_phone" class="form-control">
                            </div>
                            <div class="col-12">
                                <label for="contact_message" class="form-label">Mensaje</label>
                                <textarea name="message" id="contact_message" class="form-control" rows="3" required></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-outline-primary w-100">
                                    <i class="bi bi-send me-2"></i>Enviar Mensaje
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            @if($mapLocations->isNotEmpty())
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="bi bi-map text-primary me-2"></i>Mapa</h5>
                    </div>
                    <div class="card-body p-0">
                        <div id="business-map" style="height: 250px;"></div>
                    </div>
                </div>
            @endif

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    @if($business->phone)
                        <a href="tel:{{ $business->phone }}" class="btn btn-primary w-100 mb-2">
                            <i class="bi bi-telephone me-2"></i>Llamar
                        </a>
                    @endif

                    @if($locations->isNotEmpty() && $locations->first()->phone)
                        <?php $whatsapp = preg_replace('/[^0-9]/', '', $locations->first()->phone); ?>
                        <a href="https://wa.me/{{ $whatsapp }}" target="_blank" class="btn btn-success w-100 mb-2">
                            <i class="bi bi-whatsapp me-2"></i>WhatsApp
                        </a>
                    @endif

                    @if($business->email)
                        <a href="mailto:{{ $business->email }}" class="btn btn-outline-primary w-100 mb-2">
                            <i class="bi bi-envelope me-2"></i>Email
                        </a>
                    @endif

                    @if($business->website)
                        <a href="{{ $business->website }}" target="_blank" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-globe me-2"></i>Sitio Web
                        </a>
                    @endif
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-share text-primary me-2"></i>Compartir</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex gap-2">
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}" target="_blank" class="btn btn-outline-primary flex-grow-1">
                            <i class="bi bi-facebook"></i>
                        </a>
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->fullUrl()) }}&text={{ urlencode($business->name) }}" target="_blank" class="btn btn-outline-info flex-grow-1">
                            <i class="bi bi-twitter-x"></i>
                        </a>
                        <a href="https://wa.me/?text={{ urlencode($business->name . ' - ' . request()->fullUrl()) }}" target="_blank" class="btn btn-outline-success flex-grow-1">
                            <i class="bi bi-whatsapp"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@if($mapLocations->isNotEmpty())
<script>
document.addEventListener('DOMContentLoaded', function() {
    var map = L.map('business-map', { scrollWheelZoom: false }).setView([{{ $mapLocations->first()->latitude }}, {{ $mapLocations->first()->longitude }}], 14);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    @foreach($mapLocations as $location)
        L.marker([{{ $location['latitude'] }}, {{ $location['longitude'] }}]).addTo(map)
            .bindPopup('<strong>{{ $location['name'] }}</strong><br>{{ $location['address'] }}');
    @endforeach

    @if($mapLocations->count() > 1)
        var group = new L.featureGroup([
            @foreach($mapLocations as $location)
                L.marker([{{ $location['latitude'] }}, {{ $location['longitude'] }}]),
            @endforeach
        ]);
        map.fitBounds(group.getBounds(), {padding: [50, 50]});
    @endif
});
</script>
@endif
@endpush
