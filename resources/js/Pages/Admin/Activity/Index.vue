<template>
  <AdminLayout>
    <Head title="Actividad" />

    <PageHeader :title="'Actividad'" :breadcrumbs="breadcrumbs" backHref="/dashboard" />

    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <form class="row g-2 align-items-end" @submit.prevent="submitSearch">
          <div class="col-12 col-md-6">
            <label class="form-label">Buscar</label>
            <input
              v-model="search"
              type="text"
              class="form-control"
              placeholder="Evento, descripcion, usuario o IP"
            />
          </div>
          <div class="col-12 col-md-4">
            <label class="form-label">Evento</label>
            <select v-model="event" class="form-select">
              <option value="">Todos</option>
              <option v-for="item in events" :key="item" :value="item">
                {{ item }}
              </option>
            </select>
          </div>
          <div class="col-12 col-md-2 d-flex gap-2">
            <button class="btn btn-outline-primary" type="submit">Filtrar</button>
            <button class="btn btn-outline-secondary" type="button" @click="clearFilters">Limpiar</button>
          </div>
        </form>
      </div>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th scope="col">Fecha</th>
              <th scope="col">Evento</th>
              <th scope="col">Actor</th>
              <th scope="col">Usuario</th>
              <th scope="col">Descripcion</th>
              <th scope="col">IP</th>
              <th scope="col">Subject</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="logs.data.length === 0">
              <td colspan="7" class="text-center text-muted py-4">No hay actividad registrada.</td>
            </tr>
            <tr v-for="log in logs.data" :key="log.id">
              <td class="text-muted">{{ log.created_at }}</td>
              <td class="fw-semibold">{{ log.event }}</td>
              <td>
                <div v-if="log.actor">
                  <div class="fw-semibold">{{ log.actor.name }}</div>
                  <div class="text-muted small">{{ log.actor.email }}</div>
                </div>
                <span v-else class="text-muted">-</span>
              </td>
              <td>
                <div v-if="log.user">
                  <div class="fw-semibold">{{ log.user.name }}</div>
                  <div class="text-muted small">{{ log.user.email }}</div>
                </div>
                <span v-else class="text-muted">-</span>
              </td>
              <td class="text-muted">{{ log.description || '-' }}</td>
              <td class="text-muted">{{ log.ip_address || '-' }}</td>
              <td class="text-muted">
                <span v-if="log.subject_type">{{ log.subject_type }} #{{ log.subject_id }}</span>
                <span v-else>-</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="card-footer d-flex flex-wrap gap-2 align-items-center justify-content-between">
        <div class="text-muted small">
          Mostrando {{ logs.data.length }} de {{ logs.total }} registros
        </div>
        <Pagination :links="logs.links" />
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed, ref } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import Pagination from '@/Components/Admin/Pagination.vue'

const props = defineProps({
  logs: {
    type: Object,
    required: true,
  },
  events: {
    type: Array,
    default: () => [],
  },
  filters: {
    type: Object,
    default: () => ({}),
  },
})

const search = ref(props.filters.search ?? '')
const event = ref(props.filters.event ?? '')

const breadcrumbs = [
  { label: 'Actividad' },
  { label: 'Registros', active: true },
]

const submitSearch = () => {
  router.get(
    '/admin/activity',
    { search: search.value, event: event.value },
    { preserveState: true, replace: true, preserveScroll: true }
  )
}

const clearFilters = () => {
  search.value = ''
  event.value = ''
  submitSearch()
}
</script>
