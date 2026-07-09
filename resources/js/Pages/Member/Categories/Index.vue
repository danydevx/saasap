<template>
  <MemberLayout>
    <Head title="Categorias del Menu" />

    <PageHeader
      title="Categorias del Menu"
      :breadcrumbs="breadcrumbs"
      :backHref="`/member/businesses/${business?.id}/content`"
    />

    <div class="container-fluid py-4">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <p class="text-muted mb-0">{{ business?.name }}</p>
        </div>
        <button @click="openCreateModal" class="btn btn-primary">
          <i class="bi bi-plus-lg"></i> Nueva Categoria
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
      No hay categorias creadas. Crea tu primera categoria para empezar.
    </div>

    <div class="mb-3">
      <Link :href="`/member/businesses/${business.id}/menu-products?uncategorized=1`" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-dash-circle me-1"></i>Productos sin categoria
      </Link>
    </div>

    <div v-for="category in categories" :key="category.id" class="card mb-3">
      <div class="card-header d-flex justify-content-between align-items-center">
        <div>
          <span :class="{ 'text-muted': !category.active }">
            <strong>{{ category.title }}</strong>
            <span v-if="!category.active" class="badge bg-secondary ms-2">Inactiva</span>
          </span>
          <small class="text-muted d-block">{{ category.children?.length || 0 }} subcategorias, {{ category.products?.length || 0 }} productos</small>
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

    <div ref="modalElement" class="modal fade" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ editingCategory ? 'Editar Categoria' : 'Nueva Categoria' }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form @submit.prevent="submitForm">
            <div class="modal-body">
              <div class="mb-3">
                <FieldText
                  id="category-title"
                  label="Nombre de la categoria"
                  v-model="form.title"
                  required
                />
              </div>
              <div class="mb-3">
                <FieldTextarea
                  id="category-description"
                  label="Descripcion"
                  v-model="form.description"
                  :rows="3"
                />
              </div>
              <div class="mb-3">
                <FieldSelect
                  id="category-parent"
                  label="Categoria padre"
                  v-model="form.parent_id"
                >
                  <option :value="null">Ninguna (categoria principal)</option>
                  <option v-for="cat in flatCategories" :key="cat.id" :value="cat.id">
                    {{ cat.nested_title }}
                  </option>
                </FieldSelect>
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
                <small class="text-muted d-block">JPG, PNG o WebP, max 5MB. Tambien puedes ingresar una URL abajo.</small>
              </div>
              <div class="mb-3">
                <FieldUrl
                  id="category-image-url"
                  label="URL de imagen (alternativo)"
                  v-model="form.image"
                  placeholder="https://..."
                />
              </div>
              <div class="mb-3">
                <FieldNumber
                  id="category-sort"
                  label="Orden"
                  v-model="form.sort_order"
                />
              </div>
              <div class="mb-3">
                <FieldSwitch
                  id="category-active"
                  label="Activa"
                  v-model="form.active"
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
    </div>
  </MemberLayout>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import { Head, usePage, Link, router } from '@inertiajs/vue3'
import { Modal } from 'bootstrap'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldTextarea from '@/Components/Fields/FieldTextarea.vue'
import FieldSelect from '@/Components/Fields/FieldSelect.vue'
import FieldNumber from '@/Components/Fields/FieldNumber.vue'
import FieldSwitch from '@/Components/Fields/FieldSwitch.vue'
import FieldUrl from '@/Components/Fields/FieldUrl.vue'

const props = defineProps({
  business: Object,
  categories: Array,
})

const business = computed(() => props.business)

const breadcrumbs = computed(() => [
  { label: business.value?.name, href: `/member/businesses/${business.value?.id}/content` },
  { label: 'Categorias del Menu', active: true },
])

const modalElement = ref(null)
let categoryModal = null

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

const openCreateModal = () => {
  editingCategory.value = null
  imagePreview.value = null
  form.value = {
    title: '',
    description: '',
    parent_id: null,
    image: '',
    sort_order: 0,
    active: true,
  }
  nextTick(() => categoryModal.show())
}

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
  nextTick(() => categoryModal.show())
}

const handleImageChange = (e) => {
  const file = e.target.files[0]
  if (!file) return

  const maxSize = 5 * 1024 * 1024
  if (file.size > maxSize) {
    alert('El archivo supera el tamano maximo de 5MB.')
    return
  }

  const allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif']
  if (!allowedTypes.includes(file.type)) {
    alert('Solo se permiten imagenes (JPEG, PNG, WebP, GIF).')
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
  if (!confirm(`Eliminar la categoria "${category.title}"?`)) return

  router.delete(`/member/businesses/${props.business.id}/menu-categories/${category.id}`, {
    preserveScroll: true,
  })
}

const closeModal = () => {
  categoryModal.hide()
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

onMounted(() => {
  categoryModal = new Modal(modalElement.value)
})
</script>
