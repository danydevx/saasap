<template>
  <section class="section-promotions">
    <div class="section-promotions__inner">
      <h2 v-if="title" class="section-promotions__title">{{ title }}</h2>
      <p v-if="subtitle" class="section-promotions__subtitle">{{ subtitle }}</p>

      <div v-if="buttons && buttons.length" class="section-promotions__buttons">
        <a
          v-for="(btn, idx) in buttons"
          :key="idx"
          :href="btn.url || '#'"
          class="btn btn-primary me-2 mb-2"
          :target="btn.open_in_new_tab ? '_blank' : '_self'"
        >
          {{ btn.text }}
        </a>
      </div>

      <div v-if="items.length === 0" class="text-muted text-center py-4">
        No hay promociones disponibles.
      </div>

      <div v-else-if="viewMode === 'carousel'" class="section-promotions__carousel">
        <div
          v-for="item in itemsWithDiscount"
          :key="item.id"
          class="section-promotions__carousel-item"
          @click="goToPromotion(item.slug)"
        >
          <div v-if="showImage && item.image" class="section-promotions__item-image">
            <img :src="item.image" :alt="item.name" />
          </div>
          <div class="section-promotions__item-content">
            <div class="section-promotions__item-header">
              <h3 class="section-promotions__item-title">{{ item.name }}</h3>
              <span v-if="item.discountPercent" class="section-promotions__item-discount">
                -{{ item.discountPercent }}%
              </span>
            </div>
            <p v-if="showDescription && item.description" class="section-promotions__item-desc">
              {{ truncateText(item.description, 80) }}
            </p>
            <div v-if="showPrice" class="section-promotions__item-prices">
              <span v-if="item.regular_price" class="section-promotions__price-original">
                ${{ item.regular_price }}
              </span>
              <span v-if="item.promotion_price" class="section-promotions__price-promotion">
                ${{ item.promotion_price }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <div v-else class="section-promotions__list">
        <div
          v-for="item in itemsWithDiscount"
          :key="item.id"
          class="section-promotions__item"
          @click="goToPromotion(item.slug)"
        >
          <div v-if="showImage && item.image" class="section-promotions__item-image">
            <img :src="item.image" :alt="item.name" />
          </div>
          <div class="section-promotions__item-content">
            <div class="section-promotions__item-header">
              <h3 class="section-promotions__item-title">{{ item.name }}</h3>
              <span v-if="item.discountPercent" class="section-promotions__item-discount">
                -{{ item.discountPercent }}%
              </span>
            </div>
            <p v-if="showDescription && item.description" class="section-promotions__item-desc">
              {{ truncateText(item.description, 100) }}
            </p>
            <div v-if="showPrice" class="section-promotions__item-prices">
              <span v-if="item.regular_price" class="section-promotions__price-original">
                ${{ item.regular_price }}
              </span>
              <span v-if="item.promotion_price" class="section-promotions__price-promotion">
                ${{ item.promotion_price }}
              </span>
            </div>
            <p v-if="item.expires_at" class="section-promotions__item-valid">
              Valido hasta: {{ formatDate(item.expires_at) }}
            </p>
            <p v-if="item.coupon_code" class="section-promotions__item-coupon">
              Codigo: {{ item.coupon_code }}
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  title: String,
  subtitle: String,
  buttons: {
    type: Array,
    default: () => [],
  },
  items: {
    type: Array,
    default: () => [],
  },
  config: {
    type: Object,
    default: () => ({}),
  },
  businessSlug: {
    type: String,
    default: '',
  },
})

const viewMode = computed(() => props.config?.view_mode || 'list')
const showImage = computed(() => props.config?.show_image !== false)
const showPrice = computed(() => props.config?.show_price !== false)
const showDescription = computed(() => props.config?.show_description !== true)

const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('es-ES', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
  })
}

const truncateText = (text, length) => {
  if (!text || text.length <= length) return text
  return text.substring(0, length) + '...'
}

const itemsWithDiscount = computed(() => {
  return props.items.map(item => {
    let discountPercent = null
    if (item.regular_price && item.promotion_price && item.regular_price > item.promotion_price) {
      discountPercent = Math.round((1 - item.promotion_price / item.regular_price) * 100)
    }
    return { ...item, discountPercent }
  })
})

const goToPromotion = (slug) => {
  window.location.href = `/m/${props.businessSlug}/promociones/${slug}`
}
</script>

<style lang="less">
.section-promotions {
  padding: 48px 16px;
  background: #f8f9fa;

  &__inner {
    max-width: 1024px;
    margin: 0 auto;
  }

  &__title {
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0 0 8px;
    text-align: center;
    color: #212529;
  }

  &__subtitle {
    font-size: 1rem;
    color: #6c757d;
    text-align: center;
    margin: 0 0 16px;
  }

  &__buttons {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 24px;
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

  &__carousel-item {
    flex: 0 0 280px;
    scroll-snap-align: start;
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;

    &:hover {
      transform: translateY(-4px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
    }
  }

  &__item-image {
    width: 100%;
    height: 160px;
    overflow: hidden;

    img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
  }

  &__item-content {
    padding: 16px;
  }

  &__list {
    display: flex;
    flex-direction: column;
    gap: 16px;
  }

  &__item {
    background: #fff;
    border-radius: 8px;
    padding: 16px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;

    &:hover {
      transform: translateX(4px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
    }
  }

  &__item-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 12px;
    margin-bottom: 8px;
  }

  &__item-title {
    font-size: 1.125rem;
    font-weight: 600;
    margin: 0;
    color: #212529;
  }

  &__item-discount {
    background: #dc3545;
    color: #fff;
    font-size: 0.875rem;
    font-weight: 700;
    padding: 4px 8px;
    border-radius: 4px;
    white-space: nowrap;
  }

  &__item-desc {
    font-size: 0.875rem;
    color: #6c757d;
    margin: 0 0 8px;
  }

  &__item-prices {
    display: flex;
    gap: 12px;
    align-items: center;
    margin-bottom: 8px;
  }

  &__price-original {
    font-size: 0.875rem;
    color: #6c757d;
    text-decoration: line-through;
  }

  &__price-promotion {
    font-size: 1.125rem;
    font-weight: 700;
    color: #dc3545;
  }

  &__item-valid {
    font-size: 0.75rem;
    color: #adb5bd;
    margin: 0 0 4px;
  }

  &__item-coupon {
    font-size: 0.75rem;
    color: #0d6efd;
    margin: 0;
    font-weight: 600;
  }
}
</style>
