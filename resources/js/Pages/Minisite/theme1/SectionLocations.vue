<template>
  <section class="section-locations">
    <div class="section-locations__inner">
      <h2 v-if="title" class="section-locations__title">{{ title }}</h2>

      <div v-if="items.length === 0" class="text-muted text-center py-4">
        No hay ubicaciones disponibles.
      </div>

      <div v-else class="section-locations__list">
        <div
          v-for="item in items"
          :key="item.id"
          class="section-locations__item"
        >
          <div class="section-locations__icon">
            <i class="bi bi-geo-alt-fill"></i>
          </div>
          <div class="section-locations__content">
            <h3 class="section-locations__name">{{ item.name }}</h3>
            <p v-if="showAddress && item.full_address" class="section-locations__address">
              {{ item.full_address }}
            </p>
            <div class="section-locations__contact">
              <a v-if="showPhone && item.phone" :href="'tel:' + item.phone" class="section-locations__contact-item">
                <i class="bi bi-telephone"></i> {{ item.phone }}
              </a>
              <a v-if="showEmail && item.email" :href="'mailto:' + item.email" class="section-locations__contact-item">
                <i class="bi bi-envelope"></i> {{ item.email }}
              </a>
              <a v-if="item.directions_url" :href="item.directions_url" target="_blank" class="section-locations__contact-item">
                <i class="bi bi-signpost"></i> Cómo llegar
              </a>
            </div>
          </div>
        </div>
      </div>

      <div v-if="buttons && buttons.length" class="section-locations__buttons">
        <a
          v-for="(btn, index) in buttons"
          :key="index"
          :href="btn.url"
          class="btn"
          :class="'btn-' + (btn.style || 'primary')"
        >
          {{ btn.text }}
        </a>
      </div>
    </div>
  </section>
</template>

<script setup>
const props = defineProps({
  title: String,
  items: {
    type: Array,
    default: () => [],
  },
  config: {
    type: Object,
    default: () => ({}),
  },
  description: String,
  buttons: {
    type: Array,
    default: () => [],
  },
})

const showAddress = computed(() => props.config?.show_address !== false)
const showPhone = computed(() => props.config?.show_phone !== false)
const showEmail = computed(() => props.config?.show_email !== false)
const showHours = computed(() => props.config?.show_hours !== false)
</script>

<script>
import { computed, defineComponent } from 'vue'
export default defineComponent({ name: 'SectionLocations' })
</script>

<style lang="less">
.section-locations {
  padding: 48px 16px;

  &__inner {
    max-width: 600px;
    margin: 0 auto;
  }

  &__title {
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0 0 24px;
    text-align: center;
    color: #212529;
  }

  &__list {
    display: flex;
    flex-direction: column;
    gap: 16px;
  }

  &__item {
    display: flex;
    gap: 16px;
    padding: 16px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  }

  &__icon {
    flex-shrink: 0;
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #e7f1ff;
    border-radius: 50%;
    color: #0d6efd;
    font-size: 1.25rem;
  }

  &__content {
    flex: 1;
    min-width: 0;
  }

  &__name {
    font-size: 1rem;
    font-weight: 600;
    margin: 0 0 8px;
    color: #212529;
  }

  &__address {
    font-size: 0.875rem;
    color: #6c757d;
    margin: 0 0 8px;
  }

  &__contact {
    display: flex;
    flex-direction: column;
    gap: 4px;
    margin-bottom: 8px;
  }

  &__contact-item {
    font-size: 0.875rem;
    color: #0d6efd;
    text-decoration: none;

    &:hover {
      text-decoration: underline;
    }

    i {
      margin-right: 4px;
    }
  }

  &__hours {
    font-size: 0.875rem;
    color: #6c757d;

    i {
      margin-right: 4px;
    }
  }

  &__buttons {
    display: flex;
    justify-content: center;
    gap: 8px;
    margin-top: 24px;
  }
}
</style>
