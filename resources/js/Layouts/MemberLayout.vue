<template>
  <div>
    <nav class="navbar navbar-expand-lg bg-body-tertiary border-bottom">
      <div class="container-fluid">
        <Link href="/member" class="navbar-brand">Mi SaaS</Link>

        <div class="ms-auto d-flex align-items-center gap-2">
          <Link href="/member/account" class="btn btn-outline-secondary btn-sm">Cuenta</Link>
          <Link href="/member/payments" class="btn btn-outline-secondary btn-sm">Pagos</Link>
          <Link href="/member/invoices" class="btn btn-outline-secondary btn-sm">Comprobantes</Link>
          <Link v-if="canSupport" href="/member/support" class="btn btn-outline-secondary btn-sm">Soporte</Link>
          <Link href="/member/help" class="btn btn-outline-secondary btn-sm">Ayuda</Link>
          <Link href="/member/preferences" class="btn btn-outline-secondary btn-sm">Preferencias</Link>
          <Link href="/member/notification-preferences" class="btn btn-outline-secondary btn-sm">Notif. preferencias</Link>
          <Link v-if="canUseApi" href="/member/api-keys" class="btn btn-outline-secondary btn-sm">API Keys</Link>
          <Link v-if="canUseWebhooks" href="/member/webhooks" class="btn btn-outline-secondary btn-sm">Webhooks</Link>
          <Link href="/member/sessions" class="btn btn-outline-secondary btn-sm">Sesiones</Link>
          <Link href="/member/files" class="btn btn-outline-secondary btn-sm">Archivos</Link>
          <Link href="/member/notifications" class="btn btn-outline-secondary btn-sm">
            Notificaciones
            <span v-if="unreadCount > 0" class="badge text-bg-primary ms-1">
              {{ unreadCount }}
            </span>
          </Link>
          <Link href="/member/activity" class="btn btn-outline-secondary btn-sm">Actividad</Link>
          <div class="dropdown">
            <button
              class="btn btn-outline-secondary btn-sm dropdown-toggle"
              type="button"
              data-bs-toggle="dropdown"
              aria-expanded="false"
            >
              <i class="bi bi-person"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
              <li>
                <Link href="/member/profile" class="dropdown-item" prefetch="hover">Perfil</Link>
              </li>
              <li>
                <Link href="/member/password" class="dropdown-item" prefetch="hover">Cambiar password</Link>
              </li>
              <li><hr class="dropdown-divider" /></li>
              <li>
                <Link href="/logout" method="post" as="button" class="dropdown-item">
                  Salir
                </Link>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </nav>

    <main class="py-4">
      <div class="container">
        <slot />
      </div>
    </main>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

const page = usePage()
const unreadCount = computed(() => page.props.notificationUnreadCount || 0)
const features = computed(() => page.props.features || {})

const canUseApi = computed(() => features.value.can_use_api !== false)
const canUseWebhooks = computed(() => features.value.can_use_webhooks !== false)
const canSupport = computed(() => features.value.module_support !== false)
</script>
