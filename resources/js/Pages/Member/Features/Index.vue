<template>
  <MemberLayout>
    <Head :title="`Caracteristicas - ${business?.name || ''}`" />

    <PageHeader
      title="Caracteristicas"
      :breadcrumbs="breadcrumbs"
      :backHref="'/member/business-modules'"
    >
      <template #actions>
        <button class="btn btn-outline-primary" @click="openImportModal">
          <i class="bi bi-download me-1"></i>Importar
        </button>
        <button class="btn btn-primary" @click="openCreateModal">
          <i class="bi bi-plus me-1"></i>Nuevo
        </button>
      </template>
    </PageHeader>

    <div class="container-fluid py-4">
      <div v-if="businessFeatures.length === 0 && !showImportModal" class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
          <i class="bi bi-check-circle display-1 text-muted"></i>
          <p class="text-muted mt-3">No hay caracteristicas configuradas.</p>
          <p class="text-muted small">Puedes importar caracteristicas predefinidas o crear las tuyas propias.</p>
          <div class="d-flex gap-2 justify-content-center">
            <button class="btn btn-outline-primary" @click="showImportModal = true">
              <i class="bi bi-download me-1"></i>Importar predefinidas
            </button>
            <button class="btn btn-primary" @click="openCreateModal">
              <i class="bi bi-plus me-1"></i>Crear caracteristica
            </button>
          </div>
        </div>
      </div>

      <div v-else>
        <div v-if="locations.length > 0" class="alert alert-info d-flex align-items-center mb-4" role="alert">
          <i class="bi bi-info-circle me-2"></i>
          <div>
            Asigna caracteristicas a ubicaciones especificas o al negocio en general.
          </div>
        </div>

        <div class="row g-4">
          <div v-for="bf in businessFeatures" :key="bf.id" class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                  <div class="d-flex align-items-center gap-2">
                    <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                      <i :class="bf.feature_icon || 'bi bi-check'"></i>
                    </div>
                    <div>
                      <strong>{{ bf.feature_title }}</strong>
                      <p v-if="bf.location_id && bf.location_name" class="text-muted small mb-0">{{ bf.location_name }}</p>
                    </div>
                  </div>
                  <div class="dropdown">
                    <button class="btn btn-sm btn-link text-muted" data-bs-toggle="dropdown">
                      <i class="bi bi-three-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                      <li>
                        <button class="dropdown-item" @click="openEditModal(bf)">
                          <i class="bi bi-pencil me-2"></i>Editar
                        </button>
                      </li>
                      <li>
                        <button class="dropdown-item" @click="removeAssignment(bf)">
                          <i class="bi bi-unlink me-2"></i>Desvincular
                        </button>
                      </li>
                      <li><hr class="dropdown-divider"></li>
                      <li>
                        <button class="dropdown-item text-danger" @click="deleteFeature(bf)">
                          <i class="bi bi-trash me-2"></i>Eliminar
                        </button>
                      </li>
                    </ul>
                  </div>
                </div>
                <p v-if="bf.feature_description" class="text-muted small mb-0">{{ bf.feature_description }}</p>
                <div class="mt-2">
                  <span v-if="bf.is_active" class="badge bg-success">Activo</span>
                  <span v-else class="badge bg-secondary">Inactivo</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="text-center mt-4">
          <button class="btn btn-outline-primary" @click="openImportModal" v-if="availableFeaturesCount > 0">
            <i class="bi bi-download me-1"></i>Importar mas ({{ availableFeaturesCount }} disponibles)
          </button>
        </div>
      </div>
    </div>

    <div ref="createModalElement" class="modal fade" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Nueva caracteristica</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form @submit.prevent="submitCreate">
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label">Titulo</label>
                <input v-model="createForm.title" type="text" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Descripcion</label>
                <textarea v-model="createForm.description" class="form-control" rows="2"></textarea>
              </div>
              <div class="mb-3">
                <label class="form-label">Icono (Bootstrap Icons)</label>
                <div class="input-group">
                  <span class="input-group-text"><i :class="createForm.icon || 'bi bi-check'"></i></span>
                  <input v-model="createForm.icon" type="text" class="form-control" placeholder="bi bi-check">
                </div>
              </div>
              <div class="mb-3" v-if="locations.length > 0">
                <label class="form-label">Ubicacion (opcional)</label>
                <select v-model="createForm.location_id" class="form-select">
                  <option :value="null">Todas las ubicaciones</option>
                  <option v-for="loc in locations" :key="loc.id" :value="loc.id">
                    {{ loc.name }}
                  </option>
                </select>
              </div>
              <div class="form-check form-switch">
                <input v-model="createForm.is_active" class="form-check-input" type="checkbox" id="createIsActive">
                <label class="form-check-label" for="createIsActive">Activo</label>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-primary" :disabled="createSending">
                <span v-if="createSending">Guardando...</span>
                <span v-else>Crear</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div ref="editModalElement" class="modal fade" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Editar caracteristica</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form @submit.prevent="submitEdit">
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label">Titulo</label>
                <input v-model="editForm.title" type="text" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Descripcion</label>
                <textarea v-model="editForm.description" class="form-control" rows="2"></textarea>
              </div>
              <div class="mb-3">
                <label class="form-label">Icono (Bootstrap Icons)</label>
                <div class="input-group">
                  <span class="input-group-text"><i :class="editForm.icon || 'bi bi-check'"></i></span>
                  <input v-model="editForm.icon" type="text" class="form-control" placeholder="bi bi-check">
                </div>
              </div>
              <div class="mb-3" v-if="locations.length > 0">
                <label class="form-label">Ubicacion</label>
                <select v-model="editForm.location_id" class="form-select">
                  <option :value="null">Todas las ubicaciones</option>
                  <option v-for="loc in locations" :key="loc.id" :value="loc.id">
                    {{ loc.name }}
                  </option>
                </select>
              </div>
              <div class="form-check form-switch">
                <input v-model="editForm.is_active" class="form-check-input" type="checkbox" id="editIsActive">
                <label class="form-check-label" for="editIsActive">Activo</label>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-primary" :disabled="editSending">
                <span v-if="editSending">Guardando...</span>
                <span v-else>Actualizar</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div ref="importModalElement" class="modal fade" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Importar caracteristicas predefinidas</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <p class="text-muted">Selecciona las caracteristicas que deseas importar de nuestro catalogo.</p>
            <div v-for="category in groupedAvailableFeatures" :key="category.id" class="mb-4">
              <h6><i :class="category.icon"></i> {{ category.name }}</h6>
              <div class="row g-2">
                <div v-for="feature in category.features" :key="feature.id" class="col-md-6">
                  <div class="form-check">
                    <input
                      class="form-check-input"
                      type="checkbox"
                      :value="feature.id"
                      v-model="importForm.feature_ids"
                      :id="`import-${feature.id}`"
                    >
                    <label class="form-check-label" :for="`import-${feature.id}`">
                      <i :class="feature.icon"></i> {{ feature.title }}
                    </label>
                  </div>
                </div>
              </div>
            </div>
            <div v-if="availableFeatures.length === 0" class="text-center py-4">
              <p class="text-muted mb-0">No hay caracteristicas disponibles para importar.</p>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-primary" @click="submitImport" :disabled="importForm.feature_ids.length === 0 || importSending">
              <span v-if="importSending">Importando...</span>
              <span v-else>Importar ({{ importForm.feature_ids.length }})</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import { Modal } from 'bootstrap'

const props = defineProps({
  business: Object,
  businessFeatures: Array,
  availableFeatures: Array,
  locations: { type: Array, default: () => [] },
})

const business = computed(() => props.business)

const breadcrumbs = computed(() => [
  { label: business.value?.name, href: '/member/business-modules' },
  { label: 'Caracteristicas', active: true },
])

const createModalElement = ref(null)
const editModalElement = ref(null)
const importModalElement = ref(null)
let createModal = null
let editModal = null
let importModal = null

let createSending = ref(false)
let editSending = ref(false)
let importSending = ref(false)
let showImportModal = ref(false)

const createForm = ref({
  title: '',
  description: '',
  icon: 'bi bi-check',
  location_id: null,
  is_active: true,
})

const editForm = ref({
  id: null,
  title: '',
  description: '',
  icon: 'bi bi-check',
  location_id: null,
  is_active: true,
  feature_id: null,
})

const importForm = ref({
  feature_ids: [],
})

const groupedAvailableFeatures = computed(() => {
  const grouped = {}
  props.availableFeatures.forEach(f => {
    const catId = f.category_id || 'uncategorized'
    const catName = f.category?.name || 'Sin categoria'
    const catIcon = f.category?.icon || 'bi bi-folder'
    if (!grouped[catId]) {
      grouped[catId] = { id: catId, name: catName, icon: catIcon, features: [] }
    }
    grouped[catId].features.push(f)
  })
  return Object.values(grouped)
})

const availableFeaturesCount = computed(() => props.availableFeatures.length)

const openCreateModal = () => {
  createForm.value = {
    title: '',
    description: '',
    icon: 'bi bi-check',
    location_id: null,
    is_active: true,
  }
  nextTick(() => {
    createModal.show()
  })
}

const openImportModal = () => {
  importForm.value.feature_ids = []
  nextTick(() => {
    importModal.show()
  })
}

const openEditModal = (bf) => {
  editForm.value = {
    id: bf.id,
    title: bf.feature_title,
    description: bf.feature_description || '',
    icon: bf.feature_icon || 'bi bi-check',
    location_id: bf.location_id,
    is_active: bf.is_active,
    feature_id: bf.feature_id,
  }
  nextTick(() => {
    editModal.show()
  })
}

const submitCreate = () => {
  createSending.value = true
  router.post(`/member/businesses/${business.value.id}/features`, createForm.value, {
    onFinish: () => {
      createSending.value = false
      createModal.hide()
    },
  })
}

const submitEdit = () => {
  editSending.value = true
  router.put(`/member/businesses/${business.value.id}/features/${editForm.value.id}`, {
    title: editForm.value.title,
    description: editForm.value.description,
    icon: editForm.value.icon,
    location_id: editForm.value.location_id,
    is_active: editForm.value.is_active,
  }, {
    onFinish: () => {
      editSending.value = false
      editModal.hide()
    },
  })
}

const submitImport = () => {
  if (importForm.value.feature_ids.length === 0) return
  importSending.value = true
  router.post(`/member/businesses/${business.value.id}/features/import`, {
    feature_ids: importForm.value.feature_ids,
  }, {
    onFinish: () => {
      importSending.value = false
      importForm.value.feature_ids = []
      importModal.hide()
    },
  })
}

const removeAssignment = (bf) => {
  router.delete(`/member/businesses/${business.value.id}/feature-assignments/${bf.id}`, {
    preserveScroll: true,
  })
}

const deleteFeature = (bf) => {
  if (!confirm('Estas seguro de eliminar esta caracteristica?')) return
  router.delete(`/member/businesses/${business.value.id}/features/${bf.id}`, {
    preserveScroll: true,
  })
}

onMounted(() => {
  createModal = new Modal(createModalElement.value)
  editModal = new Modal(editModalElement.value)
  importModal = new Modal(importModalElement.value)
})
</script>
