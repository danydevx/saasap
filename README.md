# SaaS Starter --- Laravel + Inertia + Vue

Starter avanzado para construir aplicaciones SaaS multiusuario con
Laravel, Inertia y Vue.

Este proyecto proporciona una base reutilizable para lanzar nuevos SaaS
rápidamente, incluyendo autenticación, roles, billing, módulos
activables, notificaciones, automatizaciones y más.

El objetivo es evitar empezar desde cero cada vez y contar con una
arquitectura sólida y escalable desde el inicio.

------------------------------------------------------------------------

# Stack Tecnológico

## Backend

-   Laravel 12
-   Spatie laravel-permission
-   Stripe API
-   Jobs / Queues
-   Scheduler

## Frontend

-   Inertia.js
-   Vue 3
-   JavaScript (NO TypeScript)
-   Pinia
-   Axios
-   Bootstrap 5.3
-   Bootstrap Icons

## Arquitectura

-   RBAC (roles y permisos)
-   Feature Flags
-   Módulos activables
-   Servicios reutilizables
-   Inertia SPA híbrido

------------------------------------------------------------------------

# Características Principales

## Auth y usuarios

-   Registro
-   Login
-   Verificación de email
-   Recuperación de contraseña
-   Perfiles de usuario

## Control de acceso

-   Roles
-   Permisos
-   Policies
-   RBAC avanzado

## Sistema SaaS

-   Dashboard Admin
-   Dashboard Member
-   Settings globales
-   Feature flags
-   Módulos activables

## Billing

-   Planes
-   Suscripciones
-   Pagos
-   Integración Stripe
-   Webhooks de pago

## Operación del sistema

-   Activity Log
-   Security Events
-   System Errors
-   Jobs y colas
-   Scheduler

## Comunicación

-   Emails transaccionales
-   Motor de plantillas
-   Notificaciones internas
-   Preferencias de notificación
-   Anuncios del sistema

## Integraciones

-   API Keys
-   Webhooks
-   Portal de integraciones

## Automatización

-   Automatizaciones internas
-   Eventos del sistema
-   Jobs programados

## Otros módulos

-   Media / archivos
-   Invitaciones
-   Documentos legales
-   Help center
-   Soporte
-   Exportaciones

------------------------------------------------------------------------

# Arquitectura del Proyecto

El sistema separa claramente tres áreas:

## Admin

Gestión completa del SaaS:

-   usuarios
-   billing
-   settings
-   módulos
-   activity log
-   errores
-   integraciones

## Member

Área del usuario registrado:

-   perfil
-   suscripción
-   notificaciones
-   archivos
-   integraciones
-   soporte

## Public

Área pública del sistema:

-   registro
-   login
-   recuperación de contraseña

------------------------------------------------------------------------

# Módulos del Sistema

El starter incluye múltiples módulos que pueden activarse o
desactivarse.

Ejemplos:

users\
billing\
support\
media\
exports\
api\
webhooks\
automations\
legal\
notifications\
announcements\
integrations

Esto permite reutilizar el sistema para diferentes productos.

------------------------------------------------------------------------

# Instalación

1.  Instalar dependencias

composer install

2.  Crear archivo de entorno

cp .env.example .env

3.  Generar key

php artisan key:generate

4.  Migrar base de datos

php artisan migrate --seed

5.  Instalar frontend

npm install

6.  Compilar assets

npm run build

------------------------------------------------------------------------

# Crear Superadmin

El sistema debe tener un administrador inicial.

Dependiendo de la configuración puede hacerse mediante:

-   Seeder
-   Comando artisan

Ejemplo:

php artisan saas:create-superadmin

------------------------------------------------------------------------

# Variables de Entorno Importantes

APP_NAME\
APP_URL

DB_CONNECTION

MAIL_MAILER\
MAIL_HOST

STRIPE_KEY\
STRIPE_SECRET

QUEUE_CONNECTION

------------------------------------------------------------------------

# Jobs y Scheduler

Para funcionamiento correcto del sistema se recomienda ejecutar:

Worker de colas

php artisan queue:work

Scheduler

php artisan schedule:work

------------------------------------------------------------------------

# Stripe

El sistema utiliza Stripe para billing.

Debe configurarse:

-   STRIPE_KEY
-   STRIPE_SECRET
-   endpoint de webhooks

Eventos recomendados:

invoice.paid\
invoice.payment_failed\
customer.subscription.updated\
customer.subscription.deleted

------------------------------------------------------------------------

# Convenciones del Proyecto

## Permisos

Formato:

module.action

Ejemplo:

users.view\
users.create\
users.update\
users.delete

## Rutas

Admin

/admin/\*

Member

/member/\*

Public

/login\
/register

------------------------------------------------------------------------

# Documentación

Documentación adicional disponible en:

docs/

Incluye:

-   arquitectura
-   módulos
-   permisos
-   instalación
-   creación de nuevos módulos

------------------------------------------------------------------------

# AGENTS.md

El archivo AGENTS.md define reglas para el agente IA.

Incluye:

-   stack del proyecto
-   reglas de Vue
-   reglas de Laravel
-   convenciones del sistema
-   estructura CRUD
-   control de acceso

------------------------------------------------------------------------

# Crear Nuevos Módulos

Al crear un módulo nuevo se recomienda seguir este checklist:

-   rutas
-   controlador
-   vistas Inertia
-   permisos
-   policies
-   menú
-   feature flags si aplica
-   activity log si aplica
-   exports si aplica
-   documentación si aplica

------------------------------------------------------------------------

# Uso como Starter

Este repositorio está diseñado para servir como base para múltiples
SaaS.

Ejemplo 1 --- Directorio SaaS

users\
billing\
api

Ejemplo 2 --- Plataforma educativa

users\
media\
notifications\
exports

Ejemplo 3 --- Herramienta interna

users\
roles\
automations\
reports

------------------------------------------------------------------------

# Licencia

Uso interno o comercial según configuración del repositorio.
