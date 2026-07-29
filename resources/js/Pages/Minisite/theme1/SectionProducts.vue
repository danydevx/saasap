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
            <div v-else class="section-products__image-placeholder">
              <i class="bi bi-image"></i>
            </div>
            <div class="section-products__content">
              <h3 class="section-products__name">{{ item.name }}</h3>
              <p v-if="item.description" class="section-products__desc">
                {{ item.description }}
              </p>
              <div class="section-products__price">
                <span v-if="showPrice && item.price" class="section-products__price-current">
                  {{ formatCurrency(item.price) }}
                </span>
                <span v-if="showComparePrice && item.compare_at_price" class="section-products__price-compare">
                  {{ formatCurrency(item.compare_at_price) }}
                </span>
              </div>
              <div v-if="showStock && item.quantity !== null" class="section-products__stock">
                <span v-if="item.quantity > 0" class="badge bg-success">
                  <i class="bi bi-check-circle me-1"></i>En stock
                </span>
                <span v-else class="badge bg-danger">
                  <i class="bi bi-x-circle me-1"></i>Agotado
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
          class="section-products__card"
          @click="openProductModal(item)"
        >
          <div class="section-products__card-image-wrapper">
            <a
              v-if="showImage && item.image"
              :href="item.image"
              class="section-products__image-wrapper glightbox"
              data-gallery="minisite-products"
              :data-title="item.name"
              @click.stop
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
            <div v-else class="section-products__image-placeholder">
              <i class="bi bi-image"></i>
            </div>
            <button class="section-products__quick-view-btn" @click.stop="openProductModal(item)">
              <i class="bi bi-eye"></i>
            </button>
          </div>
          <div class="section-products__card-body">
            <h3 class="section-products__name">{{ item.name }}</h3>
            <p v-if="item.description" class="section-products__desc">
              {{ truncateText(item.description, 80) }}
            </p>
            <div class="section-products__price">
              <span v-if="showPrice && item.price" class="section-products__price-current">
                {{ formatCurrency(item.price) }}
              </span>
              <span v-if="showComparePrice && item.compare_at_price" class="section-products__price-compare">
                {{ formatCurrency(item.compare_at_price) }}
              </span>
            </div>
            <div v-if="showStock && item.quantity !== null" class="section-products__stock">
              <span v-if="item.quantity > 0" class="badge bg-success">
                <i class="bi bi-check-circle me-1"></i>En stock
              </span>
              <span v-else class="badge bg-danger">
                <i class="bi bi-x-circle me-1"></i>Agotado
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

    <div v-if="selectedProduct" class="product-modal" @click="closeProductModal">
      <div class="product-modal__content" @click.stop>
        <button class="product-modal__close" @click="closeProductModal">
          <i class="bi bi-x-lg"></i>
        </button>
        <div class="product-modal__gallery">
          <div v-if="selectedProduct.image" class="product-modal__main-image">
            <img :src="selectedProduct.image" :alt="selectedProduct.name" />
          </div>
          <div v-if="selectedProduct.gallery && selectedProduct.gallery.length > 1" class="product-modal__thumbs">
            <a
              v-for="(img, idx) in selectedProduct.gallery"
              :key="img.id"
              :href="img.path"
              class="product-modal__thumb glightbox"
              data-gallery="product-gallery"
              :data-title="selectedProduct.name"
            >
              <img :src="img.path" :alt="img.title || selectedProduct.name" />
            </a>
          </div>
        </div>
        <div class="product-modal__info">
          <h2 class="product-modal__name">{{ selectedProduct.name }}</h2>
          <div class="product-modal__prices">
            <span v-if="selectedProduct.price" class="product-modal__price">
              {{ formatCurrency(selectedProduct.price) }}
            </span>
            <span v-if="selectedProduct.compare_at_price" class="product-modal__price-compare">
              {{ formatCurrency(selectedProduct.compare_at_price) }}
            </span>
            <span v-if="selectedProduct.compare_at_price" class="product-modal__discount">
              -{{ discountPercent(selectedProduct) }}% OFF
            </span>
          </div>
          <div v-if="selectedProduct.sku" class="product-modal__sku">
            <strong>SKU:</strong> {{ selectedProduct.sku }}
          </div>
          <div v-if="selectedProduct.barcode" class="product-modal__barcode">
            <strong>EAN:</strong> {{ selectedProduct.barcode }}
          </div>
          <div v-if="selectedProduct.quantity !== null" class="product-modal__stock">
            <span v-if="selectedProduct.quantity > 0" class="badge bg-success">
              <i class="bi bi-check-circle me-1"></i>En stock ({{ selectedProduct.quantity }})
            </span>
            <span v-else class="badge bg-danger">
              <i class="bi bi-x-circle me-1"></i>Agotado
            </span>
          </div>
          <p v-if="selectedProduct.description" class="product-modal__description">
            {{ selectedProduct.description }}
          </p>
          <div class="product-modal__actions">
            <a
              v-if="selectedProduct.whatsapp_contact"
              :href="`https://wa.me/${selectedProduct.whatsapp_contact}?text=Hola, me interesa el producto: ${selectedProduct.name}`"
              target="_blank"
              class="btn btn-success"
            >
              <i class="bi bi-whatsapp me-2"></i>Contactar por WhatsApp
            </a>
            <a
              :href="`/m/${businessSlug}/productos/${selectedProduct.slug}`"
              class="btn btn-outline-primary"
            >
              <i class="bi bi-eye me-2"></i>Ver detalles completos
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed, onMounted, nextTick, ref } from 'vue'
import { Swiper, SwiperSlide } from 'swiper/vue'
import { FreeMode } from 'swiper/modules'
import GLightbox from 'glightbox'
import { usePriceFormatter } from '@/Composables/usePriceFormatter'
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
  businessSlug: {
    type: String,
    default: '',
  },
})

const modules = [FreeMode]
const selectedProduct = ref(null)

const { formatPrice } = usePriceFormatter({
  locale: 'es-MX',
  currency: '$',
  decimals: 2,
})

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
const showStock = computed(() => props.config?.show_stock !== false)
const viewMode = computed(() => props.config?.view_mode || 'grid')

const discountPercent = (item) => {
  if (!item.compare_at_price || !item.price) return 0
  return Math.round((1 - item.price / item.compare_at_price) * 100)
}

const truncateText = (text, length) => {
  if (!text || text.length <= length) return text
  return text.substring(0, length) + '...'
}

const openProductModal = (item) => {
  selectedProduct.value = item
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
}

const closeProductModal = () => {
  selectedProduct.value = null
}

const formatCurrency = (value) => {
  if (value === null || value === undefined) return ''
  return formatPrice(value) || ''
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
    gap: 16px;

    @media (max-width: 768px) {
      grid-template-columns: repeat(2, 1fr);
    }

    @media (max-width: 480px) {
      grid-template-columns: 1fr;
    }
  }

  &__carousel {
    padding-bottom: 16px;
  }

  &__swiper {
    width: 100%;
    padding: 0 0 16px 0;

    .swiper-slide {
      width: 200px;
    }
  }

  &__card {
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s, box-shadow 0.2s;
    cursor: pointer;

    &:hover {
      transform: translateY(-4px);
      box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);

      .section-products__quick-view-btn {
        opacity: 1;
      }
    }

    &-image-wrapper {
      position: relative;
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
    height: 160px;
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
    transition: transform 0.3s;

    .section-products__image-wrapper:hover & {
      transform: scale(1.05);
    }
  }

  &__image-placeholder {
    width: 100%;
    height: 160px;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #adb5bd;
    font-size: 3rem;
  }

  &__badge {
    position: absolute;
    top: 8px;
    right: 8px;
    background: #dc3545;
    color: #fff;
    font-size: 0.75rem;
    font-weight: 700;
    padding: 4px 8px;
    border-radius: 4px;
  }

  &__quick-view-btn {
    position: absolute;
    bottom: 8px;
    right: 8px;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.9);
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.2s;
    cursor: pointer;
    color: #495057;

    &:hover {
      background: #fff;
      color: #0d6efd;
    }
  }

  &__content {
    padding: 16px;
  }

  &__name {
    font-size: 1rem;
    font-weight: 600;
    margin: 0 0 8px;
    color: #212529;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  &__desc {
    font-size: 0.875rem;
    color: #6c757d;
    margin: 0 0 12px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    line-height: 1.4;
  }

  &__price {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
  }

  &__price-current {
    font-size: 1.25rem;
    font-weight: 700;
    color: #198754;
  }

  &__price-compare {
    font-size: 0.875rem;
    color: #6c757d;
    text-decoration: line-through;
  }

  &__stock {
    margin-top: 8px;
  }

  &__buttons {
    display: flex;
    justify-content: center;
    gap: 8px;
    margin-top: 24px;
  }
}

.product-modal {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.7);
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 16px;
  overflow-y: auto;

  &__content {
    background: #fff;
    border-radius: 16px;
    max-width: 800px;
    width: 100%;
    max-height: 90vh;
    overflow-y: auto;
    position: relative;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0;

    @media (max-width: 768px) {
      grid-template-columns: 1fr;
    }
  }

  &__close {
    position: absolute;
    top: 12px;
    right: 12px;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.9);
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 10;
    color: #495057;
    transition: all 0.2s;

    &:hover {
      background: #fff;
      color: #dc3545;
    }
  }

  &__gallery {
    padding: 24px;
    background: #f8f9fa;
  }

  &__main-image {
    width: 100%;
    height: 300px;
    border-radius: 8px;
    overflow: hidden;
    margin-bottom: 12px;

    img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
  }

  &__thumbs {
    display: flex;
    gap: 8px;
    overflow-x: auto;
    padding-bottom: 8px;
  }

  &__thumb {
    flex: 0 0 60px;
    height: 60px;
    border-radius: 4px;
    overflow: hidden;
    display: block;

    img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    &:hover {
      opacity: 0.8;
    }
  }

  &__info {
    padding: 24px;
  }

  &__name {
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0 0 16px;
    color: #212529;
  }

  &__prices {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
    margin-bottom: 16px;
  }

  &__price {
    font-size: 1.75rem;
    font-weight: 700;
    color: #198754;
  }

  &__price-compare {
    font-size: 1.125rem;
    color: #6c757d;
    text-decoration: line-through;
  }

  &__discount {
    background: #dc3545;
    color: #fff;
    font-size: 0.875rem;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 4px;
  }

  &__sku,
  &__barcode {
    font-size: 0.875rem;
    color: #6c757d;
    margin-bottom: 8px;

    strong {
      color: #495057;
    }
  }

  &__stock {
    margin-bottom: 16px;
  }

  &__description {
    font-size: 0.9375rem;
    color: #495057;
    line-height: 1.6;
    margin-bottom: 24px;
  }

  &__actions {
    display: flex;
    flex-direction: column;
    gap: 8px;

    .btn {
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 12px 24px;
      font-size: 1rem;
      font-weight: 600;
    }
  }
}
</style>
