<template>
  <MemberLayout>
    <Head :title="`Servicios - ${business?.name || ''}`" />

    <PageHeader
      title="Servicios"
      :breadcrumbs="breadcrumbs"
      :backHref="'/member/business-modules'"
    >
      <template #actions>
        <Link :href="`/member/businesses/${business?.id}/services/create`" class="btn btn-primary">
          <i class="bi bi-plus me-1"></i>Nuevo servicio
        </Link>
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
          <Link :href="`/member/businesses/${business?.id}/services/${row.id}/edit`" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-pencil"></i>
          </Link>
          <button class="btn btn-sm btn-outline-danger" @click="deleteService(row)">
            <i class="bi bi-trash"></i>
          </button>
        </div>
      </template>
    </BaseDataTable>
  </MemberLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import BaseDataTable from '@/Components/DataTable/BaseDataTable.vue'

const props = defineProps({
  business: Object,
  services: Object,
  locations: { type: Array, default: () => [] },
  dataTable: Object,
})

const business = computed(() => props.business)

const breadcrumbs = computed(() => [
  { label: 'Mis Negocios', href: '/member/business-modules' },
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

const onDataTableUpdated = (data) => {
  perPage.value = data.per_page
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
</script>
