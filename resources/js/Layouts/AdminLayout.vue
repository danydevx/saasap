<template>
  <div>
    <nav class="navbar navbar-expand-lg bg-dark border-bottom" data-bs-theme="dark">
      <div class="container-fluid">
        <a class="navbar-brand" href="#">Mi SaaS</a>

        <button
          class="navbar-toggler"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#mainNav"
        >
          <span class="navbar-toggler-icon"></span>
        </button>

        <div id="mainNav" class="collapse navbar-collapse">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item">
              <Link href="/dashboard" class="nav-link" prefetch="hover">Dashboard</Link>
            </li>
            <li class="nav-item">
              <Link href="/admin/users" class="nav-link" prefetch="hover">Usuarios</Link>
            </li>
            <li class="nav-item">
              <Link href="/admin/roles" class="nav-link" prefetch="hover">Roles</Link>
            </li>
            <li class="nav-item">
              <Link href="/admin/permissions" class="nav-link" prefetch="hover">Permisos</Link>
            </li>
            <li v-if="canViewPlans" class="nav-item">
              <Link href="/admin/plans" class="nav-link" prefetch="hover">Planes</Link>
            </li>
            <li v-if="canViewCoupons" class="nav-item">
              <Link href="/admin/coupons" class="nav-link" prefetch="hover">Cupones</Link>
            </li>
            <li v-if="canViewInvoices" class="nav-item">
              <Link href="/admin/invoices" class="nav-link" prefetch="hover">Comprobantes</Link>
            </li>
            <li v-if="canViewSupport" class="nav-item">
              <Link href="/admin/support" class="nav-link" prefetch="hover">Soporte</Link>
            </li>
            <li v-if="canViewHelp" class="nav-item">
              <Link href="/admin/help" class="nav-link" prefetch="hover">Ayuda</Link>
            </li>
            <li v-if="canViewReports" class="nav-item">
              <Link href="/admin/reports" class="nav-link" prefetch="hover">Reportes</Link>
            </li>
            <li v-if="canViewExports" class="nav-item">
              <Link href="/admin/exports" class="nav-link" prefetch="hover">Exportaciones</Link>
            </li>
            <li v-if="canViewPayments" class="nav-item">
              <Link href="/admin/payments" class="nav-link" prefetch="hover">Pagos</Link>
            </li>
            <li v-if="canViewSettings" class="nav-item">
              <Link href="/admin/settings" class="nav-link" prefetch="hover">Settings</Link>
            </li>
            <li v-if="canViewActivity" class="nav-item">
              <Link href="/admin/activity" class="nav-link" prefetch="hover">Actividad</Link>
            </li>
            <li v-if="canViewSystemErrors" class="nav-item">
              <Link href="/admin/system-errors" class="nav-link" prefetch="hover">Errores</Link>
            </li>
            <li v-if="canViewApiKeys" class="nav-item">
              <Link href="/admin/api-keys" class="nav-link" prefetch="hover">API Keys</Link>
            </li>
            <li v-if="canViewWebhooks" class="nav-item">
              <Link href="/admin/webhooks" class="nav-link" prefetch="hover">Webhooks</Link>
            </li>
            <li v-if="canViewQueues" class="nav-item">
              <Link href="/admin/queues" class="nav-link" prefetch="hover">Colas</Link>
            </li>
            <li v-if="canViewFeatureFlags" class="nav-item">
              <Link href="/admin/feature-flags" class="nav-link" prefetch="hover">Feature Flags</Link>
            </li>
          </ul>

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
                <Link href="/profile" class="dropdown-item" prefetch="hover">Perfil</Link>
              </li>
              <li><hr class="dropdown-divider" /></li>
              <li v-if="canViewSettings">
                <Link href="/admin/settings" class="dropdown-item" prefetch="hover">Settings</Link>
              </li>
              <li v-if="canViewCoupons">
                <Link href="/admin/coupons" class="dropdown-item" prefetch="hover">Cupones</Link>
              </li>
              <li v-if="canViewInvoices">
                <Link href="/admin/invoices" class="dropdown-item" prefetch="hover">Comprobantes</Link>
              </li>
              <li v-if="canViewSupport">
                <Link href="/admin/support" class="dropdown-item" prefetch="hover">Soporte</Link>
              </li>
              <li v-if="canViewHelp">
                <Link href="/admin/help" class="dropdown-item" prefetch="hover">Ayuda</Link>
              </li>
              <li v-if="canViewReports">
                <Link href="/admin/reports" class="dropdown-item" prefetch="hover">Reportes</Link>
              </li>
              <li v-if="canViewExports">
                <Link href="/admin/exports" class="dropdown-item" prefetch="hover">Exportaciones</Link>
              </li>
              <li v-if="canViewPayments">
                <Link href="/admin/payments" class="dropdown-item" prefetch="hover">Pagos</Link>
              </li>
              <li v-if="canViewActivity">
                <Link href="/admin/activity" class="dropdown-item" prefetch="hover">Actividad</Link>
              </li>
              <li v-if="canViewSystemErrors">
                <Link href="/admin/system-errors" class="dropdown-item" prefetch="hover">Errores</Link>
              </li>
              <li v-if="canViewApiKeys">
                <Link href="/admin/api-keys" class="dropdown-item" prefetch="hover">API Keys</Link>
              </li>
              <li v-if="canViewWebhooks">
                <Link href="/admin/webhooks" class="dropdown-item" prefetch="hover">Webhooks</Link>
              </li>
              <li v-if="canViewQueues">
                <Link href="/admin/queues" class="dropdown-item" prefetch="hover">Colas</Link>
              </li>
              <li v-if="canViewFeatureFlags">
                <Link href="/admin/feature-flags" class="dropdown-item" prefetch="hover">Feature Flags</Link>
              </li>
              <li v-if="canViewSettings || canViewActivity || canViewPayments || canViewCoupons || canViewInvoices || canViewSupport || canViewHelp || canViewReports || canViewExports || canViewSystemErrors || canViewApiKeys || canViewWebhooks || canViewQueues || canViewFeatureFlags"><hr class="dropdown-divider" /></li>
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
      <div class="container-fluid">
        <slot />
      </div>
    </main>

    <notifications position="top right" />

  </div>
</template>

<script setup>
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

const page = usePage()
const permissions = computed(() => page.props.auth?.permissions || [])
const userId = computed(() => page.props.auth?.user?.id)
const canViewSettings = computed(() => permissions.value.includes('settings.view') || userId.value === 1)
const canViewPlans = computed(() => permissions.value.includes('plans.view') || userId.value === 1)
const canViewActivity = computed(() => permissions.value.includes('activity.view') || userId.value === 1)
const canViewPayments = computed(() => permissions.value.includes('payments.view') || userId.value === 1)
const canViewCoupons = computed(() => permissions.value.includes('coupons.view') || userId.value === 1)
const canViewInvoices = computed(() => permissions.value.includes('invoices.view') || userId.value === 1)
const canViewSupport = computed(() => permissions.value.includes('support.view') || userId.value === 1)
const canViewHelp = computed(() => permissions.value.includes('help.view') || userId.value === 1)
const canViewReports = computed(() => permissions.value.includes('reports.view') || userId.value === 1)
const canViewExports = computed(() => permissions.value.includes('exports.view') || userId.value === 1)
const canViewSystemErrors = computed(() => permissions.value.includes('system-errors.view') || userId.value === 1)
const canViewApiKeys = computed(() => permissions.value.includes('api-keys.view') || userId.value === 1)
const canViewWebhooks = computed(() => permissions.value.includes('webhooks.view') || userId.value === 1)
const canViewQueues = computed(() => permissions.value.includes('queues.view') || userId.value === 1)
const canViewFeatureFlags = computed(() => permissions.value.includes('feature-flags.view') || userId.value === 1)
</script>
