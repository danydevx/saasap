<template>
  <MemberLayout>
    <Head :title="`Personalizacion de Marca - ${business.name}`" />

    <PageHeader
      title="Personalizacion de Marca"
      :breadcrumbs="breadcrumbs"
      :backHref="'/member/business-modules'"
    />

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div v-if="$page.props.flash?.success" class="alert alert-success alert-dismissible fade show" role="alert">
          {{ $page.props.flash.success }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>

        <ul class="nav nav-tabs mb-4" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link" :class="{ active: activeTab === 'colors' }" @click="activeTab = 'colors'" type="button">
              <i class="bi bi-droplet me-1"></i>Colores
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" :class="{ active: activeTab === 'fonts' }" @click="activeTab = 'fonts'" type="button">
              <i class="bi bi-type me-1"></i>Tipografias
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" :class="{ active: activeTab === 'styles' }" @click="activeTab = 'styles'" type="button">
              <i class="bi bi-sliders me-1"></i>Estilos
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" :class="{ active: activeTab === 'preview' }" @click="activeTab = 'preview'" type="button">
              <i class="bi bi-eye me-1"></i>Vista Previa
            </button>
          </li>
        </ul>

        <form @submit.prevent="submit">
          <div class="tab-content">
            <div class="tab-pane fade" :class="{ 'show active': activeTab === 'colors' }" role="tabpanel">
              <div class="row g-3">
                <div class="col-12">
                  <h6 class="text-muted mb-3">Modo Claro</h6>
                </div>

                <div class="col-6 col-md-3">
                  <FieldColorpicker
                    id="brand-primary-light"
                    label="Color Principal"
                    v-model="form.colors.brand_primary.light"
                    placeholder="#3B82F6"
                  />
                </div>

                <div class="col-6 col-md-3">
                  <FieldColorpicker
                    id="brand-secondary-light"
                    label="Color Secundario"
                    v-model="form.colors.brand_secondary.light"
                    placeholder="#8B5CF6"
                  />
                </div>

                <div class="col-6 col-md-3">
                  <FieldColorpicker
                    id="brand-tertiary-light"
                    label="Color Terciario"
                    v-model="form.colors.brand_tertiary.light"
                    placeholder="#EC4899"
                  />
                </div>

                <div class="col-6 col-md-3">
                  <FieldColorpicker
                    id="brand-quaternary-light"
                    label="Color Cuaternario"
                    v-model="form.colors.brand_quaternary.light"
                    placeholder="#10B981"
                  />
                </div>

                <div class="col-6 col-md-3">
                  <FieldColorpicker
                    id="accent-light"
                    label="Color Acento"
                    v-model="form.colors.accent.light"
                    placeholder="#F59E0B"
                  />
                </div>

                <div class="col-6 col-md-3">
                  <FieldColorpicker
                    id="hover-light"
                    label="Color Hover"
                    v-model="form.colors.hover.light"
                    placeholder="#1F2937"
                  />
                </div>

                <div class="col-6 col-md-3">
                  <FieldColorpicker
                    id="brand-link-light"
                    label="Color Enlace"
                    v-model="form.colors.brand_link.light"
                    placeholder="#3B82F6"
                    hint="Color para enlaces del body"
                  />
                </div>

                <div class="col-6 col-md-3">
                  <FieldColorpicker
                    id="brand-bgcolor-header-light"
                    label="Color Header"
                    v-model="form.colors.brand_bgcolor_header.light"
                    placeholder="#FFFFFF"
                    hint="Fondo del header"
                  />
                </div>

                <div class="col-6 col-md-3">
                  <FieldColorpicker
                    id="brand-bgcolor-footer-light"
                    label="Color Footer"
                    v-model="form.colors.brand_bgcolor_footer.light"
                    placeholder="#F8F9FA"
                    hint="Fondo del footer"
                  />
                </div>

                <div class="col-12">
                  <hr class="my-4" />
                  <h6 class="text-muted mb-3">Modo Oscuro</h6>
                </div>

                <div class="col-6 col-md-3">
                  <FieldColorpicker
                    id="brand-primary-dark"
                    label="Color Principal"
                    v-model="form.colors.brand_primary.dark"
                    placeholder="#60A5FA"
                  />
                </div>

                <div class="col-6 col-md-3">
                  <FieldColorpicker
                    id="brand-secondary-dark"
                    label="Color Secundario"
                    v-model="form.colors.brand_secondary.dark"
                    placeholder="#A78BFA"
                  />
                </div>

                <div class="col-6 col-md-3">
                  <FieldColorpicker
                    id="brand-tertiary-dark"
                    label="Color Terciario"
                    v-model="form.colors.brand_tertiary.dark"
                    placeholder="#F472B6"
                  />
                </div>

                <div class="col-6 col-md-3">
                  <FieldColorpicker
                    id="brand-quaternary-dark"
                    label="Color Cuaternario"
                    v-model="form.colors.brand_quaternary.dark"
                    placeholder="#34D399"
                  />
                </div>

                <div class="col-6 col-md-3">
                  <FieldColorpicker
                    id="accent-dark"
                    label="Color Acento"
                    v-model="form.colors.accent.dark"
                    placeholder="#FBBF24"
                  />
                </div>

                <div class="col-6 col-md-3">
                  <FieldColorpicker
                    id="hover-dark"
                    label="Color Hover"
                    v-model="form.colors.hover.dark"
                    placeholder="#374151"
                  />
                </div>

                <div class="col-6 col-md-3">
                  <FieldColorpicker
                    id="brand-link-dark"
                    label="Color Enlace"
                    v-model="form.colors.brand_link.dark"
                    placeholder="#60A5FA"
                    hint="Color para enlaces del body"
                  />
                </div>

                <div class="col-6 col-md-3">
                  <FieldColorpicker
                    id="brand-bgcolor-header-dark"
                    label="Color Header"
                    v-model="form.colors.brand_bgcolor_header.dark"
                    placeholder="#1A1A2E"
                    hint="Fondo del header"
                  />
                </div>

                <div class="col-6 col-md-3">
                  <FieldColorpicker
                    id="brand-bgcolor-footer-dark"
                    label="Color Footer"
                    v-model="form.colors.brand_bgcolor_footer.dark"
                    placeholder="#16213E"
                    hint="Fondo del footer"
                  />
                </div>
              </div>
            </div>

            <div class="tab-pane fade" :class="{ 'show active': activeTab === 'fonts' }" role="tabpanel">
              <div class="row g-3">
                <div class="col-12 col-md-6">
                  <FieldSelect
                    id="font-heading"
                    label="Fuente para Encabezados"
                    v-model="form.fonts.heading"
                    :options="fontOptions"
                    :formError="errors.fonts?.heading"
                  />
                  <small class="text-muted d-block mb-3">Usada en titulos y subtitulos</small>
                </div>

                <div class="col-12 col-md-6">
                  <FieldSelect
                    id="font-body"
                    label="Fuente para Cuerpo de Texto"
                    v-model="form.fonts.body"
                    :options="fontOptions"
                    :formError="errors.fonts?.body"
                  />
                  <small class="text-muted d-block mb-3">Usada en parrafos y texto general</small>
                </div>

                <div class="col-12 col-md-6">
                  <FieldSelect
                    id="font-buttons"
                    label="Fuente para Botones"
                    v-model="form.fonts.buttons"
                    :options="fontOptions"
                    :formError="errors.fonts?.buttons"
                  />
                  <small class="text-muted d-block mb-3">Usada en botones y elementos interactivos</small>
                </div>

                <div class="col-12 col-md-8">
                  <FieldUrl
                    id="custom-font-url"
                    label="URL de Fuente Personalizada (opcional)"
                    v-model="form.custom_font_url"
                    :formError="errors.custom_font_url"
                    placeholder="https://fonts.googleapis.com/css2?family=TuFuente"
                  />
                  <small class="text-muted d-block">Pega el link de Google Fonts para usar una fuente no listada</small>
                </div>

                <div class="col-12">
                  <div class="card bg-light">
                    <div class="card-body">
                      <h6 class="card-title">Vista Previa de Fuentes</h6>
                      <div class="p-3 border rounded bg-white">
                        <h4 :style="{ fontFamily: getFontFamily(form.fonts.heading) }">Encabezado H4</h4>
                        <p class="mb-2" :style="{ fontFamily: getFontFamily(form.fonts.body) }">
                          Este es un parrafo de ejemplo. El cuerpo de texto se muestra asi.
                        </p>
                        <button
                          class="btn btn-primary"
                          :style="{ fontFamily: getFontFamily(form.fonts.buttons) }"
                        >
                          Boton de Ejemplo
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="tab-pane fade" :class="{ 'show active': activeTab === 'styles' }" role="tabpanel">
              <div class="row g-3">
                <div class="col-12 col-md-6">
                  <FieldSwitch
                    id="dark-mode"
                    label="Habilitar modo oscuro"
                    v-model="form.dark_mode"
                  />
                  <small class="text-muted d-block mb-3">Permite a los visitantes cambiar al tema oscuro</small>
                </div>

                <div class="col-12 col-md-6">
                  <label class="form-label">Estilo de Botones</label>
                  <div class="d-flex gap-2">
                    <div
                      class="btn btn-primary flex-grow-1"
                      :style="{ borderRadius: getButtonsBorderRadius('rounded') }"
                      :class="{ 'border border-2 border-dark': form.buttons_style === 'rounded' }"
                      @click="form.buttons_style = 'rounded'"
                      style="cursor: pointer;"
                    >
                      <i class="bi bi-circle me-1"></i> Redondos
                    </div>
                    <div
                      class="btn btn-primary flex-grow-1"
                      :style="{ borderRadius: getButtonsBorderRadius('square') }"
                      :class="{ 'border border-2 border-dark': form.buttons_style === 'square' }"
                      @click="form.buttons_style = 'square'"
                      style="cursor: pointer;"
                    >
                      <i class="bi bi-square me-1"></i> Cuadrados
                    </div>
                    <div
                      class="btn btn-primary flex-grow-1"
                      :style="{ borderRadius: getButtonsBorderRadius('round') }"
                      :class="{ 'border border-2 border-dark': form.buttons_style === 'round' }"
                      @click="form.buttons_style = 'round'"
                      style="cursor: pointer;"
                    >
                      <i class="bi bi-app me-1"></i> Esquinas Redondas
                    </div>
                  </div>
                  <small class="text-muted d-block mt-2">Selecciona el estilo de los botones</small>
                </div>
              </div>
            </div>

            <div class="tab-pane fade" :class="{ 'show active': activeTab === 'preview' }" role="tabpanel">
              <div class="text-center mb-4">
                <div class="btn-group" role="group">
                  <button
                    type="button"
                    class="btn"
                    :class="previewMode === 'light' ? 'btn-primary' : 'btn-outline-secondary'"
                    @click="previewMode = 'light'"
                  >
                    <i class="bi bi-sun me-1"></i>Claro
                  </button>
                  <button
                    type="button"
                    class="btn"
                    :class="previewMode === 'dark' ? 'btn-primary' : 'btn-outline-secondary'"
                    @click="previewMode = 'dark'"
                  >
                    <i class="bi bi-moon me-1"></i>Oscuro
                  </button>
                </div>
              </div>

              <div class="preview-frame" :class="previewMode === 'dark' ? 'dark-mode' : 'light-mode'">
                <div class="preview-header" :style="{ backgroundColor: getColor('brand_bgcolor_header') }">
                  <h1 :style="{ color: getColor('brand_primary') }">Mi Negocio</h1>
                </div>
                <div class="preview-content">
                  <div class="preview-section" :style="{ borderLeftColor: getColor('brand_secondary') }">
                    <h3 :style="{ color: getColor('brand_primary') }">Nuestros Servicios</h3>
                    <p>Descubre todo lo que podemos ofrecerte con la mejor calidad.</p>
                    <button
                      class="btn btn-primary"
                      :style="{
                        backgroundColor: getColor('brand_primary'),
                        borderColor: getColor('brand_primary'),
                        borderRadius: getButtonsBorderRadius(form.buttons_style)
                      }"
                    >
                      Conocer mas
                    </button>
                  </div>
                  <div class="preview-section" :style="{ borderLeftColor: getColor('accent') }">
                    <h3 :style="{ color: getColor('accent') }">Contactanos</h3>
                    <p>Estamos aqui para ayudarte.</p>
                    <a
                      href="#"
                      :style="{ color: getColor('brand_link') }"
                      class="me-3"
                    >
                      Visitar nuestro sitio
                    </a>
                    <a
                      href="#"
                      :style="{ color: getColor('hover') }"
                    >
                      Enviar mensaje (hover)
                    </a>
                  </div>
                </div>
                <div class="preview-footer" :style="{ backgroundColor: getColor('brand_bgcolor_footer') }">
                  <p :style="{ color: getColor('brand_primary') }">© 2024 Mi Negocio - Todos los derechos reservados</p>
                </div>
              </div>
            </div>
          </div>

          <div class="d-flex gap-2 mt-4 pt-4 border-top">
            <button type="submit" class="btn btn-primary" :disabled="sending">
              {{ sending ? 'Guardando...' : 'Guardar Configuracion' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { ref, reactive, computed, watchEffect } from 'vue'
import { Head, router, usePage } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldColorpicker from '@/Components/Fields/FieldColorpicker.vue'
import FieldSelect from '@/Components/Fields/FieldSelect.vue'
import FieldSwitch from '@/Components/Fields/FieldSwitch.vue'
import FieldUrl from '@/Components/Fields/FieldUrl.vue'
import { toast } from 'vue3-toastify'

const page = usePage()
const business = computed(() => page.props.business)
const branding = computed(() => page.props.branding)
const errors = computed(() => page.props.errors || {})

const breadcrumbs = computed(() => [
  { label: 'Mis Negocios', href: '/member/business-modules' },
  { label: 'Personalizacion de Marca', active: true },
])

const activeTab = ref('colors')
const previewMode = ref('light')
const sending = ref(false)

const defaultColors = {
  brand_primary: { light: '#3B82F6', dark: '#60A5FA' },
  brand_secondary: { light: '#8B5CF6', dark: '#A78BFA' },
  brand_tertiary: { light: '#EC4899', dark: '#F472B6' },
  brand_quaternary: { light: '#10B981', dark: '#34D399' },
  accent: { light: '#F59E0B', dark: '#FBBF24' },
  hover: { light: '#1F2937', dark: '#374151' },
  brand_link: { light: '#3B82F6', dark: '#60A5FA' },
  brand_bgcolor_header: { light: '#FFFFFF', dark: '#1A1A2E' },
  brand_bgcolor_footer: { light: '#F8F9FA', dark: '#16213E' },
}

const form = reactive({
  colors: branding.value?.colors ? { ...branding.value.colors } : { ...defaultColors },
  fonts: branding.value?.fonts ? { ...branding.value.fonts } : {
    heading: 'Poppins',
    body: 'Open Sans',
    buttons: 'Poppins',
  },
  custom_font_url: branding.value?.custom_font_url || '',
  dark_mode: branding.value?.dark_mode ?? true,
  buttons_style: branding.value?.buttons_style || 'round',
})

const fontLinkId = 'dynamic-google-fonts'

const googleFontsUrl = computed(() => {
  const fonts = [
    form.fonts.heading,
    form.fonts.body,
    form.fonts.buttons,
  ].filter(f => f && f !== 'custom')

  if (fonts.length === 0) return ''

  const families = [...new Set(fonts)].map(f => `family=${f.replace(/ /g, '+')}:wght@400;500;600;700`).join('&')
  return `https://fonts.googleapis.com/css2?${families}&display=swap`
})

watchEffect(() => {
  if (!googleFontsUrl.value) return

  let link = document.getElementById(fontLinkId)
  if (!link) {
    link = document.createElement('link')
    link.id = fontLinkId
    link.rel = 'stylesheet'
    document.head.appendChild(link)
  }
  link.href = googleFontsUrl.value
})

const fontOptions = [
  { value: 'Poppins', label: 'Poppins' },
  { value: 'Montserrat', label: 'Montserrat' },
  { value: 'Playfair Display', label: 'Playfair Display' },
  { value: 'Merriweather', label: 'Merriweather' },
  { value: 'Oswald', label: 'Oswald' },
  { value: 'Raleway', label: 'Raleway' },
  { value: 'Ubuntu', label: 'Ubuntu' },
  { value: 'Lato', label: 'Lato' },
  { value: 'Inter', label: 'Inter' },
  { value: 'Nunito', label: 'Nunito' },
  { value: 'Open Sans', label: 'Open Sans' },
  { value: 'Roboto', label: 'Roboto' },
  { value: 'Source Sans Pro', label: 'Source Sans Pro' },
]

const getColor = (colorKey) => {
  const color = form.colors[colorKey]
  if (!color) return '#000000'
  return previewMode.value === 'dark' ? (color.dark || color.light) : (color.light || color.dark || '#000000')
}

const getFontFamily = (fontName) => {
  if (!fontName || fontName === 'custom') {
    return 'sans-serif'
  }
  return `'${fontName}', sans-serif`
}

const getButtonsBorderRadius = (style) => {
  const radius = {
    rounded: '50px',
    square: '0px',
    round: '8px',
  }
  return radius[style] || '8px'
}

const submit = () => {
  sending.value = true

  const data = new FormData()
  data.append('colors', JSON.stringify(form.colors))
  data.append('fonts', JSON.stringify(form.fonts))
  data.append('custom_font_url', form.custom_font_url || '')
  data.append('dark_mode', form.dark_mode ? '1' : '0')
  data.append('buttons_style', form.buttons_style || 'round')

  router.post(`/member/businesses/${business.value.id}/branding`, data, {
    onSuccess: () => {
      sending.value = false
      toast.success('Configuración de marca guardada correctamente')
    },
    onError: (errors) => {
      sending.value = false
      toast.error('Error al guardar la configuración')
    },
    onFinish: () => {
      sending.value = false
    },
  })
}
</script>

<style scoped>
.preview-frame {
  border: 2px solid #dee2e6;
  border-radius: 12px;
  overflow: hidden;
  min-height: 400px;
}

.preview-frame.light-mode {
  background-color: #ffffff;
  color: #212529;
}

.preview-frame.dark-mode {
  background-color: #1a1a2e;
  color: #f8f9fa;
}

.preview-header {
  padding: 2rem;
  text-align: center;
}

.preview-content {
  padding: 2rem;
}

.preview-section {
  padding: 1rem;
  margin-bottom: 1rem;
  border-left: 4px solid;
  border-radius: 4px;
  background: rgba(0,0,0,0.02);
}

.preview-footer {
  padding: 1rem 2rem;
  text-align: center;
}
</style>
