<template>
  <MemberLayout>
    <Head :title="`Productos - ${business?.name || ''}`" />

    <PageHeader
      title="Productos"
      :breadcrumbs="breadcrumbs"
      :backHref="'/member/business-modules'"
    >
      <template #actions>
        <button class="btn btn-primary" @click="openCreateModal">
          <i class="bi bi-plus me-1"></i>Nuevo producto
        </button>
      </template>
    </PageHeader>

    <BaseDataTable
      ref="dataTableRef"
      :endpoint="`/member/businesses/${business?.id}/products`"
      :columns="columns"
      :initial-data="dataTable"
      :initial-per-page="perPage"
      :reorderable="true"
      :reorder-endpoint="`/member/businesses/${business?.id}/products/reorder`"
      search-placeholder="Buscar productos..."
      empty-title="No hay productos"
      empty-text="Comienza creando tu primer producto."
      @updated="onDataTableUpdated"
    >
      <template #cell-name="{ row }">
        <strong>{{ row.name }}</strong>
        <p v-if="row.description" class="text-muted small mb-0">{{ row.description.substring(0, 60) }}...</p>
      </template>

      <template #cell-category="{ row }">
        <span v-if="row.category">{{ row.category.name }}</span>
        <span v-else class="text-muted">-</span>
      </template>

      <template #cell-price="{ row }">
        <span v-if="row.price" class="fw-semibold">${{ row.price }}</span>
        <span v-else class="text-muted">-</span>
      </template>

      <template #cell-quantity="{ row }">
        <span v-if="row.quantity !== null">{{ row.quantity }}</span>
        <span v-else class="text-muted">-</span>
      </template>

      <template #cell-location="{ row }">
        <span v-if="row.location">{{ row.location.name }}</span>
        <span v-else class="text-muted">Todas</span>
      </template>

      <template #cell-is_active="{ row }">
        <span :class="row.is_active ? 'badge bg-success' : 'badge bg-secondary'">
          {{ row.is_active ? 'Activo' : 'Inactivo' }}
        </span>
      </template>

      <template #cell-actions="{ row }">
        <div class="actions">
          <Link
            :href="`/member/businesses/${business?.id}/products/${row.id}/edit`"
            class="btn btn-sm btn-outline-primary"
          >
            <i class="bi bi-pencil"></i>
          </Link>
          <button
            class="btn btn-sm btn-outline-danger"
            @click="deleteProduct(row)"
            :disabled="deleting === row.id"
          >
            <i class="bi bi-trash"></i>
          </button>
        </div>
      </template>
    </BaseDataTable>

    <div ref="modalElement" class="modal fade" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Nuevo Producto</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form @submit.prevent="createProduct">
            <div class="modal-body">
              <div class="row g-3">
                <div class="col-12 col-md-8">
                  <FieldText
                    id="product-name"
                    label="Nombre"
                    v-model="form.name"
                    required
                  />
                </div>

                <div class="col-12 col-md-4">
                  <FieldText
                    id="product-slug"
                    label="Slug"
                    v-model="form.slug"
                  />
                </div>

                <div class="col-12">
                  <FieldTextarea
                    id="product-description"
                    label="Descripcion"
                    v-model="form.description"
                    :rows="2"
                  />
                </div>

                <div class="col-12 col-md-6">
                  <FieldSelect
                    id="product-category"
                    label="Categoria"
                    v-model="form.category_id"
                  >
                    <option :value="null">Sin categoria</option>
                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                  </FieldSelect>
                </div>

                <div class="col-12 col-md-6">
                  <FieldSelect
                    id="product-location"
                    label="Ubicacion"
                    v-model="form.business_location_id"
                  >
                    <option :value="null">Todas las ubicaciones</option>
                    <option v-for="loc in locations" :key="loc.id" :value="loc.id">{{ loc.name }}</option>
                  </FieldSelect>
                </div>

                <div class="col-12 col-md-4">
                  <FieldNumber
                    id="product-price"
                    label="Precio"
                    v-model="form.price"
                  />
                </div>

                <div class="col-12 col-md-4">
                  <FieldNumber
                    id="product-compare-price"
                    label="Precio anterior"
                    v-model="form.compare_at_price"
                  />
                </div>

                <div class="col-12 col-md-4">
                  <FieldNumber
                    id="product-quantity"
                    label="Cantidad en stock"
                    v-model="form.quantity"
                  />
                </div>

                <div class="col-12 col-md-6">
                  <FieldText
                    id="product-sku"
                    label="SKU"
                    v-model="form.sku"
                  />
                </div>

                <div class="col-12 col-md-6">
                  <FieldText
                    id="product-barcode"
                    label="Codigo de barras"
                    v-model="form.barcode"
                  />
                </div>

                <div class="col-12 col-md-4">
                  <FieldSwitch
                    id="product-featured"
                    label="Producto destacado"
                    v-model="form.is_featured"
                  />
                </div>

                <div class="col-12 col-md-4">
                  <FieldPhone
                    id="product-whatsapp"
                    label="WhatsApp"
                    v-model="form.whatsapp_contact"
                  />
                </div>

                <div class="col-12 col-md-4">
                  <FieldSwitch
                    id="product-active"
                    label="Producto activo"
                    v-model="form.is_active"
                  />
                </div>
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
import { ref, computed, onMounted, nextTick } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { Modal } from 'bootstrap'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import BaseDataTable from '@/Components/DataTable/BaseDataTable.vue'
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldNumber from '@/Components/Fields/FieldNumber.vue'
import FieldTextarea from '@/Components/Fields/FieldTextarea.vue'
import FieldSelect from '@/Components/Fields/FieldSelect.vue'
import FieldSwitch from '@/Components/Fields/FieldSwitch.vue'
import FieldPhone from '@/Components/Fields/FieldPhone.vue'

const page = usePage()
const business = computed(() => page.props.business)
const products = computed(() => page.props.products || { data: [] })
const locations = computed(() => page.props.locations || [])
const categories = computed(() => page.props.categories || [])
const dataTable = computed(() => page.props.dataTable)

const breadcrumbs = computed(() => [
  { label: business.value?.name, href: '/member/business-modules' },
  { label: 'Productos', active: true },
])

const columns = [
  { key: 'name', label: 'Nombre', sortable: true },
  { key: 'category', label: 'Categoria', sortable: false },
  { key: 'price', label: 'Precio', sortable: true },
  { key: 'quantity', label: 'Stock', sortable: true },
  { key: 'location', label: 'Ubicacion', sortable: false },
  { key: 'is_active', label: 'Estado', sortable: true },
  { key: 'actions', label: 'Acciones', sortable: false },
]

const dataTableRef = ref(null)
const modalElement = ref(null)
let productModal = null

const creating = ref(false)
const deleting = ref(null)
const perPage = ref(10)

const form = ref({
  name: '',
  slug: '',
  category_id: null,
  description: '',
  price: '',
  compare_at_price: '',
  sku: '',
  barcode: '',
  quantity: '',
  is_active: true,
  is_featured: false,
  whatsapp_contact: '',
  business_location_id: null,
})

const onDataTableUpdated = (data) => {
  perPage.value = data.per_page
}

const openCreateModal = () => {
  form.value.name = ''
  form.value.slug = ''
  form.value.category_id = null
  form.value.description = ''
  form.value.price = ''
  form.value.compare_at_price = ''
  form.value.sku = ''
  form.value.barcode = ''
  form.value.quantity = ''
  form.value.is_active = true
  form.value.is_featured = false
  form.value.whatsapp_contact = ''
  form.value.business_location_id = null
  nextTick(() => {
    productModal.show()
  })
}

const closeCreateModal = () => {
  productModal.hide()
}

const createProduct = () => {
  creating.value = true
  const data = { ...form.value }
  if (!data.slug && data.name) {
    data.slug = data.name.toLowerCase().replace(/\s+/g, '-').replace(/[^a-z0-9-]/g, '')
  }
  router.post(`/member/businesses/${business.value.id}/products`, data, {
    onFinish: () => {
      creating.value = false
      closeCreateModal()
      if (dataTableRef.value) {
        dataTableRef.value.reload()
      }
    },
  })
}

const deleteProduct = (product) => {
  if (confirm(`Eliminar el producto "${product.name}"?`)) {
    deleting.value = product.id
    router.delete(`/member/businesses/${business.value.id}/products/${product.id}`, {
      preserveScroll: true,
      onFinish: () => {
        deleting.value = null
        if (dataTableRef.value) {
          dataTableRef.value.reload()
        }
      },
    })
  }
}

onMounted(() => {
  productModal = new Modal(modalElement.value)
  productModal._element.addEventListener('hidden.bs.modal', () => {
    form.value = {
      name: '',
      slug: '',
      category_id: null,
      description: '',
      price: '',
      compare_at_price: '',
      sku: '',
      barcode: '',
      quantity: '',
      is_active: true,
      is_featured: false,
      whatsapp_contact: '',
      business_location_id: null,
    }
  })
})
</script>
