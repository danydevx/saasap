<template>
  <MemberLayout>
    <Head :title="`Editar Ubicacion - ${business.name}`" />

    <PageHeader
      :title="'Editar Ubicacion'"
      :breadcrumbs="breadcrumbs"
      :backHref="`/member/businesses/${business.id}/locations`"
    />

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <form @submit.prevent="submit" id="location-form">
          <div class="row g-3">
            <div class="col-12 col-md-8">
              <div class="form-group">
                <div class="form-floating">
                  <input
                    type="text"
                    id="location-name"
                    v-model="form.name"
                    class="form-control"
                    :class="{ 'is-invalid': errors.name }"
                    placeholder=" "
                  />
                  <label for="location-name">Nombre <strong class="text-danger">*</strong></label>
                  <div v-if="errors.name" class="invalid-feedback">{{ errors.name }}</div>
                </div>
              </div>
            </div>

            <div class="col-12 col-md-4">
              <div class="form-group">
                <div class="form-check form-switch mt-3 pt-3">
                  <input
                    type="checkbox"
                    id="location-primary"
                    v-model="form.is_primary"
                    class="form-check-input"
                  />
                  <label class="form-check-label" for="location-primary">Ubicacion principal</label>
                </div>
              </div>
            </div>

            <div class="col-12">
              <div class="form-group">
                <div class="form-floating">
                  <input
                    type="text"
                    id="location-address-1"
                    v-model="form.address_line_1"
                    class="form-control"
                    :class="{ 'is-invalid': errors.address_line_1 }"
                    placeholder=" "
                  />
                  <label for="location-address-1">Direccion linea 1 <strong class="text-danger">*</strong></label>
                  <div v-if="errors.address_line_1" class="invalid-feedback">{{ errors.address_line_1 }}</div>
                </div>
              </div>
            </div>

            <div class="col-12">
              <div class="form-group">
                <div class="form-floating">
                  <input
                    type="text"
                    id="location-address-2"
                    v-model="form.address_line_2"
                    class="form-control"
                    placeholder=" "
                  />
                  <label for="location-address-2">Direccion linea 2</label>
                </div>
              </div>
            </div>

            <div class="col-12 col-md-6">
              <div class="form-group">
                <div class="form-floating">
                  <input
                    type="text"
                    id="location-city"
                    v-model="form.city"
                    class="form-control"
                    :class="{ 'is-invalid': errors.city }"
                    placeholder=" "
                  />
                  <label for="location-city">Ciudad / Colonia <strong class="text-danger">*</strong></label>
                  <div v-if="errors.city" class="invalid-feedback">{{ errors.city }}</div>
                </div>
              </div>
            </div>

            <div class="col-12 col-md-6">
              <LocationSelector
                v-model="locationData"
                :state-error="errors.state_code"
                :municipality-error="errors.municipality"
                required
                @state-changed="onStateChanged"
                @municipality-changed="onMunicipalityChanged"
              />
            </div>

            <div class="col-12 col-md-4">
              <div class="form-group">
                <div class="form-floating">
                  <input
                    type="text"
                    id="location-postal"
                    v-model="form.postal_code"
                    class="form-control"
                    placeholder=" "
                  />
                  <label for="location-postal">Codigo Postal</label>
                </div>
              </div>
            </div>

            <div class="col-12 col-md-6">
              <div class="form-group">
                <div class="form-floating">
                  <input
                    type="tel"
                    id="location-phone"
                    v-model="form.phone"
                    class="form-control"
                    placeholder=" "
                  />
                  <label for="location-phone">Telefono</label>
                </div>
              </div>
            </div>

            <div class="col-12 col-md-6">
              <div class="form-group">
                <div class="form-floating">
                  <input
                    type="email"
                    id="location-email"
                    v-model="form.email"
                    class="form-control"
                    :class="{ 'is-invalid': errors.email }"
                    placeholder=" "
                  />
                  <label for="location-email">Email</label>
                  <div v-if="errors.email" class="invalid-feedback">{{ errors.email }}</div>
                </div>
              </div>
            </div>

            <div class="col-12">
              <MapPicker
                label="Ubicacion en el mapa"
                :lat="form.latitude"
                :lng="form.longitude"
                @update:lat="form.latitude = $event"
                @update:lng="form.longitude = $event"
              />
            </div>

            <div class="col-12">
              <div class="form-group">
                <div class="form-floating">
                  <input
                    type="url"
                    id="location-directions"
                    v-model="form.directions_url"
                    class="form-control"
                    placeholder=" "
                  />
                  <label for="location-directions">Como llegar (URL de Google Maps)</label>
                </div>
              </div>
            </div>

            <div class="col-12 col-md-4">
              <div class="form-check form-switch mt-3 pt-3">
                <input
                  type="checkbox"
                  id="location-active"
                  v-model="form.is_active"
                  class="form-check-input"
                />
                <label class="form-check-label" for="location-active">Ubicacion activa</label>
              </div>
            </div>
          </div>

          <div class="col-12 d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary" :disabled="sending">
              {{ sending ? 'Actualizando...' : 'Actualizar Ubicacion' }}
            </button>
            <Link :href="`/member/businesses/${business.id}/locations`" class="btn btn-outline-secondary">Cancelar</Link>
          </div>
        </form>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { computed, reactive, ref } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import MapPicker from '@/Components/MapPicker.vue'
import LocationSelector from '@/Components/LocationSelector.vue'

const page = usePage()
const business = computed(() => page.props.business)
const location = computed(() => page.props.location)

const locationData = ref({
  state_code: location.value.state_code || '',
  municipality: location.value.municipality || '',
})

const errors = reactive({
  name: '',
  address_line_1: '',
  city: '',
  email: '',
  state_code: '',
  municipality: '',
})

const sending = ref(false)

const form = reactive({
  name: location.value.name || '',
  address_line_1: location.value.address_line_1 || '',
  address_line_2: location.value.address_line_2 || '',
  city: location.value.city || '',
  postal_code: location.value.postal_code || '',
  phone: location.value.phone || '',
  email: location.value.email || '',
  directions_url: location.value.directions_url || '',
  latitude: location.value.latitude || '',
  longitude: location.value.longitude || '',
  is_primary: !!location.value.is_primary,
  is_active: !!location.value.is_active,
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
        { label: 'Ubicaciones', href: `/member/businesses/${biz.id}/locations` },
        { label: 'Editar Ubicacion', active: true },
      ]
    }
  }
  return [
    { label: 'Mis Negocios', href: '/member/business-modules' },
    { label: 'Editar Ubicacion', active: true },
  ]
})

const onStateChanged = ({ lat, lng }) => {
  if (lat && lng) {
    form.latitude = parseFloat(lat).toFixed(7)
    form.longitude = parseFloat(lng).toFixed(7)
  }
}

const onMunicipalityChanged = ({ lat, lng }) => {
  if (lat && lng) {
    form.latitude = parseFloat(lat).toFixed(7)
    form.longitude = parseFloat(lng).toFixed(7)
  }
}

const validateForm = () => {
  let isValid = true

  errors.name = ''
  errors.address_line_1 = ''
  errors.city = ''
  errors.email = ''
  errors.state_code = ''
  errors.municipality = ''

  if (!form.name || form.name.trim() === '') {
    errors.name = 'El nombre es obligatorio.'
    isValid = false
  } else if (form.name.length > 150) {
    errors.name = 'El nombre no puede tener más de 150 caracteres.'
    isValid = false
  }

  if (!form.address_line_1 || form.address_line_1.trim() === '') {
    errors.address_line_1 = 'La dirección es obligatoria.'
    isValid = false
  } else if (form.address_line_1.length > 255) {
    errors.address_line_1 = 'La dirección no puede tener más de 255 caracteres.'
    isValid = false
  }

  if (!form.city || form.city.trim() === '') {
    errors.city = 'La ciudad es obligatoria.'
    isValid = false
  } else if (form.city.length > 100) {
    errors.city = 'La ciudad no puede tener más de 100 caracteres.'
    isValid = false
  }

  if (form.email && form.email.trim() !== '') {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    if (!emailRegex.test(form.email)) {
      errors.email = 'El email no es válido.'
      isValid = false
    } else if (form.email.length > 150) {
      errors.email = 'El email no puede tener más de 150 caracteres.'
      isValid = false
    }
  }

  return isValid
}

const submit = () => {
  if (!validateForm()) {
    return
  }

  sending.value = true

  const formData = new FormData()
  formData.append('name', form.name)
  formData.append('address_line_1', form.address_line_1)
  formData.append('address_line_2', form.address_line_2 || '')
  formData.append('city', form.city)
  formData.append('state', '')
  formData.append('state_code', locationData.value.state_code || '')
  formData.append('municipality', locationData.value.municipality || '')
  formData.append('postal_code', form.postal_code || '')
  formData.append('country', 'MX')
  formData.append('phone', form.phone || '')
  formData.append('email', form.email || '')
  formData.append('latitude', form.latitude || '')
  formData.append('longitude', form.longitude || '')
  formData.append('directions_url', form.directions_url || '')
  formData.append('is_primary', form.is_primary ? '1' : '0')
  formData.append('is_active', form.is_active ? '1' : '0')
  formData.append('_method', 'PUT')

  window.axios.post(`/member/businesses/${business.value.id}/locations/${location.value.id}`, formData, {
    headers: { 'Content-Type': 'multipart/form-data' },
  }).then(() => {
    window.location.href = `/member/businesses/${business.value.id}/locations?success=updated`
  }).catch((error) => {
    sending.value = false
    if (error.response && error.response.data && error.response.data.errors) {
      const serverErrors = error.response.data.errors
      if (serverErrors.name) errors.name = serverErrors.name[0]
      if (serverErrors.address_line_1) errors.address_line_1 = serverErrors.address_line_1[0]
      if (serverErrors.city) errors.city = serverErrors.city[0]
      if (serverErrors.email) errors.email = serverErrors.email[0]
      if (serverErrors.state_code) errors.state_code = serverErrors.state_code[0]
      if (serverErrors.municipality) errors.municipality = serverErrors.municipality[0]
    }
  })
}
</script>
