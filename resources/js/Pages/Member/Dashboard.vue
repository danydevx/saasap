<template>
  <MemberLayout>
    <Head title="Mi dashboard" />

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
      <div>
        <h1 class="h4 mb-1">Hola, {{ userName }}</h1>
        <p class="text-muted mb-0">Bienvenido a tu panel personal.</p>
      </div>
      <Link href="/profile" class="btn btn-outline-secondary">Ver perfil</Link>
    </div>

    <div v-if="showOnboarding" class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <div class="d-flex flex-wrap align-items-start justify-content-between gap-2">
          <div>
            <h2 class="h6 mb-1">Bienvenido, {{ userName }}</h2>
            <p class="text-muted mb-0">Completa estos pasos para empezar con tu cuenta.</p>
          </div>
          <button type="button" class="btn btn-primary btn-sm" @click="completeOnboarding">
            Entendido
          </button>
        </div>
        <div class="row g-2 mt-3">
          <div class="col-12 col-md-6">
            <div class="border rounded-3 p-3 h-100">
              <div class="fw-semibold mb-1">Completar perfil</div>
              <div class="text-muted small">Agrega tus datos para personalizar tu cuenta.</div>
              <Link href="/member/profile" class="btn btn-sm btn-outline-secondary mt-2">Ir a perfil</Link>
            </div>
          </div>
          <div class="col-12 col-md-6">
            <div class="border rounded-3 p-3 h-100">
              <div class="fw-semibold mb-1">Revisar cuenta</div>
              <div class="text-muted small">Consulta tu plan, estado y limites.</div>
              <Link href="/member/account" class="btn btn-sm btn-outline-secondary mt-2">Ver cuenta</Link>
            </div>
          </div>
          <div class="col-12 col-md-6">
            <div class="border rounded-3 p-3 h-100">
              <div class="fw-semibold mb-1">Cambiar password</div>
              <div class="text-muted small">Actualiza tu clave cuando lo necesites.</div>
              <Link href="/member/password" class="btn btn-sm btn-outline-secondary mt-2">Cambiar password</Link>
            </div>
          </div>
          <div class="col-12 col-md-6">
            <div class="border rounded-3 p-3 h-100">
              <div class="fw-semibold mb-1">Explorar panel</div>
              <div class="text-muted small">Revisa tus notificaciones y actividad reciente.</div>
              <Link href="/member/notifications" class="btn btn-sm btn-outline-secondary mt-2">Ver notificaciones</Link>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-3">
      <div class="col-12 col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <h2 class="h6 mb-2">Estado de cuenta</h2>
            <p class="mb-0" :class="accountStatusClass">{{ accountStatusLabel }}</p>
            <p class="text-muted small mb-0">Tu cuenta esta activa y lista para usar.</p>
          </div>
        </div>
      </div>

      <div class="col-12 col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <h2 class="h6 mb-2">Email verificado</h2>
            <p class="mb-0" :class="emailStatusClass">{{ emailStatusLabel }}</p>
            <p class="text-muted small mb-0">Mantiene tu cuenta segura.</p>
          </div>
        </div>
      </div>

      <div class="col-12 col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <h2 class="h6 mb-2">Perfil</h2>
            <p class="text-muted mb-3">Actualiza tus datos personales y redes.</p>
            <Link href="/profile" class="btn btn-sm btn-primary">Editar perfil</Link>
          </div>
        </div>
      </div>

      <div class="col-12 col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <h2 class="h6 mb-2">Plan actual</h2>
            <div v-if="subscription">
              <div class="fw-semibold">{{ subscription.plan_name }}</div>
              <div class="text-muted small">Estado: {{ subscription.status }}</div>
              <div class="text-muted small" v-if="subscription.ends_at">Vence: {{ subscription.ends_at }}</div>
            </div>
            <div v-else class="text-muted">Sin suscripcion activa.</div>
          </div>
        </div>
      </div>

      <div class="col-12">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
              <h2 class="h6 mb-0">Actividad reciente</h2>
              <span class="text-muted small">{{ activities.length }} eventos</span>
            </div>
            <div v-if="activities.length" class="list-group list-group-flush mt-3">
              <div
                v-for="(activity, index) in activities"
                :key="`${activity.type}-${activity.created_at}-${index}`"
                class="list-group-item px-0"
              >
                <div class="d-flex flex-wrap align-items-start justify-content-between gap-2">
                  <div>
                    <div class="fw-semibold">{{ activity.description || activity.type }}</div>
                    <div v-if="activity.description" class="text-muted small">{{ activity.type }}</div>
                  </div>
                  <div class="text-muted small">{{ formatActivityDate(activity.created_at) }}</div>
                </div>
              </div>
            </div>
            <div v-else class="text-muted small mt-3">Aun no tienes actividad registrada.</div>
          </div>
        </div>
      </div>

      <div class="col-12">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h2 class="h6 mb-3">Accesos rapidos</h2>
            <div class="row g-2">
              <div class="col-12 col-md-4">
                <div class="border rounded-3 p-3 h-100">
                  <div class="fw-semibold mb-1">Cuenta</div>
                  <div class="text-muted small">Resumen de tu plan y estado.</div>
                  <Link href="/member/account" class="btn btn-sm btn-outline-secondary mt-2">Ver cuenta</Link>
                </div>
              </div>
              <div class="col-12 col-md-4">
                <div class="border rounded-3 p-3 h-100">
                  <div class="fw-semibold mb-1">Notificaciones</div>
                  <div class="text-muted small">Tus alertas y eventos.</div>
                  <Link href="/member/notifications" class="btn btn-sm btn-outline-secondary mt-2">Ver notificaciones</Link>
                </div>
              </div>
              <div class="col-12 col-md-4">
                <div class="border rounded-3 p-3 h-100">
                  <div class="fw-semibold mb-1">Soporte</div>
                  <div class="text-muted small">Proximamente.</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h2 class="h6 mb-3">Limites del plan</h2>
            <div class="row g-2">
              <div class="col-12 col-md-4">
                <div class="border rounded-3 p-3 h-100">
                  <div class="text-muted small">Max items</div>
                  <div class="fw-semibold">{{ limits.max_items ?? '-' }}</div>
                </div>
              </div>
              <div class="col-12 col-md-4">
                <div class="border rounded-3 p-3 h-100">
                  <div class="text-muted small">Max requests/dia</div>
                  <div class="fw-semibold">{{ limits.max_requests_per_day ?? '-' }}</div>
                </div>
              </div>
              <div class="col-12 col-md-4">
                <div class="border rounded-3 p-3 h-100">
                  <div class="text-muted small">Puede usar AI</div>
                  <div class="fw-semibold">{{ limits.can_use_ai ? 'Si' : 'No' }}</div>
                </div>
              </div>
              <div class="col-12 col-md-4">
                <div class="border rounded-3 p-3 h-100">
                  <div class="text-muted small">Puede exportar</div>
                  <div class="fw-semibold">{{ limits.can_export ? 'Si' : 'No' }}</div>
                </div>
              </div>
              <div class="col-12 col-md-4">
                <div class="border rounded-3 p-3 h-100">
                  <div class="text-muted small">Puede subir archivos</div>
                  <div class="fw-semibold">{{ limits.can_upload_files ? 'Si' : 'No' }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'

const page = usePage()
const userName = computed(() => page.props.auth?.user?.name || 'Usuario')
const isActive = computed(() => !!page.props.auth?.user?.is_active)
const isVerified = computed(() => !!page.props.auth?.user?.email_verified_at)
const subscription = computed(() => page.props.subscription || null)
const limits = computed(() => page.props.limits || {})
const activities = computed(() => page.props.activities || [])
const onboardingCompletedAt = computed(() => page.props.onboardingCompletedAt || null)
const showOnboarding = computed(() => !onboardingCompletedAt.value)

const accountStatusLabel = computed(() => (isActive.value ? 'Activa' : 'Inactiva'))
const accountStatusClass = computed(() => (isActive.value ? 'text-success' : 'text-danger'))
const emailStatusLabel = computed(() => (isVerified.value ? 'Verificado' : 'Pendiente'))
const emailStatusClass = computed(() => (isVerified.value ? 'text-success' : 'text-warning'))

const completeOnboarding = () => {
  router.put('/member/onboarding/complete', {}, {
    preserveScroll: true,
  })
}

const formatActivityDate = (value) => {
  if (!value) return '-'
  const parsed = new Date(value)
  if (Number.isNaN(parsed.getTime())) {
    return value
  }
  return parsed.toLocaleString()
}
</script>
