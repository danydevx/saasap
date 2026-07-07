<template>
  <MemberLayout>
    <Head :title="`Nuevo Producto - ${business.name}`" />

    <PageHeader
      :title="'Nuevo Producto'"
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
                placeholder="Shampoo premium"
                v-model="form.name"
                :formError="form.errors.name"
                required
              />
            </div>

            <div class="col-12 col-md-4">
              <FieldText
                id="product-slug"
                label="Slug"
                placeholder="shampoo-premium"
                v-model="form.slug"
                :formError="form.errors.slug"
              />
              <small class="text-muted">Se genera automaticamente si se deja vacio.</small>
            </div>

            <div class="col-12">
              <div class="mb-3">
                <label for="product-description" class="form-label">Descripcion</label>
                <textarea
                  id="product-description"
                  class="form-control"
                  rows="3"
                  v-model="form.description"
                  :class="{ 'is-invalid': form.errors.description }"
                ></textarea>
                <div v-if="form.errors.description" class="invalid-feedback">{{ form.errors.description }}</div>
              </div>
            </div>

            <div class="col-12 col-md-6">
              <label for="product-location" class="form-label">Ubicacion</label>
              <select id="product-location" class="form-select" v-model="form.business_location_id" :class="{ 'is-invalid': form.errors.business_location_id }">
                <option value="">Todas las ubicaciones</option>
                <option v-for="loc in locations" :key="loc.id" :value="loc.id">
                  {{ loc.name }}
                </option>
              </select>
              <div v-if="form.errors.business_location_id" class="invalid-feedback">{{ form.errors.business_location_id }}</div>
            </div>

            <div class="col-12 col-md-3">
              <FieldNumber
                id="product-price"
                label="Precio"
                placeholder="0.00"
                v-model="form.price"
                :formError="form.errors.price"
              />
            </div>

            <div class="col-12 col-md-3">
              <FieldNumber
                id="product-compare-price"
                label="Precio anterior"
                placeholder="0.00"
                v-model="form.compare_at_price"
                :formError="form.errors.compare_at_price"
              />
            </div>

            <div class="col-12 col-md-4">
              <FieldText
                id="product-sku"
                label="SKU"
                placeholder="SKU-001"
                v-model="form.sku"
                :formError="form.errors.sku"
              />
            </div>

            <div class="col-12 col-md-4">
              <FieldText
                id="product-barcode"
                label="Codigo de barras"
                placeholder="123456789"
                v-model="form.barcode"
                :formError="form.errors.barcode"
              />
            </div>

            <div class="col-12 col-md-4">
              <FieldNumber
                id="product-quantity"
                label="Cantidad en stock"
                placeholder="0"
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
              <FieldSwitch
                id="product-whatsapp-contact"
                label="Contactar por WhatsApp"
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
              {{ form.processing ? 'Creando...' : 'Crear Producto' }}
            </button>
            <Link :href="`/member/businesses/${business.id}/products`" class="btn btn-outline-secondary">Cancelar</Link>
          </div>
        </form>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { computed, watch } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldNumber from '@/Components/Fields/FieldNumber.vue'
import FieldSwitch from '@/Components/Fields/FieldSwitch.vue'

const props = defineProps({
  business: { type: Object, required: true },
  locations: { type: Array, default: () => [] },
})

const business = computed(() => props.business)

const form = useForm({
  name: '',
  slug: '',
  description: '',
  price: '',
  compare_at_price: '',
  sku: '',
  barcode: '',
  quantity: '',
  is_active: true,
  is_featured: false,
  whatsapp_contact: false,
  sort_order: 0,
  business_location_id: '',
})

watch(() => form.name, (val) => {
  if (val && !form.slug) {
    form.slug = val.toLowerCase().replace(/\s+/g, '-').replace(/[^a-z0-9-]/g, '')
  }
})

const breadcrumbs = computed(() => [
  { label: business.value.name, href: `/member/businesses/${business.value.id}/products` },
  { label: 'Nuevo', active: true },
])

const submit = () => {
  form.post(`/member/businesses/${business.value.id}/products`)
}
</script>
