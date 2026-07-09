<template>
  <MemberLayout>
    <Head :title="`Nueva Resena - ${business.name}`" />

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
      <div>
        <Link :href="`/member/businesses/${business.id}/reviews`" class="text-decoration-none text-muted small">
          <i class="bi bi-arrow-left me-1"></i>Volver
        </Link>
        <h1 class="h4 mb-1 mt-1">Nueva Resena</h1>
      </div>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <form @submit.prevent="submit">
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <FieldText
                id="review-client-name"
                label="Nombre del cliente"
                v-model="form.client_name"
                :formError="errors.client_name"
                required
              />
            </div>

            <div class="col-md-6">
              <FieldText
                id="review-company"
                label="Empresa"
                v-model="form.company"
              />
            </div>

            <div class="col-12">
              <FieldTextarea
                id="review-comment"
                label="Comentario"
                v-model="form.comment"
                :formError="errors.comment"
                :rows="4"
                required
              />
            </div>

            <div class="col-md-6">
              <FieldSelect
                id="review-rating"
                label="Calificacion"
                v-model="form.rating"
                :formError="errors.rating"
                required
              >
                <option value="">Seleccionar...</option>
                <option value="5">5 Estrellas</option>
                <option value="4">4 Estrellas</option>
                <option value="3">3 Estrellas</option>
                <option value="2">2 Estrellas</option>
                <option value="1">1 Estrella</option>
              </FieldSelect>
            </div>

            <div class="col-md-6">
              <FieldUrl
                id="review-google-link"
                label="Link de Google"
                v-model="form.google_link"
                placeholder="https://..."
              />
            </div>

            <div class="col-12">
              <FieldSwitch
                id="review-active"
                label="Activa"
                v-model="form.is_active"
              />
            </div>

            <div class="col-12">
              <button type="submit" class="btn btn-primary" :disabled="sending">
                {{ sending ? 'Guardando...' : 'Guardar' }}
              </button>
              <Link :href="`/member/businesses/${business.id}/reviews`" class="btn btn-outline-secondary ms-2">
                Cancelar
              </Link>
            </div>
          </div>
        </form>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { computed, reactive, ref } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldTextarea from '@/Components/Fields/FieldTextarea.vue'
import FieldSelect from '@/Components/Fields/FieldSelect.vue'
import FieldUrl from '@/Components/Fields/FieldUrl.vue'
import FieldSwitch from '@/Components/Fields/FieldSwitch.vue'

const page = usePage()
const business = computed(() => page.props.business)
const errors = computed(() => page.props.errors || {})
const sending = computed(() => false)

const form = reactive({
  client_name: '',
  company: '',
  comment: '',
  rating: '',
  google_link: '',
  is_active: true,
})

const submit = () => {
  router.post(`/member/businesses/${business.value.id}/reviews`, form)
}
</script>
