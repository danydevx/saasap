# Core del Starter Kit

Este starter kit consolida las capas esenciales de un SaaS para reutilizarlas en nuevos productos.

## Areas principales
- Admin: gestion operativa, configuracion y control
- Member: area de uso para clientes finales
- Public/Auth: autenticacion y flujos publicos

## Servicios clave
- `SettingService`
- `FeatureService`
- `ModuleService`
- `AccessService`
- `TemplateRenderService`
- `AutomationService`
- `NotificationPreferenceService`
- `SecurityService`

## Comandos base
- `saas:install`
- `saas:create-superadmin`

## Principio
Cada modulo debe:
- tener permisos claros
- respetar feature flags y modulos activables
- usar policies para recursos

Consulta `docs/MODULOS.md` para la lista completa.
