<template>
  <div class="property-location-section">
    <div class="row g-3">
      <div class="col-12">
        <h5 class="border-bottom pb-2 mb-3">
          <i class="bi bi-geo-alt me-2"></i>Ubicación
        </h5>
      </div>

      <div class="col-md-6">
        <label class="form-label">País</label>
        <select class="form-select" v-model="form.country" :class="{ 'is-invalid': errors.country }">
          <option value="">Selecciona un país</option>
          <option v-for="c in countries" :key="c.value" :value="c.value">{{ c.label }}</option>
        </select>
        <div v-if="errors.country" class="invalid-feedback">{{ errors.country }}</div>
      </div>

      <div class="col-md-6">
        <label class="form-label">Estado</label>
        <select class="form-select" v-model="form.state" :disabled="!form.country" :class="{ 'is-invalid': errors.state }">
          <option value="">{{ form.country ? 'Selecciona un estado' : 'Selecciona un país primero' }}</option>
          <option v-for="s in states" :key="s.value" :value="s.value">{{ s.label }}</option>
        </select>
        <div v-if="errors.state" class="invalid-feedback">{{ errors.state }}</div>
      </div>

      <div class="col-md-6">
        <label class="form-label">Ciudad</label>
        <input type="text" class="form-control" v-model="form.city" placeholder="Nombre de la ciudad" :class="{ 'is-invalid': errors.city }">
        <div v-if="errors.city" class="invalid-feedback">{{ errors.city }}</div>
      </div>

      <div class="col-md-6">
        <label class="form-label">Municipio</label>
        <select class="form-select" v-model="form.municipality" :disabled="!form.state" :class="{ 'is-invalid': errors.municipality }">
          <option value="">{{ form.state ? 'Selecciona un municipio' : 'Selecciona un estado primero' }}</option>
          <option v-for="m in municipalities" :key="m.value" :value="m.value">{{ m.label }}</option>
        </select>
        <div v-if="errors.municipality" class="invalid-feedback">{{ errors.municipality }}</div>
      </div>

      <div class="col-md-4">
        <label class="form-label">Colonia</label>
        <input type="text" class="form-control" v-model="form.colony" placeholder="Nombre de la colonia">
      </div>

      <div class="col-md-4">
        <label class="form-label">Código Postal</label>
        <input type="text" class="form-control" v-model="form.postal_code" placeholder="00000">
      </div>

      <div class="col-md-4">
        <label class="form-label">Calle</label>
        <input type="text" class="form-control" v-model="form.street" placeholder="Nombre de la calle">
      </div>

      <div class="col-md-6">
        <label class="form-label">Número Exterior</label>
        <input type="text" class="form-control" v-model="form.exterior_number" placeholder="S/N">
      </div>

      <div class="col-md-6">
        <label class="form-label">Número Interior</label>
        <input type="text" class="form-control" v-model="form.interior_number" placeholder="(Opcional)">
      </div>

      <div class="col-12">
        <label class="form-label">Referencias</label>
        <textarea class="form-control" v-model="form.references" rows="2" placeholder="Indicaciones para llegar, puntos de referencia..."></textarea>
      </div>

      <div class="col-md-4">
        <label class="form-label">Latitud</label>
        <input type="text" class="form-control" v-model="form.latitude" placeholder="19.4326" @blur="onLatLngChange">
        <small class="text-muted">Ej: 19.4326</small>
      </div>

      <div class="col-md-4">
        <label class="form-label">Longitud</label>
        <input type="text" class="form-control" v-model="form.longitude" placeholder="-99.1332" @blur="onLatLngChange">
        <small class="text-muted">Ej: -99.1332</small>
      </div>

      <div class="col-md-4">
        <label class="form-label">Precisión del mapa</label>
        <div class="form-check form-switch mt-2">
          <input class="form-check-input" type="checkbox" v-model="form.show_exact_location" id="showExactLocation">
          <label class="form-check-label" for="showExactLocation">
            {{ form.show_exact_location ? 'Ubicación exacta' : 'Ubicación aproximada' }}
          </label>
        </div>
        <small class="text-muted">
          {{ form.show_exact_location ? 'Se mostrará la ubicación exacta en el mapa público' : 'La ubicación se mostrará con aproximación' }}
        </small>
      </div>

      <div class="col-12">
        <MapPicker
          label="Ubicación en el mapa"
          :lat="form.latitude"
          :lng="form.longitude"
          @update:lat="form.latitude = $event"
          @update:lng="form.longitude = $event"
          @reverse-geocoded="onReverseGeocoded"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, watch } from 'vue'
import MapPicker from '@/Components/MapPicker.vue'
import axios from 'axios'

const props = defineProps({
  modelValue: {
    type: Object,
    default: () => ({}),
  },
  errors: {
    type: Object,
    default: () => ({}),
  },
})

const emit = defineEmits(['update:modelValue'])

const countries = ref([
  { value: 'MX', label: 'México' },
])

const states = ref([])
const municipalities = ref([])
let geocodeTimeout = null

const form = reactive({
  country: props.modelValue?.country || 'MX',
  state: props.modelValue?.state || '',
  city: props.modelValue?.city || '',
  municipality: props.modelValue?.municipality || '',
  colony: props.modelValue?.colony || '',
  postal_code: props.modelValue?.postal_code || '',
  street: props.modelValue?.street || '',
  exterior_number: props.modelValue?.exterior_number || '',
  interior_number: props.modelValue?.interior_number || '',
  references: props.modelValue?.references || '',
  latitude: props.modelValue?.latitude || '',
  longitude: props.modelValue?.longitude || '',
  show_exact_location: props.modelValue?.show_exact_location ?? false,
})

function onReverseGeocoded(data) {
  if (data.address) form.street = data.address
  if (data.number) form.exterior_number = data.number
  if (data.colony) form.colony = data.colony
  if (data.postal_code) form.postal_code = data.postal_code
  if (data.city) form.city = data.city
  if (data.municipality && !form.municipality) form.municipality = data.municipality
  if (data.state) {
    const stateOption = states.value.find(s => s.label === data.state || s.value === data.state)
    if (stateOption) {
      form.state = stateOption.value
      loadMunicipalities(stateOption.value)
    }
  }
  if (data.country) {
    const countryOption = countries.value.find(c => c.label === data.country || c.value === data.country)
    if (countryOption) form.country = countryOption.value
  }
}

watch(
  () => form,
  (newVal) => {
    emit('update:modelValue', { ...newVal })
  },
  { deep: true, immediate: true }
)

watch(
  () => props.modelValue,
  (newVal) => {
    if (newVal && typeof newVal === 'object') {
      const hadState = !!form.state
      Object.keys(newVal).forEach(key => {
        if (form.hasOwnProperty(key) && newVal[key] !== undefined && newVal[key] !== null) {
          form[key] = newVal[key]
        }
      })
      if (newVal.state && !hadState) {
        loadMunicipalities(newVal.state)
      }
    }
  },
  { deep: true }
)

watch(
  () => form.state,
  async (newState, oldState) => {
    if (newState) {
      await loadMunicipalities(newState)
    } else {
      municipalities.value = []
      form.municipality = ''
    }
    if (oldState !== undefined) {
      triggerGeocode()
    }
  }
)

watch(
  () => form.city,
  () => { triggerGeocode() }
)

watch(
  () => form.municipality,
  () => { triggerGeocode() }
)

watch(
  () => form.colony,
  () => { triggerGeocode() }
)

watch(
  () => form.postal_code,
  () => { triggerGeocode() }
)

function triggerGeocode() {
  if (geocodeTimeout) clearTimeout(geocodeTimeout)
  geocodeTimeout = setTimeout(async () => {
    await geocodeFromAddress()
  }, 1000)
}

async function geocodeFromAddress() {
  const hasAddress = form.city || form.municipality || form.colony || form.postal_code || form.state
  if (!hasAddress) return

  const addressParts = []
  if (form.colony) addressParts.push(form.colony)
  if (form.postal_code) addressParts.push(form.postal_code)
  if (form.city) addressParts.push(form.city)
  if (form.municipality) addressParts.push(form.municipality)
  if (form.state) addressParts.push(form.state)
  addressParts.push('México')

  const query = addressParts.join(', ')

  try {
    const response = await fetch(
      `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=1&countrycodes=mx`
    )
    const data = await response.json()
    if (data && data.length > 0) {
      form.latitude = parseFloat(data[0].lat).toFixed(7)
      form.longitude = parseFloat(data[0].lon).toFixed(7)
    }
  } catch (error) {
    console.error('Error geocoding address:', error)
  }
}

async function loadStates() {
  try {
    const { data } = await axios.get('/api/v1/location-data/states')
    states.value = data.states.map(s => ({
      value: s.code,
      label: s.name,
    }))
    if (form.state && !municipalities.value.length) {
      await loadMunicipalities(form.state)
    }
  } catch (error) {
    console.error('Error loading states:', error)
    states.value = []
  }
}

async function loadMunicipalities(stateCode) {
  try {
    const { data } = await axios.get(`/api/v1/location-data/municipalities/${stateCode}`)
    municipalities.value = data.municipalities.map(m => ({
      value: m.code,
      label: m.name,
    }))
  } catch (error) {
    console.error('Error loading municipalities:', error)
    municipalities.value = []
  }
}

function onLatLngChange() {
  if (form.latitude && form.longitude) {
    form.latitude = parseFloat(form.latitude)
    form.longitude = parseFloat(form.longitude)
  }
}

loadStates()
</script>

<style scoped>
.property-location-section {
  padding: 1rem 0;
}

.location-map-container {
  background: #f8f9fa;
}

.invalid-feedback {
  display: block;
}
</style>
