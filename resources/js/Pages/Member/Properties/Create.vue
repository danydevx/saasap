<template>
  <MemberLayout>
    <Head :title="`Nueva Propiedad - ${business?.name || ''}`" />

    <PageHeader
      title="Nueva Propiedad"
      :breadcrumbs="breadcrumbs"
      :backHref="`/member/businesses/${business?.id}/properties`"
    />

    <div v-if="!selectedTypeId" class="card border-0 shadow-sm mb-4">
      <div class="card-body">
        <h5 class="card-title mb-4">Selecciona el tipo de propiedad</h5>
        <div class="row g-3">
          <div class="col-md-4" v-for="type in propertyTypes" :key="type.id">
            <div
              class="card h-100 cursor-pointer"
              :class="{ 'border-primary': selectedTypeId === type.id }"
              @click="selectType(type.id)"
            >
              <div class="card-body text-center">
                <i :class="type.icon || 'bi bi-building'" style="font-size: 2rem;"></i>
                <h6 class="mt-2 mb-0">{{ type.name }}</h6>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-else class="card border-0 shadow-sm">
      <div class="card-body">
        <form @submit.prevent="submit">
          <div class="mb-4">
            <button type="button" class="btn btn-link p-0" @click="changeType">
              <i class="bi bi-arrow-left me-1"></i>Cambiar tipo de propiedad
            </button>
          </div>

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
              {{ sending ? 'Creando...' : 'Crear Propiedad' }}
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
const propertyTypes = computed(() => page.props.propertyTypes || [])
const formSchema = computed(() => page.props.formSchema)
const selectedTypeId = computed(() => page.props.selectedTypeId)
const limitInfo = computed(() => page.props.limitInfo)
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
        { label: 'Nueva Propiedad', active: true },
      ]
    }
  }
  return [
    { label: 'Mis Negocios', href: '/member/business-modules' },
    { label: 'Nueva Propiedad', active: true },
  ]
})

const sending = ref(false)
const mainImageFile = ref(null)

const form = reactive({
  property_type_id: selectedTypeId.value,
})

const selectType = (typeId) => {
  window.location.href = `/member/businesses/${business.value.id}/properties/create?type=${typeId}`
}

const changeType = () => {
  window.location.href = `/member/businesses/${business.value.id}/properties/create`
}

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

  router.post(`/member/businesses/${business.value.id}/properties`, formData, {
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

<style scoped>
.cursor-pointer {
  cursor: pointer;
}
</style>
