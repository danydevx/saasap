<template>
  <Footer
    :business="business"
    :text="text"
    :showSocial="showSocial"
    :socialNetworks="socialNetworks"
  />
</template>

<script setup>
import Footer from '@/Pages/Minisite/theme1/Footer.vue'

defineProps({
  business: Object,
  text: String,
  showSocial: Boolean,
  socialNetworks: {
    type: Array,
    default: () => [],
  },
  config: {
    type: Object,
    default: () => ({}),
  },
})
</script>
