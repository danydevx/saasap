<template>
  <MemberLayout>
    <Head :title="`Editar Propiedad - ${business?.name || ''}`" />

    <PageHeader
      title="Editar Propiedad"
      :breadcrumbs="breadcrumbs"
      :backHref="`/member/businesses/${business?.id}/properties`"
    />

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <form @submit.prevent="submit">
          <div v-if="formSchema" class="row g-3">
            <template v-for="section in formSchema.sections" :key="section.id">
              <div class="col-12">
                <h5 class="border-bottom pb-2 mb-3">{{ section.name }}</h5>
              </div>

              <template v-for="field in section.fields" :key="field.id">
                <div class="col-12" :class="getFieldColClass(field.field_type)">
                  <FieldText
                    v-if="field.field_type === 'text'"
                    :id="`field-${field.field_key}`"
                    :label="field.label"
                    v-model="form[field.field_key]"
                    :formError="errors[field.field_key]"
                    :placeholder="field.placeholder"
                    :helpText="field.help_text"
                    :required="field.is_required"
                  />

                  <FieldTextarea
                    v-else-if="field.field_type === 'textarea'"
                    :id="`field-${field.field_key}`"
                    :label="field.label"
                    v-model="form[field.field_key]"
                    :formError="errors[field.field_key]"
                    :placeholder="field.placeholder"
                    :helpText="field.help_text"
                    :required="field.is_required"
                    :rows="4"
                  />

                  <FieldNumber
                    v-else-if="field.field_type === 'number' || field.field_type === 'decimal'"
                    :id="`field-${field.field_key}`"
                    :label="field.label"
                    v-model="form[field.field_key]"
                    :formError="errors[field.field_key]"
                    :placeholder="field.placeholder"
                    :helpText="field.help_text"
                    :required="field.is_required"
                  />

                  <FieldPrice
                    v-else-if="field.field_type === 'price'"
                    :id="`field-${field.field_key}`"
                    :label="field.label"
                    v-model="form[field.field_key]"
                    :formError="errors[field.field_key]"
                    :placeholder="field.placeholder"
                    :helpText="field.help_text"
                    :required="field.is_required"
                    currencyLabel="Monto"
                  />

                  <FieldSelect
                    v-else-if="field.field_type === 'select'"
                    :id="`field-${field.field_key}`"
                    :label="field.label"
                    v-model="form[field.field_key]"
                    :formError="errors[field.field_key]"
                    :helpText="field.help_text"
                    :required="field.is_required"
                  >
                    <option value="">Selecciona una opción</option>
                    <option v-for="opt in field.options" :key="opt.value" :value="opt.value">
                      {{ opt.label }}
                    </option>
                  </FieldSelect>

                  <FieldRadio
                    v-else-if="field.field_type === 'radio'"
                    :id="`field-${field.field_key}`"
                    :label="field.label"
                    v-model="form[field.field_key]"
                    :formError="errors[field.field_key]"
                    :helpText="field.help_text"
                    :required="field.is_required"
                    :options="field.options"
                  />

                  <FieldCheckbox
                    v-else-if="field.field_type === 'checkbox'"
                    :id="`field-${field.field_key}`"
                    :label="field.label"
                    v-model="form[field.field_key]"
                    :formError="errors[field.field_key]"
                    :helpText="field.help_text"
                  />

                  <FieldDate
                    v-else-if="field.field_type === 'date'"
                    :id="`field-${field.field_key}`"
                    :label="field.label"
                    v-model="form[field.field_key]"
                    :formError="errors[field.field_key]"
                    :placeholder="field.placeholder"
                    :helpText="field.help_text"
                    :required="field.is_required"
                  />

                  <FieldImage
                    v-else-if="field.field_type === 'image'"
                    :id="`field-${field.field_key}`"
                    :label="field.label"
                    v-model="mainImageFile"
                    :helpText="field.help_text"
                    :required="field.is_required"
                    :maxFiles="1"
                    :maxSizeMb="5"
                    accept="image/jpeg,image/png,image/webp"
                    :initialUrl="property.main_image_url"
                  />

                  <FieldSwitch
                    v-else-if="field.field_type === 'boolean'"
                    :id="`field-${field.field_key}`"
                    :label="field.label"
                    v-model="form[field.field_key]"
                  />
                </div>
              </template>
            </template>
          </div>

          <div class="col-12 d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary" :disabled="sending">
              {{ sending ? 'Guardando...' : 'Guardar Cambios' }}
            </button>
            <Link :href="`/member/businesses/${business?.id}/properties`" class="btn btn-outline-secondary">
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
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldTextarea from '@/Components/Fields/FieldTextarea.vue'
import FieldNumber from '@/Components/Fields/FieldNumber.vue'
import FieldSelect from '@/Components/Fields/FieldSelect.vue'
import FieldRadio from '@/Components/Fields/FieldRadio.vue'
import FieldCheckbox from '@/Components/Fields/FieldCheckbox.vue'
import FieldDate from '@/Components/Fields/FieldDate.vue'
import FieldSwitch from '@/Components/Fields/FieldSwitch.vue'
import FieldImage from '@/Components/Fields/FieldImage.vue'
import FieldPrice from '@/Components/Fields/FieldPrice.vue'

const page = usePage()
const business = computed(() => page.props.business)
const property = computed(() => page.props.property)
const propertyType = computed(() => page.props.propertyType)
const formSchema = computed(() => page.props.formSchema)
const dynamicValues = computed(() => page.props.dynamicValues || {})
const errors = computed(() => page.props.errors || {})
const businessMenu = computed(() => page.props.businessMenu || [])

const breadcrumbs = computed(() => {
  const path = window.location.pathname
  const businessMatch = path.match(/^\/member\/businesses\/(\d+)/)
  if (businessMatch) {
    const businessId = parseInt(businessMatch[1])
    const biz = businessMenu.value.find(b => b.id === businessId)
    if (biz) {
      return [
        { label: 'Mis Negocios', href: '/member/business-modules' },
        { label: biz.name, href: `/member/businesses/${biz.id}/edit` },
        { label: 'Propiedades', href: `/member/businesses/${biz.id}/properties` },
        { label: 'Editar Propiedad', active: true },
      ]
    }
  }
  return [
    { label: 'Mis Negocios', href: '/member/business-modules' },
    { label: 'Propiedades', href: '/member/business-modules/properties' },
    { label: 'Editar Propiedad', active: true },
  ]
})

const sending = ref(false)
const mainImageFile = ref(null)

const form = reactive({
  property_type_id: property.value?.property_type_id,
  title: property.value?.title || '',
  description: property.value?.description || '',
  operation_type: property.value?.operation_type || '',
  price: property.value?.price || '',
  currency: property.value?.currency || 'MXN',
  price_period: property.value?.price_period || 'single',
  status: property.value?.status || 'draft',
  is_featured: property.value?.is_featured || false,
  is_public: property.value?.is_public || false,
  remove_main_image: false,
  ...dynamicValues.value,
})

const getFieldColClass = (fieldType) => {
  if (['textarea', 'image'].includes(fieldType)) {
    return 'col-12'
  }
  if (['select', 'radio', 'checkbox'].includes(fieldType)) {
    return 'col-12 col-md-6'
  }
  return 'col-12 col-md-6'
}

const submit = () => {
  sending.value = true
  const formData = new FormData()
  formData.append('_method', 'PUT')

  Object.keys(form).forEach(key => {
    const val = form[key]
    if (val !== null && val !== '') {
      if (typeof val === 'boolean') {
        formData.append(key, val ? '1' : '0')
      } else {
        formData.append(key, val)
      }
    }
  })

  if (mainImageFile.value instanceof File) {
    formData.append('main_image', mainImageFile.value)
  }

  if (form.remove_main_image) {
    formData.append('remove_main_image', '1')
  }

  router.post(`/member/businesses/${business.value.id}/properties/${property.value.id}`, formData, {
    preserveScroll: true,
    onError: () => {
      sending.value = false
    },
    onFinish: () => {
      sending.value = false
    },
  })
}
</script>
