<template>
  <MemberLayout>
    <Head :title="`Promociones - ${business?.name || ''}`" />

    <PageHeader
      title="Promociones"
      :breadcrumbs="breadcrumbs"
      :backHref="'/member/business-modules'"
    >
      <template #actions>
        <Link :href="`/member/businesses/${business?.id}/promotions/create`" class="btn btn-primary btn-sm">
          <i class="bi bi-plus-lg me-1"></i>
          Nueva Promocion
        </Link>
      </template>
    </PageHeader>

    <BaseDataTable
      ref="dataTableRef"
      :endpoint="`/member/businesses/${business?.id}/promotions`"
      :columns="columns"
      :initial-data="dataTable"
      :reorderable="true"
      :reorder-endpoint="`/member/businesses/${business?.id}/promotions/reorder`"
      search-placeholder="Buscar promociones..."
      empty-title="No hay promociones"
      empty-text="Comienza creando tu primera promocion."
      @updated="onDataTableUpdated"
    >
      <template #cell-image="{ row }">
        <img v-if="row.image" :src="row.image" class="rounded" style="width: 50px; height: 50px; object-fit: cover;" />
        <i v-else class="bi bi-image text-muted fs-4"></i>
      </template>

      <template #cell-name="{ row }">
        <strong>{{ row.name }}</strong>
      </template>

      <template #cell-regular_price="{ row }">
        {{ formatPrice(row.regular_price) }}
      </template>

      <template #cell-promotion_price="{ row }">
        <span class="text-success fw-bold">{{ formatPrice(row.promotion_price) }}</span>
      </template>

      <template #cell-coupon_code="{ row }">
        <span v-if="row.coupon_code" class="badge bg-warning text-dark">{{ row.coupon_code }}</span>
        <span v-else>-</span>
      </template>

      <template #cell-expires_at="{ row }">
        <span v-if="row.expires_at" :class="isExpired(row.expires_at) ? 'text-danger' : 'text-muted'">
          {{ formatDate(row.expires_at) }}
        </span>
        <span v-else>-</span>
      </template>

      <template #cell-is_active="{ row }">
        <span :class="row.is_active ? 'badge bg-success' : 'badge bg-secondary'">
          {{ row.is_active ? 'Activa' : 'Inactiva' }}
        </span>
      </template>

      <template #cell-actions="{ row }">
        <div class="actions">
          <Link :href="`/member/businesses/${business?.id}/promotions/${row.id}/edit`" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-pencil"></i>
          </Link>
          <button
            class="btn btn-sm btn-outline-danger"
            @click="deletePromotion(row)"
          >
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
  { label: 'Promociones', active: true },
])

const columns = [
  { key: 'image', label: 'Imagen', sortable: false },
  { key: 'name', label: 'Nombre', sortable: true },
  { key: 'regular_price', label: 'Precio Regular', sortable: true },
  { key: 'promotion_price', label: 'Precio Promo', sortable: true },
  { key: 'coupon_code', label: 'Cupon', sortable: false },
  { key: 'expires_at', label: 'Expira', sortable: true },
  { key: 'is_active', label: 'Estado', sortable: true },
  { key: 'actions', label: 'Acciones', sortable: false },
]

const dataTableRef = ref(null)

const onDataTableUpdated = (data) => {
  // Optional: handle data update
}

const formatPrice = (price) => {
  if (!price && price !== 0) return '-'
  return new Intl.NumberFormat('es-AR', { style: 'currency', currency: 'ARS' }).format(price)
}

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('es-AR')
}

const isExpired = (date) => {
  return new Date(date) < new Date()
}

const deletePromotion = (row) => {
  if (!confirm(`Eliminar la promocion "${row.name}"?`)) return
  router.delete(`/member/businesses/${business.value.id}/promotions/${row.id}`, {
    preserveScroll: true,
  })
}
</script>
