# Convenciones del proyecto

## Permisos
- Formato: `modulo.accion`
- Ejemplos: `users.view`, `support.reply`, `api-keys.manage`

## Rutas
- Admin: `/admin/...`
- Member: `/member/...`
- Public/Auth: rutas publicas y de autenticacion

## Policies
- Usar policies para validar acceso por recurso.
- No depender solo del middleware de ruta.

## Feature Flags vs Modulos
- Modulos: habilitan o deshabilitan funcionalidades completas.
- Feature flags: habilitan funciones internas de un modulo.

## Servicios
Usar los servicios base en lugar de logica duplicada:
- `AccessService` para acceso + features + modulos
- `ModuleService` para modulos
- `FeatureService` para flags
- `SettingService` para configuracion

## Comentarios
- Todos los comentarios en codigo deben estar en espanol.
