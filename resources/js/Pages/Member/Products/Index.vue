<template>
  <MemberLayout>
    <Head :title="business ? `Productos - ${business.name}` : 'Productos'" />

    <PageHeader
      title="Productos del Menu"
      :breadcrumbs="breadcrumbs"
      :backHref="'/member/business-modules'"
    >
      <template #description>
        <p class="text-muted mb-0">Gestiona los productos de tu menu. Arrastra para reordenar.</p>
      </template>
      <template #actions>
        <Link :href="`/member/businesses/${business?.id}/menu-categories`" class="btn btn-outline-secondary btn-sm">
          <i class="bi bi-folder me-1"></i>Categorias
        </Link>
        <button class="btn btn-primary btn-sm" @click="openCreateModal">
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

    <div ref="createModalElement" class="modal fade" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Nuevo Producto</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form @submit.prevent="createProduct">
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label">Nombre</label>
                <input v-model="createForm.title" type="text" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Descripcion</label>
                <textarea v-model="createForm.description" class="form-control" rows="2"></textarea>
              </div>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Precio</label>
                  <input v-model="createForm.base_price" type="number" step="0.01" class="form-control">
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Mostrar precio</label>
                  <select v-model="createForm.show_price" class="form-select">
                    <option :value="true">Si</option>
                    <option :value="false">No</option>
                  </select>
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label">Categoria</label>
                <select v-model="createForm.category_id" class="form-select">
                  <option :value="null">Sin categoria</option>
                  <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.title }}</option>
                </select>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-primary" :disabled="creating">
                {{ creating ? 'Creando...' : 'Crear Producto' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { ref, computed, nextTick, onMounted } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import SortableCards from '@/Components/DataTable/SortableCards.vue'
import { Modal } from 'bootstrap'

const props = defineProps({
  business: Object,
  products: Object,
  categories: Array,
  selectedCategory: [Number, String],
})

const business = computed(() => props.business)

const breadcrumbs = computed(() => [
  { label: 'Mis Negocios', href: '/member/business-modules' },
  { label: 'Productos del Menu', active: true },
])

const sortableCardsRef = ref(null)
const createModalElement = ref(null)
let createModal = null

const loading = ref(false)
const creating = ref(false)
const filterCategory = ref(props.selectedCategory)

const createForm = ref({
  title: '',
  description: '',
  base_price: '',
  show_price: true,
  category_id: null,
})

const openCreateModal = () => {
  createForm.value = {
    title: '',
    description: '',
    base_price: '',
    show_price: true,
    category_id: props.selectedCategory || null,
  }
  nextTick(() => {
    createModal.show()
  })
}

const createProduct = () => {
  creating.value = true
  router.post(`/member/businesses/${business.value.id}/menu-products`, {
    title: createForm.value.title,
    description: createForm.value.description,
    base_price: createForm.value.base_price || null,
    show_price: createForm.value.show_price,
    category_id: createForm.value.category_id,
  }, {
    onSuccess: () => {
      creating.value = false
      createModal.hide()
    },
    onError: () => {
      creating.value = false
    },
  })
}

onMounted(() => {
  if (createModalElement.value) {
    createModal = new Modal(createModalElement.value)
  }
})

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

const onReordered = (ids) => {
  console.log('Reordered:', ids)
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
</script>

<style scoped>
.card-img-top {
  height: 120px;
}
</style>
