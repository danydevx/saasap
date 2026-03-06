<template>
  <AdminLayout>
    <Head title="Settings" />

    <PageHeader :title="'Settings'" :breadcrumbs="breadcrumbs" backHref="/dashboard" />

    <div class="row g-3">
      <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h2 class="h6 mb-3">App</h2>

            <div class="row g-3">
              <div class="col-12">
                <FieldText
                  id="app-name"
                  label="Nombre de la app"
                  placeholder="Mi SaaS"
                  v-model="form.app.name"
                  :formError="form.errors['app.name']"
                  required
                />
              </div>

              <div class="col-12">
                <FieldEmail
                  id="app-email"
                  label="Email"
                  placeholder="contacto@empresa.com"
                  v-model="form.app.email"
                  :formError="form.errors['app.email']"
                />
              </div>

              <div class="col-12">
                <FieldText
                  id="app-phone"
                  label="Telefono"
                  placeholder="+52 555 000 0000"
                  v-model="form.app.phone"
                  :formError="form.errors['app.phone']"
                />
              </div>

              <div class="col-12">
                <FieldText
                  id="app-description"
                  label="Descripcion"
                  placeholder="Describe la app"
                  v-model="form.app.description"
                  :formError="form.errors['app.description']"
                />
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h2 class="h6 mb-3">Regional</h2>

            <div class="row g-3">
              <div class="col-12">
                <FieldText
                  id="regional-timezone"
                  label="Timezone"
                  placeholder="America/Mexico_City"
                  v-model="form.regional.timezone"
                  :formError="form.errors['regional.timezone']"
                  required
                />
              </div>

              <div class="col-12">
                <FieldText
                  id="regional-locale"
                  label="Locale"
                  placeholder="es"
                  v-model="form.regional.locale"
                  :formError="form.errors['regional.locale']"
                  required
                />
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h2 class="h6 mb-3">System</h2>

            <div class="row g-3">
              <div class="col-12 col-md-6">
                <FieldSwitch
                  id="system-allow-registration"
                  label="Permitir registro"
                  v-model="form.system.allow_registration"
                  :formError="form.errors['system.allow_registration']"
                />
              </div>

              <div class="col-12 col-md-6">
                <FieldSwitch
                  id="system-require-approval"
                  label="Requiere aprobacion de usuario"
                  v-model="form.system.require_user_approval"
                  :formError="form.errors['system.require_user_approval']"
                />
              </div>

              <div class="col-12 col-md-6">
                <FieldNumber
                  id="system-default-pagination"
                  label="Paginacion por defecto"
                  placeholder="10"
                  v-model="form.system.default_pagination"
                  :formError="form.errors['system.default_pagination']"
                  :min="5"
                  :max="100"
                  required
                />
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12">
        <button type="button" class="btn btn-primary" :disabled="form.processing" @click="submit">
          {{ form.processing ? 'Guardando...' : 'Guardar cambios' }}
        </button>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed, watch } from 'vue'
import { Head, useForm, usePage } from '@inertiajs/vue3'
import { useNotification } from '@kyvg/vue3-notification'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldEmail from '@/Components/Fields/FieldEmail.vue'
import FieldNumber from '@/Components/Fields/FieldNumber.vue'
import FieldSwitch from '@/Components/Fields/FieldSwitch.vue'

const props = defineProps({
  settings: {
    type: Object,
    required: true,
  },
})

const { notify } = useNotification()
const page = usePage()

const breadcrumbs = [
  { label: 'Settings' },
]

const form = useForm({
  app: {
    name: props.settings.app.name || '',
    email: props.settings.app.email || '',
    phone: props.settings.app.phone || '',
    description: props.settings.app.description || '',
  },
  regional: {
    timezone: props.settings.regional.timezone || '',
    locale: props.settings.regional.locale || '',
  },
  system: {
    allow_registration: !!props.settings.system.allow_registration,
    require_user_approval: !!props.settings.system.require_user_approval,
    default_pagination: props.settings.system.default_pagination ?? 10,
  },
})

const flashSuccess = computed(() => page.props.flash?.success)
const flashError = computed(() => page.props.flash?.error)

watch(flashSuccess, (value) => {
  if (!value) return
  notify({ type: 'success', text: value })
})

watch(flashError, (value) => {
  if (!value) return
  notify({ type: 'error', text: value })
})

const submit = () => {
  form.put('/admin/settings', {
    preserveScroll: true,
  })
}
</script>
