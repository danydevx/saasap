<template>
  <AdminLayout>
    <Head title="Caracteristicas" />

    <div class="container-fluid py-4">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h1 class="h4 mb-1">Caracteristicas</h1>
          <p class="text-muted mb-0">Caracteristicas predefinidas para negocios</p>
        </div>
        <button class="btn btn-primary" @click="openCreateModal">
          <i class="bi bi-plus me-1"></i>Nueva caracteristica
        </button>
      </div>

      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <div v-if="features.length === 0" class="text-center py-5">
            <i class="bi bi-check-circle display-1 text-muted"></i>
            <p class="text-muted mt-3">No hay caracteristicas creadas aun.</p>
            <button class="btn btn-primary" @click="openCreateModal">Crear primera caracteristica</button>
          </div>

          <div v-else class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Orden</th>
                  <th>Nombre</th>
                  <th>Categoria</th>
                  <th>Icono</th>
                  <th>Negocios</th>
                  <th>Activo</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="feature in features" :key="feature.id">
                  <td>{{ feature.sort_order }}</td>
                  <td>
                    <strong>{{ feature.title }}</strong>
                    <p v-if="feature.description" class="text-muted small mb-0">{{ feature.description.substring(0, 60) }}...</p>
                  </td>
                  <td>
                    <span v-if="feature.category">
                      <i :class="feature.category.icon"></i> {{ feature.category.name }}
                    </span>
                    <span v-else class="text-muted">—</span>
                  </td>
                  <td><i :class="feature.icon || 'bi bi-check'"></i></td>
                  <td>{{ feature.businesses_count || 0 }}</td>
                  <td>
                    <span v-if="feature.is_active" class="badge bg-success">Activo</span>
                    <span v-else class="badge bg-secondary">Inactivo</span>
                  </td>
                  <td>
                    <button class="btn btn-sm btn-outline-primary me-1" @click="openEditModal(feature)">
                      <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" @click="deleteFeature(feature)" :disabled="(feature.businesses_count || 0) > 0">
                      <i class="bi bi-trash"></i>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div ref="modalElement" class="modal fade" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ editingFeature ? 'Editar caracteristica' : 'Nueva caracteristica' }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form @submit.prevent="submitFeature">
            <div class="modal-body">
              <div class="row g-3">
                <div class="col-md-8">
                  <label class="form-label">Titulo</label>
                  <input v-model="form.title" type="text" class="form-control" required>
                </div>
                <div class="col-md-4">
                  <label class="form-label">Categoria</label>
                  <select v-model="form.category_id" class="form-select" required>
                    <option value="">Seleccionar...</option>
                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                      {{ cat.name }}
                    </option>
                  </select>
                </div>
                <div class="col-12">
                  <label class="form-label">Descripcion</label>
                  <textarea v-model="form.description" class="form-control" rows="2"></textarea>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Icono (Bootstrap Icons)</label>
                  <div class="input-group">
                    <span class="input-group-text"><i :class="form.icon || 'bi bi-check'"></i></span>
                    <input v-model="form.icon" type="text" class="form-control" placeholder="bi bi-check">
                  </div>
                </div>
                <div class="col-md-3">
                  <label class="form-label">Orden</label>
                  <input v-model.number="form.sort_order" type="number" min="0" class="form-control">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                  <div class="form-check form-switch">
                    <input v-model="form.is_active" class="form-check-input" type="checkbox" id="isActive">
                    <label class="form-check-label" for="isActive">Activo</label>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-primary" :disabled="sending">
                <span v-if="sending">Guardando...</span>
                <span v-else>{{ editingFeature ? 'Actualizar' : 'Crear' }}</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Head, useForm, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { ref, onMounted, nextTick } from 'vue'
import { Modal } from 'bootstrap'

const props = defineProps({
  features: Array,
  categories: Array,
})

const modalElement = ref(null)
let featureModal = null
let editingFeature = ref(null)
let sending = false

const form = useForm({
  title: '',
  description: '',
  category_id: '',
  icon: 'bi bi-check',
  sort_order: 0,
  is_active: true,
})

const openCreateModal = () => {
  editingFeature.value = null
  form.reset()
  form.icon = 'bi bi-check'
  form.sort_order = 0
  form.is_active = true
  nextTick(() => {
    featureModal.show()
  })
}

const openEditModal = (feature) => {
  editingFeature.value = feature
  form.title = feature.title
  form.description = feature.description || ''
  form.category_id = feature.category_id
  form.icon = feature.icon || 'bi bi-check'
  form.sort_order = feature.sort_order || 0
  form.is_active = feature.is_active
  nextTick(() => {
    featureModal.show()
  })
}

const submitFeature = () => {
  sending = true
  if (editingFeature.value) {
    form.put(`/admin/features/${editingFeature.value.id}`, {
      onFinish: () => {
        sending = false
        featureModal.hide()
      },
    })
  } else {
    form.post('/admin/features', {
      onFinish: () => {
        sending = false
        featureModal.hide()
      },
    })
  }
}

const deleteFeature = (feature) => {
  if (!confirm(`Estas seguro de eliminar "${feature.title}"?`)) {
    return
  }
  if ((feature.businesses_count || 0) > 0) {
    alert('No puedes eliminar una caracteristica que esta siendo usada por negocios.')
    return
  }
  router.delete(`/admin/features/${feature.id}`, {
    preserveScroll: true,
  })
}

onMounted(() => {
  featureModal = new Modal(modalElement.value)
})
</script>
