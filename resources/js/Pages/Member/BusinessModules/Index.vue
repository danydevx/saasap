<template>
  <MemberLayout>
    <Head title="Mis Negocios" />

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
      <div>
        <h1 class="h4 mb-1">Mis Negocios</h1>
        <p class="text-muted mb-0">Gestiona tus negocios y su contenido.</p>
      </div>
      <div>
        <Link href="/member/business-modules" class="btn btn-outline-secondary btn-sm">
          <i class="bi bi-grid me-1"></i>Modulos
        </Link>
      </div>
    </div>

    <div class="row g-4" v-if="businesses.data.length">
      <div class="col-12 col-lg-6" v-for="business in businesses.data" :key="business.id">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-header bg-white py-3">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <h2 class="h5 mb-0">{{ business.name }}</h2>
                <span class="text-muted small">{{ business.slug }}</span>
              </div>
              <span class="badge" :class="business.is_active ? 'bg-success' : 'bg-secondary'">
                {{ business.is_active ? 'Activo' : 'Inactivo' }}
              </span>
            </div>
          </div>
          <div class="card-body">
            <p class="text-muted small mb-3">{{ business.description || 'Sin descripcion' }}</p>

            <div class="mb-3">
              <div class="text-muted small mb-2">Modulos activos:</div>
              <div class="d-flex flex-wrap gap-1">
                <span v-for="mod in getEnabledModules(business.modules)" :key="mod" class="badge text-bg-light">
                  {{ getModuleLabel(mod) }}
                </span>
                <span v-if="!getEnabledModules(business.modules).length" class="text-muted small">
                  Ningun modulo activo
                </span>
              </div>
            </div>

            <hr>

            <div class="row g-2">
              <div class="col-6 col-md-3">
                <Link
                  :href="`/member/businesses/${business.id}/hero`"
                  class="btn btn-outline-primary btn-sm w-100"
                >
                  <i class="bi bi-house d-block mb-1"></i>
                  <small>Hero</small>
                </Link>
              </div>
              <div class="col-6 col-md-3" v-if="hasModule(business.modules, 'locations')">
                <Link
                  :href="`/member/businesses/${business.id}/locations`"
                  class="btn btn-outline-primary btn-sm w-100"
                >
                  <i class="bi bi-geo-alt d-block mb-1"></i>
                  <small>Ubicaciones</small>
                </Link>
              </div>
              <div class="col-6 col-md-3" v-if="hasModule(business.modules, 'services')">
                <Link
                  :href="`/member/businesses/${business.id}/services`"
                  class="btn btn-outline-primary btn-sm w-100"
                >
                  <i class="bi bi-scissors d-block mb-1"></i>
                  <small>Servicios</small>
                </Link>
              </div>
              <div class="col-6 col-md-3" v-if="hasModule(business.modules, 'products')">
                <Link
                  :href="`/member/businesses/${business.id}/products`"
                  class="btn btn-outline-primary btn-sm w-100"
                >
                  <i class="bi bi-bag d-block mb-1"></i>
                  <small>Productos</small>
                </Link>
              </div>
              <div class="col-6 col-md-3" v-if="hasModule(business.modules, 'gallery')">
                <Link
                  :href="`/member/businesses/${business.id}/gallery`"
                  class="btn btn-outline-primary btn-sm w-100"
                >
                  <i class="bi bi-images d-block mb-1"></i>
                  <small>Galeria</small>
                </Link>
              </div>
              <div class="col-6 col-md-3" v-if="hasModule(business.modules, 'appointments')">
                <Link
                  :href="`/member/businesses/${business.id}/appointments`"
                  class="btn btn-outline-primary btn-sm w-100"
                >
                  <i class="bi bi-calendar-check d-block mb-1"></i>
                  <small>Citas</small>
                </Link>
              </div>
              <div class="col-6 col-md-3" v-if="hasModule(business.modules, 'appointments')">
                <Link
                  :href="`/member/businesses/${business.id}/slots`"
                  class="btn btn-outline-primary btn-sm w-100"
                >
                  <i class="bi bi-clock d-block mb-1"></i>
                  <small>Slots</small>
                </Link>
              </div>
              <div class="col-6 col-md-3" v-if="hasModule(business.modules, 'leads')">
                <Link
                  :href="`/member/businesses/${business.id}/leads`"
                  class="btn btn-outline-primary btn-sm w-100"
                >
                  <i class="bi bi-people d-block mb-1"></i>
                  <small>Leads</small>
                </Link>
              </div>
              <div class="col-6 col-md-3" v-if="hasModule(business.modules, 'contact_form')">
                <Link
                  :href="`/member/businesses/${business.id}/contact-form/submissions`"
                  class="btn btn-outline-primary btn-sm w-100"
                >
                  <i class="bi bi-envelope d-block mb-1"></i>
                  <small>Contactos</small>
                </Link>
              </div>
              <div class="col-6 col-md-3" v-if="hasModule(business.modules, 'ai_chatbot')">
                <Link
                  :href="`/member/businesses/${business.id}/ai-chatbot`"
                  class="btn btn-outline-primary btn-sm w-100"
                >
                  <i class="bi bi-robot d-block mb-1"></i>
                  <small>AI Chatbot</small>
                </Link>
              </div>
              <div class="col-6 col-md-3" v-if="hasModule(business.modules, 'reviews')">
                <Link
                  :href="`/member/businesses/${business.id}/reviews`"
                  class="btn btn-outline-primary btn-sm w-100"
                >
                  <i class="bi bi-star d-block mb-1"></i>
                  <small>Reviews</small>
                </Link>
              </div>
              <div class="col-6 col-md-3" v-if="hasModule(business.modules, 'socialmedia')">
                <Link
                  :href="`/member/businesses/${business.id}/social-networks`"
                  class="btn btn-outline-primary btn-sm w-100"
                >
                  <i class="bi bi-share d-block mb-1"></i>
                  <small>Redes</small>
                </Link>
              </div>
              <div class="col-6 col-md-3" v-if="hasModule(business.modules, 'promotions')">
                <Link
                  :href="`/member/businesses/${business.id}/promotions`"
                  class="btn btn-outline-primary btn-sm w-100"
                >
                  <i class="bi bi-tag d-block mb-1"></i>
                  <small>Promotions</small>
                </Link>
              </div>
              <div class="col-6 col-md-3" v-if="hasModule(business.modules, 'restaurant_menu')">
                <Link
                  :href="`/member/businesses/${business.id}/menu-categories`"
                  class="btn btn-outline-primary btn-sm w-100"
                >
                  <i class="bi bi-list-ul d-block mb-1"></i>
                  <small>Menú</small>
                </Link>
              </div>
              <div class="col-6 col-md-3" v-if="hasModule(business.modules, 'about')">
                <Link
                  :href="`/member/businesses/${business.id}/about`"
                  class="btn btn-outline-primary btn-sm w-100"
                >
                  <i class="bi bi-info-circle d-block mb-1"></i>
                  <small>Acerca de</small>
                </Link>
              </div>
              <div class="col-6 col-md-3" v-if="hasModule(business.modules, 'features')">
                <Link
                  :href="`/member/businesses/${business.id}/features`"
                  class="btn btn-outline-primary btn-sm w-100"
                >
                  <i class="bi bi-check-circle d-block mb-1"></i>
                  <small>Caracteristicas</small>
                </Link>
              </div>
            </div>
          </div>
          <div class="card-footer bg-white">
            <Link :href="`/member/businesses/${business.id}/modules`" class="btn btn-link btn-sm text-decoration-none p-0">
              <i class="bi bi-gear me-1"></i>Configurar modulos
            </Link>
            <span class="text-muted mx-2">|</span>
            <Link :href="`/b/${business.slug}`" target="_blank" class="btn btn-link btn-sm text-decoration-none p-0">
              <i class="bi bi-box-arrow-up-right me-1"></i>Ver minisitio
            </Link>
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
import MemberPagination from '@/Components/Member/Pagination.vue'

const props = defineProps({
  businesses: {
    type: Object,
    default: () => ({ data: [], links: [] }),
  },
})

const moduleLabels = {
  locations: 'Ubicaciones',
  services: 'Servicios',
  products: 'Productos',
  gallery: 'Galeria',
  appointments: 'Citas',
  leads: 'Leads',
  contact_form: 'Formulario',
  ai_chatbot: 'AI Chatbot',
  reviews: 'Reviews',
  promotions: 'Promotions',
  restaurant_menu: 'Menu',
  socialmedia: 'Redes Sociales',
  about: 'Acerca de',
  features: 'Caracteristicas',
}

const getModuleLabel = (key) => moduleLabels[key] || key

const getEnabledModules = (modules) => {
  if (!modules) return []
  return modules.filter((m) => m.is_enabled).map((m) => m.module_key)
}

const hasModule = (modules, key) => {
  if (!modules) return false
  return modules.some((m) => m.is_enabled && m.module_key === key)
}
</script>
