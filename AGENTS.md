# Guia para agentes internos

## Stack
- Laravel 12
- Inertia + Vue 3 (Composition API, `<script setup>`)
- Bootstrap 5.3
- Spatie Permission

## Reglas generales
- Todos los comentarios en codigo deben estar en espanol.
- No usar TypeScript.
- No refactorizar modulos no relacionados.
- Mantener compatibilidad con lo existente.

## Frontend
- Usar layouts existentes: `AdminLayout` y `MemberLayout`.
- Inertia: props compartidos incluyen `auth`, `features`, `modules`.
- Ocultar UI cuando el permiso o modulo no aplica, pero siempre validar en backend.

## Backend
- Permisos: `modulo.accion`.
- Modulos: `module:clave` en rutas.
- Policies para recursos (no depender solo de middleware).
- Servicios base a reutilizar: `SettingService`, `FeatureService`, `ModuleService`, `AccessService`.

## Seguridad
- No exponer secretos en frontend.
- Validar propiedad del recurso en policies.
- Respetar modulo y feature flag antes de ejecutar acciones.

## Crear modulos nuevos
1. Definir key en `system_modules`.
2. Agregar rutas con `module:clave`.
3. Crear permisos en seeder.
4. Implementar policies si aplica.
5. Actualizar menus y docs.

## Comandos utiles
- `php artisan saas:install`
- `php artisan saas:create-superadmin`
