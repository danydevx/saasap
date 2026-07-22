<template>
  <MemberLayout>
    <Head :title="`Preguntas Frecuentes - ${business?.name || ''}`" />

    <PageHeader
      title="Preguntas Frecuentes"
      :breadcrumbs="breadcrumbs"
      :backHref="'/member/business-modules'"
    >
      <template #actions>
        <Link :href="`/member/businesses/${business?.id}/faqs/create`" class="btn btn-primary">
          <i class="bi bi-plus me-1"></i>Nueva Pregunta
        </Link>
      </template>
    </PageHeader>

    <div class="mb-3 d-flex gap-2">
      <Link
        :href="`/member/businesses/${business?.id}/faqs`"
        class="btn btn-outline-secondary btn-sm"
      >
        Todas
      </Link>
      <Link
        :href="`/member/businesses/${business?.id}/faq-categories`"
        class="btn btn-outline-secondary btn-sm"
      >
        <i class="bi bi-folder me-1"></i>Categorias
      </Link>
    </div>

    <div class="row mb-3">
      <div class="col-md-4">
        <BulkSelect
          v-model:selectedIds="selectedIds"
          :current-page-ids="currentPageIds"
          :delete-endpoint="`/member/businesses/${business?.id}/faqs/bulk-delete`"
          item-name="preguntas"
          @deleted="onBulkDeleted"
        />
      </div>
    </div>

    <BaseDataTable
      ref="dataTableRef"
      :endpoint="`/member/businesses/${business?.id}/faqs`"
      :columns="columns"
      :initial-data="dataTable"
      :initial-per-page="perPage"
      :reorderable="true"
      :reorder-endpoint="`/member/businesses/${business?.id}/faqs/reorder`"
      search-placeholder="Buscar preguntas..."
      empty-title="No hay preguntas frecuentes"
      empty-text="Comienza creando tu primera pregunta frecuente."
      @updated="onDataTableUpdated"
    >
      <template #cell-checkbox="{ row }">
        <BulkSelectRowCheckbox
          :id="row.id"
          v-model:selectedIds="selectedIds"
        />
      </template>
      <template #cell-question="{ row }">
        <div class="d-flex align-items-center gap-2">
          <img
            v-if="row.image"
            :src="row.image"
            class="rounded"
            style="width: 40px; height: 40px; object-fit: cover;"
          />
          <div v-else class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
            <i class="bi bi-question-circle text-muted"></i>
          </div>
          <div>
            <strong>{{ row.question }}</strong>
            <p v-if="row.category" class="text-muted small mb-0">
              <span class="badge bg-light text-dark">{{ row.category.name }}</span>
            </p>
          </div>
        </div>
      </template>

      <template #cell-answer="{ value }">
        <span class="text-muted small">{{ value.substring(0, 80) }}{{ value.length > 80 ? '...' : '' }}</span>
      </template>

      <template #cell-is_active="{ value }">
        <span v-if="value" class="badge bg-success">Activo</span>
        <span v-else class="badge bg-secondary">Inactivo</span>
      </template>

      <template #cell-actions="{ row }">
        <div class="actions">
          <Link :href="`/member/businesses/${business?.id}/faqs/${row.id}/edit`" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-pencil"></i>
          </Link>
          <button class="btn btn-sm btn-outline-danger" @click="deleteFaq(row)">
            <i class="bi bi-trash"></i>
          </button>
        </div>
      </template>
    </BaseDataTable>
  </MemberLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import BaseDataTable from '@/Components/DataTable/BaseDataTable.vue'
import { BulkSelect, BulkSelectRowCheckbox } from '@/Components/BulkSelect'

const props = defineProps({
  business: Object,
  faqs: Object,
  categories: { type: Array, default: () => [] },
  dataTable: Object,
})

const page = usePage()
const business = computed(() => page.props.business)
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
        { label: 'Preguntas Frecuentes', active: true },
      ]
    }
  }
  return [
    { label: 'Mis Negocios', href: '/member/business-modules' },
    { label: 'Preguntas Frecuentes', active: true },
  ]
})

const perPage = ref(10)

const columns = [
  { key: 'checkbox', label: '', sortable: false, width: '40px' },
  { key: 'question', label: 'Pregunta', sortable: true },
  { key: 'answer', label: 'Respuesta', sortable: false },
  { key: 'is_active', label: 'Estado', sortable: true },
  { key: 'actions', label: 'Acciones', sortable: false },
]

const dataTableRef = ref(null)
const selectedIds = ref([])

const currentPageIds = computed(() => {
  if (!props.dataTable?.data) return []
  return props.dataTable.data.map(row => row.id)
})

const onDataTableUpdated = (data) => {
  perPage.value = data.per_page
  selectedIds.value = []
}

const onBulkDeleted = () => {
  if (dataTableRef.value) {
    dataTableRef.value.reload()
  }
}

const deleteFaq = (faq) => {
  if (!confirm(`¿Estas seguro de eliminar "${faq.question}"?`)) {
    return
  }

  router.delete(`/member/businesses/${business.value.id}/faqs/${faq.id}`, {
    preserveScroll: true,
    onSuccess: () => {
      if (dataTableRef.value) {
        dataTableRef.value.reload()
      }
    },
  })
}

const deleteSelected = () => {
  if (selectedIds.value.length === 0) return

  const count = selectedIds.value.length
  if (confirm(`Eliminar ${count} pregunta${count > 1 ? 's' : ''} seleccionada${count > 1 ? 's' : ''}?`)) {
    router.post(`/member/businesses/${business.value.id}/faqs/bulk-delete`, {
      ids: selectedIds.value,
    }, {
      preserveScroll: true,
      onSuccess: () => {
        selectedIds.value = []
        if (dataTableRef.value) {
          dataTableRef.value.reload()
        }
      },
    })
  }
}
</script>
