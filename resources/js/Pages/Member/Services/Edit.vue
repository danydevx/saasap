<template>
  <MemberLayout>
    <Head :title="`Editar Servicio - ${business?.name || ''}`" />

    <PageHeader
      :title="'Editar Servicio'"
      :breadcrumbs="breadcrumbs"
      :backHref="`/member/businesses/${business?.id}/services`"
    />

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <form @submit.prevent="submit">
          <div class="row g-3">
            <div class="col-12 col-md-8">
              <FieldText
                id="service-name"
                label="Nombre"
                v-model="form.name"
                :formError="errors.name"
                required
              />
            </div>

            <div class="col-12 col-md-4">
              <FieldText
                id="service-slug"
                label="Slug"
                v-model="form.slug"
                :formError="errors.slug"
              />
            </div>

            <div class="col-12">
              <FieldTextarea
                id="service-description"
                label="Descripcion"
                v-model="form.description"
                :formError="errors.description"
                :rows="3"
              />
            </div>

            <div class="col-12 col-md-6">
              <FieldSelect
                id="service-location"
                label="Ubicacion"
                v-model="form.business_location_id"
                :options="locationOptions"
                :formError="errors.business_location_id"
              />
            </div>

            <div class="col-12 col-md-3">
              <FieldNumber
                id="service-duration"
                label="Duracion (minutos)"
                v-model="form.duration_minutes"
                :formError="errors.duration_minutes"
                required
              />
            </div>

            <div class="col-12 col-md-3">
              <FieldNumber
                id="service-price"
                label="Precio"
                v-model="form.price"
                :formError="errors.price"
              />
            </div>

            <div class="col-12 col-md-4">
              <FieldSwitch
                id="service-deposit-required"
                label="Requiere deposito"
                v-model="form.deposit_required"
              />
            </div>

            <div v-if="form.deposit_required" class="col-12 col-md-4">
              <FieldNumber
                id="service-deposit-amount"
                label="Monto deposito"
                v-model="form.deposit_amount"
                :formError="errors.deposit_amount"
              />
            </div>

            <div class="col-12 col-md-4">
              <FieldSwitch
                id="service-online-booking"
                label="Permite reserva online"
                v-model="form.allows_online_booking"
              />
            </div>

            <div class="col-12 col-md-4">
              <FieldPhone
                id="service-whatsapp"
                label="WhatsApp"
                placeholder="+54 9 11 1234-5678"
                v-model="form.whatsapp_contact"
                :formError="errors.whatsapp_contact"
              />
            </div>

            <div class="col-12 col-md-4">
              <FieldSwitch
                id="service-active"
                label="Servicio activo"
                v-model="form.is_active"
              />
            </div>

            <div class="col-12 col-md-4">
              <FieldNumber
                id="service-sort-order"
                label="Orden"
                placeholder="0"
                v-model="form.sort_order"
                :formError="errors.sort_order"
              />
              <small class="text-muted">Menor numero aparece primero.</small>
            </div>

            <div class="col-12">
              <FieldImage
                id="service-image"
                label="Imagen principal"
                v-model="mainImage"
                :initialPreview="initialPreview"
                :maxFiles="1"
                :maxSizeMb="2"
                accept="image/jpeg"
              />
              <small class="text-muted">JPG, max 2MB</small>
            </div>

            <div class="col-12">
              <ServiceImageUpload
                :businessId="business?.id"
                :serviceId="service?.id"
                :images="props.serviceImages || []"
                :maxFiles="10"
                :maxSizeMb="2"
                label="Galería de imágenes"
                @updated="reloadPage"
              />
            </div>
          </div>

          <div class="col-12 d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary" :disabled="sending">
              {{ sending ? 'Actualizando...' : 'Actualizar Servicio' }}
            </button>
            <Link :href="`/member/businesses/${business?.id}/services`" class="btn btn-outline-secondary">Cancelar</Link>
          </div>
        </form>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { computed, ref, reactive, onMounted } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldNumber from '@/Components/Fields/FieldNumber.vue'
import FieldTextarea from '@/Components/Fields/FieldTextarea.vue'
import FieldSelect from '@/Components/Fields/FieldSelect.vue'
import FieldSwitch from '@/Components/Fields/FieldSwitch.vue'
import FieldPhone from '@/Components/Fields/FieldPhone.vue'
import FieldImage from '@/Components/Fields/FieldImage.vue'
import ServiceImageUpload from '@/Components/Fields/ServiceImageUpload.vue'

const props = defineProps({
  business: { type: Object, required: true },
  service: { type: Object, required: true },
  locations: { type: Array, default: () => [] },
  serviceImages: { type: Array, default: () => [] },
})

const page = usePage()
const business = computed(() => props.business)
const service = computed(() => props.service)

const sending = ref(false)
const mainImage = ref(null)
const initialPreview = computed(() => service.value?.image ? `/storage/${service.value.image}` : '')

const locationOptions = computed(() => [
  { value: '', label: 'Todas las ubicaciones' },
  ...(props.locations || []).map(l => ({ value: l.id, label: l.name }))
])

const form = reactive({
  name: '',
  slug: '',
  description: '',
  duration_minutes: 30,
  price: '',
  deposit_required: false,
  deposit_amount: '',
  allows_online_booking: true,
  whatsapp_contact: '',
  is_active: true,
  sort_order: 0,
  business_location_id: '',
})

const errors = reactive({})

onMounted(() => {
  form.name = service.value?.name || ''
  form.slug = service.value?.slug || ''
  form.description = service.value?.description || ''
  form.duration_minutes = service.value?.duration_minutes || 30
  form.price = service.value?.price || ''
  form.deposit_required = !!service.value?.deposit_required
  form.deposit_amount = service.value?.deposit_amount || ''
  form.allows_online_booking = !!service.value?.allows_online_booking
  form.whatsapp_contact = service.value?.whatsapp_contact || ''
  form.is_active = !!service.value?.is_active
  form.sort_order = service.value?.sort_order ?? 0
  form.business_location_id = service.value?.business_location_id || ''
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
        { label: 'Servicios', href: `/member/businesses/${biz.id}/services` },
        { label: 'Editar Servicio', active: true },
      ]
    }
  }
  return [
    { label: 'Mis Negocios', href: '/member/business-modules' },
    { label: 'Editar Servicio', active: true },
  ]
})

const submit = () => {
  Object.keys(errors).forEach(key => delete errors[key])
  sending.value = true
  const formData = new FormData()
  formData.append('_method', 'PUT')

  Object.keys(form).forEach(key => {
    const val = form[key]
    if (val !== null && val !== '') {
      if (typeof val === 'boolean') {
        formData.append(key, val ? '1' : '0')
      } else {
        formData.append(key, val)
      }
    }
  })

  if (mainImage.value instanceof File) {
    formData.append('image', mainImage.value)
  }

  router.post(`/member/businesses/${business.value.id}/services/${service.value.id}`, formData, {
    preserveScroll: true,
    onError: (errs) => {
      Object.keys(errs).forEach(key => {
        errors[key] = errs[key]
      })
    },
    onFinish: () => {
      sending.value = false
    },
  })
}

const reloadPage = () => {
  router.reload({ preserveScroll: true })
}
</script>
