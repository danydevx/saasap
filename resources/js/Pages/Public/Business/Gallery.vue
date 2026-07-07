<template>
  <div class="minisite">
    <div class="bg-light py-4 mb-4">
      <div class="container">
        <h1 class="mb-0">Galeria de fotos</h1>
      </div>
    </div>

    <div class="container py-4">
      <div v-if="images.length === 0" class="alert alert-info">
        No hay imagenes en la galeria.
      </div>

      <div class="row g-3">
        <div v-for="img in images" :key="img.id" class="col-md-4 col-lg-3">
          <div class="card h-100">
            <img :src="img.path" :alt="img.title || img.original_name" class="card-img-top object-fit-cover" style="height: 200px;" />
            <div v-if="img.title || img.description" class="card-body">
              <h6 class="card-title">{{ img.title }}</h6>
              <p v-if="img.description" class="card-text small text-muted">{{ img.description }}</p>
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
const images = computed(() => page.props.images || [])
</script>
