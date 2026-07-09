<template>
  <MemberLayout>
    <Head :title="`Slots - ${business.name}`" />

    <PageHeader
      title="Turnos de Disponibilidad"
      :breadcrumbs="breadcrumbs"
      :backHref="'/member/business-modules'"
    />

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
      <div>
        <p class="text-muted mb-0">Gestiona los turnos disponibles para reservas online.</p>
      </div>
      <div>
        <button type="button" class="btn btn-primary btn-sm" @click="openCreateModal">
          <i class="bi bi-plus me-1"></i>Nuevo Turno
        </button>
      </div>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div v-if="$page.props.flash?.success" class="alert alert-success alert-dismissible fade show" role="alert">
          {{ $page.props.flash.success }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <div v-if="$page.props.flash?.error" class="alert alert-danger alert-dismissible fade show" role="alert">
          {{ $page.props.flash.error }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>

        <div v-if="slots.data.length === 0" class="text-center text-muted py-5">
          No hay turnos de disponibilidad. Crea uno para permitir reservas online.
        </div>

        <div v-else class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th scope="col">Fecha</th>
                <th scope="col">Hora</th>
                <th scope="col">Servicio</th>
                <th scope="col">Ubicacion</th>
                <th scope="col">Cupos</th>
                <th scope="col">Estado</th>
                <th scope="col" class="text-end">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="slot in slots.data" :key="slot.id">
                <td>{{ slot.specific_date ? formatDate(slot.specific_date) : dayName(slot.day_of_week) }}</td>
                <td>{{ slot.start_time }} - {{ slot.end_time }}</td>
                <td>{{ slot.service?.name || '-' }}</td>
                <td>{{ slot.location?.name || 'Todas' }}</td>
                <td>{{ slot.slots_available }}</td>
                <td>
                  <span :class="slot.is_available ? 'bg-success' : 'bg-secondary'" class="badge">
                    {{ slot.is_available ? 'Activo' : 'Inactivo' }}
                  </span>
                </td>
                <td class="text-end">
                  <button
                    class="btn btn-sm btn-outline-primary"
                    @click="openEditModal(slot)"
                    title="Editar"
                  >
                    <i class="bi bi-pencil"></i>
                  </button>
                  <button
                    class="btn btn-sm"
                    :class="slot.is_available ? 'btn-outline-warning' : 'btn-outline-success'"
                    @click="toggleSlot(slot)"
                    :disabled="deleting === slot.id"
                    title="Cambiar estado"
                  >
                    <i :class="slot.is_available ? 'bi bi-x-lg' : 'bi bi-check-lg'"></i>
                  </button>
                  <button
                    class="btn btn-sm btn-outline-danger ms-1"
                    @click="deleteSlot(slot)"
                    :disabled="deleting === slot.id"
                    title="Eliminar"
                  >
                    <i class="bi bi-trash"></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-if="slots.links" class="d-flex justify-content-center mt-4">
          <Pagination :links="slots.links" />
        </div>
      </div>
    </div>

    <div ref="createModalElement" class="modal fade" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Nuevo Turno</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form @submit.prevent="createSlot">
            <div class="modal-body">
              <div class="mb-3">
                <FieldSelect
                  id="slot-service"
                  label="Servicio"
                  v-model="form.business_service_id"
                  required
                >
                  <option value="">Seleccionar...</option>
                  <option v-for="svc in services" :key="svc.id" :value="svc.id">{{ svc.name }}</option>
                </FieldSelect>
              </div>
              <div class="mb-3">
                <FieldSelect
                  id="slot-location"
                  label="Ubicacion"
                  v-model="form.business_location_id"
                >
                  <option :value="null">Todas las ubicaciones</option>
                  <option v-for="loc in locations" :key="loc.id" :value="loc.id">{{ loc.name }}</option>
                </FieldSelect>
              </div>
              <div class="mb-3">
                <FieldSelect
                  id="slot-schedule-type"
                  label="Tipo de schedule"
                  v-model="scheduleType"
                >
                  <option value="date">Fecha especifica</option>
                  <option value="recurring">Recurrente (dia de la semana)</option>
                </FieldSelect>
              </div>
              <div v-if="scheduleType === 'date'" class="mb-3">
                <FieldDate
                  id="slot-date"
                  label="Fecha"
                  v-model="form.specific_date"
                  :min="minDate"
                  required
                />
              </div>
              <div v-else class="mb-3">
                <FieldSelect
                  id="slot-day"
                  label="Dia de la semana"
                  v-model="form.day_of_week"
                  required
                >
                  <option :value="0">Domingo</option>
                  <option :value="1">Lunes</option>
                  <option :value="2">Martes</option>
                  <option :value="3">Miercoles</option>
                  <option :value="4">Jueves</option>
                  <option :value="5">Viernes</option>
                  <option :value="6">Sabado</option>
                </FieldSelect>
              </div>
              <div class="row g-3">
                <div class="col-6">
                  <FieldTime
                    id="slot-start"
                    label="Hora inicio"
                    v-model="form.start_time"
                    required
                  />
                </div>
                <div class="col-6">
                  <FieldTime
                    id="slot-end"
                    label="Hora fin"
                    v-model="form.end_time"
                    required
                  />
                </div>
              </div>
              <div class="mb-3 mt-3">
                <FieldNumber
                  id="slot-capacity"
                  label="Cupos disponibles"
                  v-model="form.slots_available"
                />
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-primary" :disabled="creating">
                {{ creating ? 'Creando...' : 'Crear Turno' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div ref="editModalElement" class="modal fade" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Editar Turno</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form @submit.prevent="updateSlot">
            <div class="modal-body">
              <div class="mb-3">
                <FieldSelect
                  id="edit-slot-service"
                  label="Servicio"
                  v-model="editForm.business_service_id"
                  required
                >
                  <option value="">Seleccionar...</option>
                  <option v-for="svc in services" :key="svc.id" :value="svc.id">{{ svc.name }}</option>
                </FieldSelect>
              </div>
              <div class="mb-3">
                <FieldSelect
                  id="edit-slot-location"
                  label="Ubicacion"
                  v-model="editForm.business_location_id"
                >
                  <option :value="null">Todas las ubicaciones</option>
                  <option v-for="loc in locations" :key="loc.id" :value="loc.id">{{ loc.name }}</option>
                </FieldSelect>
              </div>
              <div class="mb-3">
                <FieldSelect
                  id="edit-slot-schedule-type"
                  label="Tipo de schedule"
                  v-model="editScheduleType"
                >
                  <option value="date">Fecha especifica</option>
                  <option value="recurring">Recurrente (dia de la semana)</option>
                </FieldSelect>
              </div>
              <div v-if="editScheduleType === 'date'" class="mb-3">
                <FieldDate
                  id="edit-slot-date"
                  label="Fecha"
                  v-model="editForm.specific_date"
                  required
                />
              </div>
              <div v-else class="mb-3">
                <FieldSelect
                  id="edit-slot-day"
                  label="Dia de la semana"
                  v-model="editForm.day_of_week"
                  required
                >
                  <option :value="0">Domingo</option>
                  <option :value="1">Lunes</option>
                  <option :value="2">Martes</option>
                  <option :value="3">Miercoles</option>
                  <option :value="4">Jueves</option>
                  <option :value="5">Viernes</option>
                  <option :value="6">Sabado</option>
                </FieldSelect>
              </div>
              <div class="row g-3">
                <div class="col-6">
                  <FieldTime
                    id="edit-slot-start"
                    label="Hora inicio"
                    v-model="editForm.start_time"
                    required
                  />
                </div>
                <div class="col-6">
                  <FieldTime
                    id="edit-slot-end"
                    label="Hora fin"
                    v-model="editForm.end_time"
                    required
                  />
                </div>
              </div>
              <div class="row g-3 mt-1">
                <div class="col-6">
                  <FieldNumber
                    id="edit-slot-capacity"
                    label="Cupos disponibles"
                    v-model="editForm.slots_available"
                  />
                </div>
                <div class="col-6 d-flex align-items-end">
                  <FieldSwitch
                    id="edit-slot-active"
                    label="Activo"
                    v-model="editForm.is_available"
                  />
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-primary" :disabled="saving">
                {{ saving ? 'Guardando...' : 'Guardar Cambios' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { computed, reactive, ref, onMounted, nextTick } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { Modal } from 'bootstrap'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import Pagination from '@/Components/Admin/Pagination.vue'
import FieldSelect from '@/Components/Fields/FieldSelect.vue'
import FieldDate from '@/Components/Fields/FieldDate.vue'
import FieldTime from '@/Components/Fields/FieldTime.vue'
import FieldNumber from '@/Components/Fields/FieldNumber.vue'
import FieldSwitch from '@/Components/Fields/FieldSwitch.vue'

const page = usePage()
const business = computed(() => page.props.business)
const slots = computed(() => page.props.slots || { data: [] })
const services = computed(() => page.props.services || [])
const locations = computed(() => page.props.locations || [])

const breadcrumbs = computed(() => [
  { label: business.value.name, href: '/member/business-modules' },
  { label: 'Turnos', active: true },
])

const createModalElement = ref(null)
const editModalElement = ref(null)
let createModal = null
let editModal = null

const creating = ref(false)
const saving = ref(false)
const deleting = ref(null)
const scheduleType = ref('date')
const editScheduleType = ref('date')
const editingSlot = ref(null)

const form = reactive({
  business_service_id: '',
  business_location_id: null,
  day_of_week: null,
  specific_date: '',
  start_time: '09:00',
  end_time: '10:00',
  slots_available: 1,
})

const editForm = reactive({
  id: null,
  business_service_id: '',
  business_location_id: null,
  day_of_week: null,
  specific_date: '',
  start_time: '',
  end_time: '',
  slots_available: 1,
  is_available: true,
})

const minDate = computed(() => new Date().toISOString().split('T')[0])

const dayName = (day) => {
  const days = ['Domingo', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado']
  return days[day] || '-'
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('es-AR')
}

const resetSchedule = () => {
  form.day_of_week = null
  form.specific_date = ''
}

const resetEditSchedule = () => {
  editForm.day_of_week = null
  editForm.specific_date = ''
}

const openCreateModal = () => {
  form.business_service_id = ''
  form.business_location_id = null
  form.day_of_week = null
  form.specific_date = ''
  form.start_time = '09:00'
  form.end_time = '10:00'
  form.slots_available = 1
  scheduleType.value = 'date'
  nextTick(() => createModal.show())
}

const closeCreateModal = () => {
  createModal.hide()
}

const openEditModal = (slot) => {
  editingSlot.value = slot
  editForm.id = slot.id
  editForm.business_service_id = slot.business_service_id
  editForm.business_location_id = slot.business_location_id
  editForm.start_time = slot.start_time
  editForm.end_time = slot.end_time
  editForm.slots_available = slot.slots_available
  editForm.is_available = slot.is_available

  if (slot.specific_date) {
    editScheduleType.value = 'date'
    editForm.specific_date = slot.specific_date
    editForm.day_of_week = null
  } else {
    editScheduleType.value = 'recurring'
    editForm.day_of_week = slot.day_of_week
    editForm.specific_date = ''
  }

  nextTick(() => editModal.show())
}

const closeEditModal = () => {
  editModal.hide()
}

const createSlot = () => {
  creating.value = true
  const data = { ...form }
  if (scheduleType.value === 'recurring') {
    delete data.specific_date
  } else {
    delete data.day_of_week
  }
  router.post(`/member/businesses/${business.value.id}/slots`, data, {
    onFinish: () => {
      creating.value = false
      closeCreateModal()
    },
  })
}

const updateSlot = () => {
  saving.value = true
  const data = { ...editForm }
  if (editScheduleType.value === 'recurring') {
    delete data.specific_date
  } else {
    delete data.day_of_week
  }
  delete data.id
  router.put(`/member/businesses/${business.value.id}/slots/${editingSlot.value.id}`, data, {
    onFinish: () => {
      saving.value = false
      closeEditModal()
    },
  })
}

const toggleSlot = (slot) => {
  router.put(`/member/businesses/${business.value.id}/slots/${slot.id}`, {
    is_available: !slot.is_available,
  }, { preserveScroll: true })
}

const deleteSlot = (slot) => {
  if (confirm('Eliminar este turno?')) {
    deleting.value = slot.id
    router.delete(`/member/businesses/${business.value.id}/slots/${slot.id}`, {
      preserveScroll: true,
      onFinish: () => {
        deleting.value = null
      },
    })
  }
}

onMounted(() => {
  createModal = new Modal(createModalElement.value)
  editModal = new Modal(editModalElement.value)
})
</script>
