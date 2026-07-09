<template>
  <MemberLayout>
    <Head :title="`Nueva Promocion - ${business.name}`" />

    <PageHeader
      title="Nueva Promocion"
      :breadcrumbs="breadcrumbs"
      :backHref="`/member/businesses/${business.id}/promotions`"
    />

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <form @submit.prevent="submit">
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <FieldText
                id="promotion-name"
                label="Nombre"
                v-model="form.name"
                :formError="errors.name"
                required
              />
            </div>

            <div class="col-md-6">
              <FieldSelect
                id="promotion-location"
                label="Ubicacion"
                v-model="form.business_location_id"
              >
                <option :value="null">Todas las ubicaciones</option>
                <option v-for="loc in locations" :key="loc.id" :value="loc.id">{{ loc.name }}</option>
              </FieldSelect>
            </div>

            <div class="col-12">
              <FieldTextarea
                id="promotion-description"
                label="Descripcion"
                v-model="form.description"
                :rows="3"
              />
            </div>

            <div class="col-12">
              <label class="form-label">Imagen de la promocion</label>
              <input
                ref="imageInput"
                type="file"
                class="form-control"
                accept="image/jpeg,image/png,image/webp,image/gif"
                @change="handleImageChange"
              />
              <div v-if="imagePreview" class="mt-2">
                <img :src="imagePreview" class="img-thumbnail" style="max-height: 150px;" alt="Preview" />
              </div>
              <small class="text-muted">JPG, PNG o WebP, max 5MB</small>
            </div>

            <div class="col-md-4">
              <FieldNumber
                id="promotion-regular-price"
                label="Precio Regular"
                v-model="form.regular_price"
              />
            </div>

            <div class="col-md-4">
              <FieldNumber
                id="promotion-price"
                label="Precio Promo"
                v-model="form.promotion_price"
              />
            </div>

            <div class="col-md-4">
              <FieldText
                id="promotion-coupon"
                label="Codigo de Cupon"
                v-model="form.coupon_code"
              />
            </div>

            <div class="col-md-6">
              <FieldDate
                id="promotion-starts"
                label="Fecha de Inicio"
                v-model="form.starts_at"
              />
            </div>

            <div class="col-md-6">
              <FieldDate
                id="promotion-expires"
                label="Fecha de Vencimiento"
                v-model="form.expires_at"
              />
            </div>

            <div class="col-md-4">
              <FieldNumber
                id="promotion-sort"
                label="Orden"
                v-model="form.sort_order"
              />
              <small class="text-muted">Menor numero aparece primero.</small>
            </div>

            <div class="col-md-8 d-flex align-items-end">
              <FieldSwitch
                id="promotion-active"
                label="Activo"
                v-model="form.is_active"
              />
            </div>

            <div class="col-12">
              <button type="submit" class="btn btn-primary" :disabled="sending">
                {{ sending ? 'Guardando...' : 'Guardar' }}
              </button>
              <Link :href="`/member/businesses/${business.id}/promotions`" class="btn btn-outline-secondary ms-2">
                Cancelar
              </Link>
            </div>
          </div>
        </form>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { computed, reactive, ref } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldNumber from '@/Components/Fields/FieldNumber.vue'
import FieldTextarea from '@/Components/Fields/FieldTextarea.vue'
import FieldSelect from '@/Components/Fields/FieldSelect.vue'
import FieldDate from '@/Components/Fields/FieldDate.vue'
import FieldSwitch from '@/Components/Fields/FieldSwitch.vue'

const page = usePage()
const business = computed(() => page.props.business)

const breadcrumbs = computed(() => [
  { label: business.value.name, href: '/member/business-modules' },
  { label: 'Promociones', href: `/member/businesses/${business.value.id}/promotions` },
  { label: 'Nueva', active: true },
])
const locations = computed(() => page.props.locations || [])
const errors = computed(() => page.props.errors || {})
const sending = computed(() => false)

const imageInput = ref(null)
const imagePreview = ref(null)
const imageFile = ref(null)

const form = reactive({
  name: '',
  description: '',
  business_location_id: null,
  regular_price: '',
  promotion_price: '',
  coupon_code: '',
  starts_at: '',
  expires_at: '',
  sort_order: 0,
  is_active: true,
})

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

  imageFile.value = file
  imagePreview.value = URL.createObjectURL(file)
}

const submit = () => {
  const formData = new FormData()
  formData.append('name', form.name)
  formData.append('description', form.description || '')
  formData.append('business_location_id', form.business_location_id || '')
  formData.append('regular_price', form.regular_price || '')
  formData.append('promotion_price', form.promotion_price || '')
  formData.append('coupon_code', form.coupon_code || '')
  formData.append('starts_at', form.starts_at || '')
  formData.append('expires_at', form.expires_at || '')
  formData.append('sort_order', form.sort_order || 0)
  formData.append('is_active', form.is_active ? '1' : '0')

  if (imageFile.value) {
    formData.append('image', imageFile.value)
  }

  router.post(`/member/businesses/${business.value.id}/promotions`, formData)
}
</script>
