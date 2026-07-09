<template>
  <MemberLayout>
    <Head :title="`Categorias - ${business.name}`" />

    <PageHeader
      title="Categorias de Productos"
      :breadcrumbs="breadcrumbs"
      :backHref="`/member/businesses/${business.id}/products`"
    />

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

        <div v-else class="list-group">
          <div v-for="category in categories" :key="category.id" class="list-group-item">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <strong>{{ category.name }}</strong>
                <span v-if="category.children && category.children.length" class="badge bg-secondary ms-2">
                  {{ category.children.length }} subcategorias
                </span>
                <span class="badge" :class="category.is_active ? 'bg-success' : 'bg-secondary'">
                  {{ category.is_active ? 'Activa' : 'Inactiva' }}
                </span>
              </div>
              <div>
                <button class="btn btn-sm btn-outline-primary" @click="openEditModal(category)">
                  <i class="bi bi-pencil"></i>
                </button>
                <button class="btn btn-sm btn-outline-danger ms-1" @click="deleteCategory(category)">
                  <i class="bi bi-trash"></i>
                </button>
              </div>
            </div>
            <div v-if="category.children && category.children.length" class="ms-4 mt-2">
              <div v-for="child in category.children" :key="child.id" class="d-flex justify-content-between align-items-center border-bottom py-2">
                <div>
                  <span>{{ child.name }}</span>
                  <span class="badge" :class="child.is_active ? 'bg-success' : 'bg-secondary'">
                    {{ child.is_active ? 'Activa' : 'Inactiva' }}
                  </span>
                </div>
                <div>
                  <button class="btn btn-sm btn-outline-primary" @click="openEditModal(child)">
                    <i class="bi bi-pencil"></i>
                  </button>
                  <button class="btn btn-sm btn-outline-danger ms-1" @click="deleteCategory(child)">
                    <i class="bi bi-trash"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
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
                <FieldSelect
                  id="category-parent"
                  label="Categoria padre"
                  v-model="form.parent_id"
                >
                  <option :value="null">Ninguna (categoria principal)</option>
                  <option v-for="cat in flatCategories" :key="cat.id" :value="cat.id" :disabled="cat.id === editingCategory?.id">{{ cat.name }}</option>
                </FieldSelect>
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
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { Modal } from 'bootstrap'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldTextarea from '@/Components/Fields/FieldTextarea.vue'
import FieldSelect from '@/Components/Fields/FieldSelect.vue'
import FieldSwitch from '@/Components/Fields/FieldSwitch.vue'

const page = usePage()
const business = page.props.business
const categories = page.props.categories || []

const breadcrumbs = computed(() => [
  { label: business.name, href: '/member/business-modules' },
  { label: 'Productos', href: `/member/businesses/${business.id}/products` },
  { label: 'Categorias', active: true },
])

const modalElement = ref(null)
let categoryModal = null

const showCreateModal = ref(false)
const showEditModal = ref(false)
const sending = ref(false)
const editingCategory = ref(null)

const form = reactive({
  name: '',
  description: '',
  parent_id: null,
  is_active: true,
})

const flatCategories = computed(() => {
  const result = []
  const flatten = (cats, depth = 0) => {
    for (const cat of cats) {
      result.push(cat)
      if (cat.children && cat.children.length) {
        flatten(cat.children, depth + 1)
      }
    }
  }
  flatten(categories)
  return result
})

const openCreateModal = () => {
  editingCategory.value = null
  form.name = ''
  form.description = ''
  form.parent_id = null
  form.is_active = true
  nextTick(() => categoryModal.show())
}

const openEditModal = (category) => {
  editingCategory.value = category
  form.name = category.name
  form.description = category.description || ''
  form.parent_id = category.parent_id
  form.is_active = category.is_active
  nextTick(() => categoryModal.show())
}

const closeModal = () => {
  categoryModal.hide()
}

const createCategory = () => {
  sending.value = true
  router.post(`/member/businesses/${business.id}/product-categories`, form, {
    onFinish: () => {
      sending.value = false
      closeModal()
    },
  })
}

const updateCategory = () => {
  sending.value = true
  router.put(`/member/businesses/${business.id}/product-categories/${editingCategory.value.id}`, form, {
    onFinish: () => {
      sending.value = false
      closeModal()
    },
  })
}

const deleteCategory = (category) => {
  if (confirm(`Eliminar la categoria "${category.name}"?`)) {
    router.delete(`/member/businesses/${business.id}/product-categories/${category.id}`, {
      preserveScroll: true,
    })
  }
}

onMounted(() => {
  categoryModal = new Modal(modalElement.value)
})
</script>
