<template>
  <AdminLayout>
    <Head title="Gestionar Modulos" />

    <div class="container-fluid py-4">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 mb-0">Modulos: {{ business?.name || 'Cargando...' }}</h1>
        <Link href="/admin/business-modules" class="btn btn-outline-secondary">
          Volver
        </Link>
      </div>

      <div v-if="business?.user?.plan_name" class="alert alert-info mb-4">
        <strong>Plan del usuario:</strong> {{ business.user.plan_name }}
        <br><small>Solo puedes activar modulos incluidos en el plan.</small>
      </div>

      <div v-if="$page.props.flash?.success" class="alert alert-success alert-dismissible fade show" role="alert">
        {{ $page.props.flash.success }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>

      <form v-if="business?.modules?.length" @submit.prevent="submit">
        <div class="card border-0 shadow-sm mb-4">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th scope="col">Modulo</th>
                  <th scope="col">Estado</th>
                  <th scope="col" class="text-end">Accion</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(module, index) in form.modules" :key="module.id">
                  <td class="fw-semibold">
                    {{ module.module_name }}
                    <span v-if="!module.allowed_by_plan" class="badge bg-secondary ms-2">No disponible en plan</span>
                  </td>
                  <td>
                    <span :class="module.is_enabled ? 'badge bg-success' : 'badge bg-secondary'">
                      {{ module.is_enabled ? 'Habilitado' : 'Deshabilitado' }}
                    </span>
                  </td>
                  <td class="text-end">
                    <button
                      v-if="module.allowed_by_plan"
                      type="button"
                      class="btn btn-sm"
                      :class="module.is_enabled ? 'btn-outline-danger' : 'btn-outline-success'"
                      @click="toggleModule(index)"
                    >
                      {{ module.is_enabled ? 'Deshabilitar' : 'Habilitar' }}
                    </button>
                    <span v-else class="text-muted small">
                      Requiere upgrade de plan
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div class="d-flex justify-content-end gap-2">
          <Link href="/admin/business-modules" class="btn btn-outline-secondary">
            Cancelar
          </Link>
          <button type="submit" class="btn btn-primary" :disabled="form.processing">
            <span v-if="form.processing">Guardando...</span>
            <span v-else>Guardar Cambios</span>
          </button>
        </div>
      </form>

      <div v-else class="mt-4">
        <p class="text-muted">No hay modulos disponibles.</p>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed, reactive } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const page = usePage()
const business = computed(() => page.props.business)

const form = reactive({
  modules: business.value?.modules?.map(m => ({
    id: m.id,
    is_enabled: m.is_enabled,
  })) || [],
  processing: false,
})

const toggleModule = (index) => {
  form.modules[index].is_enabled = !form.modules[index].is_enabled
}

const submit = () => {
  form.processing = true
  router.put(`/admin/business-modules/${business.value.id}`, form, {
    onFinish: () => {
      form.processing = false
    },
  })
}
</script>
