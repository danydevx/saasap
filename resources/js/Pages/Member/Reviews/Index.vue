<template>
  <MemberLayout>
    <Head :title="`Resenas - ${business?.name || ''}`" />

    <PageHeader
      title="Resenas"
      :breadcrumbs="breadcrumbs"
      :backHref="'/member/business-modules'"
    >
      <template #actions>
        <button @click="openCreateModal" class="btn btn-primary btn-sm">
          <i class="bi bi-plus-lg me-1"></i>
          Nueva Resena
        </button>
      </template>
    </PageHeader>

    <BaseDataTable
      ref="dataTableRef"
      :endpoint="`/member/businesses/${business?.id}/reviews`"
      :columns="columns"
      :initial-data="dataTable"
      search-placeholder="Buscar resenas..."
      empty-title="No hay resenas"
      empty-text="Comienza creando tu primera resena."
      @updated="onDataTableUpdated"
    >
      <template #cell-client_name="{ row }">
        <strong>{{ row.client_name }}</strong>
      </template>

      <template #cell-company="{ row }">
        {{ row.company || '-' }}
      </template>

      <template #cell-rating="{ row }">
        <span class="text-warning">
          <i v-for="n in row.rating" :key="n" class="bi bi-star-fill"></i>
        </span>
      </template>

      <template #cell-is_active="{ row }">
        <span :class="row.is_active ? 'badge bg-success' : 'badge bg-secondary'">
          {{ row.is_active ? 'Activa' : 'Inactiva' }}
        </span>
      </template>

      <template #cell-actions="{ row }">
        <div class="actions">
          <button @click="openEditModal(row)" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-pencil"></i>
          </button>
        </div>
      </template>
    </BaseDataTable>

    <div ref="modalElement" class="modal fade" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ editingReview ? 'Editar Resena' : 'Nueva Resena' }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form @submit.prevent="submitReview">
            <div class="modal-body">
              <div class="mb-3">
                <FieldText
                  id="review-client-name"
                  label="Nombre del cliente"
                  v-model="form.client_name"
                  required
                />
              </div>
              <div class="mb-3">
                <FieldText
                  id="review-company"
                  label="Empresa"
                  v-model="form.company"
                />
              </div>
              <div class="mb-3">
                <label class="form-label">Calificacion *</label>
                <div class="rating-stars">
                  <i
                    v-for="n in 5"
                    :key="n"
                    class="bi"
                    :class="n <= form.rating ? 'bi-star-fill text-warning' : 'bi-star text-muted'"
                    style="font-size: 1.5rem; cursor: pointer;"
                    @click="form.rating = n"
                  ></i>
                </div>
              </div>
              <div class="mb-3">
                <FieldTextarea
                  id="review-comment"
                  label="Comentario"
                  v-model="form.comment"
                  :rows="3"
                />
              </div>
              <div class="mb-3">
                <FieldSwitch
                  id="review-active"
                  label="Activa"
                  v-model="form.is_active"
                />
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-primary" :disabled="sending">
                {{ sending ? 'Guardando...' : 'Guardar' }}
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
import { Head, router, usePage } from '@inertiajs/vue3'
import { Modal } from 'bootstrap'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import BaseDataTable from '@/Components/DataTable/BaseDataTable.vue'
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldTextarea from '@/Components/Fields/FieldTextarea.vue'
import FieldSwitch from '@/Components/Fields/FieldSwitch.vue'

const page = usePage()
const business = computed(() => page.props.business)
const dataTable = computed(() => page.props.dataTable)

const breadcrumbs = computed(() => [
  { label: business.value?.name, href: '/member/business-modules' },
  { label: 'Resenas', active: true },
])

const columns = [
  { key: 'client_name', label: 'Cliente', sortable: true },
  { key: 'company', label: 'Empresa', sortable: false },
  { key: 'rating', label: 'Calificacion', sortable: true },
  { key: 'is_active', label: 'Estado', sortable: true },
  { key: 'actions', label: 'Acciones', sortable: false },
]

const dataTableRef = ref(null)
const modalElement = ref(null)
let reviewModal = null
const sending = ref(false)
const editingReview = ref(null)

const form = ref({
  client_name: '',
  company: '',
  rating: 5,
  comment: '',
  is_active: true,
})

const onDataTableUpdated = (data) => {
  // Optional: handle data update
}

const openCreateModal = () => {
  editingReview.value = null
  form.value = {
    client_name: '',
    company: '',
    rating: 5,
    comment: '',
    is_active: true,
  }
  nextTick(() => {
    reviewModal.show()
  })
}

const openEditModal = (review) => {
  editingReview.value = review
  form.value = {
    client_name: review.client_name,
    company: review.company || '',
    rating: review.rating,
    comment: review.comment || '',
    is_active: review.is_active,
  }
  nextTick(() => {
    reviewModal.show()
  })
}

const submitReview = () => {
  sending.value = true
  if (editingReview.value) {
    router.put(`/member/businesses/${business.value.id}/reviews/${editingReview.value.id}`, form.value, {
      preserveScroll: true,
      onSuccess: () => {
        sending.value = false
        reviewModal.hide()
        if (dataTableRef.value) {
          dataTableRef.value.reload()
        }
      },
      onError: () => {
        sending.value = false
      },
    })
  } else {
    router.post(`/member/businesses/${business.value.id}/reviews`, form.value, {
      preserveScroll: true,
      onSuccess: () => {
        sending.value = false
        reviewModal.hide()
        if (dataTableRef.value) {
          dataTableRef.value.reload()
        }
      },
      onError: () => {
        sending.value = false
      },
    })
  }
}

onMounted(() => {
  reviewModal = new Modal(modalElement.value)
  reviewModal._element.addEventListener('hidden.bs.modal', () => {
    editingReview.value = null
    form.value = {
      client_name: '',
      company: '',
      rating: 5,
      comment: '',
      is_active: true,
    }
  })
})
</script>
