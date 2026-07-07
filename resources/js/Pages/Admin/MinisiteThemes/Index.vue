<template>
  <AdminLayout>
    <Head title="Themes de Minisite" />

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
      <div>
        <Link href="/admin" class="text-decoration-none text-muted small">
          <i class="bi bi-arrow-left me-1"></i>Admin
        </Link>
        <h1 class="h4 mb-1 mt-1">Themes de Minisite</h1>
      </div>
      <button @click="showCreateModal = true" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i>Nuevo Theme
      </button>
    </div>

    <div v-if="$page.props.flash?.success" class="alert alert-success alert-dismissible fade show" role="alert">
      {{ $page.props.flash.success }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <div v-if="$page.props.flash?.error" class="alert alert-danger alert-dismissible fade show" role="alert">
      {{ $page.props.flash.error }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>

    <div class="row g-4">
      <div v-for="theme in themes" :key="theme.id" class="col-md-6 col-lg-4">
        <div class="card h-100" :class="{ 'border-primary': theme.is_active }">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-3">
              <div>
                <h5 class="card-title mb-1">{{ theme.name }}</h5>
                <span class="badge" :class="theme.is_active ? 'bg-success' : 'bg-secondary'">
                  {{ theme.is_active ? 'Activo' : 'Inactivo' }}
                </span>
              </div>
              <div class="btn-group btn-group-sm">
                <button @click="editTheme(theme)" class="btn btn-outline-primary">
                  <i class="bi bi-pencil"></i>
                </button>
                <button @click="deleteTheme(theme)" class="btn btn-outline-danger" :disabled="theme.businesses_count > 0">
                  <i class="bi bi-trash"></i>
                </button>
              </div>
            </div>

            <p class="text-muted small mb-3">{{ theme.description }}</p>

            <div class="theme-preview rounded p-3 mb-3" :style="getPreviewStyle(theme)">
              <div class="d-flex gap-2 mb-2">
                <div class="preview-btn" style="width: 30px; height: 20px; border-radius: 4px; background: var(--preview-primary);"></div>
                <div class="preview-btn" style="width: 30px; height: 20px; border-radius: 20px; background: var(--preview-secondary);"></div>
                <div class="preview-btn" style="width: 30px; height: 20px; border-radius: 0; background: var(--preview-accent);"></div>
              </div>
              <div class="bg-white rounded p-2" style="font-size: 10px;">
                <div style="height: 8px; width: 60%; background: #eee; border-radius: 2px; margin-bottom: 4px;"></div>
                <div style="height: 6px; width: 80%; background: #f5f5f5; border-radius: 2px; margin-bottom: 2px;"></div>
                <div style="height: 6px; width: 40%; background: #f5f5f5; border-radius: 2px;"></div>
              </div>
            </div>

            <div class="small text-muted">
              <span class="me-3">
                <i class="bi bi-building me-1"></i>{{ theme.businesses_count || 0 }} negocios
              </span>
              <span>
                <i class="bi bi-grid me-1"></i>{{ theme.layout_config?.page_style || 'N/A' }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-if="themes.length === 0" class="alert alert-info mt-4">
      No hay themes creados. Crea tu primer theme para empezar.
    </div>

    <div v-if="showCreateModal || editingTheme" class="modal show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5)">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ editingTheme ? 'Editar Theme' : 'Nuevo Theme' }}</h5>
            <button type="button" class="btn-close" @click="closeModal"></button>
          </div>
          <form @submit.prevent="submitForm">
            <div class="modal-body">
              <div class="row g-3">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">Nombre</label>
                    <input v-model="form.name" type="text" class="form-control" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">Slug (identificador único)</label>
                    <input v-model="form.slug" type="text" class="form-control" required>
                  </div>
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label">Descripción</label>
                <textarea v-model="form.description" class="form-control" rows="2"></textarea>
              </div>

              <div class="mb-3">
                <label class="form-label">URL de imagen de preview</label>
                <input v-model="form.preview_image" type="text" class="form-control" placeholder="https://...">
              </div>

              <div class="mb-3 form-check">
                <input v-model="form.is_active" type="checkbox" class="form-check-input" id="activeCheck">
                <label class="form-check-label" for="activeCheck">Theme activo</label>
              </div>

              <hr>

              <h6>Colores</h6>
              <div class="row g-3">
                <div class="col-6 col-md-3">
                  <label class="form-label small">Primary</label>
                  <div class="d-flex gap-2">
                    <input type="color" v-model="form.css_variables.colors.primary" class="form-control form-control-color" style="width: 50px;">
                    <input type="text" v-model="form.css_variables.colors.primary" class="form-control form-control-sm">
                  </div>
                </div>
                <div class="col-6 col-md-3">
                  <label class="form-label small">Secondary</label>
                  <div class="d-flex gap-2">
                    <input type="color" v-model="form.css_variables.colors.secondary" class="form-control form-control-color" style="width: 50px;">
                    <input type="text" v-model="form.css_variables.colors.secondary" class="form-control form-control-sm">
                  </div>
                </div>
                <div class="col-6 col-md-3">
                  <label class="form-label small">Accent</label>
                  <div class="d-flex gap-2">
                    <input type="color" v-model="form.css_variables.colors.accent" class="form-control form-control-color" style="width: 50px;">
                    <input type="text" v-model="form.css_variables.colors.accent" class="form-control form-control-sm">
                  </div>
                </div>
                <div class="col-6 col-md-3">
                  <label class="form-label small">Background</label>
                  <div class="d-flex gap-2">
                    <input type="color" v-model="form.css_variables.colors.background" class="form-control form-control-color" style="width: 50px;">
                    <input type="text" v-model="form.css_variables.colors.background" class="form-control form-control-sm">
                  </div>
                </div>
              </div>

              <div class="row g-3 mt-2">
                <div class="col-6 col-md-3">
                  <label class="form-label small">Text</label>
                  <div class="d-flex gap-2">
                    <input type="color" v-model="form.css_variables.colors.text" class="form-control form-control-color" style="width: 50px;">
                    <input type="text" v-model="form.css_variables.colors.text" class="form-control form-control-sm">
                  </div>
                </div>
                <div class="col-6 col-md-3">
                  <label class="form-label small">Text Light</label>
                  <div class="d-flex gap-2">
                    <input type="color" v-model="form.css_variables.colors.text_light" class="form-control form-control-color" style="width: 50px;">
                    <input type="text" v-model="form.css_variables.colors.text_light" class="form-control form-control-sm">
                  </div>
                </div>
              </div>

              <hr>

              <h6>Fuentes</h6>
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label small">Fuente para títulos</label>
                  <input v-model="form.css_variables.fonts.headings" type="text" class="form-control" placeholder="Montserrat, sans-serif">
                </div>
                <div class="col-md-6">
                  <label class="form-label small">Fuente para cuerpo</label>
                  <input v-model="form.css_variables.fonts.body" type="text" class="form-control" placeholder="Inter, sans-serif">
                </div>
              </div>

              <hr>

              <h6>Estilos</h6>
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label small">Border Radius</label>
                  <input v-model="form.css_variables.border_radius" type="text" class="form-control" placeholder="8px">
                </div>
                <div class="col-md-6">
                  <label class="form-label small">Estilo de botón</label>
                  <select v-model="form.css_variables.button_style" class="form-select">
                    <option value="rounded-pill">Rounded Pill</option>
                    <option value="rounded-0">Sin redondear</option>
                    <option value="rounded-20">Muy redondeado</option>
                    <option value="rounded-8">Redondeado medio</option>
                  </select>
                </div>
              </div>

              <hr>

              <h6>Configuración de Layout</h6>
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label small">Estilo de página</label>
                  <select v-model="form.layout_config.page_style" class="form-select">
                    <option value="dark">Oscuro</option>
                    <option value="light">Claro</option>
                    <option value="clean">Limpio</option>
                    <option value="warm">Cálido</option>
                    <option value="fresh">Fresco</option>
                    <option value="dramatic">Dramático</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label small">Estilo de Hero</label>
                  <select v-model="form.layout_config.hero_style" class="form-select">
                    <option value="fullwidth">Full Width</option>
                    <option value="centered">Centrado</option>
                    <option value="split">Dividido</option>
                    <option value="boxed">En caja</option>
                    <option value="fullbleed">Full Bleed</option>
                    <option value="friendly">Friendly</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" @click="closeModal">Cancelar</button>
              <button type="submit" class="btn btn-primary" :disabled="sending">
                {{ sending ? 'Guardando...' : 'Guardar' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { usePage, Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  themes: Array,
})

const showCreateModal = ref(false)
const editingTheme = ref(null)
const sending = ref(false)

const defaultCssVariables = {
  colors: {
    primary: null,
    secondary: null,
    accent: null,
    background: null,
    text: null,
    text_light: null,
  },
  fonts: {
    headings: 'Montserrat, sans-serif',
    body: 'Inter, sans-serif',
  },
  border_radius: '8px',
  card_style: 'shadow-lg',
  button_style: 'rounded-pill',
}

const defaultLayoutConfig = {
  page_style: 'light',
  section_style: 'spacious',
  cards_per_row: 3,
  hero_style: 'fullwidth',
  animations: {
    page_transition: 'fade-in 0.5s ease-out',
    cards_hover: 'lift: translateY(-8px)',
    buttons_hover: 'scale(1.05)',
    sections: 'fade-in-up 0.6s ease-out',
  },
}

const form = ref({
  name: '',
  slug: '',
  description: '',
  preview_image: '',
  is_active: true,
  css_variables: { ...defaultCssVariables },
  layout_config: { ...defaultLayoutConfig },
})

const editTheme = (theme) => {
  editingTheme.value = theme
  form.value = {
    name: theme.name,
    slug: theme.slug,
    description: theme.description || '',
    preview_image: theme.preview_image || '',
    is_active: theme.is_active,
    css_variables: { ...theme.css_variables },
    layout_config: { ...theme.layout_config },
  }
}

const deleteTheme = (theme) => {
  if (theme.businesses_count > 0) return
  if (!confirm(`¿Eliminar el theme "${theme.name}"?`)) return

  router.delete(`/admin/minisite-themes/${theme.id}`)
}

const closeModal = () => {
  showCreateModal.value = false
  editingTheme.value = null
  form.value = {
    name: '',
    slug: '',
    description: '',
    preview_image: '',
    is_active: true,
    css_variables: { ...defaultCssVariables },
    layout_config: { ...defaultLayoutConfig },
  }
}

const cleanColor = (value) => {
  if (!value || value === '#000000') return null
  return value
}

const cleanFormColors = (data) => {
  const cleaned = { ...data }
  if (cleaned.css_variables?.colors) {
    cleaned.css_variables.colors = { ...cleaned.css_variables.colors }
    Object.keys(cleaned.css_variables.colors).forEach(key => {
      cleaned.css_variables.colors[key] = cleanColor(cleaned.css_variables.colors[key])
    })
  }
  return cleaned
}

const submitForm = () => {
  sending.value = true

  const formData = cleanFormColors(form.value)

  if (editingTheme.value) {
    router.put(
      `/admin/minisite-themes/${editingTheme.value.id}`,
      formData,
      {
        onFinish: () => {
          sending.value = false
          closeModal()
        },
      }
    )
  } else {
    router.post(
      '/admin/minisite-themes',
      formData,
      {
        onFinish: () => {
          sending.value = false
          closeModal()
        },
      }
    )
  }
}

const getPreviewStyle = (theme) => {
  if (!theme.css_variables?.colors) return {}
  const { primary, secondary, accent } = theme.css_variables.colors
  return {
    '--preview-primary': primary || '#1a1a2e',
    '--preview-secondary': secondary || '#6B7280',
    '--preview-accent': accent || '#3B82F6',
    backgroundColor: theme.css_variables.colors.background || '#f8f9fa',
  }
}
</script>

<style scoped>
.theme-preview {
  border: 1px solid #dee2e6;
}

.preview-btn {
  display: inline-block;
  transition: transform 0.2s;
}

.preview-btn:hover {
  transform: scale(1.1);
}
</style>
