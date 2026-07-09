<template>
  <MemberLayout>
    <Head :title="`Nuevo Servicio - ${business.name}`" />

    <PageHeader
      :title="'Nuevo Servicio'"
      :breadcrumbs="breadcrumbs"
      :backHref="`/member/businesses/${business.id}/services`"
    />

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <form @submit.prevent="submit">
          <div class="row g-3">
            <div class="col-12 col-md-8">
              <FieldText
                id="service-name"
                label="Nombre"
                placeholder="Corte de cabello"
                v-model="form.name"
                :formError="form.errors.name"
                required
              />
            </div>

            <div class="col-12 col-md-4">
              <FieldText
                id="service-slug"
                label="Slug"
                placeholder="corte-de-cabello"
                v-model="form.slug"
                :formError="form.errors.slug"
              />
              <small class="text-muted">Se genera automaticamente si se deja vacio.</small>
            </div>

            <div class="col-12">
              <FieldTextarea
                id="service-description"
                label="Descripcion"
                v-model="form.description"
                :formError="form.errors.description"
                :rows="3"
              />
            </div>

            <div class="col-12 col-md-6">
              <FieldSelect
                id="service-location"
                label="Ubicacion"
                v-model="form.business_location_id"
                :options="locationOptions"
                :formError="form.errors.business_location_id"
              />
            </div>

            <div class="col-12 col-md-3">
              <FieldNumber
                id="service-duration"
                label="Duracion (minutos)"
                placeholder="30"
                v-model="form.duration_minutes"
                :formError="form.errors.duration_minutes"
                required
              />
            </div>

            <div class="col-12 col-md-3">
              <FieldNumber
                id="service-price"
                label="Precio"
                placeholder="0.00"
                v-model="form.price"
                :formError="form.errors.price"
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
                placeholder="0.00"
                v-model="form.deposit_amount"
                :formError="form.errors.deposit_amount"
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
                :formError="form.errors.whatsapp_contact"
              />
            </div>

            <div class="col-12 col-md-4">
              <FieldSwitch
                id="service-active"
                label="Servicio activo"
                v-model="form.is_active"
              />
            </div>
          </div>

          <div class="col-12 d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary" :disabled="form.processing">
              {{ form.processing ? 'Creando...' : 'Crear Servicio' }}
            </button>
            <Link :href="`/member/businesses/${business.id}/services`" class="btn btn-outline-secondary">Cancelar</Link>
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
import FieldTextarea from '@/Components/Fields/FieldTextarea.vue'
import FieldSelect from '@/Components/Fields/FieldSelect.vue'
import FieldSwitch from '@/Components/Fields/FieldSwitch.vue'
import FieldPhone from '@/Components/Fields/FieldPhone.vue'

const props = defineProps({
  business: { type: Object, required: true },
  locations: { type: Array, default: () => [] },
})

const business = computed(() => props.business)

const locationOptions = computed(() => [
  { value: '', label: 'Todas las ubicaciones' },
  ...props.locations.map(l => ({ value: l.id, label: l.name }))
])

const form = useForm({
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

watch(() => form.name, (val) => {
  if (val && !form.slug) {
    form.slug = val.toLowerCase().replace(/\s+/g, '-').replace(/[^a-z0-9-]/g, '')
  }
})

const breadcrumbs = computed(() => [
  { label: business.value.name, href: `/member/businesses/${business.value.id}/services` },
  { label: 'Nuevo', active: true },
])

const submit = () => {
  form.post(`/member/businesses/${business.value.id}/services`)
}
</script>
