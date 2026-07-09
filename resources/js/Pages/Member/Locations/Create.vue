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

            <div class="col-12 col-md-4">
              <FieldText
                id="location-city"
                label="Ciudad"
                placeholder="Buenos Aires"
                v-model="form.city"
                :formError="form.errors.city"
                required
              />
            </div>

            <div class="col-12 col-md-4">
              <FieldText
                id="location-state"
                label="Provincia/Estado"
                placeholder="CABA"
                v-model="form.state"
                :formError="form.errors.state"
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
import { computed } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldNumber from '@/Components/Fields/FieldNumber.vue'
import FieldSwitch from '@/Components/Fields/FieldSwitch.vue'
import MapPicker from '@/Components/MapPicker.vue'

const props = defineProps({
  business: {
    type: Object,
    required: true,
  },
})

const business = computed(() => props.business)

const form = useForm({
  name: '',
  address_line_1: '',
  address_line_2: '',
  city: '',
  state: '',
  postal_code: '',
  country: '',
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

const submit = () => {
  form.post(`/member/businesses/${business.value.id}/locations`)
}
</script>
