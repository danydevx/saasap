<template>
  <MemberLayout>
    <Head :title="`Galeria - ${business.name}`" />

    <PageHeader
      title="Galeria"
      :breadcrumbs="breadcrumbs"
      :backHref="'/member/business-modules'"
    >
      <template #description>
        <p class="text-muted mb-0">Gestiona las imagenes de tu galeria. Arrastra para reordenar.</p>
      </template>
      <template #actions>
        <button class="btn btn-primary btn-sm" @click="openUploadModal">
          <i class="bi bi-plus-lg me-1"></i>
          Subir imagen
        </button>
      </template>
    </PageHeader>

    <div v-if="$page.props.flash?.success" class="alert alert-success alert-dismissible fade show" role="alert">
      {{ $page.props.flash.success }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>

    <SortableCards
      ref="sortableCardsRef"
      :items="images.data"
      item-class="col-6 col-md-4 col-lg-3"
      :reorderable="true"
      :reorder-endpoint="`/member/businesses/${business?.id}/gallery/reorder`"
      :loading="loading"
      empty-title="No hay imagenes en la galeria"
      empty-text="Comienza subiendo tu primera imagen."
      toast-message="Orden actualizado"
      :has-pagination="!!images.links"
      @reordered="onReordered"
    >
      <template #header>
        <div class="d-flex flex-wrap align-items-center justify-content-between">
          <p class="text-muted mb-0">Arrastra las imagenes para reordenar.</p>
        </div>
      </template>

      <template #item="{ item: img }">
        <div class="card-img-top ratio ratio-4x3 bg-light d-flex align-items-center justify-content-center overflow-hidden">
          <img v-if="img.path" :src="img.path" :alt="img.title || img.original_name" class="w-100 h-100" style="object-fit: cover;" />
          <i v-else class="bi bi-image text-muted fs-1"></i>
        </div>
        <div class="card-body">
          <h5 class="card-title mb-1">{{ img.title || 'Sin titulo' }}</h5>
          <p v-if="img.description" class="card-text small text-muted">{{ img.description }}</p>
          <p v-if="img.location" class="card-text small text-muted">
            <i class="bi bi-geo-alt me-1"></i>{{ img.location.name }}
          </p>
          <div class="d-flex gap-2 mt-2 flex-wrap">
            <button
              class="btn btn-sm btn-outline-primary"
              @click="openEditModal(img)"
            >
              <i class="bi bi-pencil me-1"></i>Editar
            </button>
            <button
              class="btn btn-sm"
              :class="img.is_active ? 'btn-outline-secondary' : 'btn-outline-success'"
              @click="toggleActive(img)"
            >
              {{ img.is_active ? 'Desactivar' : 'Activar' }}
            </button>
            <button
              class="btn btn-sm btn-outline-danger"
              @click="deleteImage(img)"
            >
              <i class="bi bi-trash me-1"></i>Eliminar
            </button>
          </div>
        </div>
      </template>

      <template #pagination>
        <div v-if="images.links" class="d-flex justify-content-center mt-4">
          <Pagination :links="images.links" />
        </div>
      </template>
    </SortableCards>

    <div ref="uploadModalElement" class="modal fade" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Subir imagen</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form @submit.prevent="uploadImage">
            <div class="modal-body">
              <div class="mb-3">
                <label for="image-file" class="form-label">Archivo *</label>
                <input
                  id="image-file"
                  type="file"
                  class="form-control"
                  accept="image/jpeg,image/png,image/webp,image/gif"
                  @change="onFileChange"
                  :class="{ 'is-invalid': formError }"
                  required
                />
                <div v-if="formError" class="invalid-feedback d-block">{{ formError }}</div>
                <small class="text-muted">Max: 5MB. Formatos: JPEG, PNG, WebP, GIF</small>
              </div>
              <FieldText
                id="upload-title"
                label="Titulo"
                v-model="uploadForm.title"
              />
              <FieldTextarea
                id="upload-description"
                label="Descripcion"
                v-model="uploadForm.description"
                :rows="2"
              />
              <FieldSelect
                id="upload-location"
                label="Ubicacion"
                v-model="uploadForm.business_location_id"
              >
                <option :value="null">Sin ubicacion</option>
                <option v-for="loc in locations" :key="loc.id" :value="loc.id">{{ loc.name }}</option>
              </FieldSelect>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-primary" :disabled="uploading || !uploadForm.file">
                {{ uploading ? 'Subiendo...' : 'Subir' }}
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
            <h5 class="modal-title">Editar imagen</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form @submit.prevent="saveEdit">
            <div class="modal-body">
              <div class="text-center mb-3">
                <img v-if="editForm.path" :src="editForm.path" class="img-thumbnail" style="max-height: 200px;" :alt="editForm.title" />
              </div>
              <FieldText
                id="edit-title"
                label="Titulo"
                v-model="editForm.title"
              />
              <FieldTextarea
                id="edit-description"
                label="Descripcion"
                v-model="editForm.description"
                :rows="2"
              />
              <FieldSelect
                id="edit-location"
                label="Ubicacion"
                v-model="editForm.business_location_id"
              >
                <option :value="null">Sin ubicacion</option>
                <option v-for="loc in locations" :key="loc.id" :value="loc.id">{{ loc.name }}</option>
              </FieldSelect>
              <div class="row g-3">
                <div class="col-6">
                  <FieldNumber
                    id="edit-sort"
                    label="Orden"
                    v-model="editForm.sort_order"
                  />
                </div>
                <div class="col-6 d-flex align-items-end">
                  <FieldSwitch
                    id="edit-active"
                    label="Activo"
                    v-model="editForm.is_active"
                  />
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-primary" :disabled="saving">
                {{ saving ? 'Guardando...' : 'Guardar' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { computed, reactive, ref, onMounted, nextTick } from 'vue'
import { Head, usePage, router } from '@inertiajs/vue3'
import { Modal } from 'bootstrap'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import Pagination from '@/Components/Member/Pagination.vue'
import SortableCards from '@/Components/DataTable/SortableCards.vue'
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldTextarea from '@/Components/Fields/FieldTextarea.vue'
import FieldSelect from '@/Components/Fields/FieldSelect.vue'
import FieldNumber from '@/Components/Fields/FieldNumber.vue'
import FieldSwitch from '@/Components/Fields/FieldSwitch.vue'

const page = usePage()
const business = computed(() => page.props.business)
const images = computed(() => page.props.images || { data: [], links: [] })
const locations = computed(() => page.props.locations || [])

const breadcrumbs = computed(() => [
  { label: 'Mis Negocios', href: '/member/business-modules' },
  { label: 'Galeria', active: true },
])

const sortableCardsRef = ref(null)
const uploadModalElement = ref(null)
const editModalElement = ref(null)
let uploadModal = null
let editModal = null

const loading = ref(false)
const uploading = ref(false)
const saving = ref(false)
const formError = ref('')

const uploadForm = reactive({
  file: null,
  title: '',
  description: '',
  business_location_id: null,
})

const editForm = reactive({
  id: null,
  path: '',
  title: '',
  description: '',
  business_location_id: null,
  sort_order: 0,
  is_active: true,
})

const resetUploadForm = () => {
  uploadForm.file = null
  uploadForm.title = ''
  uploadForm.description = ''
  uploadForm.business_location_id = null
  formError.value = ''
}

const openUploadModal = () => {
  resetUploadForm()
  nextTick(() => uploadModal.show())
}

const closeUploadModal = () => {
  uploadModal.hide()
}

const openEditModal = (img) => {
  editForm.id = img.id
  editForm.path = img.path
  editForm.title = img.title || ''
  editForm.description = img.description || ''
  editForm.business_location_id = img.business_location_id
  editForm.sort_order = img.sort_order || 0
  editForm.is_active = img.is_active
  nextTick(() => editModal.show())
}

const closeEditModal = () => {
  editModal.hide()
}

const onFileChange = (e) => {
  formError.value = ''
  const file = e.target.files[0]
  if (!file) return

  const maxSize = 5 * 1024 * 1024
  if (file.size > maxSize) {
    formError.value = 'El archivo supera el tamano maximo de 5MB.'
    return
  }

  const allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif']
  if (!allowedTypes.includes(file.type)) {
    formError.value = 'Solo se permiten imagenes (JPEG, PNG, WebP, GIF).'
    return
  }

  uploadForm.file = file
}

const uploadImage = () => {
  if (!uploadForm.file) return

  uploading.value = true
  formError.value = ''

  const formData = new FormData()
  formData.append('file', uploadForm.file)
  formData.append('title', uploadForm.title)
  formData.append('description', uploadForm.description)
  if (uploadForm.business_location_id) {
    formData.append('business_location_id', uploadForm.business_location_id)
  }

  router.post(`/member/businesses/${business.value.id}/gallery`, formData, {
    preserveScroll: true,
    onFinish: () => {
      uploading.value = false
      closeUploadModal()
    },
    onError: (errors) => {
      uploading.value = false
      if (errors.file) {
        formError.value = errors.file
      }
    },
  })
}

const saveEdit = () => {
  saving.value = true

  router.put(`/member/businesses/${business.value.id}/gallery/${editForm.id}`, {
    title: editForm.title,
    description: editForm.description,
    business_location_id: editForm.business_location_id,
    sort_order: editForm.sort_order,
    is_active: editForm.is_active,
  }, {
    preserveScroll: true,
    onFinish: () => {
      saving.value = false
      closeEditModal()
    },
  })
}

const toggleActive = (img) => {
  router.put(`/member/businesses/${business.value.id}/gallery/${img.id}`, {
    title: img.title,
    description: img.description,
    is_active: !img.is_active,
    sort_order: img.sort_order,
    business_location_id: img.business_location_id,
  }, { preserveScroll: true })
}

const deleteImage = (img) => {
  if (confirm('Estas seguro de eliminar esta imagen?')) {
    router.delete(`/member/businesses/${business.value.id}/gallery/${img.id}`, {
      preserveScroll: true,
    })
  }
}

const onReordered = (ids) => {
  console.log('Reordered:', ids)
}

onMounted(() => {
  uploadModal = new Modal(uploadModalElement.value)
  editModal = new Modal(editModalElement.value)
})
</script>
