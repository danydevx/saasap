# Starter Kit SaaS Interno

Este proyecto es una base reusable para lanzar nuevos SaaS con rapidez, manteniendo una arquitectura consistente y escalable.

## Contenido principal
- Autenticacion, registro y verificacion de email
- Admin y Member dashboards
- Roles, permisos y policies (RBAC)
- Settings, feature flags y modulos activables
- Billing, pagos y suscripciones
- Soporte, help center, activity log y security events
- Notificaciones, anuncios y automatizaciones
- Integraciones: API keys y webhooks

## Documentacion interna
- `docs/STARTER.md` resumen del core y arquitectura
- `docs/MODULOS.md` listado de modulos disponibles
- `docs/CONVENCIONES.md` reglas de naming y estructura
- `docs/INSTALACION.md` guia de instalacion y bootstrap

## Primeros pasos (resumen)
1. Configurar `.env`
2. `php artisan migrate`
3. `php artisan saas:install`
4. `php artisan saas:create-superadmin`
5. `php artisan queue:work` y `php artisan schedule:work`

## Nota
Este repositorio esta pensado como starter kit interno. Ajusta modulos y features segun el producto objetivo.
