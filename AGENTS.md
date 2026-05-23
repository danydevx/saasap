# AGENTS.md

# Guía para agentes internos

## Objetivo

Desarrollar y mantener una aplicación SaaS modular utilizando Laravel + Inertia + Vue 3, priorizando:

- simplicidad
- mantenibilidad
- seguridad
- reutilización
- rendimiento
- compatibilidad con la arquitectura existente

El código generado debe ser consistente, escalable y fácil de mantener a largo plazo.

---

# Stack oficial

## Backend
- PHP 8.3+
- Laravel 12
- MySQL / MariaDB
- Spatie Permission

## Frontend
- Vue 3
- Composition API
- `<script setup>`
- Inertia.js
- Pinia
- Axios
- Bootstrap 5.3
- Bootstrap Icons
- Less

---

# Reglas críticas

## Arquitectura
- NO usar TypeScript.
- NO usar Vue Options API.
- SIEMPRE usar Composition API.
- SIEMPRE usar `<script setup>`.
- NO introducir librerías nuevas sin justificación clara.
- NO romper compatibilidad con módulos existentes.
- NO refactorizar módulos no relacionados.
- Mantener estructura modular y reutilizable.
- Mantener compatibilidad con la arquitectura actual del proyecto.

---

# Convenciones generales

## Código
- Todos los comentarios deben estar en español.
- NO usar emoticons ni emojis.
- Mantener código limpio y legible.
- Evitar complejidad innecesaria.
- Evitar sobreingeniería.
- Priorizar claridad sobre abstracción.

## Naming
- Variables y funciones descriptivas.
- Componentes Vue en PascalCase.
- Stores usando formato:
```txt
catalog.store.js
editor.store.js
users.store.js
```

## Organización Vue
Orden recomendado dentro de `<script setup>`:

1. imports
2. props
3. emits
4. stores
5. refs/reactive
6. computed
7. watchers
8. methods/functions
9. lifecycle hooks

---

# Frontend

## UI
- Usar Bootstrap 5.3 como base principal.
- Evitar CSS innecesario cuando Bootstrap ya resuelve el problema.
- Usar Bootstrap Icons.
- Mantener consistencia visual con el sistema actual.
- Mantener diseño responsive.
- Priorizar mobile-first cuando aplique.

## Layouts
Usar layouts existentes:
- `AdminLayout`
- `MemberLayout`

## Inertia
Props globales compartidos:
```js
auth
features
modules
```

## Permisos en frontend
- Ocultar UI cuando el usuario no tenga acceso.
- Nunca confiar únicamente en frontend.
- Validar SIEMPRE en backend.

## Formularios
- Usar `useForm()` de Inertia.
- Mostrar errores provenientes del backend.
- Mantener UX consistente.
- Validaciones backend obligatorias.

## Componentes reutilizables
Priorizar reutilización de:
- Fields
- Tables
- Modals
- Cards
- Panels
- Toolbars
- Dropdowns
- ConfirmDialogs

---

# Estilos

## LESS
- Todos los estilos personalizados deben ir en archivos `.less`.
- Mantener variables centralizadas.
- Evitar estilos inline.

## CSS
- Evitar `!important`.
- Preferir variables CSS.
- Evitar duplicación de estilos.
- Mantener coherencia visual.

---

# Backend

## Permisos
Formato obligatorio:
```txt
modulo.accion
```

Ejemplos:
```txt
users.view
users.create
users.edit
users.delete
```

## Módulos
Las rutas protegidas por módulos deben usar:
```php
module:clave
```

## Policies
- Implementar policies para recursos sensibles.
- NO depender únicamente de middleware.
- Validar ownership del recurso cuando aplique.

## Servicios reutilizables
Priorizar reutilización de:
- `SettingService`
- `FeatureService`
- `ModuleService`
- `AccessService`

## Controladores
- Mantener controladores delgados.
- Mover lógica compleja a:
  - Services
  - Actions
  - Helpers reutilizables

## Requests
- Usar FormRequest cuando tenga sentido.
- Centralizar validaciones complejas.

## Models
- Definir correctamente:
  - fillables
  - casts
  - scopes
  - relaciones
- Mantener nombres coherentes.

---

# Seguridad

## Backend
- Validar permisos SIEMPRE.
- Validar módulos activos.
- Validar feature flags.
- Validar ownership del recurso.
- Nunca confiar datos provenientes del frontend.

## Frontend
- No exponer secretos.
- No exponer lógica sensible.
- No exponer permisos críticos únicamente en UI.

---

# Base de datos

## Migraciones
- Mantener nombres consistentes.
- NO eliminar columnas existentes sin autorización.
- Usar foreign keys correctamente.
- Evitar cambios destructivos innecesarios.

## Seeders
- Crear permisos automáticamente.
- Mantener datos iniciales coherentes.
- Mantener módulos sincronizados con permisos.

---

# Crear nuevos módulos

## Flujo obligatorio

1. Registrar módulo en:
```txt
system_modules
```

2. Agregar middleware:
```php
module:clave
```

3. Crear permisos correspondientes.

4. Implementar policies si aplica.

5. Registrar menús y navegación.

6. Agregar documentación mínima.

7. Agregar feature flags si aplica.

---

# Git

## Reglas
- No modificar múltiples módulos innecesariamente.
- Realizar commits pequeños y claros.
- Evitar archivos basura.
- Respetar `.gitignore`.

## Nunca subir
- `.env`
- logs
- cache
- archivos temporales
- credenciales
- dumps
- builds innecesarios

---

# Performance

## Frontend
- Lazy load cuando aplique.
- Evitar watchers innecesarios.
- Evitar rerenders costosos.
- Evitar lógica pesada dentro de templates.
- Mantener componentes desacoplados.

## Backend
- Evitar N+1 queries.
- Usar eager loading correctamente.
- Usar paginación en listados grandes.
- Evitar consultas duplicadas.
- Optimizar relaciones y scopes.

---

# Convenciones Vue

## Preferido
```js
const form = useForm({})
```

## Evitar
- componentes gigantes
- lógica duplicada
- watchers innecesarios
- computed excesivos
- mezclar responsabilidades

---

# Convenciones Laravel

## Preferir
- Services
- Policies
- FormRequests
- Scopes
- Resources
- Eager loading

## Evitar
- lógica compleja en controllers
- consultas repetidas
- validaciones duplicadas
- lógica de permisos en vistas únicamente

---

# Comandos útiles

```bash
php artisan saas:install
php artisan saas:create-superadmin
php artisan migrate
php artisan db:seed
php artisan optimize:clear

npm install
npm run dev
npm run build
```

---

# Filosofía del proyecto

El proyecto prioriza:

- simplicidad
- estabilidad
- modularidad
- rendimiento
- mantenibilidad
- UX clara
- escalabilidad controlada

Los agentes deben generar código:
- limpio
- entendible
- reutilizable
- consistente
- optimizado
- listo para producción
- alineado con la arquitectura existente