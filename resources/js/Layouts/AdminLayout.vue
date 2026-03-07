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
            <li v-if="canViewPayments" class="nav-item">
              <Link href="/admin/payments" class="nav-link" prefetch="hover">Pagos</Link>
            </li>
            <li v-if="canViewSettings" class="nav-item">
              <Link href="/admin/settings" class="nav-link" prefetch="hover">Settings</Link>
            </li>
            <li v-if="canViewActivity" class="nav-item">
              <Link href="/admin/activity" class="nav-link" prefetch="hover">Actividad</Link>
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
              <li v-if="canViewPayments">
                <Link href="/admin/payments" class="dropdown-item" prefetch="hover">Pagos</Link>
              </li>
              <li v-if="canViewActivity">
                <Link href="/admin/activity" class="dropdown-item" prefetch="hover">Actividad</Link>
              </li>
              <li v-if="canViewSettings || canViewActivity || canViewPayments || canViewCoupons || canViewInvoices || canViewSupport || canViewHelp"><hr class="dropdown-divider" /></li>
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
</script>
