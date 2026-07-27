<template>
  <div class="minisite-preview">
    <div class="minisite-preview__frame">
      <div class="minisite-preview__status-bar">
        <span class="minisite-preview__time">11:32</span>
        <div class="minisite-preview__indicators">
          <i class="bi bi-signal"></i>
          <i class="bi bi-wifi-2"></i>
          <i class="bi bi-battery-full"></i>
        </div>
      </div>

      <div class="minisite-preview__screen">
        <div class="minisite-preview__scroll-area">
          <component
            :is="heroComponent"
            v-if="setting"
            :business="business"
            :title="setting.hero_title || business.name"
            :subtitle="setting.hero_subtitle"
            :backgroundImage="setting.hero_background_image"
          />

          <div class="minisite-preview__sections">
            <template v-for="section in sections" :key="section.id">
              <div v-if="section.is_active" class="minisite-preview__section-wrapper">
                <div v-if="hasContent(section)" class="minisite-preview__section">
                  <component
                    :is="sectionComponent(section.section_type)"
                    v-if="section.section_type !== 'custom'"
                    :title="section.title"
                    :description="section.description"
                    :buttons="section.buttons"
                    v-bind="sectionProps(section)"
                  />
                </div>
                <div v-else class="minisite-preview__section minisite-preview__section--empty">
                  <div class="minisite-preview__empty-state">
                    <i :class="sectionIcon(section.section_type)"></i>
                    <p>{{ section.title || sectionTypes[section.section_type] || 'Sección' }}</p>
                    <small>Sin contenido</small>
                  </div>
                </div>
              </div>
            </template>
          </div>

          <Footer
            v-if="setting"
            :business="business"
            :text="setting.footer_text"
            :showSocial="setting.footer_show_social"
            :socialNetworks="socialNetworks"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import HeroLeft from '@/Pages/Minisite/theme1/HeroLeft.vue'
import HeroCenter from '@/Pages/Minisite/theme1/HeroCenter.vue'
import HeroRight from '@/Pages/Minisite/theme1/HeroRight.vue'
import SectionServices from '@/Pages/Minisite/theme1/SectionServices.vue'
import SectionGallery from '@/Pages/Minisite/theme1/SectionGallery.vue'
import SectionPromotions from '@/Pages/Minisite/theme1/SectionPromotions.vue'
import SectionContactForm from '@/Pages/Minisite/theme1/SectionContactForm.vue'
import Footer from '@/Pages/Minisite/theme1/Footer.vue'

const props = defineProps({
  business: {
    type: Object,
    required: true,
  },
  setting: {
    type: Object,
    default: null,
  },
  sections: {
    type: Array,
    default: () => [],
  },
  socialNetworks: {
    type: Array,
    default: () => [],
  },
  sectionTypes: {
    type: Object,
    default: () => ({}),
  },
})

const heroComponent = computed(() => {
  if (!props.setting) return HeroLeft
  const layout = props.setting.hero_layout || 'left'
  return { left: HeroLeft, center: HeroCenter, right: HeroRight }[layout] || HeroLeft
})

const sectionComponent = (type) => {
  const components = {
    services: SectionServices,
    gallery: SectionGallery,
    promotions: SectionPromotions,
    contact_form: SectionContactForm,
  }
  return components[type] || null
}

const sectionIcon = (type) => {
  const icons = {
    services: 'bi bi-briefcase',
    gallery: 'bi bi-images',
    promotions: 'bi bi-tag',
    contact_form: 'bi bi-envelope',
  }
  return icons[type] || 'bi bi-grid'
}

const hasContent = (section) => {
  if (section.section_type === 'services') {
    return section.items && section.items.length > 0
  }
  if (section.section_type === 'gallery') {
    return section.items && section.items.length > 0
  }
  if (section.section_type === 'promotions') {
    return section.items && section.items.length > 0
  }
  if (section.section_type === 'contact_form') {
    return section.form && section.form.id
  }
  return false
}

const sectionProps = (section) => {
  if (section.section_type === 'services') {
    return { items: section.items || [], config: section.config || {} }
  }
  if (section.section_type === 'gallery') {
    return { items: section.items || [], config: section.config || {} }
  }
  if (section.section_type === 'promotions') {
    return { items: section.items || [], config: section.config || {} }
  }
  if (section.section_type === 'contact_form') {
    return { form: section.form || {}, config: section.config || {} }
  }
  return {}
}
</script>

<style lang="less" scoped>
.minisite-preview {
  &__frame {
    width: 320px;
    height: 580px;
    background: #1a1a1a;
    border-radius: 32px;
    padding: 12px;
    box-shadow:
      0 25px 50px -12px rgba(0, 0, 0, 0.25),
      inset 0 0 0 1px rgba(255, 255, 255, 0.1);
    margin: 0 auto;
  }

  &__status-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 16px;
    color: #fff;
    font-size: 12px;
    font-weight: 500;
  }

  &__time {
    font-weight: 600;
  }

  &__indicators {
    display: flex;
    gap: 4px;
    align-items: center;
  }

  &__screen {
    background: #fff;
    border-radius: 24px;
    height: calc(100% - 40px);
    overflow: hidden;
  }

  &__scroll-area {
    height: 100%;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
  }

  &__sections {
    display: flex;
    flex-direction: column;
  }

  &__section-wrapper {
    width: 100%;
  }

  &__section {
    &--empty {
      padding: 24px 16px;
    }
  }

  &__empty-state {
    text-align: center;
    padding: 24px;
    background: #f8f9fa;
    border-radius: 8px;
    color: #6c757d;

    i {
      font-size: 32px;
      margin-bottom: 8px;
      display: block;
      color: #adb5bd;
    }

    p {
      margin: 0 0 4px 0;
      font-weight: 600;
      color: #495057;
    }

    small {
      font-size: 12px;
    }
  }
}

.minisite-preview__scroll-area::-webkit-scrollbar {
  width: 4px;
}

.minisite-preview__scroll-area::-webkit-scrollbar-track {
  background: transparent;
}

.minisite-preview__scroll-area::-webkit-scrollbar-thumb {
  background: rgba(0, 0, 0, 0.2);
  border-radius: 2px;
}
</style>
