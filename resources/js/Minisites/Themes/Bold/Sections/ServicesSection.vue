<template>
  <section class="services-section py-5" :style="{ backgroundColor: sectionBgColor }">
    <div class="container">
      <div v-if="showTitle" class="text-center mb-5">
        <h2 class="section-title" :style="{ color: titleColor }">{{ title }}</h2>
        <p v-if="subtitle" class="section-subtitle lead" :style="{ color: subtitleColor }">{{ subtitle }}</p>
      </div>

      <div class="row g-4">
        <div
          v-for="(service, index) in services"
          :key="service.id"
          class="col-md-6 col-lg-4"
        >
          <div class="service-card h-100" :class="cardStyle">
            <div class="service-header">
              <h3 class="service-name">{{ service.name }}</h3>
              <div v-if="service.price" class="service-price">
                <span class="price-amount">${{ service.price }}</span>
                <span v-if="service.duration_minutes" class="price-duration">{{ service.duration_minutes }} min</span>
              </div>
            </div>

            <p v-if="service.description" class="service-description">{{ service.description }}</p>

            <div v-if="showBookingButton && service.allows_online_booking" class="service-actions mt-auto">
              <a
                :href="`/b/${businessSlug}/book?service=${service.slug}`"
                class="btn service-btn w-100"
                :class="buttonStyle"
              >
                Reservar
              </a>
            </div>

            <div v-if="service.whatsapp_contact && showWhatsApp" class="service-whatsapp mt-2">
              <a
                :href="`https://wa.me/${service.whatsapp_contact.replace(/[^0-9]/g, '')}`"
                target="_blank"
                class="whatsapp-link"
              >
                <i class="bi bi-whatsapp me-1"></i>Consultar por WhatsApp
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  services: {
    type: Array,
    default: () => []
  },
  businessSlug: {
    type: String,
    default: ''
  },
  title: {
    type: String,
    default: 'Nuestros Servicios'
  },
  subtitle: {
    type: String,
    default: ''
  },
  showTitle: {
    type: Boolean,
    default: true
  },
  showBookingButton: {
    type: Boolean,
    default: true
  },
  showWhatsApp: {
    type: Boolean,
    default: true
  },
  titleColor: {
    type: String,
    default: '#1a1a2e'
  },
  subtitleColor: {
    type: String,
    default: '#6B7280'
  },
  sectionBgColor: {
    type: String,
    default: '#ffffff'
  },
  cardStyle: {
    type: String,
    default: 'card-light'
  },
  buttonStyle: {
    type: String,
    default: 'btn-primary'
  },
})

const cardStyle = computed(() => `service-card-${props.cardStyle}`)
const buttonStyle = computed(() => `btn-${props.buttonStyle}`)
</script>

<style scoped>
.services-section {
  background: #ffffff;
}

.section-title {
  font-family: 'Oswald', sans-serif;
  font-size: 2.5rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 2px;
  margin-bottom: 1rem;
}

.section-subtitle {
  font-family: 'Roboto Condensed', sans-serif;
  max-width: 600px;
  margin: 0 auto;
}

.service-card {
  padding: 1.5rem;
  border-radius: 8px;
  transition: all 0.3s ease;
  display: flex;
  flex-direction: column;
}

.service-card-light {
  background: #f8f9fa;
  border: 1px solid #e9ecef;
}

.service-card-light:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.service-card-dark {
  background: #1a1a2e;
  color: white;
  border: 1px solid #2d2d44;
}

.service-card-dark:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
}

.service-card-accent {
  background: linear-gradient(135deg, #FF4500 0%, #FF6B35 100%);
  color: white;
}

.service-card-accent:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 25px rgba(255, 69, 0, 0.4);
}

.service-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 1rem;
  gap: 1rem;
}

.service-name {
  font-family: 'Oswald', sans-serif;
  font-size: 1.25rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 1px;
  margin: 0;
}

.service-price {
  text-align: right;
  white-space: nowrap;
}

.price-amount {
  display: block;
  font-family: 'Oswald', sans-serif;
  font-size: 1.5rem;
  font-weight: 700;
  color: #FF4500;
}

.service-card-dark .price-amount,
.service-card-accent .price-amount {
  color: white;
}

.price-duration {
  font-size: 0.8rem;
  opacity: 0.7;
}

.service-description {
  font-family: 'Roboto Condensed', sans-serif;
  font-size: 0.95rem;
  line-height: 1.6;
  opacity: 0.85;
  margin-bottom: 1rem;
  flex-grow: 1;
}

.service-card-light .service-description {
  color: #495057;
}

.service-actions {
  margin-top: auto;
}

.service-btn {
  font-family: 'Oswald', sans-serif;
  text-transform: uppercase;
  letter-spacing: 1px;
  font-weight: 500;
  padding: 0.6rem 1.5rem;
  border-radius: 0;
  transition: all 0.3s ease;
}

.btn-primary {
  background: #FF4500;
  border-color: #FF4500;
}

.btn-primary:hover {
  background: #e03e00;
  border-color: #e03e00;
  transform: translateY(-2px);
  box-shadow: 0 4px 15px rgba(255, 69, 0, 0.4);
}

.btn-secondary {
  background: #1a1a2e;
  border-color: #1a1a2e;
  color: white;
}

.btn-secondary:hover {
  background: #2d2d44;
  border-color: #2d2d44;
  color: white;
  transform: translateY(-2px);
}

.btn-outline {
  background: transparent;
  border: 2px solid currentColor;
}

.btn-outline:hover {
  background: rgba(255, 255, 255, 0.1);
  transform: translateY(-2px);
}

.service-whatsapp {
  text-align: center;
}

.whatsapp-link {
  font-family: 'Roboto Condensed', sans-serif;
  font-size: 0.85rem;
  color: #25D366;
  text-decoration: none;
  transition: opacity 0.3s ease;
}

.whatsapp-link:hover {
  opacity: 0.8;
  color: #25D366;
}

@media (max-width: 768px) {
  .section-title {
    font-size: 2rem;
  }

  .service-header {
    flex-direction: column;
  }

  .service-price {
    text-align: left;
  }
}
</style>
