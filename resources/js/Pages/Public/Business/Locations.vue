<template>
  <div class="minisite">
    <div class="bg-light py-4 mb-4">
      <div class="container">
        <h1 class="mb-0">Nuestras ubicaciones</h1>
      </div>
    </div>

    <div class="container py-4">
      <div v-if="locations.length === 0" class="alert alert-info">
        No hay ubicaciones disponibles.
      </div>

      <div class="row g-4">
        <div v-for="loc in locations" :key="loc.id" class="col-md-6">
          <div class="card h-100">
            <div class="card-body">
              <h5 class="card-title">
                <i class="bi bi-geo-alt-fill me-2 text-primary"></i>{{ loc.name }}
                <span v-if="loc.is_primary" class="badge bg-primary ms-2">Principal</span>
              </h5>
              <p class="card-text">
                <i class="bi bi-house me-2 text-muted"></i>{{ loc.address_line_1 }}
                <span v-if="loc.address_line_2">{{ loc.address_line_2 }}</span>
              </p>
              <p class="card-text" v-if="loc.city || loc.state || loc.postal_code">
                <i class="bi bi-map me-2 text-muted"></i>{{ loc.city }}{{ loc.state ? ', ' + loc.state : '' }} {{ loc.postal_code }}
              </p>
              <p class="card-text" v-if="loc.country">
                <i class="bi bi-flag me-2 text-muted"></i>{{ loc.country }}
              </p>
              <p class="card-text" v-if="loc.phone">
                <i class="bi bi-telephone me-2 text-muted"></i>
                <a :href="`tel:${loc.phone}`">{{ loc.phone }}</a>
              </p>
              <p class="card-text" v-if="loc.email">
                <i class="bi bi-envelope me-2 text-muted"></i>
                <a :href="`mailto:${loc.email}`">{{ loc.email }}</a>
              </p>
              <p class="card-text small text-muted" v-if="loc.opening_hours">
                <i class="bi bi-clock me-2 text-muted"></i>{{ loc.opening_hours }}
              </p>
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
const locations = computed(() => page.props.locations || [])
</script>
