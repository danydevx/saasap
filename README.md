# Starter Kit SaaS Interno

Base reusable para lanzar productos SaaS con rapidez y consistencia.

## Stack
- Laravel 12
- Inertia + Vue 3 (Composition API, `<script setup>`)
- Bootstrap 5.3
- Spatie Permission

## Modulos principales
- Auth y registro
- Roles y permisos (RBAC)
- Admin / Member dashboards
- Settings, Feature Flags y Modulos activables
- Billing, pagos y suscripciones
- Soporte y help center
- Notificaciones, anuncios y automatizaciones
- Integraciones (API keys y webhooks)

## Requisitos
- PHP 8.2+
- Base de datos configurada
- Node/Bun segun el proyecto

## Instalacion rapida
1. Configurar `.env`
2. `php artisan migrate`
3. `php artisan saas:install`
4. `php artisan saas:create-superadmin`
5. `php artisan queue:work`
6. `php artisan schedule:work`

## Comandos principales
- `php artisan saas:install`
- `php artisan saas:create-superadmin`

## Documentacion interna
- `docs/architecture.md`
- `docs/modules.md`
- `docs/permissions.md`
- `docs/settings-features-modules.md`
- `docs/local-setup.md`
- `docs/deployment.md`
- `docs/new-module-checklist.md`

## Nota
Este proyecto es un starter kit interno. Ajusta modulos y features segun el producto objetivo.
