<template>
  <div class="minisite">
    <div class="bg-light py-4 mb-4">
      <div class="container">
        <h1 class="mb-0">Reservar turno</h1>
      </div>
    </div>

    <div class="container py-4">
      <div class="row">
        <div class="col-lg-8">
          <div v-if="$page.props.flash?.success" class="alert alert-success alert-dismissible fade show" role="alert">
            {{ $page.props.flash.success }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>

          <div class="card">
            <div class="card-body">
              <form @submit.prevent="submit">
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label">Servicio *</label>
                    <select v-model="form.service_id" @change="onServiceChange" class="form-select" required>
                      <option value="">Seleccionar servicio...</option>
                      <option v-for="svc in services" :key="svc.id" :value="svc.id">
                        {{ svc.name }} - {{ formatPrice(svc.price) }} ({{ svc.duration_minutes }} min)
                      </option>
                    </select>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">Ubicacion *</label>
                    <select v-model="form.location_id" class="form-select" required>
                      <option value="">Seleccionar ubicacion...</option>
                      <option v-for="loc in locations" :key="loc.id" :value="loc.id">
                        {{ loc.name }}
                      </option>
                    </select>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">Fecha *</label>
                    <input type="date" v-model="form.appointment_date" class="form-control" required :min="minDate" />
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">Horario disponible *</label>
                    <select v-model="form.start_time" class="form-select" required>
                      <option value="">Seleccionar horario...</option>
                      <option v-for="slot in filteredSlots" :key="slot.id" :value="slot.start_time">
                        {{ slot.start_time }} - {{ slot.end_time || '' }}
                      </option>
                    </select>
                  </div>

                  <div class="col-12"><hr></div>

                  <div class="col-md-4">
                    <label class="form-label">Tu nombre *</label>
                    <input type="text" v-model="form.customer_name" class="form-control" required />
                  </div>

                  <div class="col-md-4">
                    <label class="form-label">Tu email *</label>
                    <input type="email" v-model="form.customer_email" class="form-control" required />
                  </div>

                  <div class="col-md-4">
                    <label class="form-label">Tu telefono</label>
                    <input type="tel" v-model="form.customer_phone" class="form-control" />
                  </div>

                  <div class="col-12">
                    <label class="form-label">Notas adicionales</label>
                    <textarea v-model="form.notes" class="form-control" rows="3"></textarea>
                  </div>

                  <div class="col-12">
                    <button type="submit" class="btn btn-primary btn-lg w-100" :disabled="sending">
                      <span v-if="sending">Confirmando...</span>
                      <span v-else>Confirmar reserva</span>
                    </button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>

        <div class="col-lg-4">
          <div class="card bg-light">
            <div class="card-body">
              <h5 class="card-title">Informacion de tu reserva</h5>
              <p class="text-muted small">Complete el formulario para reservar su turno</p>
              <ul class="list-unstyled mb-0">
                <li v-if="selectedService" class="mb-2">
                  <i class="bi bi-scissors me-2 text-primary"></i>{{ selectedService.name }}
                </li>
                <li v-if="selectedService" class="mb-2">
                  <i class="bi bi-clock me-2 text-primary"></i>{{ selectedService.duration_minutes }} minutos
                </li>
                <li v-if="selectedService" class="mb-2">
                  <i class="bi bi-currency-dollar me-2 text-primary"></i>{{ formatPrice(selectedService.price) }}
                </li>
              </ul>
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
import { computed, reactive } from 'vue'
import { Link, usePage, router } from '@inertiajs/vue3'

const page = usePage()
const business = computed(() => page.props.business)
const services = computed(() => page.props.services || [])
const locations = computed(() => page.props.locations || [])
const availableSlots = computed(() => page.props.availableSlots || [])
const selectedService = computed(() => page.props.selectedService)
const sending = computed(() => false)

const minDate = computed(() => {
  return new Date().toISOString().split('T')[0]
})

const form = reactive({
  service_id: selectedService.value?.id || '',
  location_id: page.props.selectedLocation?.id || '',
  appointment_date: '',
  start_time: '',
  customer_name: '',
  customer_email: '',
  customer_phone: '',
  notes: '',
})

const filteredSlots = computed(() => {
  if (!form.appointment_date) return availableSlots.value
  return availableSlots.value.filter(slot => {
    if (slot.specific_date) {
      return slot.specific_date === form.appointment_date
    }
    return true
  })
})

const formatPrice = (price) => {
  if (!price && price !== 0) return '-'
  return new Intl.NumberFormat('es-AR', { style: 'currency', currency: 'ARS' }).format(price)
}

const onServiceChange = () => {
  router.get(`/b/${business.value.slug}/book`, {
    service: form.service_id,
    location: form.location_id,
  }, { preserveState: true })
}

const submit = () => {
  router.post(`/b/${business.value.slug}/book`, form)
}
</script>
