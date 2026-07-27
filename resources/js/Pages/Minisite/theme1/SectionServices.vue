<template>
  <section class="section-services">
    <div class="section-services__inner">
      <h2 v-if="title" class="section-services__title">{{ title }}</h2>

      <div v-if="items.length === 0" class="text-muted text-center py-4">
        No hay servicios disponibles.
      </div>

      <div v-else-if="viewMode === 'carousel'" class="section-services__carousel">
        <div
          v-for="item in items"
          :key="item.id"
          class="section-services__card"
        >
          <img
            v-if="showImage && item.image"
            :src="item.image"
            :alt="item.name"
            class="section-services__card-image"
          />
          <div class="section-services__card-body">
            <h3 class="section-services__card-title">{{ item.name }}</h3>
            <p v-if="showDescription && item.description" class="section-services__card-desc">
              {{ item.description }}
            </p>
            <p v-if="showPrice && item.price" class="section-services__card-price">
              ${{ item.price }}
            </p>
          </div>
        </div>
      </div>

      <div v-else class="section-services__list">
        <div
          v-for="item in items"
          :key="item.id"
          class="section-services__list-item"
        >
          <img
            v-if="showImage && item.image"
            :src="item.image"
            :alt="item.name"
            class="section-services__list-image"
          />
          <div class="section-services__list-content">
            <h3 class="section-services__list-title">{{ item.name }}</h3>
            <p v-if="showDescription && item.description" class="section-services__list-desc">
              {{ item.description }}
            </p>
          </div>
          <div v-if="showPrice && item.price" class="section-services__list-price">
            ${{ item.price }}
          </div>
        </div>
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

const viewMode = computed(() => props.config?.view_mode || 'carousel')
const showImage = computed(() => props.config?.show_image !== false)
const showPrice = computed(() => props.config?.show_price !== false)
const showDescription = computed(() => props.config?.show_description === true)
</script>

<script>
import { computed, defineComponent } from 'vue'
export default defineComponent({ name: 'SectionServices' })
</script>

<style lang="less">
.section-services {
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

  &__carousel {
    display: flex;
    gap: 16px;
    overflow-x: auto;
    padding-bottom: 16px;
    scroll-snap-type: x mandatory;
    -webkit-overflow-scrolling: touch;

    &::-webkit-scrollbar {
      height: 4px;
    }

    &::-webkit-scrollbar-thumb {
      background: #dee2e6;
      border-radius: 2px;
    }
  }

  &__card {
    flex: 0 0 200px;
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    scroll-snap-align: start;
  }

  &__card-image {
    width: 100%;
    height: 120px;
    object-fit: cover;
  }

  &__card-body {
    padding: 12px;
  }

  &__card-title {
    font-size: 1rem;
    font-weight: 600;
    margin: 0 0 8px;
    color: #212529;
  }

  &__card-desc {
    font-size: 0.875rem;
    color: #6c757d;
    margin: 0 0 8px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }

  &__card-price {
    font-size: 1rem;
    font-weight: 700;
    color: #0d6efd;
    margin: 0;
  }

  &__list {
    display: flex;
    flex-direction: column;
    gap: 12px;
  }

  &__list-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  }

  &__list-image {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 4px;
  }

  &__list-content {
    flex: 1;
    min-width: 0;
  }

  &__list-title {
    font-size: 1rem;
    font-weight: 600;
    margin: 0 0 4px;
    color: #212529;
  }

  &__list-desc {
    font-size: 0.875rem;
    color: #6c757d;
    margin: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  &__list-price {
    font-size: 1rem;
    font-weight: 700;
    color: #0d6efd;
    white-space: nowrap;
  }
}
</style>
