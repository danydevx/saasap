<template>
  <AdminLayout>
    <Head title="Editar Modulo" />

    <PageHeader title="Editar Modulo" :breadcrumbs="breadcrumbs" backHref="/admin/business-module-definitions" />

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <form @submit.prevent="submit">
          <div class="row g-3">
            <div class="col-12 col-md-6">
              <FieldText
                id="def-key"
                label="Key"
                v-model="form.key"
                :formError="form.errors.key"
                required
              />
            </div>

            <div class="col-12 col-md-6">
              <FieldText
                id="def-name"
                label="Nombre"
                v-model="form.name"
                :formError="form.errors.name"
                required
              />
            </div>

            <div class="col-12">
              <FieldText
                id="def-description"
                label="Descripcion"
                v-model="form.description"
                :formError="form.errors.description"
              />
            </div>

            <div class="col-12 col-md-6">
              <FieldText
                id="def-icon"
                label="Icono (Bootstrap Icons)"
                v-model="form.icon"
                :formError="form.errors.icon"
              />
              <small class="text-muted">
                <a href="https://icons.getbootstrap.com/" target="_blank">Ver iconos disponibles</a>
              </small>
            </div>

            <div class="col-12 col-md-6">
              <FieldNumber
                id="def-sort"
                label="Orden"
                v-model="form.sort_order"
                :formError="form.errors.sort_order"
                :min="0"
              />
            </div>

            <div class="col-12 col-md-6">
              <FieldSwitch
                id="def-settings"
                label="Tiene configuraciones propias"
                v-model="form.has_settings"
              />
            </div>

            <div class="col-12 col-md-6">
              <FieldSwitch
                id="def-active"
                label="Modulo activo"
                v-model="form.is_active"
              />
            </div>
          </div>

          <div class="col-12 d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary" :disabled="form.processing">
              {{ form.processing ? 'Actualizando...' : 'Actualizar Modulo' }}
            </button>
            <Link href="/admin/business-module-definitions" class="btn btn-outline-secondary">Cancelar</Link>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldNumber from '@/Components/Fields/FieldNumber.vue'
import FieldSwitch from '@/Components/Fields/FieldSwitch.vue'

const props = defineProps({
  definition: {
    type: Object,
    required: true,
  },
})

const form = useForm({
  key: props.definition.key,
  name: props.definition.name,
  description: props.definition.description || '',
  icon: props.definition.icon || '',
  sort_order: props.definition.sort_order ?? 0,
  has_settings: !!props.definition.has_settings,
  is_active: !!props.definition.is_active,
})

const breadcrumbs = [
  { label: 'Modulos de Negocio', href: '/admin/business-module-definitions' },
  { label: 'Editar', active: true },
]

const submit = () => {
  form.put(`/admin/business-module-definitions/${props.definition.id}`)
}
</script>
