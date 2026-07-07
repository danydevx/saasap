<template>
  <MemberLayout>
    <Head :title="`Editar Servicio - ${business.name}`" />

    <PageHeader
      :title="'Editar Servicio'"
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
                v-model="form.name"
                :formError="form.errors.name"
                required
              />
            </div>

            <div class="col-12 col-md-4">
              <FieldText
                id="service-slug"
                label="Slug"
                v-model="form.slug"
                :formError="form.errors.slug"
              />
            </div>

            <div class="col-12">
              <div class="mb-3">
                <label for="service-description" class="form-label">Descripcion</label>
                <textarea
                  id="service-description"
                  class="form-control"
                  rows="3"
                  v-model="form.description"
                  :class="{ 'is-invalid': form.errors.description }"
                ></textarea>
                <div v-if="form.errors.description" class="invalid-feedback">{{ form.errors.description }}</div>
              </div>
            </div>

            <div class="col-12 col-md-6">
              <label for="service-location" class="form-label">Ubicacion</label>
              <select id="service-location" class="form-select" v-model="form.business_location_id" :class="{ 'is-invalid': form.errors.business_location_id }">
                <option value="">Todas las ubicaciones</option>
                <option v-for="loc in locations" :key="loc.id" :value="loc.id">
                  {{ loc.name }}
                </option>
              </select>
              <div v-if="form.errors.business_location_id" class="invalid-feedback">{{ form.errors.business_location_id }}</div>
            </div>

            <div class="col-12 col-md-3">
              <FieldNumber
                id="service-duration"
                label="Duracion (minutos)"
                v-model="form.duration_minutes"
                :formError="form.errors.duration_minutes"
                required
              />
            </div>

            <div class="col-12 col-md-3">
              <FieldNumber
                id="service-price"
                label="Precio"
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
              <FieldSwitch
                id="service-whatsapp-contact"
                label="Contactar por WhatsApp"
                v-model="form.whatsapp_contact"
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
              {{ form.processing ? 'Actualizando...' : 'Actualizar Servicio' }}
            </button>
            <Link :href="`/member/businesses/${business.id}/services`" class="btn btn-outline-secondary">Cancelar</Link>
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

const props = defineProps({
  business: { type: Object, required: true },
  service: { type: Object, required: true },
  locations: { type: Array, default: () => [] },
})

const business = computed(() => props.business)

const form = useForm({
  name: props.service.name,
  slug: props.service.slug,
  description: props.service.description || '',
  image: props.service.image || '',
  duration_minutes: props.service.duration_minutes,
  price: props.service.price || '',
  deposit_required: !!props.service.deposit_required,
  deposit_amount: props.service.deposit_amount || '',
  allows_online_booking: !!props.service.allows_online_booking,
  whatsapp_contact: !!props.service.whatsapp_contact,
  is_active: !!props.service.is_active,
  sort_order: props.service.sort_order ?? 0,
  business_location_id: props.service.business_location_id || '',
})

const breadcrumbs = computed(() => [
  { label: business.value.name, href: `/member/businesses/${business.value.id}/services` },
  { label: 'Editar', active: true },
])

const submit = () => {
  form.put(`/member/businesses/${business.value.id}/services/${props.service.id}`)
}
</script>
