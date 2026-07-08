<template>
  <MemberLayout>
    <Head :title="`Reviews - ${business.name}`" />

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
      <div>
        <h1 class="h4 mb-1">{{ business.name }}</h1>
        <p class="text-muted mb-0">Gestiona las reseñas de tu negocio.</p>
      </div>
      <button @click="openCreateModal" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i>
        Nueva Reseña
      </button>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div v-if="$page.props.flash?.success" class="alert alert-success alert-dismissible fade show" role="alert">
          {{ $page.props.flash.success }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>

        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th scope="col">Cliente</th>
                <th scope="col">Empresa</th>
                <th scope="col">Calificacion</th>
                <th scope="col">Activa</th>
                <th scope="col" class="text-end">Accion</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="reviews.data.length === 0">
                <td colspan="5" class="text-center text-muted py-4">
                  No hay reseñas registradas.
                </td>
              </tr>
              <tr v-for="review in reviews.data" :key="review.id">
                <td class="fw-semibold">{{ review.client_name }}</td>
                <td>{{ review.company || '-' }}</td>
                <td>
                  <span class="text-warning">
                    <i v-for="n in review.rating" :key="n" class="bi bi-star-fill"></i>
                  </span>
                </td>
                <td>
                  <span v-if="review.is_active" class="badge bg-success">Activa</span>
                  <span v-else class="badge bg-secondary">Inactiva</span>
                </td>
                <td class="text-end">
                  <button @click="openEditModal(review)" class="btn btn-sm btn-outline-primary">
                    Editar
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-if="reviews.links" class="d-flex justify-content-center mt-4">
          <MemberPagination :links="reviews.links" />
        </div>
      </div>
    </div>

    <div ref="modalElement" class="modal fade" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ editingReview ? 'Editar Reseña' : 'Nueva Reseña' }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form @submit.prevent="submitReview">
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label">Nombre del cliente *</label>
                <input v-model="form.client_name" type="text" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Empresa</label>
                <input v-model="form.company" type="text" class="form-control">
              </div>
              <div class="mb-3">
                <label class="form-label">Calificación *</label>
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
                <label class="form-label">Comentario</label>
                <textarea v-model="form.comment" class="form-control" rows="3"></textarea>
              </div>
              <div class="mb-3 form-check">
                <input v-model="form.is_active" type="checkbox" class="form-check-input" id="activeCheck">
                <label class="form-check-label" for="activeCheck">Activa</label>
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
import { computed, ref, nextTick } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { Modal } from 'bootstrap'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import MemberPagination from '@/Components/Member/Pagination.vue'

const page = usePage()
const business = computed(() => page.props.business)
const reviews = computed(() => page.props.reviews || { data: [], links: [] })

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

import { onMounted } from 'vue'
onMounted(() => {
  reviewModal = new Modal(modalElement.value)
})
</script>
