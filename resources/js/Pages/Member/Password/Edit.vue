<template>
  <MemberLayout>
    <Head title="Cambiar password" />

    <PageHeader :title="'Cambiar password'" :breadcrumbs="breadcrumbs" backHref="/member" />

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <form class="row g-3" @submit.prevent="submit">
          <div class="col-12 col-md-6">
            <div class="form-floating">
              <input
                id="current-password"
                v-model="form.current_password"
                type="password"
                class="form-control"
                placeholder="********"
                autocomplete="current-password"
                :class="{ 'is-invalid': form.errors.current_password }"
                required
              />
              <label for="current-password">Password actual</label>
              <div v-if="form.errors.current_password" class="invalid-feedback">
                {{ form.errors.current_password }}
              </div>
            </div>
          </div>

          <div class="col-12 col-md-6">
            <div class="form-floating">
              <input
                id="new-password"
                v-model="form.password"
                type="password"
                class="form-control"
                placeholder="********"
                autocomplete="new-password"
                :class="{ 'is-invalid': form.errors.password }"
                required
              />
              <label for="new-password">Nueva password</label>
              <div class="form-text">
                Minimo 8 caracteres, con letras y numeros.
              </div>
              <div v-if="form.errors.password" class="invalid-feedback">
                {{ form.errors.password }}
              </div>
            </div>
          </div>

          <div class="col-12 col-md-6">
            <div class="form-floating">
              <input
                id="new-password-confirmation"
                v-model="form.password_confirmation"
                type="password"
                class="form-control"
                placeholder="********"
                autocomplete="new-password"
                :class="{ 'is-invalid': form.errors.password_confirmation }"
                required
              />
              <label for="new-password-confirmation">Confirmar password</label>
              <div v-if="form.errors.password_confirmation" class="invalid-feedback">
                {{ form.errors.password_confirmation }}
              </div>
            </div>
          </div>

          <div class="col-12">
            <button type="submit" class="btn btn-primary" :disabled="form.processing">
              {{ form.processing ? 'Guardando...' : 'Guardar cambios' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { computed, watch } from 'vue'
import { Head, useForm, usePage } from '@inertiajs/vue3'
import { useNotification } from '@kyvg/vue3-notification'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'

const { notify } = useNotification()
const page = usePage()

const breadcrumbs = [
  { label: 'Password' },
]

const form = useForm({
  current_password: '',
  password: '',
  password_confirmation: '',
})

const flashSuccess = computed(() => page.props.flash?.success)
const flashError = computed(() => page.props.flash?.error)

watch(flashSuccess, (value) => {
  if (!value) return
  notify({ type: 'success', text: value })
})

watch(flashError, (value) => {
  if (!value) return
  notify({ type: 'error', text: value })
})

const submit = () => {
  form.put('/member/password', {
    preserveScroll: true,
    onFinish: () => form.reset('current_password', 'password', 'password_confirmation'),
  })
}
</script>
