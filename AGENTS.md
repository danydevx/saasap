# Guia para agentes internos

## Principios
- Mantener compatibilidad con lo ya construido.
- Evitar refactors innecesarios.
- Preferir cambios incrementales y seguros.

## Comentarios en codigo
- Todos los comentarios deben estar en espanol.
- No incluir comentarios en ingles.

## Convenciones clave
- Permisos: `modulo.accion`.
- Modulos activables: usar `module:clave` en rutas.
- Policies: validar acceso por recurso, no solo por rutas.

## Checklist de cambios
- Actualizar seeders si se agregan permisos o modulos.
- Revisar menu admin/member si se agregan secciones nuevas.
- Evitar exponer secretos en frontend.

## Comandos utiles
- `php artisan saas:install`
- `php artisan saas:create-superadmin`
