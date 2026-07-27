<template>
  <MemberLayout>
    <Head :title="`Editar Sección - ${business?.name || ''}`" />

    <PageHeader
      title="Editar Sección"
      :breadcrumbs="breadcrumbs"
      :backHref="`/member/businesses/${business?.id}/minisite/sections`"
    />

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <form @submit.prevent="updateSection">
          <div class="mb-3">
            <label class="form-label">Tipo de Sección</label>
            <input type="text" class="form-control" :value="sectionTypes[section.section_type]" disabled />
          </div>

          <div class="mb-3">
            <FieldText
              id="section-title"
              label="Título de la Sección"
              v-model="form.title"
              placeholder="Ej: Nuestros Servicios"
            />
          </div>

          <div class="mb-3">
            <FieldTextarea
              id="section-description"
              label="Descripción"
              v-model="form.description"
              placeholder="Breve descripción de esta sección..."
              :rows="3"
            />
          </div>

          <div class="mb-4">
            <h6 class="mb-3">Botones CTA</h6>
            <div v-for="(btn, index) in form.buttons" :key="index" class="row g-3 mb-3 p-3 bg-light rounded">
              <div class="col-md-4">
                <FieldText
                  id="btn-text"
                  label="Texto"
                  v-model="btn.text"
                  placeholder="Ver más"
                />
              </div>
              <div class="col-md-5">
                <FieldText
                  id="btn-url"
                  label="URL"
                  v-model="btn.url"
                  placeholder="https://..."
                />
              </div>
              <div class="col-md-2">
                <FieldSelect
                  id="btn-style"
                  label="Estilo"
                  v-model="btn.style"
                >
                  <option value="primary">Primario</option>
                  <option value="secondary">Secundario</option>
                  <option value="outline">Outline</option>
                </FieldSelect>
              </div>
              <div class="col-md-1 d-flex align-items-end pb-3">
                <button type="button" class="btn btn-outline-danger" @click="removeButton(index)">
                  <i class="bi bi-trash"></i>
                </button>
              </div>
            </div>
            <button type="button" class="btn btn-outline-primary btn-sm" @click="addButton">
              <i class="bi bi-plus me-1"></i>Agregar botón
            </button>
          </div>

          <div v-if="section.section_type === 'services'" class="border-top pt-4">
            <h6 class="mb-3">Configuración de Servicios</h6>
            <div class="row g-3">
              <div class="col-md-6">
                <FieldSelect
                  id="view-mode"
                  label="Vista"
                  v-model="config.view_mode"
                >
                  <option value="carousel">Carrusel</option>
                  <option value="list">Lista</option>
                </FieldSelect>
              </div>
              <div class="col-12">
                <FieldSwitch
                  id="show-image"
                  label="Mostrar imagen"
                  v-model="config.show_image"
                />
              </div>
              <div class="col-12">
                <FieldSwitch
                  id="show-price"
                  label="Mostrar precio"
                  v-model="config.show_price"
                />
              </div>
              <div class="col-12">
                <FieldSwitch
                  id="show-description"
                  label="Mostrar descripción"
                  v-model="config.show_description"
                />
              </div>
            </div>
          </div>

          <div v-if="section.section_type === 'gallery'" class="border-top pt-4">
            <h6 class="mb-3">Configuración de Galería</h6>
            <div class="row g-3">
              <div class="col-md-6">
                <FieldSelect
                  id="gallery-id"
                  label="Galería"
                  v-model="config.gallery_id"
                >
                  <option :value="null">Selecciona una galería</option>
                  <option v-for="gallery in galleries" :key="gallery.id" :value="gallery.id">
                    {{ gallery.name }}
                  </option>
                </FieldSelect>
              </div>
              <div class="col-md-6">
                <FieldNumber
                  id="images-limit"
                  label="Límite de imágenes"
                  v-model="config.images_limit"
                  :min="1"
                  :max="50"
                />
              </div>
            </div>
          </div>

          <div v-if="section.section_type === 'promotions'" class="border-top pt-4">
            <h6 class="mb-3">Configuración de Promociones</h6>
            <div class="row g-3">
              <div class="col-md-6">
                <FieldSwitch
                  id="show-all-promos"
                  label="Mostrar todas las promociones"
                  v-model="config.show_all"
                />
              </div>
            </div>
          </div>

          <div v-if="section.section_type === 'contact_form'" class="border-top pt-4">
            <h6 class="mb-3">Configuración del Formulario</h6>
            <div class="row g-3">
              <div class="col-md-6">
                <FieldSelect
                  id="form-id"
                  label="Formulario"
                  v-model="config.form_id"
                >
                  <option :value="null">Selecciona un formulario</option>
                  <option v-for="formItem in forms" :key="formItem.id" :value="formItem.id">
                    {{ formItem.name }}
                  </option>
                </FieldSelect>
              </div>
            </div>
          </div>

          <div class="mt-4">
            <button type="submit" class="btn btn-primary me-2" :disabled="sending">
              <i class="bi bi-check-lg me-1"></i>
              {{ sending ? 'Guardando...' : 'Guardar' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { Head, router, usePage } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldTextarea from '@/Components/Fields/FieldTextarea.vue'
import FieldSelect from '@/Components/Fields/FieldSelect.vue'
import FieldNumber from '@/Components/Fields/FieldNumber.vue'
import FieldSwitch from '@/Components/Fields/FieldSwitch.vue'

const page = usePage()
const business = computed(() => page.props.business)
const section = computed(() => page.props.section)
const sectionTypes = computed(() => page.props.sectionTypes || {})
const galleries = computed(() => page.props.galleries || [])
const forms = computed(() => page.props.forms || [])
const businessMenu = computed(() => page.props.businessMenu || [])

const breadcrumbs = computed(() => {
  const path = window.location.pathname
  const match = path.match(/^\/member\/businesses\/(\d+)/)
  if (match) {
    const bizId = parseInt(match[1])
    const biz = businessMenu.value.find(b => b.id === bizId)
    if (biz) {
      return [
        { label: 'Mis Negocios', href: '/member/business-modules' },
        { label: biz.name, href: `/member/businesses/${biz.id}/edit` },
        { label: 'Minisite', href: `/member/businesses/${biz.id}/minisite` },
        { label: 'Secciones', href: `/member/businesses/${biz.id}/minisite/sections` },
        { label: 'Editar', active: true },
      ]
    }
  }
  return [
    { label: 'Mis Negocios', href: '/member/business-modules' },
    { label: 'Minisite', href: `/member/businesses/${business.value?.id}/minisite` },
    { label: 'Secciones', href: `/member/businesses/${business.value?.id}/minisite/sections` },
    { label: 'Editar', active: true },
  ]
})

const sending = ref(false)
const form = reactive({
  title: '',
  description: '',
  buttons: [],
})

const config = reactive({
  view_mode: 'carousel',
  show_image: true,
  show_price: true,
  show_description: false,
  gallery_id: null,
  images_limit: 10,
  show_all: true,
  form_id: null,
})

onMounted(() => {
  if (section.value) {
    form.title = section.value.title || ''
    form.description = section.value.description || ''
    form.buttons = section.value.buttons || []
    Object.assign(config, section.value.config || {})
  }
})

const addButton = () => {
  form.buttons.push({ text: '', url: '', style: 'primary' })
}

const removeButton = (index) => {
  form.buttons.splice(index, 1)
}

const updateSection = () => {
  sending.value = true

  router.put(`/member/businesses/${business.value.id}/minisite/sections/${section.value.id}`, {
    title: form.title || null,
    description: form.description || null,
    config: { ...config },
    buttons: form.buttons.filter(b => b.text && b.url),
  }, {
    onFinish: () => {
      sending.value = false
    },
  })
}
</script>
