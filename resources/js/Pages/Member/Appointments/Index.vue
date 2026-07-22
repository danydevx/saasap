<template>
  <MemberLayout>
    <Head :title="`Citas - ${business?.name || ''}`" />

    <PageHeader
      title="Citas"
      :breadcrumbs="breadcrumbs"
      :backHref="'/member/business-modules'"
    >
      <template #actions>
        <button
          v-if="selectedIds.length > 0"
          class="btn btn-danger btn-sm"
          @click="deleteSelected"
          :disabled="deleting"
        >
          <i class="bi bi-trash me-1"></i>
          Eliminar ({{ selectedIds.length }})
        </button>
        <Link :href="`/member/businesses/${business?.id}/appointments/create`" class="btn btn-primary btn-sm">
          <i class="bi bi-plus-lg me-1"></i>
          Nueva Cita
        </Link>
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
      <template #cell-checkbox="{ row }">
        <BulkSelectRowCheckbox
          :id="row.id"
          v-model:selectedIds="selectedIds"
        />
      </template>

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

      <template #header-actions>
        <BulkSelect
          v-model:selectedIds="selectedIds"
          :current-page-ids="currentPageIds"
          :delete-endpoint="`/member/businesses/${business?.id}/appointments/bulk-delete`"
          item-name="citas"
          @deleted="onBulkDeleted"
        />
      </template>
    </BaseDataTable>
  </MemberLayout>
</template>

<script setup>
import { computed, ref } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import BaseDataTable from '@/Components/DataTable/BaseDataTable.vue'
import { BulkSelect, BulkSelectRowCheckbox } from '@/Components/BulkSelect'

const page = usePage()
const business = computed(() => page.props.business)
const dataTable = computed(() => page.props.dataTable)
const businessMenu = computed(() => page.props.businessMenu || [])

const breadcrumbs = computed(() => {
  const path = window.location.pathname
  const businessMatch = path.match(/^\/member\/businesses\/(\d+)/)
  if (businessMatch) {
    const businessId = parseInt(businessMatch[1])
    const biz = businessMenu.value.find(b => b.id === businessId)
    if (biz) {
      return [
        { label: 'Mis Negocios', href: '/member/business-modules' },
        { label: biz.name, href: `/member/businesses/${biz.id}/edit` },
        { label: 'Citas', active: true },
      ]
    }
  }
  return [
    { label: 'Mis Negocios', href: '/member/business-modules' },
    { label: 'Citas', active: true },
  ]
})

const columns = [
  { key: 'checkbox', label: '', sortable: false, width: '40px' },
  { key: 'appointment_date', label: 'Fecha', sortable: true },
  { key: 'start_time', label: 'Hora', sortable: true },
  { key: 'customer_name', label: 'Cliente', sortable: true },
  { key: 'service', label: 'Servicio', sortable: false },
  { key: 'location', label: 'Ubicacion', sortable: false },
  { key: 'status', label: 'Estado', sortable: true },
  { key: 'actions', label: 'Acciones', sortable: false },
]

const dataTableRef = ref(null)
const deleting = ref(null)
const selectedIds = ref([])

const currentPageIds = computed(() => {
  if (!dataTable.value?.data) return []
  return dataTable.value.data.map(row => row.id)
})

const onDataTableUpdated = (data) => {
  selectedIds.value = []
}

const onBulkDeleted = () => {
  if (dataTableRef.value) {
    dataTableRef.value.reload()
  }
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
    deleting.value = apt.id
    router.delete(`/member/businesses/${business.value.id}/appointments/${apt.id}`, {
      preserveScroll: true,
      onFinish: () => {
        deleting.value = null
        if (dataTableRef.value) {
          dataTableRef.value.reload()
        }
      },
    })
  }
}

const deleteSelected = () => {
  if (selectedIds.value.length === 0) return

  const count = selectedIds.value.length
  if (confirm(`Eliminar ${count} cita${count > 1 ? 's' : ''} seleccionada${count > 1 ? 's' : ''}?`)) {
    deleting.value = true
    router.post(`/member/businesses/${business.value.id}/appointments/bulk-delete`, {
      ids: selectedIds.value,
    }, {
      preserveScroll: true,
      onSuccess: () => {
        selectedIds.value = []
        if (dataTableRef.value) {
          dataTableRef.value.reload()
        }
      },
      onFinish: () => {
        deleting.value = false
      },
    })
  }
}
</script>
