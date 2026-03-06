<template>
  <AdminLayout>
    <Head title="Editar usuario" />

    <PageHeader :title="'Editar usuario'" :breadcrumbs="breadcrumbs" backHref="/admin/users" />

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <form class="row g-3" @submit.prevent="submit">
          <div class="col-12 col-md-6">
            <FieldText
              id="user-name"
              label="Usuario"
              placeholder="Ej: Juan Perez"
              v-model="form.name"
              :formError="form.errors.name"
              required
            />
          </div>

          <div class="col-12 col-md-6">
            <FieldEmail
              id="user-email"
              label="Email"
              placeholder="usuario@empresa.com"
              v-model="form.email"
              :formError="form.errors.email"
              required
            />
          </div>

          <div class="col-12 col-md-6">
            <div class="form-floating">
              <input
                id="user-password"
                v-model="form.password"
                type="password"
                class="form-control"
                placeholder="********"
                autocomplete="new-password"
                :class="{ 'is-invalid': form.errors.password }"
              />
              <label for="user-password">Password (opcional)</label>
              <div class="form-text">Minimo 8 caracteres, con letras y numeros.</div>
              <div v-if="form.errors.password" class="invalid-feedback">
                {{ form.errors.password }}
              </div>
            </div>
          </div>

          <div class="col-12 col-md-6">
            <div class="form-floating">
              <input
                id="user-password-confirmation"
                v-model="form.password_confirmation"
                type="password"
                class="form-control"
                placeholder="********"
                autocomplete="new-password"
                :class="{ 'is-invalid': form.errors.password_confirmation }"
              />
              <label for="user-password-confirmation">Confirmar password</label>
              <div v-if="form.errors.password_confirmation" class="invalid-feedback">
                {{ form.errors.password_confirmation }}
              </div>
            </div>
          </div>

          <div class="col-12">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <div class="fw-semibold">Roles</div>
              <span class="text-muted small">{{ roles.length }} disponibles</span>
            </div>

            <div v-if="roles.length === 0" class="alert alert-light mb-0">
              No hay roles registrados.
            </div>
            <FieldCheckboxes
              v-else
              v-model="form.roles"
              :items="roles"
              label="Selecciona los roles"
              idPrefix="role_"
              :formError="rolesError"
            />
          </div>

          <div class="col-12">
            <div class="row g-3">
              <div class="col-12 col-md-6">
                <FieldSwitch
                  id="user-active"
                  label="Usuario activo"
                  v-model="form.is_active"
                />
              </div>
              <div class="col-12 col-md-6">
                <div class="text-muted">
                  Email verificado: {{ user.email_verified_at ? 'Si' : 'No' }}
                </div>
                <div class="text-muted">
                  Telefono: {{ user.phone || '-' }}
                </div>
                <button
                  v-if="!user.email_verified_at"
                  type="button"
                  class="btn btn-sm btn-outline-warning mt-2"
                  @click="resendVerification"
                >
                  Reenviar verificacion
                </button>
              </div>
            </div>
          </div>

          <div class="col-12">
            <div class="card border rounded-3">
              <div class="card-body">
                <h3 class="h6 mb-3">Suscripcion</h3>
                <div class="row g-3">
                  <div class="col-12 col-md-6">
                    <label class="form-label">Plan</label>
                    <select v-model="form.subscription.plan_id" class="form-select">
                      <option value="">Sin plan</option>
                      <option v-for="plan in plans" :key="plan.id" :value="plan.id">
                        {{ plan.label }}
                      </option>
                    </select>
                    <div v-if="form.errors['subscription.plan_id']" class="text-danger small mt-1">
                      {{ form.errors['subscription.plan_id'] }}
                    </div>
                  </div>

                  <div class="col-12 col-md-6">
                    <label class="form-label">Estado</label>
                    <select v-model="form.subscription.status" class="form-select">
                      <option value="">Selecciona</option>
                      <option value="pending">Pendiente</option>
                      <option value="trial">Trial</option>
                      <option value="active">Activa</option>
                      <option value="expired">Vencida</option>
                      <option value="canceled">Cancelada</option>
                    </select>
                    <div v-if="form.errors['subscription.status']" class="text-danger small mt-1">
                      {{ form.errors['subscription.status'] }}
                    </div>
                  </div>

                  <div class="col-12 col-md-4">
                    <label class="form-label">Inicio</label>
                    <input v-model="form.subscription.starts_at" type="date" class="form-control" />
                    <div v-if="form.errors['subscription.starts_at']" class="text-danger small mt-1">
                      {{ form.errors['subscription.starts_at'] }}
                    </div>
                  </div>

                  <div class="col-12 col-md-4">
                    <label class="form-label">Fin</label>
                    <input v-model="form.subscription.ends_at" type="date" class="form-control" />
                    <div v-if="form.errors['subscription.ends_at']" class="text-danger small mt-1">
                      {{ form.errors['subscription.ends_at'] }}
                    </div>
                  </div>

                  <div class="col-12 col-md-4">
                    <label class="form-label">Trial termina</label>
                    <input v-model="form.subscription.trial_ends_at" type="date" class="form-control" />
                    <div v-if="form.errors['subscription.trial_ends_at']" class="text-danger small mt-1">
                      {{ form.errors['subscription.trial_ends_at'] }}
                    </div>
                  </div>

                  <div class="col-12 col-md-4">
                    <label class="form-label">Precio</label>
                    <input v-model="form.subscription.price" type="number" class="form-control" min="0" step="0.01" />
                    <div v-if="form.errors['subscription.price']" class="text-danger small mt-1">
                      {{ form.errors['subscription.price'] }}
                    </div>
                  </div>

                  <div class="col-12 col-md-4">
                    <label class="form-label">Periodo</label>
                    <input v-model="form.subscription.billing_period" type="text" class="form-control" />
                    <div v-if="form.errors['subscription.billing_period']" class="text-danger small mt-1">
                      {{ form.errors['subscription.billing_period'] }}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-12 d-flex gap-2">
            <button type="submit" class="btn btn-primary" :disabled="form.processing">
              {{ form.processing ? 'Actualizando...' : 'Actualizar' }}
            </button>
            <Link href="/admin/users" class="btn btn-outline-secondary">Cancelar</Link>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Head, Link, router, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldEmail from '@/Components/Fields/FieldEmail.vue'
import FieldCheckboxes from '@/Components/Fields/FieldCheckboxes.vue'
import FieldSwitch from '@/Components/Fields/FieldSwitch.vue'

const props = defineProps({
  user: {
    type: Object,
    required: true,
  },
  roles: {
    type: Array,
    default: () => [],
  },
  userRoles: {
    type: Array,
    default: () => [],
  },
  plans: {
    type: Array,
    default: () => [],
  },
  subscription: {
    type: Object,
    default: null,
  },
})

const form = useForm({
  name: props.user.name,
  email: props.user.email,
  password: '',
  password_confirmation: '',
  roles: [...props.userRoles],
  is_active: props.user.is_active ?? true,
  subscription: {
    plan_id: props.subscription?.plan_id ?? '',
    status: props.subscription?.status ?? '',
    starts_at: props.subscription?.starts_at ?? '',
    ends_at: props.subscription?.ends_at ?? '',
    trial_ends_at: props.subscription?.trial_ends_at ?? '',
    price: props.subscription?.price ?? '',
    billing_period: props.subscription?.billing_period ?? '',
  },
})

const breadcrumbs = [
  { label: 'Usuarios', href: '/admin/users' },
  { label: 'Editar', active: true },
]

const rolesError = computed(() => {
  return form.errors.roles || form.errors['roles.0'] || ''
})

const submit = () => {
  form.put(`/admin/users/${props.user.id}`)
}

const resendVerification = () => {
  router.post(`/admin/users/${props.user.id}/resend-verification`)
}
</script>
