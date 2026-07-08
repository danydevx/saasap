<template>
  <MemberLayout>
    <Head title="Categorías del Menú" />

    <div class="container-fluid py-4">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h1 class="h4 mb-1">Categorías del Menú</h1>
          <p class="text-muted mb-0">{{ business?.name }}</p>
        </div>
        <button @click="showCreateModal = true" class="btn btn-primary">
          <i class="bi bi-plus-lg"></i> Nueva Categoría
        </button>
      </div>

    <div v-if="$page.props.flash?.success" class="alert alert-success alert-dismissible fade show" role="alert">
      {{ $page.props.flash.success }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <div v-if="$page.props.flash?.error" class="alert alert-danger alert-dismissible fade show" role="alert">
      {{ $page.props.flash.error }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>

    <div v-if="categories.length === 0" class="alert alert-info">
      No hay categorías creadas. Crea tu primera categoría para empezar.
    </div>

    <div class="mb-3">
      <Link :href="`/member/businesses/${business.id}/menu-products?uncategorized=1`" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-dash-circle me-1"></i>Productos sin categoría
      </Link>
    </div>

    <div v-for="category in categories" :key="category.id" class="card mb-3">
      <div class="card-header d-flex justify-content-between align-items-center">
        <div>
          <span :class="{ 'text-muted': !category.active }">
            <strong>{{ category.title }}</strong>
            <span v-if="!category.active" class="badge bg-secondary ms-2">Inactiva</span>
          </span>
          <small class="text-muted d-block">{{ category.children?.length || 0 }} subcategorías, {{ category.products?.length || 0 }} productos</small>
          <Link :href="`/member/businesses/${business.id}/menu-products?category=${category.id}`" class="text-decoration-none small">
            <i class="bi bi-box-seam me-1"></i>Ver productos
          </Link>
        </div>
        <div class="btn-group">
          <button @click="editCategory(category)" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-pencil"></i>
          </button>
          <button @click="deleteCategory(category)" class="btn btn-sm btn-outline-danger">
            <i class="bi bi-trash"></i>
          </button>
        </div>
      </div>
      <div class="card-body">
        <p v-if="category.description" class="text-muted mb-2">{{ category.description }}</p>

        <div v-if="category.children && category.children.length > 0" class="ms-4 mt-3">
          <div v-for="child in category.children" :key="child.id" class="border-start border-3 ps-3 mb-2">
            <div class="d-flex justify-content-between align-items-center">
              <span :class="{ 'text-muted': !child.active }">
                {{ child.title }}
                <span v-if="!child.active" class="badge bg-secondary ms-2">Inactiva</span>
                <Link :href="`/member/businesses/${business.id}/menu-products?category=${child.id}`" class="text-decoration-none small ms-2">
                  <i class="bi bi-box-seam"></i> {{ child.products?.length || 0 }}
                </Link>
              </span>
              <div class="btn-group btn-group-sm">
                <button @click="editCategory(child)" class="btn btn-outline-primary btn-sm">
                  <i class="bi bi-pencil"></i>
                </button>
                <button @click="deleteCategory(child)" class="btn btn-outline-danger btn-sm">
                  <i class="bi bi-trash"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-if="showCreateModal || editingCategory" class="modal show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5)">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ editingCategory ? 'Editar Categoría' : 'Nueva Categoría' }}</h5>
            <button type="button" class="btn-close" @click="closeModal"></button>
          </div>
          <form @submit.prevent="submitForm">
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label">Nombre de la categoría</label>
                <input v-model="form.title" type="text" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Descripción</label>
                <textarea v-model="form.description" class="form-control" rows="3"></textarea>
              </div>
              <div class="mb-3">
                <label class="form-label">Categoría padre</label>
                <select v-model="form.parent_id" class="form-select">
                  <option :value="null">Ninguna (categoría principal)</option>
                  <option v-for="cat in flatCategories" :key="cat.id" :value="cat.id">
                    {{ cat.nested_title }}
                  </option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label">Imagen</label>
                <input
                  ref="imageInput"
                  type="file"
                  class="form-control"
                  accept="image/jpeg,image/png,image/webp,image/gif"
                  @change="handleImageChange"
                />
                <div v-if="imagePreview || form.image" class="mt-2">
                  <img :src="imagePreview || form.image" class="img-thumbnail" style="max-height: 150px;" alt="Preview" />
                </div>
                <small class="text-muted">JPG, PNG o WebP, max 5MB. Tambien puedes ingresar una URL abajo.</small>
              </div>
              <div class="mb-3">
                <label class="form-label">URL de imagen (alternativo)</label>
                <input v-model="form.image" type="text" class="form-control" placeholder="https://...">
              </div>
              <div class="mb-3">
                <label class="form-label">Orden</label>
                <input v-model.number="form.sort_order" type="number" class="form-control">
              </div>
              <div class="mb-3 form-check">
                <input v-model="form.active" type="checkbox" class="form-check-input" id="activeCheck">
                <label class="form-check-label" for="activeCheck">Activa</label>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" @click="closeModal">Cancelar</button>
              <button type="submit" class="btn btn-primary" :disabled="sending">
                {{ sending ? 'Guardando...' : 'Guardar' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, usePage, Link, router } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'

const props = defineProps({
  business: Object,
  categories: Array,
})

const showCreateModal = ref(false)
const editingCategory = ref(null)
const sending = ref(false)
const imagePreview = ref(null)
const imageInput = ref(null)

const form = ref({
  title: '',
  description: '',
  parent_id: null,
  image: '',
  sort_order: 0,
  active: true,
})

const flatCategories = computed(() => {
  const flat = []
  const flatten = (cats, prefix = '') => {
    cats.forEach(cat => {
      flat.push({
        id: cat.id,
        title: cat.title,
        nested_title: prefix + cat.title,
      })
      if (cat.children && cat.children.length) {
        flatten(cat.children, prefix + cat.title + ' > ')
      }
    })
  }
  flatten(props.categories || [])
  return flat
})

const editCategory = (category) => {
  editingCategory.value = category
  imagePreview.value = null
  form.value = {
    title: category.title,
    description: category.description || '',
    parent_id: category.parent_id,
    image: category.image || '',
    sort_order: category.sort_order || 0,
    active: category.active,
  }
}

const handleImageChange = (e) => {
  const file = e.target.files[0]
  if (!file) return

  const maxSize = 5 * 1024 * 1024
  if (file.size > maxSize) {
    alert('El archivo supera el tamaño máximo de 5MB.')
    return
  }

  const allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif']
  if (!allowedTypes.includes(file.type)) {
    alert('Solo se permiten imágenes (JPEG, PNG, WebP, GIF).')
    return
  }

  const reader = new FileReader()
  reader.onload = (e) => {
    form.value.image = e.target.result
    imagePreview.value = e.target.result
  }
  reader.readAsDataURL(file)
}

const deleteCategory = (category) => {
  if (!confirm(`¿Eliminar la categoría "${category.title}"?`)) return

  router.delete(`/member/businesses/${props.business.id}/menu-categories/${category.id}`, {
    preserveScroll: true,
  })
}

const closeModal = () => {
  showCreateModal.value = false
  editingCategory.value = null
  imagePreview.value = null
  if (imageInput.value) imageInput.value.value = ''
  form.value = {
    title: '',
    description: '',
    parent_id: null,
    image: '',
    sort_order: 0,
    active: true,
  }
}

const submitForm = () => {
  sending.value = true

  if (editingCategory.value) {
    router.put(`/member/businesses/${props.business.id}/menu-categories/${editingCategory.value.id}`, form.value, {
      preserveScroll: true,
      onFinish: () => {
        sending.value = false
        closeModal()
      },
    })
  } else {
    router.post(`/member/businesses/${props.business.id}/menu-categories`, form.value, {
      preserveScroll: true,
      onFinish: () => {
        sending.value = false
        closeModal()
      },
    })
  }
}
</script>
