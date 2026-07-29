<template>
  <section class="section-promotions">
    <div class="section-promotions__inner">
      <h2 v-if="title" class="section-promotions__title">{{ title }}</h2>

      <div v-if="items.length === 0" class="text-muted text-center py-4">
        No hay promociones disponibles.
      </div>

      <div v-else class="section-promotions__list">
        <div
          v-for="item in itemsWithDiscount"
          :key="item.id"
          class="section-promotions__item"
          @click="goToPromotion(item.slug)"
        >
          <div class="section-promotions__item-header">
            <h3 class="section-promotions__item-title">{{ item.title }}</h3>
            <span v-if="item.discountPercent" class="section-promotions__item-discount">
              -{{ item.discountPercent }}%
            </span>
          </div>
          <p v-if="item.description" class="section-promotions__item-desc">
            {{ truncateText(item.description, 100) }}
          </p>
          <div class="section-promotions__item-prices">
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
  </section>
</template>

<script setup>
import { computed } from 'vue'

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
  businessSlug: {
    type: String,
    default: '',
  },
})

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
