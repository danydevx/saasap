<template>
  <component :is="layout">
    <Head title="Perfil" />

    <PageHeader :title="'Perfil'" :breadcrumbs="breadcrumbs" backHref="/dashboard" />

    <div class="row g-3">
      <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h2 class="h6 mb-3">Personales</h2>

            <div class="row g-3">
              <div class="col-12">
                <FieldText
                  id="profile-name"
                  label="Nombre"
                  placeholder="Tu nombre"
                  v-model="form.name"
                  :formError="form.errors.name"
                />
              </div>

              <div class="col-12">
                <FieldText
                  id="profile-phone"
                  label="Telefono"
                  placeholder="+52 555 000 0000"
                  v-model="form.phone"
                  :formError="form.errors.phone"
                />
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h2 class="h6 mb-3">Redes sociales</h2>

            <div class="row g-3">
              <div class="col-12">
                <FieldText
                  id="profile-facebook"
                  label="Facebook"
                  placeholder="https://facebook.com/usuario"
                  v-model="form.facebook"
                  :formError="form.errors.facebook"
                />
              </div>

              <div class="col-12">
                <FieldText
                  id="profile-instagram"
                  label="Instagram"
                  placeholder="https://instagram.com/usuario"
                  v-model="form.instagram"
                  :formError="form.errors.instagram"
                />
              </div>

              <div class="col-12">
                <FieldText
                  id="profile-x"
                  label="X"
                  placeholder="https://x.com/usuario"
                  v-model="form.x"
                  :formError="form.errors.x"
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
  </component>
</template>

<script setup>
import { computed, watch } from 'vue'
import { Head, useForm, usePage } from '@inertiajs/vue3'
import { toast } from 'vue3-toastify'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import FieldText from '@/Components/Fields/FieldText.vue'

const props = defineProps({
  profile: {
    type: Object,
    required: true,
  },
})

const page = usePage()

const breadcrumbs = [
  { label: 'Perfil' },
]

const form = useForm({
  name: props.profile.name || '',
  phone: props.profile.phone || '',
  facebook: props.profile.facebook || '',
  instagram: props.profile.instagram || '',
  x: props.profile.x || '',
})

const layout = computed(() => {
  const roles = page.props.auth?.roles || []
  const isAdmin = roles.includes('admin') || roles.includes('superadmin')
  return isAdmin ? AdminLayout : MemberLayout
})

const submit = () => {
  form.put('/admin/profile', {
    preserveScroll: true,
  })
}
</script>
