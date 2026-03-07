# Instalacion del Starter Kit

## Requisitos
- PHP 8.2+
- Node/Bun segun el proyecto
- Base de datos configurada

## Checklist
1. Configurar `.env`
2. Instalar dependencias
3. `php artisan saas:install`
4. `php artisan saas:create-superadmin`
5. Ejecutar colas: `php artisan queue:work`
6. Ejecutar scheduler: `php artisan schedule:work`

## Seed de usuario de prueba (opcional)
Si necesitas un usuario de prueba en local, define:
`SAAS_SEED_TEST_USER=true`

## Comandos utiles
- `php artisan saas:install` ejecuta seeders base
- `php artisan saas:create-superadmin` crea el super-admin

## Ajustes iniciales
- Revisar Settings en `/admin/settings`
- Ajustar modulos en `/admin/modules`
- Revisar Feature Flags en `/admin/feature-flags`
