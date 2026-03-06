<template>
  <AdminLayout>
    <Head title="Editar rol" />

    <PageHeader :title="'Editar rol'" :breadcrumbs="breadcrumbs" backHref="/admin/roles" />

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <form class="row g-3" @submit.prevent="submit">
          <div class="col-12">
            <FieldText
              id="role-name"
              label="Nombre del rol"
              placeholder="Ej: Administrador"
              v-model="form.name"
              :formError="form.errors.name"
              required
            />
          </div>

          <div class="col-12">
            <div class="form-check">
              <input
                id="role-blocked"
                v-model="form.blocked"
                class="form-check-input"
                type="checkbox"
              />
              <label class="form-check-label" for="role-blocked">
                Bloquear asignacion a usuarios
              </label>
            </div>
          </div>

          <div class="col-12">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <div class="fw-semibold">Permisos</div>
              <span class="text-muted small">{{ permissions.length }} disponibles</span>
            </div>

            <div v-if="permissions.length === 0" class="alert alert-light mb-0">
              No hay permisos registrados.
            </div>
            <FieldCheckboxes
              v-else
              v-model="form.permissions"
              :items="permissions"
              label="Selecciona los permisos"
              idPrefix="perm_"
              :formError="permissionsError"
            />
          </div>

          <div class="col-12 d-flex gap-2">
            <button type="submit" class="btn btn-primary" :disabled="form.processing">
              {{ form.processing ? 'Actualizando...' : 'Actualizar' }}
            </button>
            <Link href="/admin/roles" class="btn btn-outline-secondary">Cancelar</Link>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldCheckboxes from '@/Components/Fields/FieldCheckboxes.vue'

const props = defineProps({
  role: {
    type: Object,
    required: true,
  },
  permissions: {
    type: Array,
    default: () => [],
  },
  rolePermissions: {
    type: Array,
    default: () => [],
  },
})

const form = useForm({
  name: props.role.name,
  permissions: [...props.rolePermissions],
  blocked: props.role.blocked ?? false,
})

const breadcrumbs = [
  { label: 'Roles', href: '/admin/roles' },
  { label: 'Editar', active: true },
]

const permissionsError = computed(() => {
  return form.errors.permissions || form.errors['permissions.0'] || ''
})

const submit = () => {
  form.put(`/admin/roles/${props.role.id}`)
}
</script>
