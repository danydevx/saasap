<template>
  <dialog
    ref="dialogRef"
    class="border-0 rounded-3 p-0 shadow-lg"
    style="width: 600px; max-width: 95vw;"
    @click="onBackdropClick"
    @cancel.prevent="close"
  >
    <div class="p-4">
      <div class="d-flex align-items-start justify-content-between mb-3">
        <h5 class="mb-0">{{ field?.id ? 'Editar Campo' : 'Agregar Campo' }}</h5>
        <button type="button" class="btn-close" aria-label="Cerrar" @click="close"></button>
      </div>

      <form @submit.prevent="save">
        <div class="row g-3">
          <div class="col-12 col-md-6">
            <label class="form-label">Type <span class="text-danger">*</span></label>
            <select v-model="config.type" class="form-select" required @change="onTypeChange">
              <option value="text">Text</option>
              <option value="email">Email</option>
              <option value="phone">Phone</option>
              <option value="number">Number</option>
              <option value="password">Password</option>
              <option value="url">URL</option>
              <option value="textarea">Textarea</option>
              <option value="select">Select</option>
              <option value="checkbox">Checkbox</option>
              <option value="radio">Radio</option>
              <option value="date">Date</option>
              <option value="file">File</option>
              <option value="image">Image</option>
            </select>
          </div>

          <div class="col-12 col-md-6">
            <label class="form-label">Label <span class="text-danger">*</span></label>
            <input
              v-model="config.label"
              type="text"
              class="form-control"
              placeholder="Field Label"
              required
              @input="autoGenerateName"
            />
          </div>

          <div class="col-12">
            <label class="form-label">Name <span class="text-danger">*</span></label>
            <div class="input-group">
              <input
                v-model="config.name"
                type="text"
                class="form-control"
                placeholder="field_name"
                required
                pattern="[a-zA-Z0-9_-]+"
              />
              <button type="button" class="btn btn-outline-secondary" @click="generateName" title="Generar nombre">
                <i class="bi bi-arrow-clockwise"></i>
              </button>
            </div>
            <small class="text-muted">Identificador unico para el campo</small>
          </div>

          <div class="col-12">
            <label class="form-label">Help Text</label>
            <input
              v-model="config.description"
              type="text"
              class="form-control"
              placeholder="Texto de ayuda..."
            />
          </div>

          <div class="col-12 col-md-6">
            <label class="form-label">Placeholder</label>
            <input
              v-model="config.placeholder"
              type="text"
              class="form-control"
              placeholder="Placeholder..."
            />
          </div>

          <div class="col-12 col-md-6">
            <label class="form-label">Class</label>
            <input
              v-model="config.className"
              type="text"
              class="form-control"
              placeholder="form-control"
            />
          </div>

          <div class="col-12 col-md-6" v-if="config.type === 'text' || config.type === 'textarea' || config.type === 'email' || config.type === 'phone' || config.type === 'number' || config.type === 'url'">
            <label class="form-label">Max Length</label>
            <input
              v-model.number="config.maxlength"
              type="number"
              class="form-control"
              placeholder="255"
              min="1"
            />
          </div>

          <div class="col-12 col-md-6" v-if="config.type === 'date'">
            <label class="form-label">Min Date</label>
            <select v-model="config.min_date_type" class="form-select mb-1">
              <option value="none">None</option>
              <option value="now">Desde ahora</option>
              <option value="date">Fecha especifica</option>
            </select>
            <input
              v-if="config.min_date_type === 'date'"
              v-model="config.min_date"
              type="date"
              class="form-control"
            />
          </div>

          <div class="col-12 col-md-6" v-if="config.type === 'date'">
            <label class="form-label">Max Date</label>
            <select v-model="config.max_date_type" class="form-select mb-1">
              <option value="none">None</option>
              <option value="now">Hasta ahora</option>
              <option value="date">Fecha especifica</option>
            </select>
            <input
              v-if="config.max_date_type === 'date'"
              v-model="config.max_date"
              type="date"
              class="form-control"
            />
          </div>

          <div class="col-12 col-md-6" v-if="config.type === 'file'">
            <label class="form-label">File Types</label>
            <select v-model="config.file_types" class="form-select" multiple style="height: 120px;">
              <option value="pdf">PDF (.pdf)</option>
              <option value="xlsx">Excel (.xlsx)</option>
              <option value="docx">Word (.docx)</option>
            </select>
            <small class="text-muted">Solo se permiten estos tipos de archivo</small>
          </div>

          <div class="col-12 col-md-6" v-if="config.type === 'image'">
            <label class="form-label">Image Types</label>
            <select v-model="config.file_types" class="form-select" multiple style="height: 80px;" disabled>
              <option value="jpg">JPG (.jpg)</option>
              <option value="png">PNG (.png)</option>
              <option value="webp">WebP (.webp)</option>
            </select>
            <small class="text-muted">Solo se permiten estos tipos de imagen</small>
          </div>

          <div class="col-12 col-md-6" v-if="config.type === 'file' || config.type === 'image'">
            <label class="form-label">Max File Size</label>
            <select v-model="config.max_file_size" class="form-select">
              <option :value="1">1 MB</option>
              <option :value="2">2 MB</option>
              <option :value="5">5 MB</option>
              <option v-if="config.type === 'file'" :value="10">10 MB</option>
              <option v-if="config.type === 'file'" :value="20">20 MB</option>
            </select>
            <small class="text-muted">El usuario puede reducir pero no ampliar</small>
          </div>

          <div class="col-12 col-md-6" v-if="config.type !== 'file' && config.type !== 'image'">
            <label class="form-label">Value</label>
            <input
              v-model="config.value"
              type="text"
              class="form-control"
              placeholder="Valor por defecto..."
            />
          </div>

          <div class="col-12" v-if="config.type === 'select' || config.type === 'radio' || config.type === 'checkbox'">
            <label class="form-label">Options <span class="text-muted">(valor | label)</span></label>
            <div class="options-builder border rounded p-2 mb-2" style="max-height: 200px; overflow-y: auto;">
              <div v-for="(opt, index) in optionsArray" :key="index" class="d-flex gap-2 mb-2 align-items-center">
                <input
                  v-model="opt.value"
                  type="text"
                  class="form-control form-control-sm"
                  placeholder="Valor"
                  style="width: 120px;"
                />
                <input
                  v-model="opt.label"
                  type="text"
                  class="form-control form-control-sm"
                  placeholder="Label"
                  style="flex: 1;"
                />
                <button
                  type="button"
                  class="btn btn-outline-danger btn-sm"
                  @click="removeOption(index)"
                  title="Eliminar"
                >
                  <i class="bi bi-trash"></i>
                </button>
              </div>
              <div v-if="optionsArray.length === 0" class="text-muted small text-center py-2">
                Sin opciones. Agrega una abajo.
              </div>
            </div>
            <button type="button" class="btn btn-outline-primary btn-sm" @click="addOption">
              <i class="bi bi-plus me-1"></i>Agregar Opcion
            </button>
          </div>

          <div class="col-12 col-md-6" v-if="config.type === 'select'">
            <label class="form-label">Selection Type</label>
            <select v-model="config.multiple" class="form-select">
              <option :value="false">Single Selection</option>
              <option :value="true">Multiple Selection</option>
            </select>
          </div>

          <div class="col-12 col-md-6" v-if="config.type === 'select' || config.type === 'checkbox' || config.type === 'radio'">
            <label class="form-label">Default Value</label>
            <select
              v-if="config.multiple || config.type === 'checkbox'"
              v-model="config.default_value"
              class="form-select"
              multiple
              style="height: 100px;"
            >
              <option v-for="opt in optionsArray" :key="opt.value" :value="opt.value">{{ opt.label || opt.value }}</option>
            </select>
            <select
              v-else
              v-model="config.default_value"
              class="form-select"
            >
              <option value="">-- Select --</option>
              <option v-for="opt in optionsArray" :key="opt.value" :value="opt.value">{{ opt.label || opt.value }}</option>
            </select>
          </div>

          <div class="col-12">
            <div class="form-check">
              <input
                v-model="config.required"
                type="checkbox"
                class="form-check-input"
                id="required"
              />
              <label class="form-check-label" for="required">
                Required
              </label>
            </div>
          </div>

          <div class="col-12 col-md-6">
            <label class="form-label">Width</label>
            <select v-model="config.width" class="form-select">
              <option value="full">Full (100%)</option>
              <option value="half">Half (50%)</option>
              <option value="third">Third (33%)</option>
              <option value="quarter">Quarter (25%)</option>
            </select>
          </div>

          <div class="col-12 col-md-6">
            <label class="form-label">Row</label>
            <input
              v-model.number="config.row"
              type="number"
              class="form-control"
              min="1"
            />
          </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
          <button type="button" class="btn btn-outline-secondary" @click="close">
            Cancel
          </button>
          <button type="submit" class="btn btn-primary">
            {{ field?.id ? 'Update' : 'Add Field' }}
          </button>
        </div>
      </form>
    </div>
  </dialog>
</template>

<script setup>
import { computed, ref, watch, onMounted } from 'vue'

const props = defineProps({
  field: {
    type: Object,
    default: null,
  },
})

const emit = defineEmits(['close', 'save'])

const dialogRef = ref(null)

onMounted(() => {
  if (dialogRef.value) {
    dialogRef.value.showModal()
    if (!props.field?.id) {
      generateName()
    }
  }
})

const config = ref({
  type: 'text',
  required: false,
  label: '',
  description: '',
  placeholder: '',
  className: 'form-control',
  name: '',
  subtype: '',
  maxlength: 255,
  value: '',
  options: [],
  access: null,
  role: null,
  row: 1,
  width: 'full',
  multiple: false,
  default_value: '',
  min_date_type: 'none',
  min_date: '',
  max_date_type: 'none',
  max_date: '',
  file_types: ['pdf', 'xlsx', 'docx'],
  max_file_size: 20,
})

const optionsArray = ref([])

const generateId = () => {
  return Math.random().toString(36).substring(2, 10)
}

const addOption = () => {
  const lastOpt = optionsArray.value[optionsArray.value.length - 1]
  const nextNum = lastOpt ? parseInt(lastOpt.value.replace(/\D/g, '')) + 1 || optionsArray.value.length + 1 : 1
  optionsArray.value.push({ value: `option_${nextNum}`, label: `Option ${nextNum}` })
}

const removeOption = (index) => {
  optionsArray.value.splice(index, 1)
}

const generateName = () => {
  const label = config.value.label || 'field'
  const slug = label.toLowerCase()
    .replace(/[^a-z0-9]+/g, '_')
    .replace(/^_|_$/g, '')
  config.value.name = `field_${slug}_${generateId()}`
}

const autoGenerateName = () => {
  if (!config.value.name || config.value.name.startsWith('field_')) {
    generateName()
  }
}

const onTypeChange = () => {
  if (config.value.type === 'select' || config.value.type === 'radio' || config.value.type === 'checkbox') {
    if (optionsArray.value.length === 0) {
      optionsArray.value = [
        { value: 'option_1', label: 'Option 1' },
        { value: 'option_2', label: 'Option 2' },
      ]
    }
  }
  if (config.value.type === 'image') {
    config.value.file_types = ['jpg', 'png', 'webp']
    config.value.max_file_size = 5
  } else if (config.value.type === 'file') {
    config.value.file_types = ['pdf', 'xlsx', 'docx']
    config.value.max_file_size = 20
  }
}

watch(
  () => props.field,
  (field) => {
    if (field) {
      const fieldConfig = field.field_config || {}
      const rawOptions = fieldConfig.options || field.options || []

      let parsedOptions = []
      if (rawOptions.length > 0) {
        if (typeof rawOptions[0] === 'object' && rawOptions[0] !== null) {
          parsedOptions = rawOptions
        } else {
          parsedOptions = rawOptions.map((opt, idx) => ({
            value: `option_${idx + 1}`,
            label: opt,
          }))
        }
      }

      config.value = {
        type: fieldConfig.type || field.type || 'text',
        required: fieldConfig.required ?? field.is_required ?? false,
        label: fieldConfig.label || field.label || '',
        description: fieldConfig.description || '',
        placeholder: fieldConfig.placeholder || field.placeholder || '',
        className: fieldConfig.className || 'form-control',
        name: fieldConfig.name || field.name || '',
        subtype: fieldConfig.subtype || '',
        maxlength: fieldConfig.maxlength || 255,
        value: fieldConfig.value || '',
        options: parsedOptions,
        access: fieldConfig.access || null,
        role: fieldConfig.role || null,
        row: fieldConfig.row || field.row || 1,
        width: fieldConfig.width || field.width || 'full',
        multiple: fieldConfig.multiple ?? field.multiple ?? false,
        default_value: fieldConfig.default_value ?? field.default_value ?? '',
        min_date_type: fieldConfig.min_date_type || 'none',
        min_date: fieldConfig.min_date || '',
        max_date_type: fieldConfig.max_date_type || 'none',
        max_date: fieldConfig.max_date || '',
        file_types: fieldConfig.file_types || ['pdf', 'xlsx', 'docx'],
        max_file_size: fieldConfig.max_file_size || 20,
      }

      optionsArray.value = parsedOptions
    } else {
      config.value = {
        type: 'text',
        required: false,
        label: '',
        description: '',
        placeholder: '',
        className: 'form-control',
        name: '',
        subtype: '',
        maxlength: 255,
        value: '',
        options: [],
        access: null,
        role: null,
        row: 1,
        width: 'full',
        multiple: false,
        default_value: '',
        min_date_type: 'none',
        min_date: '',
        max_date_type: 'none',
        max_date: '',
        file_types: ['pdf', 'xlsx', 'docx'],
        max_file_size: 20,
      }
      optionsArray.value = []
    }
  },
  { immediate: true }
)

const openDialog = () => {
  if (!dialogRef.value || dialogRef.value.open) return
  dialogRef.value.showModal()

  if (!props.field?.id) {
    generateName()
  }
}

const closeDialog = () => {
  if (!dialogRef.value || !dialogRef.value.open) return
  dialogRef.value.close()
}

watch(
  () => props.field,
  (value) => {
    if (value !== null && dialogRef.value) {
      dialogRef.value.showModal()
    }
  }
)

const close = () => {
  emit('close')
}

const save = () => {
  let defaultValue = config.value.default_value

  if (config.value.type === 'select' && config.value.multiple) {
    if (!Array.isArray(defaultValue)) {
      defaultValue = defaultValue ? [defaultValue] : []
    }
  }

  const needsOptions = ['select', 'radio', 'checkbox'].includes(config.value.type)

  const data = {
    ...config.value,
    default_value: defaultValue,
    options: needsOptions
      ? optionsArray.value.map(o => ({ value: o.value, label: o.label })).filter(o => o.value.trim())
      : [],
  }

  if (data.type === 'text') {
    data.subtype = 'text'
  } else if (data.type === 'email') {
    data.subtype = 'email'
  } else if (data.type === 'phone') {
    data.subtype = 'tel'
  } else if (data.type === 'number') {
    data.subtype = 'number'
  } else if (data.type === 'password') {
    data.subtype = 'password'
  } else if (data.type === 'url') {
    data.subtype = 'url'
  }

  emit('save', data)
  closeDialog()
}

const onBackdropClick = (event) => {
  if (event.target === dialogRef.value) {
    close()
  }
}
</script>
