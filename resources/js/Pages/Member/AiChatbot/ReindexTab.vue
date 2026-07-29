<template>
  <div class="reindex-tab">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-search me-2"></i>Contenido Indexado</h5>
        <small class="text-muted">El chatbot utiliza este contenido para responder preguntas de los clientes</small>
      </div>
      <div class="card-body">
        <div v-if="Object.keys(embeddingCounts).length === 0" class="alert alert-info">
          <i class="bi bi-info-circle me-2"></i>
          No hay contenido indexado. Activa el chatbot e ingresa tu API key para comenzar.
        </div>
        <div v-else class="row g-3">
          <div class="col-6 col-md-3">
            <div class="stat-card">
              <div class="stat-value">{{ embeddingCounts.product || 0 }}</div>
              <div class="stat-label">Productos</div>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="stat-card">
              <div class="stat-value">{{ embeddingCounts.service || 0 }}</div>
              <div class="stat-label">Servicios</div>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="stat-card">
              <div class="stat-value">{{ embeddingCounts.promotion || 0 }}</div>
              <div class="stat-label">Promociones</div>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="stat-card">
              <div class="stat-value">{{ embeddingCounts.faq || 0 }}</div>
              <div class="stat-label">FAQs</div>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="stat-card">
              <div class="stat-value">{{ embeddingCounts.location || 0 }}</div>
              <div class="stat-label">Ubicaciones</div>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="stat-card">
              <div class="stat-value">{{ embeddingCounts.about || 0 }}</div>
              <div class="stat-label">Acerca de</div>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="stat-card">
              <div class="stat-value">{{ embeddingCounts.custom || 0 }}</div>
              <div class="stat-label">Contextos</div>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="stat-card">
              <div class="stat-value">{{ (embeddingCounts.restaurant_category || 0) + (embeddingCounts.restaurant_product || 0) }}</div>
              <div class="stat-label">Menú</div>
            </div>
          </div>
        </div>
      </div>
      <div class="card-footer">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <button
              type="button"
              class="btn btn-outline-primary"
              @click="reindex"
              :disabled="reindexing || !settings"
            >
              <span v-if="reindexing"><i class="bi bi-hourglass-split me-2"></i>Reindexando...</span>
              <span v-else><i class="bi bi-arrow-repeat me-2"></i>Reindexar Contenido</span>
            </button>
          </div>
          <small class="text-muted" v-if="lastReindex">
            Última indexación: {{ lastReindex }}
          </small>
        </div>
      </div>
    </div>

    <div v-if="reindexResult" class="alert alert-success mt-4">
      <i class="bi bi-check-circle me-2"></i>
      <strong>Indexación completada</strong>
      <ul class="mb-0 mt-2">
        <li v-for="(count, key) in reindexResult" :key="key">
          {{ formatLabel(key) }}: {{ count }}
        </li>
      </ul>
    </div>

    <div class="card mt-4">
      <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-lightbulb me-2"></i>¿Qué es la indexación?</h5>
      </div>
      <div class="card-body">
        <p>La indexación permite que el chatbot conozca información sobre tu negocio para responder preguntas de tus clientes.</p>
        <ul>
          <li><strong>Productos, Servicios, Promociones:</strong> Se indexan automáticamente cuando los creas o actualizas.</li>
          <li><strong>FAQs y Contextos:</strong> Los creas tú y se indexan manualmente.</li>
          <li><strong>Menú:</strong> Los productos del menú se indexan automáticamente.</li>
        </ul>
        <p class="mb-0"><small class="text-muted">Si no ves contenido indexado, verifica que el chatbot esté habilitado y la API key sea correcta.</small></p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const props = defineProps({
  business: Object,
  settings: Object,
  embeddingCounts: Object,
})

const emit = defineEmits(['reindex'])

const reindexing = ref(false)
const reindexResult = ref(null)
const lastReindex = ref(null)

const reindex = async () => {
  if (!props.settings) return

  reindexing.value = true
  reindexResult.value = null

  try {
    const response = await fetch(`/member/businesses/${props.business.id}/ai-chatbot/reindex`, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
        'Content-Type': 'application/json',
      },
    })

    const data = await response.json()

    if (data.success) {
      reindexResult.value = data.stats
      lastReindex.value = new Date().toLocaleString('es-MX')
      emit('reindex')
    } else {
      alert(data.message || 'Error al reindexar contenido')
    }
  } catch (error) {
    alert('Error al reindexar contenido')
  } finally {
    reindexing.value = false
  }
}

const formatLabel = (key) => {
  const labels = {
    products: 'Productos',
    services: 'Servicios',
    promotions: 'Promociones',
    faqs: 'FAQs',
    locations: 'Ubicaciones',
    about: 'Acerca de',
    custom: 'Contextos',
    restaurant_menu: 'Menú',
    restaurant_category: 'Categorías menú',
    restaurant_product: 'Productos menú',
  }
  return labels[key] || key
}
</script>

<style scoped>
.stat-card {
  background: #f8f9fa;
  border-radius: 8px;
  padding: 16px;
  text-align: center;
}

.stat-value {
  font-size: 1.75rem;
  font-weight: 700;
  color: #212529;
}

.stat-label {
  font-size: 0.875rem;
  color: #6c757d;
  margin-top: 4px;
}
</style>
