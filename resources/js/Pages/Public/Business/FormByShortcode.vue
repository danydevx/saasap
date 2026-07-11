<template>
  <div class="minisite">
    <div class="bg-light py-4 mb-4">
      <div class="container">
        <h1 class="mb-0">{{ form.name }} - {{ business.name }}</h1>
      </div>
    </div>

    <div class="container py-4">
      <div class="row">
        <div class="col-md-8">
          <div v-if="$page.props.flash?.success" class="alert alert-success alert-dismissible fade show" role="alert">
            {{ $page.props.flash.success }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>

          <div class="card">
            <div class="card-body">
              <h5 class="card-title mb-4">{{ form.name }}</h5>
              <p v-if="form.description" class="text-muted mb-4">{{ form.description }}</p>

              <form @submit.prevent="submit">
                <template v-for="(rowFields, rowIndex) in fieldsByRow" :key="rowIndex">
                  <div class="row">
                    <div
                      v-for="field in rowFields"
                      :key="field.id"
                      class="mb-3"
                      :class="getFieldWidthClass(field)"
                    >
                      <label class="form-label">
                        {{ field.label }}
                        <span v-if="field.is_required" class="text-danger">*</span>
                      </label>

                      <input
                        v-if="field.type === 'text'"
                        type="text"
                        v-model="formData[field.name]"
                        class="form-control"
                        :placeholder="field.placeholder"
                        :required="field.is_required"
                      />

                      <input
                        v-else-if="field.type === 'email'"
                        type="email"
                        v-model="formData[field.name]"
                        class="form-control"
                        :placeholder="field.placeholder"
                        :required="field.is_required"
                      />

                      <input
                        v-else-if="field.type === 'phone'"
                        type="tel"
                        v-model="formData[field.name]"
                        class="form-control"
                        :placeholder="field.placeholder"
                        :required="field.is_required"
                      />

                      <textarea
                        v-else-if="field.type === 'textarea'"
                        v-model="formData[field.name]"
                        class="form-control"
                        :placeholder="field.placeholder"
                        :required="field.is_required"
                        rows="4"
                      ></textarea>

                      <input
                        v-else-if="field.type === 'date'"
                        type="date"
                        v-model="formData[field.name]"
                        class="form-control"
                        :required="field.is_required"
                        :min="getMinDate(field)"
                        :max="getMaxDate(field)"
                      />

                      <input
                        v-else-if="field.type === 'file'"
                        type="file"
                        @change="handleFileChange($event, field.name)"
                        class="form-control"
                        :required="field.is_required"
                        :accept="getFileAccept(field)"
                        :max="field.max_file_size * 1024 * 1024"
                      />

                      <input
                        v-else-if="field.type === 'image'"
                        type="file"
                        @change="handleFileChange($event, field.name)"
                        class="form-control"
                        :required="field.is_required"
                        :accept="'.jpg,.jpeg,.png,.webp'"
                        :max="(field.max_file_size || 5) * 1024 * 1024"
                        capture="environment"
                      />

                      <select
                        v-else-if="field.type === 'select'"
                        v-model="formData[field.name]"
                        class="form-select"
                        :required="field.is_required && !field.multiple"
                        :multiple="field.multiple"
                      >
                        <option v-if="!field.multiple" value="">{{ field.placeholder || 'Seleccionar...' }}</option>
                        <option v-for="(opt, oi) in field.options" :key="oi" :value="typeof opt === 'object' ? opt.value : opt">
                          {{ typeof opt === 'object' ? opt.label : opt }}
                        </option>
                      </select>

                      <div v-else-if="field.type === 'checkbox'" class="form-check-group">
                        <div v-if="field.options && field.options.length > 0">
                          <div v-for="(opt, oi) in field.options" :key="oi" class="form-check">
                            <input
                              type="checkbox"
                              :value="typeof opt === 'object' ? opt.value : opt"
                              v-model="formData[field.name]"
                              class="form-check-input"
                              :id="'field-' + field.id + '-' + oi"
                            />
                            <label class="form-check-label" :for="'field-' + field.id + '-' + oi">
                              {{ typeof opt === 'object' ? opt.label : opt }}
                            </label>
                          </div>
                        </div>
                        <div v-else class="form-check">
                          <input
                            type="checkbox"
                            v-model="formData[field.name]"
                            class="form-check-input"
                            :id="'field-' + field.id"
                            :required="field.is_required"
                          />
                          <label class="form-check-label" :for="'field-' + field.id">
                            {{ field.placeholder || field.label }}
                          </label>
                        </div>
                      </div>
                    </div>
                  </div>
                </template>

                <button type="submit" class="btn btn-primary" :disabled="sending">
                  <span v-if="sending">Enviando...</span>
                  <span v-else>Enviar</span>
                </button>
              </form>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div v-if="form.show_phone || form.show_email" class="card">
            <div class="card-body">
              <h5 class="card-title">Informacion de contacto</h5>
              <ul class="list-unstyled mb-0">
                <li v-if="form.show_phone && business.phone" class="mb-2">
                  <i class="bi bi-telephone me-2 text-primary"></i>{{ business.phone }}
                </li>
                <li v-if="form.show_email && business.email" class="mb-2">
                  <i class="bi bi-envelope me-2 text-primary"></i>{{ business.email }}
                </li>
              </ul>
            </div>
          </div>

          <div v-if="form.show_phone && business.phone" class="mt-3">
            <a :href="`tel:${business.phone}`" class="btn btn-outline-primary w-100 mb-2">
              <i class="bi bi-telephone me-2"></i>Llamar
            </a>
          </div>
        </div>
      </div>

      <div class="mt-4">
        <Link :href="`/b/${business.slug}`" class="text-decoration-none">
          <i class="bi bi-arrow-left me-1"></i>Volver al inicio
        </Link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, reactive } from 'vue'
import { Link, usePage, router } from '@inertiajs/vue3'

const page = usePage()
const business = computed(() => page.props.business)
const form = computed(() => page.props.form)
const fields = computed(() => page.props.fields)

const formData = reactive({})

fields.value.forEach(field => {
  if (field.type === 'checkbox') {
    if (field.options && field.options.length > 0) {
      formData[field.name] = Array.isArray(field.default_value) ? field.default_value : (field.default_value ? [field.default_value] : [])
    } else {
      formData[field.name] = field.default_value === true || field.default_value === 'true' || false
    }
  } else if (field.type === 'radio') {
    formData[field.name] = field.default_value || ''
  } else if (field.type === 'select') {
    if (field.multiple) {
      formData[field.name] = Array.isArray(field.default_value) ? field.default_value : (field.default_value ? [field.default_value] : [])
    } else {
      formData[field.name] = field.default_value || ''
    }
  } else {
    formData[field.name] = field.value || ''
  }
})

const sending = computed(() => false)

const submit = () => {
  router.post(`/b/${business.value.slug}/form/${form.value.shortcode}`, formData, {
    onSuccess: () => {
      fields.value.forEach(field => {
        if (field.type === 'checkbox') {
          if (field.options && field.options.length > 0) {
            formData[field.name] = Array.isArray(field.default_value) ? field.default_value : (field.default_value ? [field.default_value] : [])
          } else {
            formData[field.name] = field.default_value === true || field.default_value === 'true' || false
          }
        } else if (field.type === 'radio') {
          formData[field.name] = field.default_value || ''
        } else if (field.type === 'select') {
          if (field.multiple) {
            formData[field.name] = Array.isArray(field.default_value) ? field.default_value : (field.default_value ? [field.default_value] : [])
          } else {
            formData[field.name] = field.default_value || ''
          }
        } else {
          formData[field.name] = field.value || ''
        }
      })
    }
  })
}

const fieldsByRow = computed(() => {
  const rows = {}
  fields.value.forEach(field => {
    const row = field.row || 1
    if (!rows[row]) {
      rows[row] = []
    }
    rows[row].push(field)
  })
  return Object.values(rows)
})

const getMinDate = (field) => {
  if (field.min_date_type === 'now') {
    return new Date().toISOString().split('T')[0]
  }
  return field.min_date || ''
}

const getMaxDate = (field) => {
  if (field.max_date_type === 'now') {
    return new Date().toISOString().split('T')[0]
  }
  return field.max_date || ''
}

const getFieldWidthClass = (field) => {
  const width = field.width || 'full'
  const widthMap = {
    full: 'col-12',
    half: 'col-12 col-md-6',
    third: 'col-12 col-md-4',
    quarter: 'col-12 col-md-3',
  }
  return widthMap[width] || 'col-12'
}

const getFileAccept = (field) => {
  const types = field.file_types || ['pdf', 'xlsx', 'docx']
  const mimeTypes = {
    pdf: '.pdf',
    xlsx: '.xlsx',
    docx: '.docx',
  }
  return types.map(t => mimeTypes[t]).join(',')
}

const handleFileChange = (event, fieldName) => {
  const file = event.target.files[0]
  if (file) {
    formData[fieldName] = file.name
  }
}
</script>
