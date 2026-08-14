<template>
  <div class="minisite-page">
    <NavigationMenu :business="business" :existingSections="existingSections" />

    <HeroSimple
      :title="pageTitle"
      :backgroundImage="business.cover_image"
      :businessSlug="business.slug"
    />

    <section class="page-content">
      <div class="page-content__inner">
        <div v-if="categories && categories.length > 0" class="category-filter">
          <button
            class="category-badge"
            :class="{ active: selectedCategory === null }"
            @click="selectedCategory = null"
          >
            Todos
          </button>
          <button
            v-for="cat in categories"
            :key="cat.id"
            class="category-badge"
            :class="{ active: selectedCategory === cat.id }"
            @click="toggleCategory(cat.id)"
          >
            {{ cat.name }}
          </button>
        </div>

        <SectionProducts
          v-if="filteredItems && filteredItems.length"
          :title="pageTitle"
          :items="filteredItems"
          :config="{ view_mode: 'grid', show_description: true, show_compare_price: true, show_stock: true }"
          :businessSlug="business.slug"
        />
        <div v-else class="text-muted text-center py-5">
          No hay productos disponibles.
        </div>
      </div>
    </section>

    <Footer
      :business="business"
      :text="setting.footer_text"
      :showSocial="setting.footer_show_social"
      :socialNetworks="socialNetworks"
    />

    <AiChatWidget
      v-if="aiChatbot && aiChatbot.is_enabled"
      :businessSlug="business.slug"
      :businessName="business.name"
      :widgetColor="aiChatbot.widget_color || '#3B82F6'"
      :widgetTheme="aiChatbot.widget_theme || 'light'"
      :allowReset="aiChatbot.allow_reset_chat"
    />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import NavigationMenu from '../../components/NavigationMenu.vue'
import HeroSimple from '../../components/HeroSimple.vue'
import SectionProducts from '../../components/SectionProducts.vue'
import Footer from '../../components/Footer.vue'
import AiChatWidget from '@/Components/Minisite/AiChatWidget.vue'

const props = defineProps({
  business: Object,
  setting: Object,
  pageTitle: String,
  sectionData: Object,
  socialNetworks: Array,
  existingSections: Array,
  aiChatbot: Object,
})

const categories = computed(() => props.sectionData?.categories || [])
const selectedCategory = ref(null)

const allItems = computed(() => props.sectionData?.items || [])

const filteredItems = computed(() => {
  if (!selectedCategory.value) {
    return allItems.value
  }
  return allItems.value.filter(item => item.category_id === selectedCategory.value)
})

const toggleCategory = (categoryId) => {
  if (selectedCategory.value === categoryId) {
    selectedCategory.value = null
  } else {
    selectedCategory.value = categoryId
  }
}
</script>

<style lang="less">
.category-filter {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-bottom: 24px;
  justify-content: center;
}

.category-badge {
  padding: 8px 16px;
  border-radius: 20px;
  border: 2px solid #dee2e6;
  background: #fff;
  color: #495057;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;

  &:hover {
    border-color: #0d6efd;
    color: #0d6efd;
  }

  &.active {
    background: #0d6efd;
    border-color: #0d6efd;
    color: #fff;
  }
}

.page-content {
  padding: 32px 16px;
  background: #f8f9fa;

  &__inner {
    max-width: 1024px;
    margin: 0 auto;
  }
}
</style>
