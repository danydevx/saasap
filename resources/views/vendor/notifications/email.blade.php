@php($brand = app(\App\Services\BrandingService::class)->forEmail())

@component('mail::message')
@include('emails.partials.header', ['brand' => $brand])

{{-- Greeting --}}
@if (! empty($greeting))
# {{ $greeting }}
@else
@if ($level === 'error')
# Ocurrio un problema
@else
# Hola
@endif
@endif

{{-- Intro Lines --}}
@foreach ($introLines as $line)
{{ $line }}

@endforeach

{{-- Action Button --}}
@if (! empty($actionText))
@include('emails.partials.button', ['url' => $actionUrl, 'label' => $actionText, 'brand' => $brand])
@endif

{{-- Outro Lines --}}
@foreach ($outroLines as $line)
{{ $line }}

@endforeach

{{-- Subcopy --}}
@if (! empty($actionText))
@slot('subcopy')
Si tienes problemas al hacer click en "{{ $actionText }}", copia y pega este enlace en tu navegador:
<span class="break-all">{{ $actionUrl }}</span>
@endslot
@endif

@include('emails.partials.footer', ['brand' => $brand])
@endcomponent
