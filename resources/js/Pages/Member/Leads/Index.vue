<template>
  <MemberLayout>
    <Head :title="`Contactos - ${business?.name || ''}`" />

    <PageHeader
      title="Contactos"
      :breadcrumbs="breadcrumbs"
      :backHref="'/member/business-modules'"
    >
      <template #actions>
        <Link :href="`/member/businesses/${business?.id}/leads/create`" class="btn btn-primary btn-sm">
          <i class="bi bi-plus-lg me-1"></i>
          Nuevo Contacto
        </Link>
      </template>
    </PageHeader>

    <BaseDataTable
      ref="dataTableRef"
      :endpoint="`/member/businesses/${business?.id}/leads`"
      :columns="columns"
      :initial-data="dataTable"
      search-placeholder="Buscar contactos..."
      empty-title="No hay contactos"
      empty-text="Comienza creando tu primer contacto."
      @updated="onDataTableUpdated"
    >
      <template #cell-name="{ row }">
        <strong>{{ row.name }}</strong>
      </template>

      <template #cell-email="{ row }">
        <a :href="`mailto:${row.email}`">{{ row.email }}</a>
      </template>

      <template #cell-phone="{ row }">
        {{ row.phone || '-' }}
      </template>

      <template #cell-status="{ row }">
        <span :class="statusClass(row.status)" class="badge">
          {{ row.status_label }}
        </span>
      </template>

      <template #cell-source="{ row }">
        {{ row.source_label }}
      </template>

      <template #cell-created_at="{ row }">
        {{ formatDate(row.created_at) }}
      </template>

      <template #cell-actions="{ row }">
        <div class="actions">
          <Link :href="`/member/businesses/${business?.id}/leads/${row.id}`" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-eye"></i>
          </Link>
          <Link :href="`/member/businesses/${business?.id}/leads/${row.id}/edit`" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-pencil"></i>
          </Link>
          <button class="btn btn-sm btn-outline-danger" @click="deleteLead(row)">
            <i class="bi bi-trash"></i>
          </button>
        </div>
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

const page = usePage()
const business = computed(() => page.props.business)
const dataTable = computed(() => page.props.dataTable)

const breadcrumbs = computed(() => [
  { label: business.value?.name, href: '/member/business-modules' },
  { label: 'Contactos', active: true },
])

const columns = [
  { key: 'name', label: 'Nombre', sortable: true },
  { key: 'email', label: 'Email', sortable: false },
  { key: 'phone', label: 'Telefono', sortable: false },
  { key: 'status', label: 'Estado', sortable: true },
  { key: 'source', label: 'Fuente', sortable: false },
  { key: 'created_at', label: 'Fecha', sortable: true },
  { key: 'actions', label: 'Acciones', sortable: false },
]

const dataTableRef = ref(null)

const onDataTableUpdated = (data) => {
  // Optional: handle data update
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('es-AR', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  })
}

const statusClass = (status) => {
  const classes = {
    new: 'bg-info',
    contacted: 'bg-primary',
    qualified: 'bg-success',
    converted: 'bg-dark',
    lost: 'bg-secondary',
  }
  return classes[status] || 'bg-secondary'
}

const deleteLead = (lead) => {
  if (confirm('¿Estás seguro de eliminar este contacto?')) {
    router.delete(`/member/businesses/${business.value.id}/leads/${lead.id}`, {
      preserveScroll: true,
      onSuccess: () => {
        if (dataTableRef.value) {
          dataTableRef.value.reload()
        }
      },
    })
  }
}
</script>
