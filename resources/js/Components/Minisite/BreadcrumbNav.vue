<template>
  <nav class="breadcrumb-nav" aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item">
        <Link :href="`/m/${baseSlug}`">Inicio</Link>
      </li>
      <li v-if="parentHref" class="breadcrumb-item">
        <Link :href="parentHref">{{ parentLabel }}</Link>
      </li>
      <li class="breadcrumb-item active" aria-current="page">{{ currentLabel }}</li>
    </ol>
  </nav>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

const props = defineProps({
  baseSlug: {
    type: String,
    required: true,
  },
  parentHref: {
    type: String,
    default: null,
  },
  parentLabel: {
    type: String,
    default: '',
  },
  currentLabel: {
    type: String,
    required: true,
  },
})
</script>

<style lang="less">
.breadcrumb-nav {
  padding: 16px 0;
}

.breadcrumb {
  margin: 0;
  padding: 0;
  background: transparent;
  font-size: 0.875rem;

  &-item {
    a {
      color: #6c757d;
      text-decoration: none;

      &:hover {
        color: #0d6efd;
      }
    }

    &.active {
      color: #495057;
    }

    & + .breadcrumb-item::before {
      content: "/";
      color: #adb5bd;
      padding: 0 4px;
    }
  }
}
</style>
