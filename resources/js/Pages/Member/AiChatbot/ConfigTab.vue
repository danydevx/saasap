<template>
  <div class="config-tab">
    <div v-if="successMessage" class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="bi bi-check-circle me-2"></i>{{ successMessage }}
      <button type="button" class="btn-close" @click="successMessage = null"></button>
    </div>

    <div v-if="errorMessage" class="alert alert-danger alert-dismissible fade show" role="alert">
      <i class="bi bi-exclamation-triangle me-2"></i>{{ errorMessage }}
      <button type="button" class="btn-close" @click="errorMessage = null"></button>
    </div>

    <form @submit.prevent="saveSettings">
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0"><i class="bi bi-robot me-2"></i>Configuración del Chatbot</h5>
        </div>
        <div class="card-body">
          <div class="row g-4">
            <div class="col-12 col-md-6">
              <div class="mb-3">
                <label class="form-label">Proveedor de IA</label>
                <select v-model="form.provider" class="form-select">
                  <option value="openai">OpenAI</option>
                  <option value="minimax">MiniMax</option>
                </select>
              </div>
            </div>

            <div class="col-12 col-md-6">
              <div class="mb-3">
                <label class="form-label">API Key</label>
                <input
                  type="password"
                  v-model="form.api_key"
                  class="form-control"
                  placeholder="sk-..."
                  autocomplete="off"
                />
                <small class="text-muted">Tu API key se guarda de forma segura y encriptada</small>
              </div>
            </div>

            <div class="col-12 col-md-6">
              <div class="mb-3">
                <label class="form-label">Modelo de Chat</label>
                <select v-model="form.model" class="form-select">
                  <option value="gpt-4o-mini">GPT-4o Mini (Recomendado)</option>
                  <option value="gpt-4o">GPT-4o</option>
                  <option value="gpt-4-turbo">GPT-4 Turbo</option>
                  <option value="gpt-3.5-turbo">GPT-3.5 Turbo</option>
                </select>
              </div>
            </div>

            <div class="col-12 col-md-6">
              <div class="mb-3">
                <label class="form-label">Modelo de Embeddings</label>
                <select v-model="form.embedding_model" class="form-select">
                  <option value="text-embedding-3-small">text-embedding-3-small (Recomendado)</option>
                  <option value="text-embedding-3-large">text-embedding-3-large</option>
                  <option value="text-embedding-ada-002">text-embedding-ada-002</option>
                </select>
              </div>
            </div>

            <div class="col-12">
              <div class="mb-3">
                <label class="form-label">System Prompt</label>
                <textarea
                  v-model="form.system_prompt"
                  class="form-control"
                  rows="4"
                  placeholder="Eres un asistente amigable de {business_name}..."
                ></textarea>
                <small class="text-muted">
                  Usa <code>{business_name}</code> para incluir el nombre del negocio automáticamente.
                </small>
              </div>
            </div>

            <div class="col-12 col-md-4">
              <div class="mb-3">
                <label class="form-label">Color del Widget</label>
                <div class="color-input-wrapper">
                  <input
                    type="color"
                    v-model="form.widget_color"
                    class="color-input"
                  />
                  <input
                    type="text"
                    v-model="form.widget_color"
                    class="form-control color-text"
                    pattern="^#[0-9A-Fa-f]{6}$"
                  />
                </div>
              </div>
            </div>

            <div class="col-12 col-md-4">
              <div class="mb-3">
                <label class="form-label">Tema del Widget</label>
                <div class="theme-selector">
                  <div class="form-check form-check-inline">
                    <input
                      class="form-check-input"
                      type="radio"
                      v-model="form.widget_theme"
                      id="themeLight"
                      value="light"
                    />
                    <label class="form-check-label" for="themeLight">
                      <i class="bi bi-sun me-1"></i>Light
                    </label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input
                      class="form-check-input"
                      type="radio"
                      v-model="form.widget_theme"
                      id="themeDark"
                      value="dark"
                    />
                    <label class="form-check-label" for="themeDark">
                      <i class="bi bi-moon me-1"></i>Dark
                    </label>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-12 col-md-4">
              <div class="mb-3">
                <div class="form-check form-switch mt-4">
                  <input
                    class="form-check-input"
                    type="checkbox"
                    v-model="form.allow_reset_chat"
                    id="allowResetChat"
                  />
                  <label class="form-check-label" for="allowResetChat">
                    Permitir reiniciar chat
                  </label>
                </div>
              </div>
            </div>

            <div class="col-12 col-md-4">
              <div class="mb-3">
                <label class="form-label">Conversaciones/mes</label>
                <input
                  type="number"
                  v-model.number="form.max_conversations_month"
                  class="form-control"
                  min="1"
                  max="10000"
                />
              </div>
            </div>

            <div class="col-12 col-md-4">
              <div class="mb-3">
                <label class="form-label">Mensajes/conversación</label>
                <input
                  type="number"
                  v-model.number="form.max_messages_conversation"
                  class="form-control"
                  min="1"
                  max="500"
                />
              </div>
            </div>

            <div class="col-12 col-md-4">
              <div class="mb-3">
                <label class="form-label">Tokens máx. por respuesta</label>
                <input
                  type="number"
                  v-model.number="form.max_tokens_response"
                  class="form-control"
                  min="100"
                  max="4000"
                />
              </div>
            </div>

            <div class="col-12">
              <div class="form-check form-switch mb-3">
                <input
                  class="form-check-input"
                  type="checkbox"
                  v-model="form.is_enabled"
                  id="is-enabled"
                />
                <label class="form-check-label" for="is-enabled">
                  <strong>Chatbot habilitado</strong>
                  <small class="d-block text-muted">Cuando está desactivado, el chatbot no aparece en el minisite</small>
                </label>
              </div>
            </div>
          </div>
        </div>
        <div class="card-footer">
          <button type="submit" class="btn btn-primary" :disabled="saving">
            <span v-if="saving"><i class="bi bi-hourglass-split me-2"></i>Guardando...</span>
            <span v-else><i class="bi bi-check-lg me-2"></i>Guardar Configuración</span>
          </button>
        </div>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, reactive, watch } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  business: Object,
  settings: Object,
})

const emit = defineEmits(['saved'])

const saving = ref(false)
const successMessage = ref(null)
const errorMessage = ref(null)

const defaultForm = {
  provider: 'openai',
  api_key: '',
  model: 'gpt-4o-mini',
  embedding_model: 'text-embedding-3-small',
  system_prompt: '',
  max_conversations_month: 500,
  max_messages_conversation: 50,
  max_tokens_response: 500,
  widget_color: '#3B82F6',
  widget_theme: 'light',
  is_enabled: false,
  allow_reset_chat: false,
}

const form = reactive({ ...defaultForm })

  watch(
  () => props.settings,
  (newSettings) => {
    if (newSettings) {
      form.provider = newSettings.provider || 'openai'
      form.api_key = newSettings.api_key || ''
      form.model = newSettings.model || 'gpt-4o-mini'
      form.embedding_model = newSettings.embedding_model || 'text-embedding-3-small'
      form.system_prompt = newSettings.system_prompt || ''
      form.max_conversations_month = newSettings.max_conversations_month || 500
      form.max_messages_conversation = newSettings.max_messages_conversation || 50
      form.max_tokens_response = newSettings.max_tokens_response || 500
      form.widget_color = newSettings.widget_color || '#3B82F6'
      form.widget_theme = newSettings.widget_theme || 'light'
      form.is_enabled = newSettings.is_enabled || false
      form.allow_reset_chat = newSettings.allow_reset_chat || false
    }
  },
  { immediate: true }
)

const saveSettings = () => {
  saving.value = true
  successMessage.value = null
  errorMessage.value = null

  router.post(`/member/businesses/${props.business.id}/ai-chatbot/settings`, form, {
    preserveScroll: true,
    onSuccess: () => {
      successMessage.value = 'Configuración guardada correctamente.'
      emit('saved')
    },
    onError: (errors) => {
      errorMessage.value = Object.values(errors)[0] || 'Error al guardar.'
    },
    onFinish: () => {
      saving.value = false
    },
  })
}
</script>

<style lang="less" scoped>
.config-tab {
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

  .card-footer {
    background: #f8f9fa;
    border-top: 1px solid #e9ecef;
    padding: 16px 20px;
  }

  .form-label {
    font-weight: 500;
    color: #495057;
    margin-bottom: 8px;
  }

  .color-input-wrapper {
    display: flex;
    gap: 8px;

    .color-input {
      width: 50px;
      height: 38px;
      padding: 2px;
      border: 1px solid #ced4da;
      border-radius: 4px;
      cursor: pointer;
    }

    .color-text {
      flex: 1;
      max-width: 120px;
    }
  }

  code {
    background: #e9ecef;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 0.875em;
  }

  .stat-card {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 16px;
    text-align: center;

    .stat-value {
      font-size: 1.5rem;
      font-weight: 700;
      color: #0d6efd;
    }

    .stat-label {
      font-size: 0.875rem;
      color: #6c757d;
      margin-top: 4px;
    }
  }
}
</style>
