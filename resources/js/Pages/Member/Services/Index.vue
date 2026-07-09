<template>
  <MemberLayout>
    <Head :title="`Servicios - ${business?.name || ''}`" />

    <PageHeader
      title="Servicios"
      :breadcrumbs="breadcrumbs"
      :backHref="'/member/business-modules'"
    >
      <template #actions>
        <button class="btn btn-primary" @click="openCreateModal">
          <i class="bi bi-plus me-1"></i>Nuevo servicio
        </button>
      </template>
    </PageHeader>

    <BaseDataTable
      ref="dataTableRef"
      :endpoint="`/member/businesses/${business?.id}/services`"
      :columns="columns"
      :initial-data="dataTable"
      :initial-per-page="perPage"
      :reorderable="true"
      :reorder-endpoint="`/member/businesses/${business?.id}/services/reorder`"
      search-placeholder="Buscar servicios..."
      empty-title="No hay servicios"
      empty-text="Comienza creando tu primer servicio."
      @updated="onDataTableUpdated"
    >
      <template #cell-name="{ row }">
        <div class="d-flex align-items-center gap-2">
          <img
            v-if="row.image"
            :src="row.image"
            class="rounded"
            style="width: 40px; height: 40px; object-fit: cover;"
          />
          <div v-else class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
            <i class="bi bi-briefcase text-muted"></i>
          </div>
          <div>
            <strong>{{ row.name }}</strong>
            <p v-if="row.description" class="text-muted small mb-0">{{ row.description.substring(0, 50) }}...</p>
          </div>
        </div>
      </template>

      <template #cell-price="{ value, row }">
        <span v-if="row.price" class="fw-semibold">${{ row.price }}</span>
        <span v-else class="text-muted">—</span>
      </template>

      <template #cell-duration_minutes="{ value }">
        {{ value }} min
      </template>

      <template #cell-allows_online_booking="{ value }">
        <span v-if="value" class="badge bg-success">Si</span>
        <span v-else class="badge bg-secondary">No</span>
      </template>

      <template #cell-is_active="{ value }">
        <span v-if="value" class="badge bg-success">Activo</span>
        <span v-else class="badge bg-secondary">Inactivo</span>
      </template>

      <template #cell-actions="{ row }">
        <div class="actions">
          <button class="btn btn-sm btn-outline-primary" @click="openEditModal(row)">
            <i class="bi bi-pencil"></i>
          </button>
          <button class="btn btn-sm btn-outline-danger" @click="deleteService(row)">
            <i class="bi bi-trash"></i>
          </button>
        </div>
      </template>
    </BaseDataTable>

    <div ref="modalElement" class="modal fade" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ editingService ? 'Editar servicio' : 'Nuevo servicio' }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form @submit.prevent="submitService">
            <div class="modal-body">
              <div class="row g-3">
                <div class="col-md-8">
                  <FieldText
                    id="service-name"
                    label="Nombre del servicio"
                    v-model="form.name"
                    required
                  />
                </div>

                <div class="col-md-4">
                  <FieldNumber
                    id="service-price"
                    label="Precio"
                    v-model="form.price"
                  />
                </div>

                <div class="col-12">
                  <FieldTextarea
                    id="service-description"
                    label="Descripcion"
                    v-model="form.description"
                    :rows="3"
                  />
                </div>

                <div class="col-md-4">
                  <label class="form-label">Imagen del servicio</label>
                  <input ref="imageInput" type="file" class="form-control" accept="image/jpeg,image/png" @change="handleImageChange">
                  <small class="text-muted d-block">JPG o PNG, max 10MB</small>
                  <div v-if="imagePreview" class="mt-2">
                    <img :src="imagePreview" class="img-thumbnail" style="max-height: 150px;">
                  </div>
                </div>

                <div class="col-md-4">
                  <FieldNumber
                    id="service-duration"
                    label="Duracion (minutos)"
                    v-model="form.duration_minutes"
                    required
                  />
                </div>

                <div class="col-md-4">
                  <FieldNumber
                    id="service-deposit-amount"
                    label="Monto deposito"
                    v-model="form.deposit_amount"
                  />
                </div>

                <div class="col-md-6">
                  <FieldSwitch
                    id="service-deposit-required"
                    label="Requiere deposito"
                    v-model="form.deposit_required"
                  />
                </div>

                <div class="col-md-6">
                  <FieldPhone
                    id="service-whatsapp"
                    label="WhatsApp"
                    placeholder="+54 9 11 1234-5678"
                    v-model="form.whatsapp_contact"
                  />
                </div>

                <div class="col-md-6">
                  <FieldSwitch
                    id="service-online-booking"
                    label="Permite reserva online"
                    v-model="form.allows_online_booking"
                  />
                </div>

                <div class="col-md-6">
                  <FieldSwitch
                    id="service-active"
                    label="Servicio activo"
                    v-model="form.is_active"
                  />
                </div>

                <div class="col-md-6">
                  <FieldNumber
                    id="service-sort-order"
                    label="Orden"
                    placeholder="0"
                    v-model="form.sort_order"
                  />
                  <small class="text-muted">Menor numero aparece primero.</small>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-primary" :disabled="sending">
                <span v-if="sending">Guardando...</span>
                <span v-else>{{ editingService ? 'Actualizar' : 'Crear' }}</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import { Modal } from 'bootstrap'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import BaseDataTable from '@/Components/DataTable/BaseDataTable.vue'
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldNumber from '@/Components/Fields/FieldNumber.vue'
import FieldTextarea from '@/Components/Fields/FieldTextarea.vue'
import FieldSwitch from '@/Components/Fields/FieldSwitch.vue'
import FieldPhone from '@/Components/Fields/FieldPhone.vue'

const props = defineProps({
  business: Object,
  services: Object,
  locations: { type: Array, default: () => [] },
  dataTable: Object,
})

const business = computed(() => props.business)

const breadcrumbs = computed(() => [
  { label: business.value?.name, href: '/member/business-modules' },
  { label: 'Servicios', active: true },
])

const perPage = ref(10)

const columns = [
  { key: 'name', label: 'Nombre', sortable: true },
  { key: 'price', label: 'Precio', sortable: true },
  { key: 'duration_minutes', label: 'Duracion', sortable: true },
  { key: 'allows_online_booking', label: 'Reservas online', sortable: true },
  { key: 'is_active', label: 'Estado', sortable: true },
  { key: 'actions', label: 'Acciones', sortable: false },
]

const dataTableRef = ref(null)
const modalElement = ref(null)
const imageInput = ref(null)
const imagePreview = ref(null)
let serviceModal = null
const editingService = ref(null)
const sending = ref(false)

const form = useForm({
  name: '',
  description: '',
  image: null,
  duration_minutes: 30,
  price: null,
  deposit_required: false,
  deposit_amount: null,
  allows_online_booking: true,
  whatsapp_contact: '',
  is_active: true,
  sort_order: 0,
})

const onDataTableUpdated = (data) => {
  perPage.value = data.per_page
}

const openCreateModal = () => {
  editingService.value = null
  form.reset()
  imagePreview.value = null
  nextTick(() => serviceModal.show())
}

const openEditModal = (service) => {
  editingService.value = service
  form.name = service.name
  form.description = service.description || ''
  form.duration_minutes = service.duration_minutes
  form.price = service.price
  form.deposit_required = service.deposit_required || false
  form.deposit_amount = service.deposit_amount
  form.allows_online_booking = service.allows_online_booking ?? true
  form.whatsapp_contact = service.whatsapp_contact || ''
  form.is_active = service.is_active ?? true
  form.sort_order = service.sort_order || 0
  imagePreview.value = service.image || null
  nextTick(() => serviceModal.show())
}

const closeModal = () => {
  serviceModal.hide()
  editingService.value = null
  form.reset()
  imagePreview.value = null
}

const handleImageChange = (e) => {
  const file = e.target.files[0]
  if (!file) return

  const maxSize = 10 * 1024 * 1024
  if (file.size > maxSize) {
    alert('El archivo supera el tamano maximo de 10MB.')
    return
  }

  const allowedTypes = ['image/jpeg', 'image/png']
  if (!allowedTypes.includes(file.type)) {
    alert('Solo se permiten archivos JPG o PNG.')
    return
  }

  form.image = file
  const reader = new FileReader()
  reader.onload = (e) => {
    imagePreview.value = e.target.result
  }
  reader.readAsDataURL(file)
}

const submitService = () => {
  sending.value = true

  const url = editingService.value
    ? `/member/businesses/${business.value.id}/services/${editingService.value.id}`
    : `/member/businesses/${business.value.id}/services`

  const method = editingService.value ? 'post' : 'post'

  router[method](url, {
    ...form.data(),
    _method: editingService.value ? 'PUT' : 'POST',
  }, {
    forceFormData: true,
    preserveScroll: true,
    onSuccess: () => {
      closeModal()
      sending.value = false
      if (dataTableRef.value) {
        dataTableRef.value.reload()
      }
    },
    onError: (errors) => {
      sending.value = false
      console.error('Errors:', errors)
    },
  })
}

const deleteService = (service) => {
  if (!confirm(`¿Estas seguro de eliminar "${service.name}"?`)) {
    return
  }

  router.delete(`/member/businesses/${business.value.id}/services/${service.id}`, {
    preserveScroll: true,
    onSuccess: () => {
      if (dataTableRef.value) {
        dataTableRef.value.reload()
      }
    },
  })
}

onMounted(() => {
  serviceModal = new Modal(modalElement.value)
  serviceModal._element.addEventListener('hidden.bs.modal', () => {
    editingService.value = null
    form.reset()
    imagePreview.value = null
  })
})
</script>
