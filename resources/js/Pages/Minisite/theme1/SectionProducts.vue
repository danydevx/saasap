<template>
  <section class="section-products">
    <div class="section-products__inner">
      <h2 v-if="title" class="section-products__title">{{ title }}</h2>

      <div v-if="items.length === 0" class="text-muted text-center py-4">
        No hay productos disponibles.
      </div>

      <div v-else-if="viewMode === 'carousel'" class="section-products__carousel">
        <swiper
          :modules="modules"
          :slides-per-view="'auto'"
          :space-between="16"
          :grab-cursor="true"
          :pagination="false"
          :navigation="false"
          class="section-products__swiper"
        >
          <swiper-slide
            v-for="item in items"
            :key="item.id"
            class="section-products__item"
          >
            <a
              v-if="showImage && item.image"
              :href="item.image"
              class="section-products__image-wrapper glightbox"
              data-gallery="minisite-products"
              :data-title="item.name"
            >
              <img :src="item.image" :alt="item.name" class="section-products__image" loading="lazy" />
              <span v-if="item.compare_at_price && showComparePrice" class="section-products__badge">
                -{{ discountPercent(item) }}%
              </span>
            </a>
            <div v-else-if="showImage && item.image" class="section-products__image-wrapper">
              <img :src="item.image" :alt="item.name" class="section-products__image" />
              <span v-if="item.compare_at_price && showComparePrice" class="section-products__badge">
                -{{ discountPercent(item) }}%
              </span>
            </div>
            <div class="section-products__content">
              <h3 class="section-products__name">{{ item.name }}</h3>
              <p v-if="item.description" class="section-products__desc">
                {{ item.description }}
              </p>
              <div class="section-products__price">
                <span v-if="showPrice && item.price" class="section-products__price-current">
                  ${{ item.price }}
                </span>
                <span v-if="showComparePrice && item.compare_at_price" class="section-products__price-compare">
                  ${{ item.compare_at_price }}
                </span>
              </div>
            </div>
          </swiper-slide>
        </swiper>
      </div>

      <div v-else class="section-products__grid">
        <div
          v-for="item in items"
          :key="item.id"
          class="section-products__item"
        >
          <a
            v-if="showImage && item.image"
            :href="item.image"
            class="section-products__image-wrapper glightbox"
            data-gallery="minisite-products"
            :data-title="item.name"
          >
            <img :src="item.image" :alt="item.name" class="section-products__image" loading="lazy" />
            <span v-if="item.compare_at_price && showComparePrice" class="section-products__badge">
              -{{ discountPercent(item) }}%
            </span>
          </a>
          <div v-else-if="showImage && item.image" class="section-products__image-wrapper">
            <img :src="item.image" :alt="item.name" class="section-products__image" />
            <span v-if="item.compare_at_price && showComparePrice" class="section-products__badge">
              -{{ discountPercent(item) }}%
            </span>
          </div>
          <div class="section-products__content">
            <h3 class="section-products__name">{{ item.name }}</h3>
            <p v-if="item.description" class="section-products__desc">
              {{ item.description }}
            </p>
            <div class="section-products__price">
              <span v-if="showPrice && item.price" class="section-products__price-current">
                ${{ item.price }}
              </span>
              <span v-if="showComparePrice && item.compare_at_price" class="section-products__price-compare">
                ${{ item.compare_at_price }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <div v-if="buttons && buttons.length" class="section-products__buttons">
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
import { computed, onMounted, nextTick } from 'vue'
import { Swiper, SwiperSlide } from 'swiper/vue'
import { FreeMode } from 'swiper/modules'
import GLightbox from 'glightbox'
import 'swiper/css'
import 'swiper/css/free-mode'
import 'glightbox/dist/css/glightbox.min.css'

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

const modules = [FreeMode]

let lightbox = null

onMounted(() => {
  nextTick(() => {
    if (lightbox) {
      lightbox.destroy()
    }
    lightbox = GLightbox({
      touchNavigation: true,
      loop: true,
      autoplayVideos: false,
      selector: '.glightbox',
    })
  })
})

const showImage = computed(() => props.config?.show_image !== false)
const showPrice = computed(() => props.config?.show_price !== false)
const showComparePrice = computed(() => props.config?.show_compare_price !== false)
const viewMode = computed(() => props.config?.view_mode || 'grid')

const discountPercent = (item) => {
  if (!item.compare_at_price || !item.price) return 0
  return Math.round((1 - item.price / item.compare_at_price) * 100)
}
</script>

<script>
import { defineComponent } from 'vue'
export default defineComponent({ name: 'SectionProducts' })
</script>

<style lang="less">
.section-products {
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

  &__grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
    gap: 16px;
  }

  &__carousel {
    padding-bottom: 16px;
  }

  &__swiper {
    width: 100%;
    padding: 0 0 16px 0;

    .swiper-slide {
      width: 160px;
    }
  }

  &__item {
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    height: auto;
  }

  &__image-wrapper {
    position: relative;
    width: 100%;
    height: 120px;
    overflow: hidden;
    display: block;

    &.glightbox {
      cursor: zoom-in;
    }
  }

  &__image {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  &__badge {
    position: absolute;
    top: 8px;
    right: 8px;
    background: #dc3545;
    color: #fff;
    font-size: 0.75rem;
    font-weight: 700;
    padding: 2px 6px;
    border-radius: 4px;
  }

  &__content {
    padding: 12px;
  }

  &__name {
    font-size: 0.875rem;
    font-weight: 600;
    margin: 0 0 4px;
    color: #212529;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  &__desc {
    font-size: 0.75rem;
    color: #6c757d;
    margin: 0 0 8px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }

  &__price {
    display: flex;
    align-items: center;
    gap: 8px;
  }

  &__price-current {
    font-size: 1rem;
    font-weight: 700;
    color: #0d6efd;
  }

  &__price-compare {
    font-size: 0.875rem;
    color: #6c757d;
    text-decoration: line-through;
  }

  &__buttons {
    display: flex;
    justify-content: center;
    gap: 8px;
    margin-top: 24px;
  }
}
</style>
