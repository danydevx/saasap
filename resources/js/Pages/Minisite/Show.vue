<template>
  <div class="minisite-page">
    <NavigationMenu :business="business" :existingSections="existingSections" />

    <MinisiteLayout
      :business="business"
      :heroLayout="setting.hero_layout"
      :heroTitle="setting.hero_title"
      :heroSubtitle="setting.hero_subtitle"
      :heroBackgroundImage="setting.hero_background_image"
      :footerText="setting.footer_text"
      :footerShowSocial="setting.footer_show_social"
      :socialNetworks="socialNetworks"
    >
      <template v-for="section in renderedSections" :key="section.id">
        <SectionServices
          v-if="section.type === 'services'"
          :title="section.title"
          :items="section.items"
          :config="section.config"
          :businessSlug="business.slug"
        />

        <SectionGallery
          v-else-if="section.type === 'gallery'"
          :title="section.title"
          :items="section.items"
          :config="section.config"
        />

        <SectionPromotions
          v-else-if="section.type === 'promotions'"
          :title="section.title"
          :items="section.items"
          :config="section.config"
          :businessSlug="business.slug"
        />

        <SectionContactForm
          v-else-if="section.type === 'contact_form'"
          :title="section.title"
          :form="section.form"
          :config="section.config"
        />

        <SectionAppointments
          v-else-if="section.type === 'appointments'"
          :title="section.title"
          :services="section.appointments?.services || []"
          :locations="section.appointments?.locations || []"
          :availableDays="section.appointments?.availableDays || []"
          :config="section.config || {}"
          :businessSlug="business.slug"
        />

        <SectionAvailability
          v-else-if="section.type === 'availability'"
          :title="section.title"
          :availability="section.availability || {}"
        />

        <SectionLocations
          v-else-if="section.type === 'locations'"
          :title="section.title"
          :items="section.items"
          :config="section.config"
        />

        <SectionAbout
          v-else-if="section.type === 'about'"
          :title="section.title"
          :content="section.content"
          :config="section.config"
        />

        <SectionFeatures
          v-else-if="section.type === 'features'"
          :title="section.title"
          :items="section.items"
          :config="section.config"
        />

        <SectionFaqs
          v-else-if="section.type === 'faqs'"
          :title="section.title"
          :items="section.items"
          :config="section.config"
        />

        <SectionProducts
          v-else-if="section.type === 'products'"
          :title="section.title"
          :items="section.items"
          :config="section.config"
          :businessSlug="business.slug"
        />

        <SectionFooter
          v-else-if="section.type === 'footer'"
          :business="business"
          :text="section.config?.text"
          :showSocial="section.config?.show_social"
          :socialNetworks="socialNetworks"
        />

        <SectionReviews
          v-else-if="section.type === 'reviews'"
          :title="section.title"
          :items="section.items"
          :config="section.config"
        />

        <SectionRestaurantMenu
          v-else-if="section.type === 'restaurant_menu'"
          :title="section.title"
          :items="section.items"
          :config="section.config"
          :businessSlug="business.slug"
        />
      </template>
    </MinisiteLayout>

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
import { computed } from 'vue'
import NavigationMenu from './theme1/NavigationMenu.vue'
import MinisiteLayout from './theme1/MinisiteLayout.vue'
import SectionServices from './theme1/SectionServices.vue'
import SectionGallery from './theme1/SectionGallery.vue'
import SectionPromotions from './theme1/SectionPromotions.vue'
import SectionContactForm from './theme1/SectionContactForm.vue'
import SectionAppointments from './theme1/SectionAppointments.vue'
import SectionAvailability from './theme1/SectionAvailability.vue'
import SectionLocations from './theme1/SectionLocations.vue'
import SectionAbout from './theme1/SectionAbout.vue'
import SectionFeatures from './theme1/SectionFeatures.vue'
import SectionFaqs from './theme1/SectionFaqs.vue'
import SectionProducts from './theme1/SectionProducts.vue'
import SectionFooter from './theme1/SectionFooter.vue'
import SectionReviews from './theme1/SectionReviews.vue'
import SectionRestaurantMenu from './theme1/SectionRestaurantMenu.vue'
import AiChatWidget from '@/Components/Minisite/AiChatWidget.vue'

const props = defineProps({
  business: Object,
  setting: Object,
  sections: {
    type: Array,
    default: () => [],
  },
  socialNetworks: {
    type: Array,
    default: () => [],
  },
  existingSections: {
    type: Array,
    default: () => [],
  },
  aiChatbot: {
    type: Object,
    default: null,
  },
})

const renderedSections = computed(() => {
  const sectionComponents = ['services', 'gallery', 'promotions', 'contact_form', 'appointments', 'availability', 'locations', 'about', 'features', 'faqs', 'products', 'footer', 'reviews', 'restaurant_menu']

  return props.sections
    .filter(section => sectionComponents.includes(section.section_type))
    .map(section => {
      const data = {
        id: section.id,
        type: section.section_type,
        title: section.title,
        config: section.config || {},
      }

      switch (section.section_type) {
        case 'services':
        case 'gallery':
        case 'promotions':
        case 'locations':
        case 'features':
        case 'faqs':
        case 'products':
        case 'reviews':
        case 'restaurant_menu':
          data.items = section.items || []
          break
        case 'contact_form':
          data.form = section.form || null
          break
        case 'appointments':
          data.appointments = section.appointments || { services: [], locations: [], availableDays: [] }
          break
        case 'availability':
          data.availability = section.availability || { schedule: [], exceptions: [] }
          break
        case 'about':
          data.content = section.content || null
          break
        case 'footer':
          data.config = {
            text: section.config?.text || '',
            show_social: section.config?.show_social ?? true,
          }
          break
      }

      return data
    })
})
</script>

<style lang="less">
.minisite-page {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}
</style>
