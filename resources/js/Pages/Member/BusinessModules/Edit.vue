<template>
  <MemberLayout>
    <Head :title="`Modulos: ${business.name}`" />

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
      <div>
        <h1 class="h4 mb-1">{{ business.name }}</h1>
        <p class="text-muted mb-0">Gestiona los modulos disponibles para este negocio.</p>
      </div>
      <div class="d-flex gap-2">
        <Link :href="`/member/businesses/${business.id}/minisite-theme`" class="btn btn-outline-primary btn-sm">
          <i class="bi bi-palette me-1"></i>Theme
        </Link>
        <Link href="/member/business-modules" class="btn btn-outline-secondary btn-sm">
          Volver
        </Link>
      </div>
    </div>

    <div v-if="business.plan_name" class="alert alert-info mb-4">
      <strong>Tu plan:</strong> {{ business.plan_name }}
      <br><small>Solo puedes activar modulos incluidos en tu plan.</small>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div v-if="$page.props.flash?.success" class="alert alert-success alert-dismissible fade show" role="alert">
          {{ $page.props.flash.success }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>

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
              <tr v-for="(module, index) in localModules" :key="module.id">
                <td class="fw-semibold">
                  {{ module.module_name }}
                  <span v-if="!module.allowed_by_plan" class="badge bg-secondary ms-2">No disponible en plan</span>
                </td>
                <td>
                  <span v-if="module.is_enabled" class="badge text-bg-success">Habilitado</span>
                  <span v-else class="badge text-bg-secondary">Deshabilitado</span>
                </td>
                <td class="text-end">
                  <button
                    v-if="module.allowed_by_plan"
                    class="btn btn-sm"
                    :class="module.is_enabled ? 'btn-outline-danger' : 'btn-outline-success'"
                    type="button"
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
      <div class="card-footer d-flex justify-content-end gap-2">
        <Link href="/member/business-modules" class="btn btn-outline-secondary">Cancelar</Link>
        <button type="button" class="btn btn-primary" @click="saveModules" :disabled="!hasChanges || saving">
          <span v-if="saving">Guardando...</span>
          <span v-else>Guardar cambios</span>
        </button>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'

const props = defineProps({
  business: {
    type: Object,
    required: true,
  },
})

const localModules = ref([...props.business.modules])
const saving = ref(false)

const hasChanges = computed(() => {
  return localModules.value.some((module, index) => {
    const original = props.business.modules[index]
    return original && module.is_enabled !== original.is_enabled
  })
})

const toggleModule = (index) => {
  localModules.value[index].is_enabled = !localModules.value[index].is_enabled
}

const saveModules = () => {
  saving.value = true
  router.put(
    `/member/businesses/${props.business.id}/modules`,
    {
      modules: localModules.value.map((m) => ({
        id: m.id,
        is_enabled: m.is_enabled,
      })),
    },
    {
      preserveScroll: true,
      onFinish: () => {
        saving.value = false
      },
    }
  )
}
</script>
