<template>
  <section class="section-reviews">
    <div class="section-reviews__inner">
      <h2 v-if="title" class="section-reviews__title">{{ title }}</h2>

      <div v-if="items.length === 0" class="text-muted text-center py-4">
        No hay reseñas disponibles.
      </div>

      <div v-else class="section-reviews__grid">
        <div
          v-for="review in items"
          :key="review.id"
          class="section-reviews__card"
        >
          <div class="section-reviews__stars">
            <i
              v-for="star in 5"
              :key="star"
              class="bi"
              :class="star <= review.rating ? 'bi-star-fill' : 'bi-star'"
            ></i>
          </div>

          <p v-if="showComment && review.comment" class="section-reviews__comment">
            "{{ review.comment }}"
          </p>

          <div class="section-reviews__author">
            <strong v-if="showClientName">{{ review.client_name }}</strong>
            <span v-if="review.company"> - {{ review.company }}</span>
          </div>

          <a
            v-if="review.google_link"
            :href="review.google_link"
            target="_blank"
            class="section-reviews__google-link"
          >
            <i class="bi bi-google"></i> Ver en Google
          </a>
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
  config: Object,
})

const showComment = computed(() => {
  return props.config?.show_comment !== false
})

const showClientName = computed(() => {
  return props.config?.show_client_name !== false
})
</script>

<script>
import { computed } from 'vue'
export default {
  name: 'SectionReviews'
}
</script>

<style lang="less">
.section-reviews {
  padding: 48px 16px;
  background: #fff;

  &__inner {
    max-width: 1024px;
    margin: 0 auto;
  }

  &__title {
    font-size: 1.75rem;
    font-weight: 700;
    margin: 0 0 32px;
    text-align: center;
    color: #212529;
  }

  &__grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 24px;
  }

  &__card {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 24px;
    display: flex;
    flex-direction: column;
    gap: 12px;
  }

  &__stars {
    color: #ffc107;
    font-size: 1.25rem;
  }

  &__comment {
    font-size: 0.95rem;
    color: #495057;
    line-height: 1.6;
    margin: 0;
    font-style: italic;
  }

  &__author {
    font-size: 0.9rem;
    color: #212529;

    strong {
      color: #212529;
    }
  }

  &__google-link {
    font-size: 0.85rem;
    color: #4285f4;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    margin-top: auto;

    &:hover {
      text-decoration: underline;
    }
  }
}
</style>
