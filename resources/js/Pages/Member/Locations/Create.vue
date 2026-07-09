<template>
  <MemberLayout>
    <Head :title="`Nueva Ubicacion - ${business.name}`" />

    <PageHeader
      :title="'Nueva Ubicacion'"
      :breadcrumbs="breadcrumbs"
      :backHref="`/member/businesses/${business.id}/locations`"
    />

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <form @submit.prevent="submit">
          <div class="row g-3">
            <div class="col-12 col-md-8">
              <FieldText
                id="location-name"
                label="Nombre"
                placeholder="Sucursal Centro"
                v-model="form.name"
                :formError="form.errors.name"
                required
              />
            </div>

            <div class="col-12 col-md-4">
              <FieldSwitch
                id="location-primary"
                label="Ubicacion principal"
                v-model="form.is_primary"
              />
            </div>

            <div class="col-12">
              <FieldText
                id="location-address-1"
                label="Direccion linea 1"
                placeholder="Av. Rivadavia 1234"
                v-model="form.address_line_1"
                :formError="form.errors.address_line_1"
                required
              />
            </div>

            <div class="col-12">
              <FieldText
                id="location-address-2"
                label="Direccion linea 2"
                placeholder="Piso 3, Depto A"
                v-model="form.address_line_2"
                :formError="form.errors.address_line_2"
              />
            </div>

            <div class="col-12 col-md-6">
              <FieldText
                id="location-city"
                label="Ciudad / Colonia"
                placeholder="Guadalajara"
                v-model="form.city"
                :formError="form.errors.city"
                required
              />
            </div>

            <div class="col-12 col-md-6">
              <LocationSelector
                v-model="locationData"
                :state-error="form.errors.state_code"
                :municipality-error="form.errors.municipality"
                required
                @state-changed="onStateChanged"
                @municipality-changed="onMunicipalityChanged"
              />
            </div>

            <div class="col-12 col-md-4">
              <FieldText
                id="location-postal"
                label="Codigo Postal"
                placeholder="C1001"
                v-model="form.postal_code"
                :formError="form.errors.postal_code"
              />
            </div>

            <div class="col-12 col-md-6">
              <FieldText
                id="location-phone"
                label="Telefono"
                placeholder="+54 11 1234 5678"
                v-model="form.phone"
                :formError="form.errors.phone"
              />
            </div>

            <div class="col-12 col-md-6">
              <FieldText
                id="location-email"
                label="Email"
                placeholder="sucursal@negocio.com"
                v-model="form.email"
                :formError="form.errors.email"
              />
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
              <FieldText
                id="location-directions"
                label="Como llegar (URL de Google Maps)"
                placeholder="https://www.google.com/maps/dir/?api=1..."
                v-model="form.directions_url"
                :formError="form.errors.directions_url"
              />
            </div>

            <div class="col-12 col-md-4">
              <FieldSwitch
                id="location-active"
                label="Ubicacion activa"
                v-model="form.is_active"
              />
            </div>
          </div>

          <div class="col-12 d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary" :disabled="form.processing">
              {{ form.processing ? 'Creando...' : 'Crear Ubicacion' }}
            </button>
            <Link :href="`/member/businesses/${business.id}/locations`" class="btn btn-outline-secondary">Cancelar</Link>
          </div>
        </form>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { computed, reactive, ref, watch } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldNumber from '@/Components/Fields/FieldNumber.vue'
import FieldSwitch from '@/Components/Fields/FieldSwitch.vue'
import MapPicker from '@/Components/MapPicker.vue'
import LocationSelector from '@/Components/LocationSelector.vue'

const props = defineProps({
  business: {
    type: Object,
    required: true,
  },
})

const business = computed(() => props.business)

const locationData = ref({ state_code: '', municipality: '' })

const form = useForm({
  name: '',
  address_line_1: '',
  address_line_2: '',
  city: '',
  state: '',
  state_code: '',
  municipality: '',
  postal_code: '',
  country: 'MX',
  phone: '',
  email: '',
  latitude: '',
  longitude: '',
  directions_url: '',
  is_primary: false,
  is_active: true,
})

const breadcrumbs = computed(() => [
  { label: business.value.name, href: '/member/business-modules' },
  { label: 'Nueva Ubicacion', active: true },
])

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

const submit = () => {
  form.state_code = locationData.value.state_code
  form.municipality = locationData.value.municipality
  form.post(`/member/businesses/${business.value.id}/locations`)
}
</script>
