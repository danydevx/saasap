<template>
  <section class="section-availability">
    <div class="section-availability__inner">
      <h2 v-if="title" class="section-availability__title">{{ title }}</h2>

      <AvailabilityCalendar
        v-if="schedule && schedule.length"
        :schedule="schedule"
        :exceptions="exceptions || []"
        :appointment-counts="{}"
      />

      <div v-else class="alert alert-info mb-0">
        <i class="bi bi-info-circle me-2"></i>
        Horarios de atención no disponibles.
      </div>
    </div>
  </section>
</template>

<script setup>
import AvailabilityCalendar from '@/Components/Availability/AvailabilityCalendar.vue'

const props = defineProps({
  title: {
    type: String,
    default: 'Horario de Atención',
  },
  availability: {
    type: Object,
    required: true,
  },
})

const schedule = props.availability?.schedule || []
const exceptions = props.availability?.exceptions || []
</script>

<style lang="less" scoped>
.section-availability {
  padding: 60px 0;
  background: #f8f9fa;

  &__inner {
    max-width: 800px;
    margin: 0 auto;
    padding: 0 20px;
  }

  &__title {
    text-align: center;
    margin-bottom: 30px;
    font-size: 1.75rem;
    font-weight: 600;
    color: #333;
  }
}
</style>
