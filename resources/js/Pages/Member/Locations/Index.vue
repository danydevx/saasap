<template>
  <MemberLayout>
    <Head :title="`Ubicaciones - ${business?.name || ''}`" />

    <PageHeader
      title="Ubicaciones"
      :breadcrumbs="breadcrumbs"
      :backHref="'/member/business-modules'"
    >
      <template #actions>
        <Link :href="`/member/businesses/${business?.id}/locations/create`" class="btn btn-primary btn-sm">
          <i class="bi bi-plus-lg me-1"></i>
          Nueva Ubicacion
        </Link>
      </template>
    </PageHeader>

    <BaseDataTable
      ref="dataTableRef"
      :endpoint="`/member/businesses/${business?.id}/locations`"
      :columns="columns"
      :initial-data="dataTable"
      search-placeholder="Buscar ubicaciones..."
      empty-title="No hay ubicaciones"
      empty-text="Comienza creando tu primera ubicacion."
      @updated="onDataTableUpdated"
    >
      <template #cell-name="{ row }">
        <div>
          <strong>{{ row.name }}</strong>
          <span v-if="row.is_primary" class="badge bg-warning ms-2">Principal</span>
        </div>
      </template>

      <template #cell-address_line_1="{ row }">
        <div>
          {{ row.address_line_1 }}
          <span v-if="row.city">, {{ row.city }}</span>
        </div>
      </template>

      <template #cell-phone="{ row }">
        {{ row.phone || '-' }}
      </template>

      <template #cell-is_primary="{ row }">
        <span v-if="row.is_primary" class="badge bg-warning">Principal</span>
        <span v-else class="text-muted">-</span>
      </template>

      <template #cell-is_active="{ row }">
        <span :class="row.is_active ? 'badge bg-success' : 'badge bg-secondary'">
          {{ row.is_active ? 'Activa' : 'Inactiva' }}
        </span>
      </template>

      <template #cell-actions="{ row }">
        <div class="actions">
          <Link :href="`/member/businesses/${business?.id}/locations/${row.id}/edit`" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-pencil"></i>
          </Link>
        </div>
      </template>
    </BaseDataTable>
  </MemberLayout>
</template>

<script setup>
import { computed, ref } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import BaseDataTable from '@/Components/DataTable/BaseDataTable.vue'

const page = usePage()
const business = computed(() => page.props.business)
const dataTable = computed(() => page.props.dataTable)

const breadcrumbs = computed(() => [
  { label: 'Mis Negocios', href: '/member/business-modules' },
  { label: 'Ubicaciones', active: true },
])

const columns = [
  { key: 'name', label: 'Nombre', sortable: true },
  { key: 'address_line_1', label: 'Direccion', sortable: false },
  { key: 'phone', label: 'Telefono', sortable: false },
  { key: 'is_primary', label: 'Principal', sortable: true },
  { key: 'is_active', label: 'Estado', sortable: true },
  { key: 'actions', label: 'Acciones', sortable: false },
]

const dataTableRef = ref(null)

const onDataTableUpdated = (data) => {
  // Optional: handle data update
}
</script>
