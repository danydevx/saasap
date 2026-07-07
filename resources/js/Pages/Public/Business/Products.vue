<template>
  <div class="minisite">
    <div class="bg-light py-4 mb-4">
      <div class="container">
        <h1 class="mb-0">Nuestros productos</h1>
      </div>
    </div>

    <div class="container py-4">
      <div v-if="products.length === 0" class="alert alert-info">
        No hay productos disponibles.
      </div>

      <div class="row g-4">
        <div v-for="prod in products" :key="prod.id" class="col-md-6 col-lg-4">
          <div class="card h-100">
            <div class="card-body">
              <h5 class="card-title">{{ prod.name }}</h5>
              <p class="card-text text-muted small">{{ prod.description || 'Sin descripcion' }}</p>
              <div class="d-flex justify-content-between align-items-center">
                <span class="fw-bold text-primary fs-5">{{ formatPrice(prod.price) }}</span>
                <span v-if="prod.sku" class="text-muted small">SKU: {{ prod.sku }}</span>
              </div>
              <div v-if="prod.quantity !== null" class="mt-2">
                <span v-if="prod.quantity > 0" class="badge bg-success">En stock ({{ prod.quantity }})</span>
                <span v-else class="badge bg-danger">Sin stock</span>
              </div>
              <a v-if="prod.whatsapp_contact && business.phone" :href="`https://wa.me/${business.phone.replace(/[^0-9]/g, '')}?text=${encodeURIComponent('Hola, me interesa el producto: ' + prod.name)}`" target="_blank" class="btn btn-success w-100 mt-3">
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
const products = computed(() => page.props.products || [])

const formatPrice = (price) => {
  if (!price && price !== 0) return '-'
  return new Intl.NumberFormat('es-AR', { style: 'currency', currency: 'ARS' }).format(price)
}
</script>
