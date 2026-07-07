<template>
  <MemberLayout>
    <Head :title="`Contactos - ${business.name}`" />

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
      <div>
        <h1 class="h4 mb-1">{{ business.name }}</h1>
        <p class="text-muted mb-0">Mensajes recibidos desde el formulario de contacto.</p>
      </div>
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
                <th scope="col">Nombre</th>
                <th scope="col">Email</th>
                <th scope="col">Telefono</th>
                <th scope="col">Notas</th>
                <th scope="col">Fecha</th>
                <th scope="col" class="text-end">Accion</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="submissions.data.length === 0">
                <td colspan="6" class="text-center text-muted py-4">
                  No hay mensajes del formulario de contacto.
                </td>
              </tr>
              <tr v-for="sub in submissions.data" :key="sub.id">
                <td class="fw-semibold">{{ sub.name }}</td>
                <td>{{ sub.email }}</td>
                <td>{{ sub.phone || '-' }}</td>
                <td>
                  <span class="text-muted small">{{ sub.notes ? sub.notes.substring(0, 50) + '...' : '-' }}</span>
                </td>
                <td>{{ formatDate(sub.created_at) }}</td>
                <td class="text-end">
                  <Link :href="`/member/businesses/${business.id}/leads/${sub.id}`" class="btn btn-sm btn-outline-primary">
                    Ver
                  </Link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-if="submissions.links" class="d-flex justify-content-center mt-4">
          <MemberPagination :links="submissions.links" />
        </div>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import MemberPagination from '@/Components/Member/Pagination.vue'

const page = usePage()
const business = computed(() => page.props.business)
const submissions = computed(() => page.props.submissions || { data: [], links: [] })

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('es-AR')
}
</script>
