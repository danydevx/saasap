<template>
  <MemberLayout>
    <Head :title="`AI Chatbot - ${business.name}`" />

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
      <div>
        <h1 class="h4 mb-1">{{ business.name }}</h1>
        <p class="text-muted mb-0">Configura el chatbot de inteligencia artificial.</p>
      </div>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div class="text-center py-5">
          <i class="bi bi-robot display-1 text-muted"></i>
          <h3 class="h5 mt-3">Modulo de AI Chatbot</h3>
          <p class="text-muted">Este modulo esta en desarrollo.</p>
          <p class="text-muted small">Pronto podras configurar el chatbot de inteligencia artificial para responder consultas de tus clientes.</p>
        </div>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Head, usePage } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'

const page = usePage()
const business = computed(() => page.props.business)
</script>
