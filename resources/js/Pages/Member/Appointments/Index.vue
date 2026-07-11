<template>
  <MemberLayout>
    <Head :title="`Citas - ${business?.name || ''}`" />

    <PageHeader
      title="Citas"
      :breadcrumbs="breadcrumbs"
      :backHref="'/member/business-modules'"
    >
      <template #actions>
        <button @click="openCreateModal" class="btn btn-primary btn-sm">
          <i class="bi bi-plus-lg me-1"></i>
          Nueva Cita
        </button>
      </template>
    </PageHeader>

    <BaseDataTable
      ref="dataTableRef"
      :endpoint="`/member/businesses/${business?.id}/appointments`"
      :columns="columns"
      :initial-data="dataTable"
      search-placeholder="Buscar citas..."
      empty-title="No hay citas"
      empty-text="Comienza creando tu primera cita."
      @updated="onDataTableUpdated"
    >
      <template #cell-appointment_date="{ row }">
        {{ formatDate(row.appointment_date) }}
      </template>

      <template #cell-start_time="{ row }">
        {{ row.start_time }}
      </template>

      <template #cell-customer_name="{ row }">
        <div>{{ row.customer_name }}</div>
        <small class="text-muted">{{ row.customer_email }}</small>
      </template>

      <template #cell-service="{ row }">
        {{ row.service?.name || '-' }}
      </template>

      <template #cell-location="{ row }">
        {{ row.location?.name || '-' }}
      </template>

      <template #cell-status="{ row }">
        <span :class="statusClass(row.status)" class="badge">
          {{ row.status_label }}
        </span>
      </template>

      <template #cell-actions="{ row }">
        <div class="actions">
          <Link :href="`/member/businesses/${business?.id}/appointments/${row.id}/edit`" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-pencil"></i>
          </Link>
          <button
            v-if="row.status !== 'cancelled'"
            class="btn btn-sm btn-outline-warning"
            @click="cancelAppointment(row)"
          >
            <i class="bi bi-x-lg"></i>
          </button>
          <button
            class="btn btn-sm btn-outline-danger"
            @click="deleteAppointment(row)"
          >
            <i class="bi bi-trash"></i>
          </button>
        </div>
      </template>
    </BaseDataTable>

    <div ref="modalElement" class="modal fade" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Nueva Cita</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form @submit.prevent="submitAppointment">
            <div class="modal-body">
              <div class="row g-3 mb-3">
                <div class="col-md-6">
                  <FieldText
                    id="appointment-name"
                    label="Nombre del cliente"
                    v-model="form.customer_name"
                    required
                  />
                </div>
                <div class="col-md-6">
                  <FieldEmail
                    id="appointment-email"
                    label="Email del cliente"
                    v-model="form.customer_email"
                    required
                  />
                </div>
                <div class="col-md-6">
                  <FieldPhone
                    id="appointment-phone"
                    label="Telefono"
                    v-model="form.customer_phone"
                  />
                </div>
                <div class="col-md-6">
                  <FieldSelect
                    id="appointment-service"
                    label="Servicio"
                    v-model="form.business_service_id"
                    required
                  >
                    <option :value="null" disabled>Seleccionar servicio</option>
                    <option v-for="svc in services" :key="svc.id" :value="svc.id">
                      {{ svc.name }} ({{ svc.duration_minutes }} min)
                    </option>
                  </FieldSelect>
                </div>
                <div class="col-md-6">
                  <FieldSelect
                    id="appointment-location"
                    label="Ubicacion"
                    v-model="form.business_location_id"
                  >
                    <option :value="null">Sin ubicacion</option>
                    <option v-for="loc in locations" :key="loc.id" :value="loc.id">
                      {{ loc.name }}
                    </option>
                  </FieldSelect>
                </div>
                <div class="col-md-3">
                  <FieldDate
                    id="appointment-date"
                    label="Fecha"
                    v-model="form.appointment_date"
                    :min="today"
                    required
                  />
                </div>
                <div class="col-md-3">
                  <FieldTime
                    id="appointment-time"
                    label="Hora"
                    v-model="form.start_time"
                    required
                  />
                </div>
                <div class="col-12">
                  <FieldTextarea
                    id="appointment-notes"
                    label="Notas"
                    v-model="form.notes"
                    :rows="2"
                    placeholder="Notas adicionales..."
                  />
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
import { computed, ref, nextTick, onMounted } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { Modal } from 'bootstrap'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import BaseDataTable from '@/Components/DataTable/BaseDataTable.vue'
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldEmail from '@/Components/Fields/FieldEmail.vue'
import FieldPhone from '@/Components/Fields/FieldPhone.vue'
import FieldSelect from '@/Components/Fields/FieldSelect.vue'
import FieldDate from '@/Components/Fields/FieldDate.vue'
import FieldTime from '@/Components/Fields/FieldTime.vue'
import FieldTextarea from '@/Components/Fields/FieldTextarea.vue'

const page = usePage()
const business = computed(() => page.props.business)
const appointments = computed(() => page.props.appointments || { data: [], links: [] })
const services = computed(() => page.props.services || [])
const locations = computed(() => page.props.locations || [])
const dataTable = computed(() => page.props.dataTable)

const breadcrumbs = computed(() => [
  { label: 'Mis Negocios', href: '/member/business-modules' },
  { label: 'Citas', active: true },
])

const columns = [
  { key: 'appointment_date', label: 'Fecha', sortable: true },
  { key: 'start_time', label: 'Hora', sortable: true },
  { key: 'customer_name', label: 'Cliente', sortable: true },
  { key: 'service', label: 'Servicio', sortable: false },
  { key: 'location', label: 'Ubicacion', sortable: false },
  { key: 'status', label: 'Estado', sortable: true },
  { key: 'actions', label: 'Acciones', sortable: false },
]

const dataTableRef = ref(null)
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

const onDataTableUpdated = (data) => {
  // Optional: handle data update
}

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
      if (dataTableRef.value) {
        dataTableRef.value.reload()
      }
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
  if (confirm('Estas seguro de cancelar esta cita?')) {
    router.post(`/member/businesses/${business.value.id}/appointments/${apt.id}/cancel`, {}, {
      preserveScroll: true,
      onSuccess: () => {
        if (dataTableRef.value) {
          dataTableRef.value.reload()
        }
      },
    })
  }
}

const deleteAppointment = (apt) => {
  if (confirm('Estas seguro de eliminar esta cita? Esta accion no se puede deshacer.')) {
    router.delete(`/member/businesses/${business.value.id}/appointments/${apt.id}`, {
      preserveScroll: true,
      onSuccess: () => {
        if (dataTableRef.value) {
          dataTableRef.value.reload()
        }
      },
    })
  }
}

onMounted(() => {
  appointmentModal = new Modal(modalElement.value)
  appointmentModal._element.addEventListener('hidden.bs.modal', () => {
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
  })
})
</script>
