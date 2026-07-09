<template>
  <MemberLayout>
    <Head :title="business ? `Productos - ${business.name}` : 'Productos'" />

    <PageHeader
      title="Productos del Menu"
      :breadcrumbs="breadcrumbs"
      :backHref="`/member/businesses/${business?.id}/modules`"
    >
      <template #description>
        <p class="text-muted mb-0">Gestiona los productos de tu menu. Arrastra para reordenar.</p>
      </template>
      <template #actions>
        <Link :href="`/member/businesses/${business?.id}/menu-categories`" class="btn btn-outline-secondary btn-sm">
          <i class="bi bi-folder me-1"></i>Categorias
        </Link>
        <button @click="openCreateModal" class="btn btn-primary btn-sm">
          <i class="bi bi-plus-lg me-1"></i>Nuevo Producto
        </button>
      </template>
    </PageHeader>

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

    <SortableCards
      ref="sortableCardsRef"
      :items="productsList"
      item-class="col-6 col-md-4 col-lg-2"
      :reorderable="true"
      :reorder-endpoint="`/member/businesses/${business?.id}/menu-products/reorder`"
      :loading="loading"
      empty-title="No hay productos"
      :empty-text="selectedCategoryName ? 'No hay productos en esta categoria.' : 'Comienza creando tu primer producto.'"
      toast-message="Orden actualizado"
      @reordered="onReordered"
    >
      <template #item="{ item: product }">
        <div class="card-img-top ratio ratio-4x3 bg-light d-flex align-items-center justify-content-center overflow-hidden">
          <img
            v-if="product.image"
            :src="product.image"
            :alt="product.title"
            class="w-100 h-100"
            style="object-fit: cover;"
          />
          <img
            v-else
            src="https://placehold.co/400x300/e9ecef/868e96?text=Sin+imagen"
            :alt="product.title"
            class="w-100 h-100"
            style="object-fit: cover;"
          />
        </div>
        <div class="card-body py-2">
          <h6 class="card-title mb-1 text-truncate">{{ product.title }}</h6>
          <p class="card-text small text-muted mb-1 text-truncate">{{ product.category?.title }}</p>
          <div class="d-flex justify-content-between align-items-center">
            <span v-if="product.show_price && product.display_price" class="fw-bold small">${{ product.display_price }}</span>
            <span v-else-if="product.show_price && product.variants?.length" class="fw-bold small">Desde ${{ product.variants[0].price }}</span>
            <span v-else class="text-muted small">-</span>
            <span v-if="product.featured" class="badge bg-warning badge-sm">Dest.</span>
          </div>
        </div>
        <div class="card-footer bg-transparent py-1">
          <div class="d-flex gap-1">
            <Link :href="`/member/businesses/${business?.id}/menu-products/${product.id}/edit`" class="btn btn-sm btn-outline-primary flex-grow-1">
              <i class="bi bi-pencil"></i>
            </Link>
            <button @click="deleteProduct(product)" class="btn btn-sm btn-outline-danger">
              <i class="bi bi-trash"></i>
            </button>
          </div>
        </div>
      </template>
    </SortableCards>

    <div v-if="products.total > products.per_page" class="d-flex justify-content-center mt-4">
      <pagination :data="products" />
    </div>

    <div ref="modalElement" class="modal fade" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Nuevo Producto</h5>
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
                {{ sending ? 'Guardando...' : 'Crear' }}
              </button>
            </div>
          </form>
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
import SortableCards from '@/Components/DataTable/SortableCards.vue'
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
  { label: business.value?.name, href: `/member/businesses/${business.value?.id}/modules` },
  { label: 'Productos del Menu', active: true },
])

const sortableCardsRef = ref(null)
const modalElement = ref(null)
let productModal = null

const loading = ref(false)
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

const onReordered = (ids) => {
  console.log('Reordered:', ids)
}

const openCreateModal = () => {
  imagePreview.value = null
  form.value = {
    title: '',
    category_id: filterCategory.value || '',
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

  if (file.size > 10 * 1024 * 1024) {
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

const deleteProduct = (product) => {
  if (!confirm(`Eliminar el producto "${product.title}"?`)) return

  router.delete(`/member/businesses/${props.business.id}/menu-products/${product.id}`, {
    preserveScroll: true,
  })
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

  router.post(`/member/businesses/${props.business.id}/menu-products`, form.value, {
    forceFormData: true,
    preserveScroll: true,
    onSuccess: () => {
      sending.value = false
      productModal.hide()
    },
    onError: (errors) => {
      sending.value = false
      console.error('Errors:', errors)
      alert('Error al crear: ' + Object.values(errors).join(', '))
    },
  })
}

onMounted(() => {
  productModal = new Modal(modalElement.value)
})
</script>

<style scoped>
.card-img-top {
  height: 120px;
}
</style>
