<template>
  <MemberLayout>
    <Head :title="`Nuevo Producto - ${business?.name || ''}`" />

    <PageHeader
      title="Nuevo Producto"
      :breadcrumbs="breadcrumbs"
      :backHref="`/member/businesses/${business?.id}/menu-products`"
    />

    <div class="container-fluid py-4">
      <form @submit.prevent="submitForm">
        <div class="row">
          <div class="col-lg-8">
            <div class="card mb-4">
              <div class="card-header">
                <h5 class="mb-0">Datos del producto</h5>
              </div>
              <div class="card-body">
                <div class="mb-3">
                  <FieldText
                    id="product-title"
                    label="Nombre del producto"
                    v-model="form.title"
                    :formError="errors.title"
                    required
                  />
                </div>
                <div class="mb-3">
                  <FieldSelect
                    id="product-category"
                    label="Categoria"
                    v-model="form.category_id"
                    :formError="errors.category_id"
                    required
                  >
                    <option value="" disabled>Seleccionar...</option>
                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.title }}</option>
                  </FieldSelect>
                </div>
                <div class="mb-3">
                  <FieldTextarea
                    id="product-description"
                    label="Descripcion"
                    v-model="form.description"
                    :formError="errors.description"
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
                        :formError="errors.base_price"
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
            </div>

            <div class="card mb-4">
              <div class="card-header">
                <h5 class="mb-0">Imagen del producto</h5>
              </div>
              <div class="card-body">
                <div class="mb-3">
                  <input ref="imageInput" type="file" class="form-control" accept="image/jpeg,image/png" @change="handleImageChange">
                  <small class="text-muted d-block">JPG o PNG, max 10MB</small>
                </div>
                <div v-if="imagePreview" class="mt-2">
                  <img :src="imagePreview" class="img-thumbnail" alt="Preview" style="max-height: 200px;">
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-4">
            <div class="card mb-4">
              <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Variantes</h5>
                <button type="button" class="btn btn-sm btn-outline-secondary" @click="addVariant">
                  <i class="bi bi-plus me-1"></i>Agregar
                </button>
              </div>
              <div class="card-body p-0">
                <div v-if="form.variants.length === 0" class="text-center text-muted py-4">
                  <p class="mb-0">No hay variantes. Agrega una para definir opciones del producto.</p>
                </div>
                <div v-else ref="variantsList" class="variants-list">
                  <div
                    v-for="(variant, index) in form.variants"
                    :key="index"
                    class="variant-item border-bottom"
                    :data-index="index"
                  >
                    <div class="variant-header d-flex align-items-center justify-content-between p-3" @click="toggleVariant(index)">
                      <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-grip-vertical variant-drag-handle text-muted" style="cursor: grab;"></i>
                        <strong>{{ variant.title || 'Variante ' + (index + 1) }}</strong>
                        <span v-if="variant.price" class="text-muted">${{ variant.price }}</span>
                      </div>
                      <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-sm btn-outline-danger" @click.stop="removeVariant(index)">
                          <i class="bi bi-trash"></i>
                        </button>
                        <i :class="isVariantExpanded(index) ? 'bi bi-chevron-up' : 'bi bi-chevron-down'"></i>
                      </div>
                    </div>
                    <div v-if="isVariantExpanded(index)" class="variant-body p-3 pt-0">
                      <div class="row">
                        <div class="col-6 mb-2">
                          <label class="form-label small">Nombre</label>
                          <input v-model="variant.title" type="text" class="form-control" placeholder="Ej: Chica">
                        </div>
                        <div class="col-6 mb-2">
                          <label class="form-label small">Precio</label>
                          <input v-model.number="variant.price" type="number" step="0.01" class="form-control" placeholder="0.00">
                        </div>
                      </div>
                      <div class="mb-2">
                        <label class="form-label small">Imagen de variante</label>
                        <input :ref="el => variantImageInputs[index] = el" type="file" class="form-control" accept="image/jpeg,image/png" @change="e => handleVariantImageChange(e, index)">
                        <small class="text-muted d-block">JPG o PNG, max 5MB</small>
                      </div>
                      <div v-if="variantImagePreviews[index]" class="mb-2">
                        <img :src="variantImagePreviews[index]" class="img-thumbnail" alt="Variante" style="max-height: 80px;">
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-primary flex-grow-1" :disabled="sending">
                {{ sending ? 'Creando...' : 'Crear Producto' }}
              </button>
              <Link :href="`/member/businesses/${business?.id}/menu-products`" class="btn btn-outline-secondary">
                Cancelar
              </Link>
            </div>
          </div>
        </div>
      </form>
    </div>
  </MemberLayout>
</template>

<script setup>
import { ref, computed, reactive, nextTick } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import Sortable from 'sortablejs'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldNumber from '@/Components/Fields/FieldNumber.vue'
import FieldTextarea from '@/Components/Fields/FieldTextarea.vue'
import FieldSelect from '@/Components/Fields/FieldSelect.vue'
import FieldSwitch from '@/Components/Fields/FieldSwitch.vue'

const page = usePage()
const business = computed(() => page.props.business)
const categories = computed(() => page.props.categories || [])
const errors = computed(() => {
  const errs = page.props.errors || {}
  const normalized = {}
  for (const [key, value] of Object.entries(errs)) {
    normalized[key] = Array.isArray(value) ? value.join(', ') : value
  }
  return normalized
})
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
        { label: 'Productos', href: `/member/businesses/${biz.id}/menu-products` },
        { label: 'Nuevo Producto', active: true },
      ]
    }
  }
  return [
    { label: 'Mis Negocios', href: '/member/business-modules' },
    { label: 'Menu del Restaurant', active: true },
    { label: 'Nuevo Producto', active: true },
  ]
})

const imageInput = ref(null)
const imagePreview = ref(null)
const variantsList = ref(null)
const sending = ref(false)

const variantImageInputs = ref([])
const variantImagePreviews = reactive({})
const expandedVariants = ref([])

let sortableInstance = null

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

const isVariantExpanded = (index) => {
  return expandedVariants.value.includes(index)
}

const toggleVariant = (index) => {
  const pos = expandedVariants.value.indexOf(index)
  if (pos > -1) {
    expandedVariants.value.splice(pos, 1)
  } else {
    expandedVariants.value.push(index)
  }
}

const initSortable = () => {
  if (!variantsList.value) return

  if (sortableInstance) {
    sortableInstance.destroy()
  }

  sortableInstance = Sortable.create(variantsList.value, {
    handle: '.variant-drag-handle',
    animation: 150,
    ghostClass: 'sortable-ghost',
    dragClass: 'sortable-drag',
    onEnd: (evt) => {
      const items = form.value.variants.splice(evt.oldIndex, 1)[0]
      form.value.variants.splice(evt.newIndex, 0, items)
    },
  })
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

const handleVariantImageChange = (e, index) => {
  const file = e.target.files[0]
  if (!file) return

  if (file.size > 5 * 1024 * 1024) {
    alert('El archivo supera el tamano maximo de 5MB.')
    return
  }

  const allowedTypes = ['image/jpeg', 'image/png']
  if (!allowedTypes.includes(file.type)) {
    alert('Solo se permiten imagenes JPG o PNG.')
    return
  }

  form.value.variants[index].image = file

  const reader = new FileReader()
  reader.onload = (e) => {
    variantImagePreviews[index] = e.target.result
  }
  reader.readAsDataURL(file)
}

const addVariant = () => {
  form.value.variants.push({
    title: '',
    price: null,
    sort_order: form.value.variants.length,
    image: null,
  })
  const newIndex = form.value.variants.length - 1
  expandedVariants.value.push(newIndex)
  nextTick(() => initSortable())
}

const removeVariant = (index) => {
  form.value.variants.splice(index, 1)
  delete variantImagePreviews[index]
  expandedVariants.value = expandedVariants.value.filter(i => i !== index)
}

const submitForm = () => {
  sending.value = true

  router.post(`/member/businesses/${business.value.id}/menu-products`, {
    ...form.value,
  }, {
    forceFormData: true,
    preserveScroll: true,
    onSuccess: () => {
      sending.value = false
    },
    onError: (errs) => {
      sending.value = false
      console.error('Errors:', errs)
    },
  })
}
</script>

<style scoped>
.variants-list {
  max-height: 500px;
  overflow-y: auto;
}

.variant-header {
  cursor: pointer;
  user-select: none;
}

.variant-header:hover {
  background-color: #f8f9fa;
}

.variant-drag-handle:active {
  cursor: grabbing;
}

.sortable-ghost {
  opacity: 0.4;
  background: #e9ecef;
}

.sortable-drag {
  opacity: 0.8;
  background: white;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}
</style>
