<template>
  <div class="d-flex flex-wrap gap-3 align-items-start justify-content-between mb-4">
    <div>
      <h1 class="h4 mb-1">{{ title }}</h1>
      <nav v-if="breadcrumbs && breadcrumbs.length" aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
          <li v-for="(crumb, index) in breadcrumbs" :key="index" class="breadcrumb-item" :class="{ active: crumb.active }">
            <Link v-if="!crumb.active && crumb.href" :href="crumb.href">{{ crumb.label }}</Link>
            <span v-else>{{ crumb.label }}</span>
          </li>
        </ol>
      </nav>
    </div>

    <div v-if="backHref || $slots.actions" class="d-flex gap-2">
      <Link v-if="backHref" :href="backHref" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>
        {{ backLabel }}
      </Link>
      <slot name="actions" />
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
  title: {
    type: String,
    required: true,
  },
  breadcrumbs: {
    type: Array,
    default: () => [],
  },
  backHref: {
    type: String,
    default: '',
  },
  backLabel: {
    type: String,
    default: '',
  },
})
</script>
