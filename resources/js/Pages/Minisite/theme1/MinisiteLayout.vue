<template>
  <div class="minisite">
    <HeroLeft
      v-if="heroLayout === 'left'"
      :business="business"
      :title="heroTitle"
      :subtitle="heroSubtitle"
      :backgroundImage="heroBackgroundImage"
    />
    <HeroCenter
      v-else-if="heroLayout === 'center'"
      :business="business"
      :title="heroTitle"
      :subtitle="heroSubtitle"
      :backgroundImage="heroBackgroundImage"
    />
    <HeroRight
      v-else
      :business="business"
      :title="heroTitle"
      :subtitle="heroSubtitle"
      :backgroundImage="heroBackgroundImage"
    />

    <div class="minisite__content">
      <slot />
    </div>

    <MinisiteFooter
      :business="business"
      :text="footerText"
      :showSocial="footerShowSocial"
      :socialNetworks="socialNetworks"
    />
  </div>
</template>

<script setup>
import HeroLeft from './HeroLeft.vue'
import HeroCenter from './HeroCenter.vue'
import HeroRight from './HeroRight.vue'
import MinisiteFooter from './Footer.vue'

defineProps({
  business: {
    type: Object,
    required: true,
  },
  heroLayout: {
    type: String,
    default: 'left',
  },
  heroTitle: {
    type: String,
    default: '',
  },
  heroSubtitle: {
    type: String,
    default: '',
  },
  heroBackgroundImage: {
    type: String,
    default: '',
  },
  footerText: {
    type: String,
    default: '',
  },
  footerShowSocial: {
    type: Boolean,
    default: true,
  },
  socialNetworks: {
    type: Array,
    default: () => [],
  },
})
</script>

<style lang="less">
.minisite {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  padding-top: 64px;

  &__content {
    flex: 1;
  }
}
</style>
