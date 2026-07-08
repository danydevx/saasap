<template>
  <MemberLayout>
    <Head :title="`Citas - ${business.name}`" />

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
      <div>
        <h1 class="h4 mb-1">{{ business.name }}</h1>
        <p class="text-muted mb-0">Gestiona las citas de tu negocio.</p>
      </div>
      <div>
        <button @click="openCreateModal" class="btn btn-primary btn-sm">
          <i class="bi bi-plus-lg me-1"></i>
          Nueva Cita
        </button>
      </div>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div v-if="$page.props.flash?.success" class="alert alert-success alert-dismissible fade show" role="alert">
          {{ $page.props.flash.success }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>

        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th scope="col">Fecha</th>
                <th scope="col">Hora</th>
                <th scope="col">Cliente</th>
                <th scope="col">Servicio</th>
                <th scope="col">Ubicacion</th>
                <th scope="col">Estado</th>
                <th scope="col" class="text-end">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="appointments.data.length === 0">
                <td colspan="7" class="text-center text-muted py-4">
                  No hay citas registradas.
                </td>
              </tr>
              <tr v-for="apt in appointments.data" :key="apt.id">
                <td>{{ formatDate(apt.appointment_date) }}</td>
                <td>{{ apt.start_time }}</td>
                <td>
                  <div>{{ apt.customer_name }}</div>
                  <small class="text-muted">{{ apt.customer_email }}</small>
                </td>
                <td>{{ apt.service?.name || '-' }}</td>
                <td>{{ apt.location?.name || '-' }}</td>
                <td>
                  <span :class="statusClass(apt.status)" class="badge">
                    {{ apt.status_label }}
                  </span>
                </td>
                <td class="text-end">
                  <div class="d-flex gap-1 justify-content-end">
                    <Link :href="`/member/businesses/${business.id}/appointments/${apt.id}/edit`" class="btn btn-sm btn-outline-primary">
                      <i class="bi bi-pencil"></i>
                    </Link>
                    <button
                      v-if="apt.status !== 'cancelled'"
                      class="btn btn-sm btn-outline-warning"
                      @click="cancelAppointment(apt)"
                    >
                      <i class="bi bi-x-lg"></i>
                    </button>
                    <button
                      class="btn btn-sm btn-outline-danger"
                      @click="deleteAppointment(apt)"
                    >
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-if="appointments.links" class="d-flex justify-content-center mt-4">
          <Pagination :links="appointments.links" />
        </div>
      </div>
    </div>

    <div ref="modalElement" class="modal fade" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Nueva Cita</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form @submit.prevent="submitAppointment">
            <div class="modal-body">
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label">Nombre del cliente *</label>
                  <input v-model="form.customer_name" type="text" class="form-control" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Email del cliente *</label>
                  <input v-model="form.customer_email" type="email" class="form-control" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Telefono</label>
                  <input v-model="form.customer_phone" type="text" class="form-control">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Servicio *</label>
                  <select v-model="form.business_service_id" class="form-select" required>
                    <option :value="null" disabled>Seleccionar servicio</option>
                    <option v-for="svc in services" :key="svc.id" :value="svc.id">
                      {{ svc.name }} ({{ svc.duration_minutes }} min)
                    </option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Ubicacion</label>
                  <select v-model="form.business_location_id" class="form-select">
                    <option :value="null">Sin ubicacion</option>
                    <option v-for="loc in locations" :key="loc.id" :value="loc.id">
                      {{ loc.name }}
                    </option>
                  </select>
                </div>
                <div class="col-md-3">
                  <label class="form-label">Fecha *</label>
                  <input v-model="form.appointment_date" type="date" class="form-control" required :min="today">
                </div>
                <div class="col-md-3">
                  <label class="form-label">Hora *</label>
                  <input v-model="form.start_time" type="time" class="form-control" required>
                </div>
                <div class="col-12">
                  <label class="form-label">Notas</label>
                  <textarea v-model="form.notes" class="form-control" rows="2" placeholder="Notas adicionales..."></textarea>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-primary" :disabled="sending">
                {{ sending ? 'Guardando...' : 'Crear Cita' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { computed, ref, nextTick } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { Modal } from 'bootstrap'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import Pagination from '@/Components/Member/Pagination.vue'

const page = usePage()
const business = computed(() => page.props.business)
const appointments = computed(() => page.props.appointments || { data: [], links: [] })
const services = computed(() => page.props.services || [])
const locations = computed(() => page.props.locations || [])

const modalElement = ref(null)
let appointmentModal = null
const sending = ref(false)
const today = new Date().toISOString().split('T')[0]

const form = ref({
  customer_name: '',
  customer_email: '',
  customer_phone: '',
  business_service_id: null,
  business_location_id: null,
  appointment_date: '',
  start_time: '',
  notes: '',
})

const openCreateModal = () => {
  form.value = {
    customer_name: '',
    customer_email: '',
    customer_phone: '',
    business_service_id: null,
    business_location_id: null,
    appointment_date: '',
    start_time: '',
    notes: '',
  }
  nextTick(() => {
    appointmentModal.show()
  })
}

const submitAppointment = () => {
  sending.value = true
  router.post(`/member/businesses/${business.value.id}/appointments`, form.value, {
    preserveScroll: true,
    onSuccess: () => {
      sending.value = false
      appointmentModal.hide()
    },
    onError: () => {
      sending.value = false
    },
  })
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('es-AR', {
    weekday: 'short',
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  })
}

const statusClass = (status) => {
  const classes = {
    pending: 'bg-warning text-dark',
    confirmed: 'bg-success',
    cancelled: 'bg-secondary',
    completed: 'bg-primary',
    no_show: 'bg-danger',
  }
  return classes[status] || 'bg-secondary'
}

const cancelAppointment = (apt) => {
  if (confirm('¿Estás seguro de cancelar esta cita?')) {
    router.post(`/member/businesses/${business.value.id}/appointments/${apt.id}/cancel`, {}, {
      preserveScroll: true,
    })
  }
}

const deleteAppointment = (apt) => {
  if (confirm('¿Estás seguro de eliminar esta cita? Esta acción no se puede deshacer.')) {
    router.delete(`/member/businesses/${business.value.id}/appointments/${apt.id}`, {
      preserveScroll: true,
    })
  }
}

import { onMounted } from 'vue'
onMounted(() => {
  appointmentModal = new Modal(modalElement.value)
})
</script>
