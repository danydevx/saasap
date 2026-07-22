<template>
  <MemberLayout>
    <Head :title="`Editar ${business.name}`" />

    <PageHeader
      :title="'Editar Negocio'"
      :breadcrumbs="breadcrumbs"
      :backHref="'/member/business-modules'"
    />

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <form @submit.prevent="submit">
          <div class="row g-3">
            <div class="col-12">
              <div class="text-center mb-4">
                <div class="position-relative d-inline-block">
                  <div
                    class="rounded-circle bg-light d-flex align-items-center justify-content-center"
                    style="width: 120px; height: 120px; overflow: hidden;"
                  >
                    <img
                      v-if="logoPreview || business.logo_path"
                      :src="logoPreview || business.logo_path"
                      alt="Logo"
                      class="w-100 h-100"
                      style="object-fit: cover;"
                    />
                    <i v-else class="bi bi-building text-muted" style="font-size: 3rem;"></i>
                  </div>
                  <label
                    for="logo-input"
                    class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle p-2 cursor-pointer"
                    style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;"
                  >
                    <i class="bi bi-camera"></i>
                    <input
                      id="logo-input"
                      type="file"
                      accept="image/jpeg,image/png,image/gif,image/webp"
                      class="d-none"
                      @change="handleLogoChange"
                    />
                  </label>
                </div>
                <div v-if="errors.logo" class="text-danger small mt-1">{{ errors.logo }}</div>
                <div class="text-muted small mt-2">JPG, PNG o WebP. Max 2MB.</div>
                <button
                  v-if="business.logo_path && !removeLogoFlag"
                  type="button"
                  class="btn btn-sm btn-outline-danger mt-2"
                  @click="removeLogo"
                >
                  <i class="bi bi-trash me-1"></i>Eliminar logo
                </button>
              </div>
              <input type="hidden" name="remove_logo" :value="removeLogoFlag ? '1' : '0'" />
            </div>

            <div class="col-12 col-md-6">
              <FieldText
                id="business-name"
                label="Nombre"
                v-model="form.name"
                :formError="errors.name"
                required
              />
            </div>

            <div class="col-12 col-md-6" v-if="industries.length">
              <label for="business-industry" class="form-label">Industria</label>
              <select
                id="business-industry"
                class="form-select"
                v-model="form.industry_id"
              >
                <option value="">Seleccionar industria...</option>
                <option v-for="ind in industries" :key="ind.id" :value="ind.id">
                  {{ ind.name }}
                </option>
              </select>
              <div v-if="errors.industry_id" class="text-danger small mt-1">{{ errors.industry_id }}</div>
            </div>

            <div class="col-12 col-md-6">
              <FieldText
                id="business-email"
                label="Email"
                type="email"
                v-model="form.email"
                :formError="errors.email"
              />
            </div>

            <div class="col-12 col-md-6">
              <FieldText
                id="business-phone"
                label="Telefono"
                v-model="form.phone"
                :formError="errors.phone"
              />
            </div>

            <div class="col-12 col-md-6">
              <FieldText
                id="business-website"
                label="Website"
                v-model="form.website"
                :formError="errors.website"
              />
            </div>

            <div class="col-12">
              <FieldTextarea
                id="business-description"
                label="Descripcion"
                v-model="form.description"
                :formError="errors.description"
                :rows="3"
              />
            </div>
          </div>

          <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary" :disabled="sending">
              {{ sending ? 'Guardando...' : 'Guardar Cambios' }}
            </button>
            <Link href="/member/business-modules" class="btn btn-outline-secondary">Cancelar</Link>
          </div>
        </form>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { computed, ref, inject } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldTextarea from '@/Components/Fields/FieldTextarea.vue'

const props = defineProps({
  business: {
    type: Object,
    required: true,
  },
  industries: {
    type: Array,
    default: () => [],
  },
})

const page = usePage()
const errors = computed(() => page.props.errors || {})
const sending = ref(false)

const business = computed(() => props.business)
const industries = computed(() => props.industries || [])
const removeLogoFlag = ref(false)
const logoPreview = ref(null)

const dynamicBreadcrumbs = inject('dynamicBreadcrumbs', null)

const breadcrumbs = computed(() => {
  if (dynamicBreadcrumbs?.value) {
    return dynamicBreadcrumbs.value
  }
  return [
    { label: 'Mis Negocios', href: '/member/business-modules' },
    { label: business.value.name, active: true },
  ]
})

const form = ref({
  name: props.business.name,
  industry_id: props.business.industry_id || '',
  description: props.business.description || '',
  phone: props.business.phone || '',
  email: props.business.email || '',
  website: props.business.website || '',
})

const handleLogoChange = (event) => {
  const file = event.target.files[0]
  if (file) {
    logoPreview.value = URL.createObjectURL(file)
    removeLogoFlag.value = false
  }
}

const removeLogo = () => {
  removeLogoFlag.value = true
  logoPreview.value = null
}

const submit = () => {
  sending.value = true
  const data = new FormData()
  data.append('name', form.value.name)
  data.append('industry_id', form.value.industry_id || '')
  data.append('description', form.value.description || '')
  data.append('phone', form.value.phone || '')
  data.append('email', form.value.email || '')
  data.append('website', form.value.website || '')

  if (logoPreview.value) {
    const fileInput = document.getElementById('logo-input')
    if (fileInput && fileInput.files[0]) {
      data.append('logo', fileInput.files[0])
    }
  }

  if (removeLogoFlag.value) {
    data.append('remove_logo', '1')
  }

  data.append('_method', 'PUT')

  router.post(`/member/businesses/${props.business.id}`, data, {
    onFinish: () => {
      sending.value = false
    },
  })
}
</script>
