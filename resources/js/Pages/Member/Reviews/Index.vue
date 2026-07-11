<template>
  <MemberLayout>
    <Head :title="`Resenas - ${business?.name || ''}`" />

    <PageHeader
      title="Resenas"
      :breadcrumbs="breadcrumbs"
      :backHref="'/member/business-modules'"
    >
      <template #description>
        <p class="text-muted mb-0">Gestiona las resenas de tu negocio. Arrastra para reordenar.</p>
      </template>
      <template #actions>
        <button @click="openCreateModal" class="btn btn-primary btn-sm">
          <i class="bi bi-plus-lg me-1"></i>
          Nueva Resena
        </button>
      </template>
    </PageHeader>

    <SortableCards
      ref="sortableCardsRef"
      :items="reviews.data"
      item-class="col-6 col-md-4 col-lg-3"
      :reorderable="true"
      :reorder-endpoint="`/member/businesses/${business?.id}/reviews/reorder`"
      :loading="loading"
      empty-title="No hay resenas"
      empty-text="Comienza creando tu primera resena."
      toast-message="Orden actualizado"
      @reordered="onReordered"
    >
      <template #item="{ item: review }">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start mb-2">
            <strong>{{ review.client_name }}</strong>
            <span class="text-warning">
              <i v-for="n in review.rating" :key="n" class="bi bi-star-fill"></i>
            </span>
          </div>
          <p v-if="review.company" class="text-muted small mb-1">{{ review.company }}</p>
          <p v-if="review.comment" class="card-text small">{{ review.comment.substring(0, 80) }}{{ review.comment.length > 80 ? '...' : '' }}</p>
          <span :class="review.is_active ? 'badge bg-success' : 'badge bg-secondary'" class="mb-2">
            {{ review.is_active ? 'Activa' : 'Inactiva' }}
          </span>
          <div class="d-flex gap-2 mt-2">
            <button @click="openEditModal(review)" class="btn btn-sm btn-outline-primary flex-grow-1">
              <i class="bi bi-pencil me-1"></i>Editar
            </button>
            <button @click="deleteReview(review)" class="btn btn-sm btn-outline-danger">
              <i class="bi bi-trash"></i>
            </button>
          </div>
        </div>
      </template>
    </SortableCards>

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
import SortableCards from '@/Components/DataTable/SortableCards.vue'
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldTextarea from '@/Components/Fields/FieldTextarea.vue'
import FieldSwitch from '@/Components/Fields/FieldSwitch.vue'

const page = usePage()
const business = computed(() => page.props.business)
const reviews = computed(() => page.props.reviews || { data: [] })

const breadcrumbs = computed(() => [
  { label: 'Mis Negocios', href: '/member/business-modules' },
  { label: 'Resenas', active: true },
])

const sortableCardsRef = ref(null)
const modalElement = ref(null)
let reviewModal = null
const loading = ref(false)
const sending = ref(false)
const editingReview = ref(null)

const form = ref({
  client_name: '',
  company: '',
  rating: 5,
  comment: '',
  is_active: true,
})

const onReordered = (ids) => {
  console.log('Reordered:', ids)
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
      },
      onError: () => {
        sending.value = false
      },
    })
  }
}

const deleteReview = (review) => {
  if (confirm(`¿Eliminar la resena de "${review.client_name}"?`)) {
    router.delete(`/member/businesses/${business.value.id}/reviews/${review.id}`, {
      preserveScroll: true,
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
