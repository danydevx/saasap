<template>
  <div
    class="minisite-layout"
    :style="themeStyles"
    :class="[pageStyleClass, layoutClasses]"
  >
    <slot />

    <footer class="minisite-footer" :style="footerStyles">
      <div class="container">
        <div class="row">
          <div class="col-md-6 text-center text-md-start">
            <h6 class="mb-2" :style="{ color: footerTextColor }">{{ business?.name }}</h6>
            <small :style="{ color: footerTextLightColor }">{{ business?.address || 'Visítanos' }}</small>
          </div>
          <div class="col-md-6 text-center text-md-end">
            <small :style="{ color: footerTextLightColor }">
              &copy; {{ new Date().getFullYear() }} {{ business?.name }}. Todos los derechos reservados.
            </small>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  business: Object,
  theme: Object,
  modules: {
    type: Array,
    default: () => [],
  },
})

const themeStyles = computed(() => {
  if (!props.theme?.css_variables) return {}

  const { colors, fonts, border_radius, button_style, card_style } = props.theme.css_variables

  return {
    '--minisite-primary': colors?.primary || '#1a1a2e',
    '--minisite-secondary': colors?.secondary || '#6B7280',
    '--minisite-accent': colors?.accent || '#3B82F6',
    '--minisite-background': colors?.background || '#ffffff',
    '--minisite-text': colors?.text || '#1a1a2e',
    '--minisite-text-light': colors?.text_light || '#6B7280',
    '--minisite-font-headings': fonts?.headings || 'Montserrat, sans-serif',
    '--minisite-font-body': fonts?.body || 'Inter, sans-serif',
    '--minisite-border-radius': border_radius || '8px',
    '--minisite-button-radius': button_style === 'rounded-pill' ? '50px' :
                                button_style === 'rounded-0' ? '0' :
                                button_style === 'rounded-20' ? '20px' :
                                button_style === 'rounded-8' ? '8px' : border_radius || '8px',
    '--minisite-card-radius': border_radius || '8px',
  }
})

const pageStyleClass = computed(() => {
  const style = props.theme?.layout_config?.page_style || 'light'
  return `minisite-style-${style}`
})

const layoutClasses = computed(() => {
  if (!props.theme?.layout_config) return ''

  const { section_style, hero_style } = props.theme.layout_config
  const classes = []

  if (section_style === 'spacious') classes.push('minisite-section-spacious')
  if (section_style === 'classic') classes.push('minisite-section-classic')
  if (section_style === 'cozy') classes.push('minisite-section-cozy')
  if (section_style === 'rounded') classes.push('minisite-section-rounded')
  if (section_style === 'dramatic') classes.push('minisite-section-dramatic')
  if (section_style === 'balanced') classes.push('minisite-section-balanced')

  if (hero_style === 'fullwidth') classes.push('minisite-hero-fullwidth')
  if (hero_style === 'centered') classes.push('minisite-hero-centered')
  if (hero_style === 'split') classes.push('minisite-hero-split')
  if (hero_style === 'boxed') classes.push('minisite-hero-boxed')
  if (hero_style === 'fullbleed') classes.push('minisite-hero-fullbleed')

  return classes.join(' ')
})

const footerStyles = computed(() => {
  if (!props.theme?.css_variables?.colors) {
    return { backgroundColor: '#f8f9fa', color: '#1a1a1a' }
  }
  const { primary, background } = props.theme.css_variables.colors
  return {
    backgroundColor: primary || '#1a1a2e',
    color: '#ffffff',
  }
})

const footerTextColor = computed(() => {
  return '#ffffff'
})

const footerTextLightColor = computed(() => {
  return 'rgba(255, 255, 255, 0.8)'
})

const animations = computed(() => {
  return props.theme?.layout_config?.animations || {}
})

const isModuleEnabled = (moduleKey) => {
  return props.modules.includes(moduleKey)
}
</script>

<style scoped>
.minisite-layout {
  font-family: var(--minisite-font-body);
  background-color: var(--minisite-background);
  color: var(--minisite-text);
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.minisite-layout :deep(h1),
.minisite-layout :deep(h2),
.minisite-layout :deep(h3),
.minisite-layout :deep(h4),
.minisite-layout :deep(h5),
.minisite-layout :deep(h6) {
  font-family: var(--minisite-font-headings);
  color: var(--minisite-primary);
}

.minisite-layout :deep(.btn-primary) {
  background-color: var(--minisite-primary);
  border-color: var(--minisite-primary);
  border-radius: var(--minisite-button-radius);
}

.minisite-layout :deep(.btn-outline-primary) {
  color: var(--minisite-primary);
  border-color: var(--minisite-primary);
  border-radius: var(--minisite-button-radius);
}

.minisite-layout :deep(.card) {
  border-radius: var(--minisite-card-radius);
  border: none;
}

.minisite-layout :deep(.section-divider) {
  border-color: var(--minisite-accent);
}

/* Dark style */
.minisite-style-dark {
  --minisite-bg-dark: #1a1a2e;
}

.minisite-style-dark .minisite-footer {
  background-color: var(--minisite-primary) !important;
  color: #ffffff;
}

/* Light style */
.minisite-style-light {
  --minisite-bg-light: #FDF8F3;
}

/* Section styles */
.minisite-section-spacious {
  --section-padding: 5rem 0;
}

.minisite-section-cozy {
  --section-padding: 3rem 0;
}

.minisite-section-dramatic {
  --section-padding: 6rem 0;
}

.minisite-section-balanced {
  --section-padding: 4rem 0;
}

.minisite-section-rounded {
  --section-padding: 4rem 0;
}

/* Animation classes */
:deep(.animate-fade-in) {
  animation: fade-in 0.5s ease-out forwards;
}

:deep(.animate-fade-in-up) {
  animation: fade-in-up 0.6s ease-out forwards;
}

:deep(.animate-slide-in) {
  animation: slide-in 0.4s ease-out forwards;
}

:deep(.animate-bounce-in) {
  animation: bounce-in 0.5s ease-out forwards;
}

:deep(.animate-zoom-in) {
  animation: zoom-in 0.4s ease-out forwards;
}

:deep(.animate-lift) {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

:deep(.animate-lift:hover) {
  transform: translateY(-8px);
  box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

:deep(.animate-glow) {
  transition: box-shadow 0.3s ease;
}

:deep(.animate-glow:hover) {
  box-shadow: 0 0 20px rgba(201, 168, 108, 0.4);
}

:deep(.animate-bounce) {
  transition: transform 0.3s ease;
}

:deep(.animate-bounce:hover) {
  transform: translateY(-4px);
}

:deep(.animate-neon-glow) {
  transition: box-shadow 0.3s ease;
}

:deep(.animate-neon-glow:hover) {
  box-shadow: 0 0 30px rgba(255, 69, 0, 0.5);
}

@keyframes fade-in {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes fade-in-up {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes slide-in {
  from { opacity: 0; transform: translateX(-20px); }
  to { opacity: 1; transform: translateX(0); }
}

@keyframes bounce-in {
  0% { opacity: 0; transform: scale(0.9); }
  50% { transform: scale(1.02); }
  100% { opacity: 1; transform: scale(1); }
}

@keyframes zoom-in {
  from { opacity: 0; transform: scale(0.95); }
  to { opacity: 1; transform: scale(1); }
}

.minisite-footer {
  margin-top: auto;
  padding: 2rem 0;
  border-top: 1px solid rgba(128, 128, 128, 0.2);
}
</style>
