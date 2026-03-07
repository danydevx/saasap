# Modulos del sistema

Cada modulo puede activarse o desactivarse desde `system_modules`.

## Lista base

| Key | Nombre | Dependencias | Notas |
| --- | --- | --- | --- |
| users | Usuarios | - | Modulo critico |
| roles | Roles | users | Modulo critico |
| permissions | Permisos | roles | Modulo critico |
| settings | Settings | - | Modulo critico |
| billing | Billing | users | Planes, pagos, suscripciones |
| support | Soporte | users | Tickets y help center |
| exports | Exportaciones | - | Exportacion de datos |
| media | Archivos | users | Media y documentos |
| api | API | users | API keys |
| webhooks | Webhooks | users | Webhooks salientes |
| automations | Automatizaciones | - | Motor de automatizacion |
| legal | Legales | users | Documentos legales |
| notifications | Notificaciones | users | In-app y email |
| announcements | Anuncios | - | Anuncios globales |
| feature-flags | Feature Flags | settings | Flags del sistema |
| integrations | Integraciones | api, webhooks | Portal de integraciones |

## Regla
- Si un modulo esta desactivado, sus rutas deben tener `module:clave`.
- El menu debe ocultar secciones del modulo.
- Los modulos criticos no se pueden desactivar (users, roles, permissions, settings).
