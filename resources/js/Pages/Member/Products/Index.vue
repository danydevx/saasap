<template>
  <MemberLayout>
    <Head :title="business ? `Productos - ${business.name}` : 'Productos'" />

    <PageHeader
      title="Productos del Menu"
      :breadcrumbs="breadcrumbs"
      :backHref="`/member/businesses/${business?.id}/content`"
    />

    <div class="container-fluid py-4">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <p class="text-muted mb-0">{{ business?.name }}</p>
        </div>
        <div class="d-flex gap-2">
          <Link :href="`/member/businesses/${business?.id}/menu-categories`" class="btn btn-outline-secondary">
            <i class="bi bi-folder me-1"></i>Categorias
          </Link>
          <button @click="openCreateModal" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Nuevo Producto
          </button>
        </div>
      </div>

      <div class="row mb-4">
      <div class="col-md-4">
        <select v-model="filterCategory" class="form-select" @change="filterProducts">
          <option :value="null">Todas las categorias</option>
          <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.title }}</option>
        </select>
      </div>
      <div v-if="filterCategory" class="col-md-2">
        <button type="button" class="btn btn-outline-secondary" @click="clearFilter">
          <i class="bi bi-x-lg me-1"></i>Limpiar
        </button>
      </div>
    </div>

    <div v-if="$page.props.flash?.success" class="alert alert-success alert-dismissible fade show" role="alert">
      {{ $page.props.flash.success }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>

    <div v-if="productsList.length === 0" class="alert alert-info">
      No hay productos{{ selectedCategoryName ? ' en esta categoria' : '' }}.
    </div>

    <div class="row">
      <div v-for="product in productsList" :key="product.id" class="col-md-4 mb-4">
        <div class="card h-100">
          <img v-if="product.image" :src="product.image" class="card-img-top" :alt="product.title" style="height: 150px; object-fit: cover;">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
              <h5 class="card-title">{{ product.title }}</h5>
              <span v-if="product.featured" class="badge bg-warning">Destacado</span>
            </div>
            <p class="card-text text-muted small">{{ product.category?.title }}</p>
            <p v-if="product.description" class="card-text small">{{ product.description.substring(0, 80) }}...</p>
            <div class="d-flex justify-content-between align-items-center">
              <span v-if="product.show_price && product.display_price" class="fw-bold">${{ product.display_price }}</span>
              <span v-else-if="product.show_price && product.variants?.length" class="fw-bold">Desde ${{ product.variants[0].price }}</span>
              <span v-else class="text-muted">Precio no visible</span>
              <div class="btn-group btn-group-sm">
                <button @click="editProduct(product)" class="btn btn-outline-primary">
                  <i class="bi bi-pencil"></i>
                </button>
                <button @click="deleteProduct(product)" class="btn btn-outline-danger">
                  <i class="bi bi-trash"></i>
                </button>
              </div>
            </div>
          </div>
          <div class="card-footer text-muted">
            <small>{{ product.active ? 'Activo' : 'Inactivo' }} | {{ product.variants?.length || 0 }} variantes</small>
          </div>
        </div>
      </div>
    </div>

    <div v-if="products.total > products.per_page" class="d-flex justify-content-center mt-4">
      <pagination :data="products" />
    </div>

    <div ref="modalElement" class="modal fade" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ editingProduct ? 'Editar Producto' : 'Nuevo Producto' }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form @submit.prevent="submitForm">
            <div class="modal-body">
              <div class="row">
                <div class="col-md-8">
                  <div class="mb-3">
                    <FieldText
                      id="product-title"
                      label="Nombre del producto"
                      v-model="form.title"
                      required
                    />
                  </div>
                  <div class="mb-3">
                    <FieldSelect
                      id="product-category"
                      label="Categoria"
                      v-model="form.category_id"
                      required
                    >
                      <option value="">Seleccionar categoria</option>
                      <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.title }}</option>
                    </FieldSelect>
                  </div>
                  <div class="mb-3">
                    <FieldTextarea
                      id="product-description"
                      label="Descripcion"
                      v-model="form.description"
                      :rows="3"
                    />
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="mb-3">
                        <FieldNumber
                          id="product-base-price"
                          label="Precio base"
                          v-model="form.base_price"
                        />
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="mb-3">
                        <FieldNumber
                          id="product-sort"
                          label="Orden"
                          v-model="form.sort_order"
                        />
                      </div>
                    </div>
                  </div>
                  <div class="mb-3">
                    <FieldSwitch
                      id="product-show-price"
                      label="Mostrar precio"
                      v-model="form.show_price"
                    />
                  </div>
                  <div class="mb-3">
                    <FieldSwitch
                      id="product-featured"
                      label="Producto destacado"
                      v-model="form.featured"
                    />
                  </div>
                  <div class="mb-3">
                    <FieldSwitch
                      id="product-active"
                      label="Activo"
                      v-model="form.active"
                    />
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="mb-3">
                    <label class="form-label">Imagen del producto</label>
                    <input ref="imageInput" type="file" class="form-control" accept="image/jpeg,image/png" @change="handleImageChange">
                    <small class="text-muted d-block">JPG o PNG, max 10MB</small>
                    <div v-if="imagePreview" class="mt-2">
                      <img :src="imagePreview" class="img-thumbnail w-100" alt="Preview">
                    </div>
                  </div>
                </div>
              </div>

              <hr>

              <h6>Variantes</h6>
              <div v-for="(variant, index) in form.variants" :key="index" class="border rounded p-3 mb-2">
                <div class="row">
                  <div class="col-md-4">
                    <input v-model="variant.title" type="text" class="form-control" placeholder="Nombre (ej: Chica)">
                  </div>
                  <div class="col-md-3">
                    <input v-model.number="variant.price" type="number" step="0.01" class="form-control" placeholder="Precio">
                  </div>
                  <div class="col-md-3">
                    <input v-model.number="variant.sort_order" type="number" class="form-control" placeholder="Orden">
                  </div>
                  <div class="col-md-2">
                    <button type="button" class="btn btn-outline-danger w-100" @click="removeVariant(index)">
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </div>
              </div>
              <button type="button" class="btn btn-outline-secondary btn-sm" @click="addVariant">
                <i class="bi bi-plus"></i> Agregar variante
              </button>
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
import FieldNumber from '@/Components/Fields/FieldNumber.vue'
import FieldTextarea from '@/Components/Fields/FieldTextarea.vue'
import FieldSelect from '@/Components/Fields/FieldSelect.vue'
import FieldSwitch from '@/Components/Fields/FieldSwitch.vue'

const props = defineProps({
  business: Object,
  products: Object,
  categories: Array,
  selectedCategory: [Number, String],
})

const business = computed(() => props.business)

const breadcrumbs = computed(() => [
  { label: business.value?.name, href: `/member/businesses/${business.value?.id}/content` },
  { label: 'Productos del Menu', active: true },
])

const modalElement = ref(null)
let productModal = null

const showCreateModal = ref(false)
const editingProduct = ref(null)
const sending = ref(false)
const filterCategory = ref(props.selectedCategory)
const imageInput = ref(null)
const imagePreview = ref(null)

const productsList = computed(() => {
  if (!props.products) return []
  if (Array.isArray(props.products)) return props.products
  if (Array.isArray(props.products.data)) return props.products.data
  return []
})

const selectedCategoryName = computed(() => {
  if (!filterCategory.value) return null
  if (filterCategory.value === 'uncategorized') return 'Sin categoria'
  const cat = props.categories.find(c => c.id === filterCategory.value)
  return cat?.title
})

const form = ref({
  title: '',
  category_id: '',
  description: '',
  image: null,
  base_price: null,
  show_price: true,
  featured: false,
  active: true,
  sort_order: 0,
  variants: [],
})

const openCreateModal = () => {
  editingProduct.value = null
  imagePreview.value = null
  form.value = {
    title: '',
    category_id: '',
    description: '',
    image: null,
    base_price: null,
    show_price: true,
    featured: false,
    active: true,
    sort_order: 0,
    variants: [],
  }
  nextTick(() => productModal.show())
}

const handleImageChange = (e) => {
  const file = e.target.files[0]
  if (!file) return

  const maxSize = 10 * 1024 * 1024
  if (file.size > maxSize) {
    alert('El archivo supera el tamano maximo de 10MB.')
    return
  }

  const allowedTypes = ['image/jpeg', 'image/png']
  if (!allowedTypes.includes(file.type)) {
    alert('Solo se permiten imagenes JPG o PNG.')
    return
  }

  form.value.image = file

  const reader = new FileReader()
  reader.onload = (e) => {
    imagePreview.value = e.target.result
  }
  reader.readAsDataURL(file)
}

const filterProducts = () => {
  let url = `/member/businesses/${props.business.id}/menu-products`
  if (filterCategory.value) {
    if (filterCategory.value === 'uncategorized') {
      url += '?uncategorized=1'
    } else {
      url += `?category=${filterCategory.value}`
    }
  }
  window.location.href = url
}

const clearFilter = () => {
  filterCategory.value = null
  window.location.href = `/member/businesses/${props.business.id}/menu-products`
}

const editProduct = (product) => {
  editingProduct.value = product
  imagePreview.value = product.image || null
  form.value = {
    title: product.title,
    category_id: product.category_id,
    description: product.description || '',
    image: null,
    base_price: product.base_price,
    show_price: product.show_price,
    featured: product.featured,
    active: product.active,
    sort_order: product.sort_order || 0,
    variants: product.variants?.map(v => ({
      id: v.id,
      title: v.title,
      price: v.price,
      sort_order: v.sort_order || 0,
    })) || [],
  }
  nextTick(() => productModal.show())
}

const deleteProduct = (product) => {
  if (!confirm(`Eliminar el producto "${product.title}"?`)) return

  router.delete(`/member/businesses/${props.business.id}/menu-products/${product.id}`, {
    preserveScroll: true,
  })
}

const closeModal = () => {
  productModal.hide()
}

const addVariant = () => {
  form.value.variants.push({
    title: '',
    price: null,
    sort_order: 0,
  })
}

const removeVariant = (index) => {
  form.value.variants.splice(index, 1)
}

const submitForm = () => {
  sending.value = true

  if (editingProduct.value) {
    router.post(`/member/businesses/${props.business.id}/menu-products/${editingProduct.value.id}`, {
      ...form.value,
      _method: 'PUT',
    }, {
      forceFormData: true,
      preserveScroll: true,
      onSuccess: () => {
        closeModal()
        sending.value = false
      },
      onError: (errors) => {
        sending.value = false
        console.error('Errors:', errors)
        alert('Error al actualizar: ' + Object.values(errors).join(', '))
      },
    })
  } else {
    router.post(`/member/businesses/${props.business.id}/menu-products`, form.value, {
      forceFormData: true,
      preserveScroll: true,
      onSuccess: () => {
        closeModal()
        sending.value = false
      },
      onError: (errors) => {
        sending.value = false
        console.error('Errors:', errors)
        alert('Error al crear: ' + Object.values(errors).join(', '))
      },
    })
  }
}

onMounted(() => {
  productModal = new Modal(modalElement.value)
})
</script>
