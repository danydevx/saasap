<template>
  <section class="section-gallery">
    <div class="section-gallery__inner">
      <h2 v-if="title" class="section-gallery__title">{{ title }}</h2>

      <div v-if="items.length === 0" class="text-muted text-center py-4">
        No hay imágenes en la galería.
      </div>

      <div v-else class="section-gallery__grid">
        <a
          v-for="item in items"
          :key="item.id"
          :href="item.path"
          class="section-gallery__item glightbox"
          data-gallery="gallery"
          :data-title="item.title || 'Imagen'"
        >
          <img :src="item.path" :alt="item.title || 'Imagen'" class="section-gallery__image" loading="lazy" />
          <div v-if="item.title" class="section-gallery__overlay">
            <span>{{ item.title }}</span>
          </div>
        </a>
      </div>
    </div>
  </section>
</template>

<script setup>
import { onMounted, nextTick } from 'vue'
import GLightbox from 'glightbox'
import 'glightbox/dist/css/glightbox.min.css'

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

onMounted(() => {
  nextTick(() => {
    const lightbox = GLightbox({
      touchNavigation: true,
      loop: true,
      autoplayVideos: true,
      selector: '.section-gallery .glightbox',
    })
  })
})
</script>

<style lang="less">
.section-gallery {
  padding: 48px 16px;
  background: #fff;

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

  &__grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 8px;
  }

  &__item {
    position: relative;
    aspect-ratio: 1;
    overflow: hidden;
    border-radius: 4px;
    cursor: pointer;
    display: block;

    &:hover .section-gallery__overlay {
      opacity: 1;
    }

    &:hover .section-gallery__image {
      transform: scale(1.05);
    }
  }

  &__image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.2s ease;
  }

  &__overlay {
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.2s ease;

    span {
      color: #fff;
      font-size: 0.875rem;
      text-align: center;
      padding: 8px;
    }
  }
}
</style>
