<template>
  <div class="contexts-tab">
    <div v-if="successMessage" class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="bi bi-check-circle me-2"></i>{{ successMessage }}
      <button type="button" class="btn-close" @click="successMessage = null"></button>
    </div>

    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-file-text me-2"></i>Contextos Personalizados</h5>
        <button class="btn btn-primary btn-sm" @click="openCreateModal">
          <i class="bi bi-plus-lg me-1"></i>Nuevo Contexto
        </button>
      </div>
      <div class="card-body">
        <div v-if="contexts.length === 0" class="text-center py-5">
          <div class="text-muted">
            <i class="bi bi-inbox" style="font-size: 3rem;"></i>
            <p class="mt-3 mb-0">No hay contextos personalizados.</p>
            <small>Crea contextos para que el chatbot tenga información adicional sobre tu negocio.</small>
          </div>
        </div>

        <div v-else class="contexts-list">
          <div
            v-for="context in contexts"
            :key="context.id"
            class="context-item"
            :class="{ inactive: !context.is_active }"
          >
            <div class="context-content">
              <div class="context-header">
                <h6 class="mb-1">{{ context.title }}</h6>
                <span
                  class="badge"
                  :class="context.is_active ? 'bg-success' : 'bg-secondary'"
                >
                  {{ context.is_active ? 'Activo' : 'Inactivo' }}
                </span>
              </div>
              <p class="text-muted small mb-0">
                {{ context.content.substring(0, 150) }}{{ context.content.length > 150 ? '...' : '' }}
              </p>
            </div>
            <div class="context-actions">
              <button class="btn btn-sm btn-outline-primary" @click="openEditModal(context)">
                <i class="bi bi-pencil"></i>
              </button>
              <button class="btn btn-sm btn-outline-danger" @click="deleteContext(context)">
                <i class="bi bi-trash"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div ref="modalElement" class="modal fade" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ editingContext ? 'Editar Contexto' : 'Nuevo Contexto' }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form @submit.prevent="saveContext">
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label">Título</label>
                <input
                  type="text"
                  v-model="form.title"
                  class="form-control"
                  placeholder="Ej: Información sobreenvíos"
                  required
                />
              </div>
              <div class="mb-3">
                <label class="form-label">Contenido</label>
                <textarea
                  v-model="form.content"
                  class="form-control"
                  rows="8"
                  placeholder="Escribe aquí información relevante que el chatbot podrá usar para responder preguntas..."
                  required
                ></textarea>
                <small class="text-muted">
                  {{ form.content.length }} caracteres
                </small>
              </div>
              <div class="form-check form-switch">
                <input
                  class="form-check-input"
                  type="checkbox"
                  v-model="form.is_active"
                  id="context-active"
                />
                <label class="form-check-label" for="context-active">
                  Contexto activo
                </label>
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
  </div>
</template>

<script setup>
import { ref, reactive, nextTick, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import { Modal } from 'bootstrap'

const props = defineProps({
  business: Object,
  contexts: Array,
})

const emit = defineEmits(['saved', 'deleted'])

const modalElement = ref(null)
let contextModal = null

const editingContext = ref(null)
const saving = ref(false)
const successMessage = ref(null)

const form = reactive({
  title: '',
  content: '',
  is_active: true,
})

const openCreateModal = () => {
  editingContext.value = null
  form.title = ''
  form.content = ''
  form.is_active = true
  nextTick(() => contextModal?.show())
}

const openEditModal = (context) => {
  editingContext.value = context
  form.title = context.title
  form.content = context.content
  form.is_active = context.is_active
  nextTick(() => contextModal?.show())
}

const closeModal = () => {
  contextModal?.hide()
}

const saveContext = () => {
  saving.value = true
  successMessage.value = null

  if (editingContext.value) {
    router.put(
      `/member/businesses/${props.business.id}/ai-chatbot/contexts/${editingContext.value.id}`,
      form,
      {
        preserveScroll: true,
        onSuccess: () => {
          closeModal()
          successMessage.value = 'Contexto actualizado correctamente.'
          emit('saved')
        },
        onFinish: () => {
          saving.value = false
        },
      }
    )
  } else {
    router.post(`/member/businesses/${props.business.id}/ai-chatbot/contexts`, form, {
      preserveScroll: true,
      onSuccess: () => {
        closeModal()
        successMessage.value = 'Contexto creado correctamente.'
        emit('saved')
      },
      onFinish: () => {
        saving.value = false
      },
    })
  }
}

const deleteContext = (context) => {
  if (confirm(`¿Eliminar el contexto "${context.title}"?`)) {
    router.delete(
      `/member/businesses/${props.business.id}/ai-chatbot/contexts/${context.id}`,
      {
        preserveScroll: true,
        onSuccess: () => {
          successMessage.value = 'Contexto eliminado correctamente.'
          emit('deleted')
        },
      }
    )
  }
}

onMounted(() => {
  contextModal = new Modal(modalElement.value)
})
</script>

<style lang="less" scoped>
.contexts-tab {
  .card {
    border: 1px solid #e9ecef;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
  }

  .card-header {
    background: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
    padding: 16px 20px;
  }

  .contexts-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
  }

  .context-item {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 16px;
    background: #f8f9fa;
    border-radius: 8px;
    border: 1px solid #e9ecef;

    &.inactive {
      opacity: 0.6;
      background: #fff;
    }

    .context-content {
      flex: 1;
      min-width: 0;

      .context-header {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;

        h6 {
          margin: 0;
          color: #212529;
        }
      }
    }

    .context-actions {
      display: flex;
      gap: 8px;
      margin-left: 16px;
    }
  }

  .form-label {
    font-weight: 500;
    color: #495057;
    margin-bottom: 8px;
  }
}
</style>
