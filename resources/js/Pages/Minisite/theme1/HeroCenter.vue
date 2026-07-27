<template>
  <div class="hero hero--center" :style="backgroundStyle">
    <div class="hero__inner">
      <div class="hero__content text-center">
        <img v-if="business.logo" :src="business.logo" :alt="business.name" class="hero__logo mx-auto" />
        <h1 class="hero__title">{{ title || business.name }}</h1>
        <p v-if="subtitle" class="hero__subtitle">{{ subtitle }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  business: Object,
  title: String,
  subtitle: String,
  backgroundImage: String,
})

const backgroundStyle = computed(() => {
  if (props.backgroundImage) {
    return {
      '--hero-bg': `url(${props.backgroundImage})`,
    }
  }
  return {}
})
</script>

<style lang="less">
.hero {
  background-color: #f8f9fa;
  background-image: var(--hero-bg, none);
  background-size: cover;
  background-position: center;

  &--center {
    .hero__inner {
      padding: 48px 16px;
      max-width: 600px;
      margin: 0 auto;
    }

    .hero__logo {
      width: 80px;
      height: 80px;
      object-fit: contain;
      border-radius: 50%;
      margin-bottom: 16px;
    }

    .hero__title {
      font-size: 1.75rem;
      font-weight: 700;
      margin: 0 0 8px;
      color: #212529;
    }

    .hero__subtitle {
      font-size: 1rem;
      margin: 0;
      color: #6c757d;
    }
  }
}
</style>
