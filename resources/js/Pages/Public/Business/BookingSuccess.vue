<template>
  <div>
    <h1>Reserva confirmada!</h1>
    <p>Tu turno ha sido reservado exitosamente.</p>
    <p>Te enviaremos un email de confirmacion.</p>
    <Link :href="`/b/${$page.props.business?.slug || ''}`">Volver al negocio</Link>
  </div>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3'
const page = usePage()
</script>
