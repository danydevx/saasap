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
        <form @submit.prevent="submit">
          <div class="row g-3">
            <div class="col-12 col-md-8">
              <FieldText
                id="location-name"
                label="Nombre"
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
                v-model="form.address_line_1"
                :formError="form.errors.address_line_1"
                required
              />
            </div>

            <div class="col-12">
              <FieldText
                id="location-address-2"
                label="Direccion linea 2"
                v-model="form.address_line_2"
                :formError="form.errors.address_line_2"
              />
            </div>

            <div class="col-12 col-md-4">
              <FieldText
                id="location-city"
                label="Ciudad"
                v-model="form.city"
                :formError="form.errors.city"
                required
              />
            </div>

            <div class="col-12 col-md-4">
              <FieldText
                id="location-state"
                label="Provincia/Estado"
                v-model="form.state"
                :formError="form.errors.state"
              />
            </div>

            <div class="col-12 col-md-4">
              <FieldText
                id="location-postal"
                label="Codigo Postal"
                v-model="form.postal_code"
                :formError="form.errors.postal_code"
              />
            </div>

            <div class="col-12 col-md-6">
              <FieldText
                id="location-phone"
                label="Telefono"
                v-model="form.phone"
                :formError="form.errors.phone"
              />
            </div>

            <div class="col-12 col-md-6">
              <FieldText
                id="location-email"
                label="Email"
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
              {{ form.processing ? 'Actualizando...' : 'Actualizar Ubicacion' }}
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
  location: {
    type: Object,
    required: true,
  },
})

const business = computed(() => props.business)

const form = useForm({
  name: props.location.name,
  address_line_1: props.location.address_line_1,
  address_line_2: props.location.address_line_2 || '',
  city: props.location.city,
  state: props.location.state || '',
  postal_code: props.location.postal_code || '',
  country: props.location.country || '',
  phone: props.location.phone || '',
  email: props.location.email || '',
  latitude: props.location.latitude || '',
  longitude: props.location.longitude || '',
  directions_url: props.location.directions_url || '',
  is_primary: !!props.location.is_primary,
  is_active: !!props.location.is_active,
})

const breadcrumbs = computed(() => [
  { label: business.value.name, href: `/member/businesses/${business.value.id}/locations` },
  { label: 'Editar', active: true },
])

const submit = () => {
  form.put(`/member/businesses/${business.value.id}/locations/${props.location.id}`)
}
</script>
