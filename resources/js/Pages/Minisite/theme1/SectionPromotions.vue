<template>
  <section class="section-promotions">
    <div class="section-promotions__inner">
      <h2 v-if="title" class="section-promotions__title">{{ title }}</h2>

      <div v-if="items.length === 0" class="text-muted text-center py-4">
        No hay promociones disponibles.
      </div>

      <div v-else class="section-promotions__list">
        <div
          v-for="item in items"
          :key="item.id"
          class="section-promotions__item"
        >
          <div class="section-promotions__item-header">
            <h3 class="section-promotions__item-title">{{ item.title }}</h3>
            <span v-if="item.discount" class="section-promotions__item-discount">
              -{{ item.discount }}%
            </span>
          </div>
          <p v-if="item.description" class="section-promotions__item-desc">
            {{ item.description }}
          </p>
          <p v-if="item.valid_until" class="section-promotions__item-valid">
            Válido hasta: {{ formatDate(item.valid_until) }}
          </p>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
defineProps({
  title: String,
  items: {
    type: Array,
    default: () => [],
  },
  config: {
    type: Object,
    default: () => ({}),
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
</script>

<style lang="less">
.section-promotions {
  padding: 48px 16px;
  background: #f8f9fa;

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
    background: #fff;
    border-radius: 8px;
    padding: 16px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
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

  &__item-valid {
    font-size: 0.75rem;
    color: #adb5bd;
    margin: 0;
  }
}
</style>
