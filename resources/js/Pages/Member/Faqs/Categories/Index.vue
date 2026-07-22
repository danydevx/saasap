<template>
  <MemberLayout>
    <Head :title="`Categorias de FAQs - ${business.name}`" />

    <PageHeader
      title="Categorias de Preguntas Frecuentes"
      :breadcrumbs="breadcrumbs"
      :backHref="`/member/businesses/${business.id}/faqs`"
    >
      <template #actions>
        <button class="btn btn-primary" @click="openCreateModal">
          <i class="bi bi-plus me-1"></i>Nueva Categoria
        </button>
      </template>
    </PageHeader>

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div v-if="$page.props.flash?.success" class="alert alert-success alert-dismissible fade show" role="alert">
          {{ $page.props.flash.success }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <div v-if="$page.props.flash?.error" class="alert alert-danger alert-dismissible fade show" role="alert">
          {{ $page.props.flash.error }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>

        <div v-if="categories.length === 0" class="text-center text-muted py-5">
          No hay categorias registradas.
        </div>

        <div v-else class="table-responsive">
          <table class="table table-hover align-middle">
            <thead>
              <tr>
                <th>Nombre</th>
                <th>Descripcion</th>
                <th>Preguntas</th>
                <th>Estado</th>
                <th style="width: 120px;">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="category in categories" :key="category.id">
                <td>
                  <strong>{{ category.name }}</strong>
                </td>
                <td class="text-muted small">
                  {{ category.description || '-' }}
                </td>
                <td>
                  <span class="badge bg-light text-dark">{{ category.faqs?.length || 0 }}</span>
                </td>
                <td>
                  <span class="badge" :class="category.is_active ? 'bg-success' : 'bg-secondary'">
                    {{ category.is_active ? 'Activa' : 'Inactiva' }}
                  </span>
                </td>
                <td>
                  <button class="btn btn-sm btn-outline-primary" @click="openEditModal(category)">
                    <i class="bi bi-pencil"></i>
                  </button>
                  <button class="btn btn-sm btn-outline-danger ms-1" @click="deleteCategory(category)">
                    <i class="bi bi-trash"></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div ref="modalElement" class="modal fade" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ editingCategory ? 'Editar Categoria' : 'Nueva Categoria' }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form @submit.prevent="editingCategory ? updateCategory() : createCategory()">
            <div class="modal-body">
              <div class="mb-3">
                <FieldText
                  id="category-name"
                  label="Nombre"
                  v-model="form.name"
                  required
                />
              </div>
              <div class="mb-3">
                <FieldTextarea
                  id="category-description"
                  label="Descripcion"
                  v-model="form.description"
                  :rows="2"
                />
              </div>
              <div class="mb-3">
                <FieldSwitch
                  id="category-active"
                  label="Activa"
                  v-model="form.is_active"
                />
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-primary" :disabled="sending">
                {{ sending ? (editingCategory ? 'Guardando...' : 'Creando...') : (editingCategory ? 'Guardar Cambios' : 'Crear Categoria') }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted, nextTick } from 'vue'
import { Head, router, usePage } from '@inertiajs/vue3'
import { Modal } from 'bootstrap'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldTextarea from '@/Components/Fields/FieldTextarea.vue'
import FieldSwitch from '@/Components/Fields/FieldSwitch.vue'

const page = usePage()
const business = computed(() => page.props.business)
const categories = page.props.categories || []
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
        { label: 'Categorias FAQ', active: true },
      ]
    }
  }
  return [
    { label: 'Mis Negocios', href: '/member/business-modules' },
    { label: 'Categorias FAQ', active: true },
  ]
})

const modalElement = ref(null)
let categoryModal = null

const sending = ref(false)
const editingCategory = ref(null)

const form = reactive({
  name: '',
  description: '',
  is_active: true,
})

const openCreateModal = () => {
  editingCategory.value = null
  form.name = ''
  form.description = ''
  form.is_active = true
  nextTick(() => categoryModal.show())
}

const openEditModal = (category) => {
  editingCategory.value = category
  form.name = category.name
  form.description = category.description || ''
  form.is_active = category.is_active
  nextTick(() => categoryModal.show())
}

const closeModal = () => {
  categoryModal.hide()
}

const createCategory = () => {
  sending.value = true
  router.post(`/member/businesses/${business.id}/faq-categories`, form, {
    onFinish: () => {
      sending.value = false
      closeModal()
    },
  })
}

const updateCategory = () => {
  sending.value = true
  router.put(`/member/businesses/${business.id}/faq-categories/${editingCategory.value.id}`, form, {
    onFinish: () => {
      sending.value = false
      closeModal()
    },
  })
}

const deleteCategory = (category) => {
  if (confirm(`Eliminar la categoria "${category.name}"? Las preguntas seran desvinculadas.`)) {
    router.delete(`/member/businesses/${business.id}/faq-categories/${category.id}`, {
      preserveScroll: true,
    })
  }
}

onMounted(() => {
  categoryModal = new Modal(modalElement.value)
})
</script>
