<template>
  <div class="minisite">
    <div class="bg-light py-4 mb-4">
      <div class="container">
        <h1 class="mb-0">Servicios</h1>
      </div>
    </div>

    <div class="container py-4">
      <div v-if="services.length === 0" class="alert alert-info">
        No hay servicios disponibles.
      </div>

      <div class="row g-4">
        <div v-for="service in services" :key="service.id" class="col-md-6 col-lg-4">
          <div class="card h-100">
            <img v-if="service.image" :src="service.image" class="card-img-top" :alt="service.name" style="height: 150px; object-fit: cover;">
            <div class="card-body">
              <h5 class="card-title">{{ service.name }}</h5>
              <p class="card-text text-muted">{{ service.description || 'Sin descripcion' }}</p>
              <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="fw-bold text-primary fs-5">{{ formatPrice(service.price) }}</span>
                <span class="badge bg-secondary">{{ service.duration_minutes }} min</span>
              </div>
              <Link :href="`/b/${business.slug}/book?service=${service.id}`" class="btn btn-primary w-100 mb-2">
                Reservar turno
              </Link>
              <a v-if="service.whatsapp_contact && business.phone" :href="`https://wa.me/${business.phone.replace(/[^0-9]/g, '')}?text=${encodeURIComponent('Hola, me interesa el servicio: ' + service.name)}`" target="_blank" class="btn btn-success w-100">
                <i class="bi bi-whatsapp me-1"></i>Contactar por WhatsApp
              </a>
            </div>
          </div>
        </div>
      </div>

      <div class="mt-4">
        <Link :href="`/b/${business.slug}`" class="text-decoration-none">
          <i class="bi bi-arrow-left me-1"></i>Volver al inicio
        </Link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

const page = usePage()
const business = computed(() => page.props.business)
const services = computed(() => page.props.services || [])

const formatPrice = (price) => {
  if (!price && price !== 0) return '-'
  return new Intl.NumberFormat('es-AR', { style: 'currency', currency: 'ARS' }).format(price)
}
</script>
