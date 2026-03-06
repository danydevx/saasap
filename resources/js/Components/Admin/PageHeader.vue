<template>
  <div class="d-flex flex-wrap gap-3 align-items-start justify-content-between mb-4">
    <div>
      <h1 class="h4 mb-1">{{ title }}</h1>
      <Breeadcrumbs :items="breadcrumbs" />
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
import Breeadcrumbs from '@/Components/Admin/Breeadcrumbs.vue'
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
