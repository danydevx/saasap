<template>
  <MemberLayout>
    <Head :title="`Nueva Cita - ${business.name}`" />

    <PageHeader
      title="Nueva Cita"
      :breadcrumbs="breadcrumbs"
      :backHref="`/member/businesses/${business.id}/appointments`"
    />

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <form @submit.prevent="submit">
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <FieldText
                id="customer_name"
                label="Nombre del cliente"
                v-model="form.customer_name"
                :formError="errors.customer_name"
                required
              />
            </div>

            <div class="col-md-6">
              <FieldEmail
                id="customer_email"
                label="Email del cliente"
                v-model="form.customer_email"
                :formError="errors.customer_email"
                required
              />
            </div>

            <div class="col-md-6">
              <FieldPhone
                id="customer_phone"
                label="Telefono"
                v-model="form.customer_phone"
              />
            </div>

            <div class="col-md-6">
              <FieldSelect
                id="business_service_id"
                label="Servicio"
                v-model="form.business_service_id"
                :formError="errors.business_service_id"
                required
              >
                <option :value="null" disabled>Seleccionar servicio</option>
                <option v-for="svc in services" :key="svc.id" :value="svc.id">
                  {{ svc.name }} ({{ svc.duration_minutes }} min)
                </option>
              </FieldSelect>
            </div>

            <div class="col-md-6">
              <FieldSelect
                id="business_location_id"
                label="Ubicacion"
                v-model="form.business_location_id"
                :formError="errors.business_location_id"
              >
                <option :value="null">Sin ubicacion</option>
                <option v-for="loc in locations" :key="loc.id" :value="loc.id">
                  {{ loc.name }}
                </option>
              </FieldSelect>
            </div>

            <div class="col-md-3">
              <FieldDate
                id="appointment_date"
                label="Fecha"
                v-model="form.appointment_date"
                :formError="errors.appointment_date"
                :min="today"
                required
              />
            </div>

            <div class="col-md-3">
              <FieldTime
                id="start_time"
                label="Hora"
                v-model="form.start_time"
                :formError="errors.start_time"
                required
              />
            </div>

            <div class="col-12">
              <FieldTextarea
                id="notes"
                label="Notas"
                v-model="form.notes"
                :rows="3"
                placeholder="Notas adicionales..."
              />
            </div>

            <div class="col-12">
              <button type="submit" class="btn btn-primary" :disabled="sending">
                {{ sending ? 'Guardando...' : 'Crear Cita' }}
              </button>
              <Link :href="`/member/businesses/${business.id}/appointments`" class="btn btn-outline-secondary ms-2">
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
import FieldEmail from '@/Components/Fields/FieldEmail.vue'
import FieldPhone from '@/Components/Fields/FieldPhone.vue'
import FieldSelect from '@/Components/Fields/FieldSelect.vue'
import FieldDate from '@/Components/Fields/FieldDate.vue'
import FieldTime from '@/Components/Fields/FieldTime.vue'
import FieldTextarea from '@/Components/Fields/FieldTextarea.vue'

const page = usePage()
const business = computed(() => page.props.business)
const services = computed(() => page.props.services || [])
const locations = computed(() => page.props.locations || [])
const errors = computed(() => page.props.errors || {})

const breadcrumbs = computed(() => [
  { label: business.value.name, href: `/member/businesses/${business.value.id}/appointments` },
  { label: 'Nueva Cita', active: true },
])

const sending = ref(false)
const today = computed(() => new Date().toISOString().split('T')[0])

const form = reactive({
  customer_name: '',
  customer_email: '',
  customer_phone: '',
  business_service_id: null,
  business_location_id: null,
  appointment_date: '',
  start_time: '',
  notes: '',
})

const submit = () => {
  sending.value = true
  router.post(`/member/businesses/${business.value.id}/appointments`, form, {
    preserveScroll: true,
    onFinish: () => {
      sending.value = false
    },
  })
}
</script>
