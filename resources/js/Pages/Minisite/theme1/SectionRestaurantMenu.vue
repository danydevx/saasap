<template>
  <section class="section-restaurant-menu">
    <div class="section-restaurant-menu__inner">
      <h2 v-if="title" class="section-restaurant-menu__title">{{ title }}</h2>

      <div v-if="items.length === 0" class="text-muted text-center py-4">
        No hay opciones disponibles en el menú.
      </div>

      <div v-else class="section-restaurant-menu__categories">
        <div
          v-for="category in items"
          :key="category.id"
          class="section-restaurant-menu__category"
        >
          <div v-if="category.title" class="section-restaurant-menu__category-header">
            <h3 class="section-restaurant-menu__category-title">{{ category.title }}</h3>
            <p v-if="category.description" class="section-restaurant-menu__category-desc">
              {{ category.description }}
            </p>
          </div>

          <div class="section-restaurant-menu__products">
            <div
              v-for="product in category.products"
              :key="product.id"
              class="section-restaurant-menu__product"
              @click="openProductModal(product)"
            >
              <div v-if="showImages && product.image" class="section-restaurant-menu__product-image">
                <img :src="product.image" :alt="product.title" loading="lazy" />
              </div>
              <div class="section-restaurant-menu__product-info">
                <h4 class="section-restaurant-menu__product-name">{{ product.title }}</h4>
                <p v-if="product.description" class="section-restaurant-menu__product-desc">
                  {{ truncateText(product.description, 80) }}
                </p>
                <div v-if="showPrices && product.price" class="section-restaurant-menu__product-price">
                  {{ formatCurrency(product.price) }}
                </div>
                <div v-if="product.has_variants" class="section-restaurant-menu__product-variants">
                  <span class="badge bg-secondary">
                    <i class="bi bi-list-ul me-1"></i>Con variantes
                  </span>
                </div>
              </div>
              <button class="section-restaurant-menu__product-btn">
                <i class="bi bi-plus-lg"></i>
              </button>
            </div>
          </div>

          <div v-if="category.children && category.children.length" class="section-restaurant-menu__subcategories">
            <div
              v-for="child in category.children"
              :key="child.id"
              class="section-restaurant-menu__subcategory"
            >
              <h4 class="section-restaurant-menu__subcategory-title">{{ child.title }}</h4>
              <div class="section-restaurant-menu__products">
                <div
                  v-for="product in child.products"
                  :key="product.id"
                  class="section-restaurant-menu__product"
                  @click="openProductModal(product)"
                >
                  <div v-if="showImages && product.image" class="section-restaurant-menu__product-image">
                    <img :src="product.image" :alt="product.title" loading="lazy" />
                  </div>
                  <div class="section-restaurant-menu__product-info">
                    <h5 class="section-restaurant-menu__product-name">{{ product.title }}</h5>
                    <p v-if="product.description" class="section-restaurant-menu__product-desc">
                      {{ truncateText(product.description, 60) }}
                    </p>
                    <div v-if="showPrices && product.price" class="section-restaurant-menu__product-price">
                      {{ formatCurrency(product.price) }}
                    </div>
                  </div>
                  <button class="section-restaurant-menu__product-btn">
                    <i class="bi bi-plus-lg"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="section-restaurant-menu__buttons">
        <a
          v-if="buttons && buttons.length"
          v-for="(btn, index) in buttons"
          :key="index"
          :href="btn.url"
          class="btn"
          :class="'btn-' + (btn.style || 'primary')"
        >
          {{ btn.text }}
        </a>
        <a
          v-if="businessSlug"
          :href="`/m/${businessSlug}/menu`"
          class="btn btn-outline-primary"
        >
          <i class="bi bi-cup-hot me-2"></i>Ver menú completo
        </a>
      </div>
    </div>

    <div v-if="selectedProduct" class="product-modal" @click="closeProductModal">
      <div class="product-modal__content" @click.stop>
        <button class="product-modal__close" @click="closeProductModal">
          <i class="bi bi-x-lg"></i>
        </button>
        <div v-if="selectedProduct.image" class="product-modal__image">
          <img :src="selectedProduct.image" :alt="selectedProduct.title" />
        </div>
        <div class="product-modal__info">
          <h2 class="product-modal__name">{{ selectedProduct.title }}</h2>
          <div v-if="selectedProduct.price && !selectedProduct.has_variants" class="product-modal__price">
            {{ formatCurrency(selectedProduct.price) }}
          </div>
          <p v-if="selectedProduct.description" class="product-modal__description">
            {{ selectedProduct.description }}
          </p>

          <div v-if="selectedProduct.has_variants && selectedProduct.variants" class="product-modal__variants">
            <h4 class="product-modal__variants-title">Opciones:</h4>
            <div
              v-for="variant in selectedProduct.variants"
              :key="variant.id"
              class="product-modal__variant"
            >
              <div class="product-modal__variant-info">
                <span class="product-modal__variant-name">{{ variant.title }}</span>
                <span v-if="variant.description" class="product-modal__variant-desc">
                  {{ variant.description }}
                </span>
              </div>
              <span class="product-modal__variant-price">{{ formatCurrency(variant.price) }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed, ref } from 'vue'

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

const selectedProduct = ref(null)

const showImages = computed(() => props.config?.show_images !== false)
const showPrices = computed(() => props.config?.show_prices !== false)

const truncateText = (text, length) => {
  if (!text || text.length <= length) return text
  return text.substring(0, length) + '...'
}

const formatCurrency = (value) => {
  if (value === null || value === undefined) return ''
  return new Intl.NumberFormat('es-MX', {
    style: 'currency',
    currency: 'MXN',
  }).format(value)
}

const openProductModal = (product) => {
  selectedProduct.value = product
}

const closeProductModal = () => {
  selectedProduct.value = null
}
</script>

<script>
import { defineComponent } from 'vue'
export default defineComponent({ name: 'SectionRestaurantMenu' })
</script>

<style lang="less">
.section-restaurant-menu {
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

  &__categories {
    display: flex;
    flex-direction: column;
    gap: 32px;
  }

  &__category {
    border-bottom: 1px solid #e9ecef;
    padding-bottom: 24px;

    &:last-child {
      border-bottom: none;
    }
  }

  &__category-header {
    margin-bottom: 16px;
  }

  &__category-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #212529;
    margin: 0 0 4px;
  }

  &__category-desc {
    font-size: 0.875rem;
    color: #6c757d;
    margin: 0;
  }

  &__products {
    display: flex;
    flex-direction: column;
    gap: 8px;
  }

  &__product {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    border-radius: 8px;
    background: #f8f9fa;
    cursor: pointer;
    transition: background 0.2s;

    &:hover {
      background: #e9ecef;
    }
  }

  &__product-image {
    flex: 0 0 64px;
    width: 64px;
    height: 64px;
    border-radius: 8px;
    overflow: hidden;

    img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
  }

  &__product-info {
    flex: 1;
    min-width: 0;
  }

  &__product-name {
    font-size: 1rem;
    font-weight: 600;
    color: #212529;
    margin: 0 0 4px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  &__product-desc {
    font-size: 0.8125rem;
    color: #6c757d;
    margin: 0 0 4px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }

  &__product-price {
    font-size: 0.9375rem;
    font-weight: 600;
    color: #198754;
  }

  &__product-variants {
    margin-top: 4px;
  }

  &__product-btn {
    flex: 0 0 32px;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #0d6efd;
    color: #fff;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background 0.2s;

    &:hover {
      background: #0b5ed7;
    }
  }

  &__subcategories {
    margin-top: 24px;
    padding-left: 16px;
    display: flex;
    flex-direction: column;
    gap: 16px;
  }

  &__subcategory-title {
    font-size: 1rem;
    font-weight: 600;
    color: #495057;
    margin: 0 0 12px;
    padding-bottom: 8px;
    border-bottom: 1px dashed #dee2e6;
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

  &__content {
    background: #fff;
    border-radius: 16px;
    max-width: 500px;
    width: 100%;
    max-height: 90vh;
    overflow-y: auto;
    position: relative;
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

    &:hover {
      background: #fff;
      color: #dc3545;
    }
  }

  &__image {
    width: 100%;
    height: 250px;
    overflow: hidden;

    img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
  }

  &__info {
    padding: 24px;
  }

  &__name {
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0 0 12px;
    color: #212529;
  }

  &__price {
    font-size: 1.25rem;
    font-weight: 700;
    color: #198754;
    margin-bottom: 16px;
  }

  &__description {
    font-size: 0.9375rem;
    color: #495057;
    line-height: 1.6;
    margin: 0 0 24px;
  }

  &__variants {
    border-top: 1px solid #e9ecef;
    padding-top: 16px;
  }

  &__variants-title {
    font-size: 1rem;
    font-weight: 600;
    color: #212529;
    margin: 0 0 12px;
  }

  &__variant {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px;
    background: #f8f9fa;
    border-radius: 8px;
    margin-bottom: 8px;

    &:last-child {
      margin-bottom: 0;
    }
  }

  &__variant-info {
    display: flex;
    flex-direction: column;
    gap: 2px;
  }

  &__variant-name {
    font-weight: 500;
    color: #212529;
  }

  &__variant-desc {
    font-size: 0.8125rem;
    color: #6c757d;
  }

  &__variant-price {
    font-weight: 600;
    color: #198754;
  }
}
</style>
