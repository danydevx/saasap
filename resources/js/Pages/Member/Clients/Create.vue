<template>
  <MemberLayout>
    <Head :title="`Nuevo Cliente - ${business.name}`" />

    <PageHeader
      title="Nuevo Cliente"
      :breadcrumbs="breadcrumbs"
      :backHref="`/member/businesses/${business.id}/clients`"
    />

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <form class="row g-3" @submit.prevent="submit">
          <div class="col-12 col-md-6">
            <FieldText
              id="client-customer-name"
              label="Nombre del cliente"
              placeholder="Persona que atendemos"
              v-model="form.customer_name"
              :formError="form.errors.customer_name"
              required
            />
          </div>

          <div class="col-12 col-md-6">
            <FieldText
              id="client-contact-person"
              label="Persona de contacto"
              placeholder="Encargado o responsable"
              v-model="form.contact_person"
              :formError="form.errors.contact_person"
            />
          </div>

          <div class="col-12 col-md-6">
            <FieldText
              id="client-company"
              label="Empresa o negocio"
              placeholder="Razon social o nombre comercial"
              v-model="form.company_name"
              :formError="form.errors.company_name"
            />
          </div>

          <div class="col-12 col-md-6">
            <FieldPhone
              id="client-whatsapp"
              label="WhatsApp"
              placeholder="+52 55 1234 5678"
              v-model="form.whatsapp"
              :formError="form.errors.whatsapp"
            />
          </div>

          <div class="col-12 col-md-6">
            <FieldEmail
              id="client-email"
              label="Email"
              placeholder="cliente@empresa.com"
              v-model="form.customer_email"
              :formError="form.errors.customer_email"
            />
          </div>

          <div class="col-12 col-md-6">
            <FieldPhone
              id="client-phone"
              label="Telefono"
              v-model="form.customer_phone"
              :formError="form.errors.customer_phone"
            />
          </div>

          <div class="col-12 col-md-6">
            <FieldText
              id="client-website"
              label="Sitio web"
              placeholder="https://empresa.com"
              v-model="form.website"
              :formError="form.errors.website"
            />
          </div>

          <div class="col-12 col-md-6">
            <FieldText
              id="client-rfc"
              label="RFC"
              placeholder="XAXX010101000"
              v-model="form.rfc"
              :formError="form.errors.rfc"
            />
          </div>

          <div class="col-12">
            <FieldText
              id="client-address-1"
              label="Direccion linea 1"
              placeholder="Av. Reforma 123"
              v-model="form.address_line_1"
              :formError="form.errors.address_line_1"
            />
          </div>

          <div class="col-12">
            <FieldText
              id="client-address-2"
              label="Direccion linea 2"
              placeholder="Piso 3, Interior B"
              v-model="form.address_line_2"
              :formError="form.errors.address_line_2"
            />
          </div>

          <div class="col-12 col-md-6">
            <LocationSelector
              v-model="locationData"
              :state-error="form.errors.state_code"
              :municipality-error="form.errors.municipality"
              @state-changed="onStateChanged"
              @municipality-changed="onMunicipalityChanged"
            />
          </div>

          <div class="col-12 col-md-3">
            <FieldText
              id="client-neighborhood"
              label="Colonia"
              placeholder="Centro"
              v-model="form.neighborhood"
              :formError="form.errors.neighborhood"
            />
          </div>

          <div class="col-12 col-md-3">
            <FieldText
              id="client-postal"
              label="Codigo postal"
              placeholder="06000"
              v-model="form.postal_code"
              :formError="form.errors.postal_code"
            />
          </div>

          <div class="col-12">
            <FieldTextarea
              id="client-notes"
              label="Notas"
              v-model="form.notes"
              :formError="form.errors.notes"
              :rows="3"
              placeholder="Informacion adicional del cliente o de la cita..."
            />
          </div>

          <div class="col-12 d-flex gap-2">
            <button type="submit" class="btn btn-primary" :disabled="form.processing">
              {{ form.processing ? 'Guardando...' : 'Crear Cliente' }}
            </button>
            <Link :href="`/member/businesses/${business.id}/clients`" class="btn btn-outline-secondary">
              Cancelar
            </Link>
          </div>
        </form>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { computed, reactive, ref } from 'vue'
import { Head, Link, useForm, usePage } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldEmail from '@/Components/Fields/FieldEmail.vue'
import FieldPhone from '@/Components/Fields/FieldPhone.vue'
import FieldTextarea from '@/Components/Fields/FieldTextarea.vue'
import LocationSelector from '@/Components/LocationSelector.vue'

const props = defineProps({
  business: {
    type: Object,
    required: true,
  },
})

const page = usePage()
const business = computed(() => page.props.business)

const breadcrumbs = computed(() => [
  { label: 'Mis Negocios', href: '/member/businesses' },
  { label: business.value.name, href: `/member/businesses/${business.value.id}/edit` },
  { label: 'Clientes', href: `/member/businesses/${business.value.id}/clients` },
  { label: 'Nuevo Cliente', active: true },
])

const locationData = ref({ state_code: '', municipality: '' })

const form = useForm({
  customer_name: '',
  contact_person: '',
  company_name: '',
  whatsapp: '',
  website: '',
  rfc: '',
  address_line_1: '',
  address_line_2: '',
  neighborhood: '',
  postal_code: '',
  state_code: '',
  municipality: '',
  customer_email: '',
  customer_phone: '',
  status: 'pending',
  notes: '',
})

const onStateChanged = () => {}
const onMunicipalityChanged = () => {}

const submit = () => {
  form.state_code = locationData.value.state_code
  form.municipality = locationData.value.municipality
  form.post(`/member/businesses/${business.value.id}/clients`)
}
</script>