<template>
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
    <SectionServices
      v-if="servicesSections.length"
      :title="servicesSections[0].title"
      :items="servicesSections[0].items"
      :config="servicesSections[0].config"
    />

    <SectionGallery
      v-if="gallerySections.length"
      :title="gallerySections[0].title"
      :items="gallerySections[0].items"
      :config="gallerySections[0].config"
    />

    <SectionPromotions
      v-if="promotionSections.length"
      :title="promotionSections[0].title"
      :items="promotionSections[0].items"
      :config="promotionSections[0].config"
    />

    <SectionContactForm
      v-if="contactSections.length && contactSections[0].form"
      :title="contactSections[0].title"
      :form="contactSections[0].form"
      :config="contactSections[0].config"
    />
  </MinisiteLayout>
</template>

<script setup>
import { computed } from 'vue'
import MinisiteLayout from './theme1/MinisiteLayout.vue'
import SectionServices from './theme1/SectionServices.vue'
import SectionGallery from './theme1/SectionGallery.vue'
import SectionPromotions from './theme1/SectionPromotions.vue'
import SectionContactForm from './theme1/SectionContactForm.vue'

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
})

const servicesSections = computed(() =>
  props.sections.filter(s => s.section_type === 'services')
)

const gallerySections = computed(() =>
  props.sections.filter(s => s.section_type === 'gallery')
)

const promotionSections = computed(() =>
  props.sections.filter(s => s.section_type === 'promotions')
)

const contactSections = computed(() =>
  props.sections.filter(s => s.section_type === 'contact_form')
)
</script>
