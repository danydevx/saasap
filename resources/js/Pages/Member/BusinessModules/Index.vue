<template>
  <MemberLayout>
    <Head title="Mis Negocios" />

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
      <div>
        <h1 class="h4 mb-1">Mis Negocios</h1>
        <p class="text-muted mb-0">Gestiona tus negocios y su contenido.</p>
      </div>
    </div>

    <div class="row g-4" v-if="businesses.data.length">
      <div class="col-12 col-lg-6" v-for="business in businesses.data" :key="business.id">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <div class="d-flex align-items-start gap-3 mb-3">
              <div
                class="rounded bg-light d-flex align-items-center justify-content-center flex-shrink-0"
                style="width: 64px; height: 64px; overflow: hidden;"
              >
                <img
                  v-if="business.logo_path"
                  :src="business.logo_path"
                  alt="Logo"
                  class="w-100 h-100"
                  style="object-fit: cover;"
                />
                <i v-else class="bi bi-building text-muted" style="font-size: 1.5rem;"></i>
              </div>
              <div class="flex-grow-1 min-width-0">
                <h2 class="h5 mb-0 text-truncate">{{ business.name }}</h2>
                <span class="text-muted small text-truncate d-block">{{ business.slug }}</span>
                <span class="badge mt-1" :class="business.is_active ? 'bg-success' : 'bg-secondary'">
                  {{ business.is_active ? 'Activo' : 'Inactivo' }}
                </span>
              </div>
            </div>

            <div class="d-flex gap-2">
              <Link :href="`/member/businesses/${business.id}/edit`" class="btn btn-outline-primary btn-sm flex-grow-1">
                <i class="bi bi-pencil me-1"></i>Editar
              </Link>
              <Link :href="`/member/businesses/${business.id}/modules`" class="btn btn-outline-secondary btn-sm flex-grow-1">
                <i class="bi bi-gear me-1"></i>Modulos
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-else class="card border-0 shadow-sm">
      <div class="card-body text-center py-5">
        <i class="bi bi-building display-1 text-muted"></i>
        <h3 class="h5 mt-3">No tienes negocios registrados</h3>
        <p class="text-muted">Crea tu primer negocio para empezar a gestionar tu contenido.</p>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'

const props = defineProps({
  businesses: {
    type: Object,
    required: true,
  },
})
</script>
