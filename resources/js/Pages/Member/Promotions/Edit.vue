<template>
  <MemberLayout>
    <Head :title="`Editar Promocion - ${business.name}`" />

    <PageHeader
      title="Editar Promocion"
      :breadcrumbs="breadcrumbs"
      :backHref="`/member/businesses/${business.id}/promotions`"
    />

    <div v-if="$page.props.flash?.success" class="alert alert-success alert-dismissible fade show" role="alert">
      {{ $page.props.flash.success }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <div v-if="$page.props.flash?.error" class="alert alert-danger alert-dismissible fade show" role="alert">
      {{ $page.props.flash.error }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>

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
              <div v-if="imagePreview || form.image" class="mt-2 d-flex align-items-center gap-2">
                <img :src="imagePreview || form.image" class="img-thumbnail" style="max-height: 150px;" alt="Preview" />
                <button
                  v-if="imagePreview || form.image"
                  type="button"
                  class="btn btn-sm btn-outline-danger"
                  @click="removeImage"
                >
                  <i class="bi bi-trash"></i> Quitar
                </button>
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

            <div class="col-12">
              <div v-if="promotion.qr_code_path" class="card bg-light p-3">
                <div class="d-flex align-items-center gap-3">
                  <img :src="promotion.qr_code_path" alt="QR Code" class="rounded" style="max-height: 120px;">
                  <div>
                    <strong>Codigo QR</strong>
                    <p class="text-muted small mb-2">Escanealo para verificar la promocion.</p>
                    <button
                      type="button"
                      class="btn btn-sm btn-outline-primary"
                      :disabled="regenerating"
                      @click="regenerateQr"
                    >
                      <i class="bi bi-arrow-clockwise me-1"></i>
                      {{ regenerating ? 'Regenerando...' : 'Regenerar QR' }}
                    </button>
                  </div>
                </div>
              </div>
              <div v-else-if="form.coupon_code" class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                El codigo QR se generara automaticamente al guardar.
              </div>
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
              <button
                type="button"
                class="btn btn-outline-danger ms-auto"
                @click="deletePromotion"
              >
                <i class="bi bi-trash me-1"></i>
                Eliminar
              </button>
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
const promotion = computed(() => page.props.promotion)
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
        { label: 'Promociones', href: `/member/businesses/${biz.id}/promotions` },
        { label: 'Editar Promocion', active: true },
      ]
    }
  }
  return [
    { label: 'Mis Negocios', href: '/member/business-modules' },
    { label: 'Editar Promocion', active: true },
  ]
})
const locations = computed(() => page.props.locations || [])
const errors = computed(() => page.props.errors || {})
const sending = computed(() => false)

const imageInput = ref(null)
const imagePreview = ref(null)
const imageFile = ref(null)
const removeImageFlag = ref(false)
const regenerating = ref(false)

const form = reactive({
  name: promotion.value.name,
  description: promotion.value.description || '',
  business_location_id: promotion.value.business_location_id,
  regular_price: promotion.value.regular_price || '',
  promotion_price: promotion.value.promotion_price || '',
  coupon_code: promotion.value.coupon_code || '',
  starts_at: promotion.value.starts_at || '',
  expires_at: promotion.value.expires_at || '',
  sort_order: promotion.value.sort_order || 0,
  is_active: promotion.value.is_active || false,
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
  removeImageFlag.value = false
  imagePreview.value = URL.createObjectURL(file)
}

const removeImage = () => {
  imageFile.value = null
  imagePreview.value = null
  removeImageFlag.value = true
  if (imageInput.value) {
    imageInput.value.value = ''
  }
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
  formData.append('_method', 'PUT')

  if (imageFile.value) {
    formData.append('image', imageFile.value)
  }
  if (removeImageFlag.value) {
    formData.append('remove_image', '1')
  }

  router.post(`/member/businesses/${business.value.id}/promotions/${promotion.value.id}`, formData)
}

const regenerateQr = () => {
  if (!confirm('Regenerar el codigo QR?')) return
  regenerating.value = true
  router.post(`/member/businesses/${business.value.id}/promotions/${promotion.value.id}/regenerate-qr`, {
    onSuccess: () => {
      regenerating.value = false
      console.log('QR regenerated, reloading...')
      window.location.reload()
    },
    onError: (errors) => {
      regenerating.value = false
      console.log('Error regenerating:', errors)
      alert('Error al regenerar el codigo QR')
    },
  })
}

const deletePromotion = () => {
  if (!confirm(`Eliminar la promocion "${promotion.value.name}"?`)) return
  router.delete(`/member/businesses/${business.value.id}/promotions/${promotion.value.id}`, {
    onSuccess: () => {
      window.location.href = `/member/businesses/${business.value.id}/promotions`
    },
  })
}
</script>
