<template>
  <MemberLayout>
    <Head :title="`Editar Producto - ${business.name}`" />

    <PageHeader
      :title="'Editar Producto'"
      :breadcrumbs="breadcrumbs"
      :backHref="`/member/businesses/${business.id}/products`"
    />

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <form @submit.prevent="submit">
          <div class="row g-3">
            <div class="col-12 col-md-8">
              <FieldText
                id="product-name"
                label="Nombre"
                v-model="form.name"
                :formError="form.errors.name"
                required
              />
            </div>

            <div class="col-12 col-md-4">
              <FieldText
                id="product-slug"
                label="Slug"
                v-model="form.slug"
                :formError="form.errors.slug"
              />
            </div>

            <div class="col-12">
              <FieldTextarea
                id="product-description"
                label="Descripcion"
                v-model="form.description"
                :formError="form.errors.description"
                :rows="3"
              />
            </div>

            <div class="col-12 col-md-6">
              <FieldSelect
                id="product-category"
                label="Categoria"
                v-model="form.category_id"
                :formError="form.errors.category_id"
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
                :formError="form.errors.business_location_id"
              >
                <option :value="null">Todas las ubicaciones</option>
                <option v-for="loc in locations" :key="loc.id" :value="loc.id">{{ loc.name }}</option>
              </FieldSelect>
            </div>

            <div class="col-12 col-md-3">
              <FieldNumber
                id="product-price"
                label="Precio"
                v-model="form.price"
                :formError="form.errors.price"
              />
            </div>

            <div class="col-12 col-md-3">
              <FieldNumber
                id="product-compare-price"
                label="Precio anterior"
                v-model="form.compare_at_price"
                :formError="form.errors.compare_at_price"
              />
            </div>

            <div class="col-12 col-md-4">
              <FieldText
                id="product-sku"
                label="SKU"
                v-model="form.sku"
                :formError="form.errors.sku"
              />
            </div>

            <div class="col-12 col-md-4">
              <FieldText
                id="product-barcode"
                label="Codigo de barras"
                v-model="form.barcode"
                :formError="form.errors.barcode"
              />
            </div>

            <div class="col-12 col-md-4">
              <FieldNumber
                id="product-quantity"
                label="Cantidad en stock"
                v-model="form.quantity"
                :formError="form.errors.quantity"
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
                id="product-whatsapp-contact"
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

          <div class="col-12 d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary" :disabled="form.processing">
              {{ form.processing ? 'Actualizando...' : 'Actualizar Producto' }}
            </button>
            <Link :href="`/member/businesses/${business.id}/products`" class="btn btn-outline-secondary">Cancelar</Link>
          </div>
        </form>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldNumber from '@/Components/Fields/FieldNumber.vue'
import FieldTextarea from '@/Components/Fields/FieldTextarea.vue'
import FieldSelect from '@/Components/Fields/FieldSelect.vue'
import FieldSwitch from '@/Components/Fields/FieldSwitch.vue'
import FieldPhone from '@/Components/Fields/FieldPhone.vue'

const props = defineProps({
  business: { type: Object, required: true },
  product: { type: Object, required: true },
  locations: { type: Array, default: () => [] },
  categories: { type: Array, default: () => [] },
})

const business = computed(() => props.business)

const form = useForm({
  name: props.product.name,
  slug: props.product.slug,
  category_id: props.product.category_id ?? null,
  description: props.product.description || '',
  price: props.product.price || '',
  compare_at_price: props.product.compare_at_price || '',
  sku: props.product.sku || '',
  barcode: props.product.barcode || '',
  quantity: props.product.quantity ?? '',
  is_active: !!props.product.is_active,
  is_featured: !!props.product.is_featured,
  whatsapp_contact: props.product.whatsapp_contact || '',
  sort_order: props.product.sort_order ?? 0,
  business_location_id: props.product.business_location_id ?? null,
})

const breadcrumbs = computed(() => [
  { label: business.value.name, href: `/member/businesses/${business.value.id}/products` },
  { label: 'Editar', active: true },
])

const submit = () => {
  form.put(`/member/businesses/${business.value.id}/products/${props.product.id}`)
}
</script>
