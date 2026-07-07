<template>
  <div class="minisite">
    <div class="bg-light py-4 mb-4">
      <div class="container">
        <h1 class="mb-0">Contacto - {{ business.name }}</h1>
      </div>
    </div>

    <div class="container py-4">
      <div class="row">
        <div class="col-md-8">
          <div v-if="$page.props.flash?.success" class="alert alert-success alert-dismissible fade show" role="alert">
            {{ $page.props.flash.success }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>

          <div class="card">
            <div class="card-body">
              <h5 class="card-title mb-4">Envianos un mensaje</h5>
              <form @submit.prevent="submit">
                <div class="mb-3">
                  <label class="form-label">Tu nombre *</label>
                  <input type="text" v-model="form.name" class="form-control" required />
                </div>

                <div class="mb-3">
                  <label class="form-label">Tu email *</label>
                  <input type="email" v-model="form.email" class="form-control" required />
                </div>

                <div class="mb-3">
                  <label class="form-label">Tu telefono</label>
                  <input type="tel" v-model="form.phone" class="form-control" />
                </div>

                <div class="mb-3">
                  <label class="form-label">Mensaje *</label>
                  <textarea v-model="form.message" class="form-control" rows="4" required></textarea>
                </div>

                <button type="submit" class="btn btn-primary" :disabled="sending">
                  <span v-if="sending">Enviando...</span>
                  <span v-else>Enviar mensaje</span>
                </button>
              </form>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Informacion de contacto</h5>
              <ul class="list-unstyled mb-0">
                <li v-if="business.phone" class="mb-2">
                  <i class="bi bi-telephone me-2 text-primary"></i>{{ business.phone }}
                </li>
                <li v-if="business.email" class="mb-2">
                  <i class="bi bi-envelope me-2 text-primary"></i>{{ business.email }}
                </li>
              </ul>
            </div>
          </div>

          <div v-if="business.phone || business.email" class="mt-3">
            <a v-if="business.phone" :href="`tel:${business.phone}`" class="btn btn-outline-primary w-100 mb-2">
              <i class="bi bi-telephone me-2"></i>Llamar
            </a>
            <a v-if="business.email" :href="`mailto:${business.email}`" class="btn btn-outline-secondary w-100">
              <i class="bi bi-envelope me-2"></i>Enviar email
            </a>
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
import { computed, reactive } from 'vue'
import { Link, usePage, router } from '@inertiajs/vue3'

const page = usePage()
const business = computed(() => page.props.business)

const form = reactive({
  name: '',
  email: '',
  phone: '',
  message: '',
})

const sending = computed(() => false)

const submit = () => {
  router.post(`/b/${business.value.slug}/contact`, form, {
    onSuccess: () => {
      form.name = ''
      form.email = ''
      form.phone = ''
      form.message = ''
    }
  })
}
</script>
