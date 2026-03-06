# AGENTS.md

## Proyecto

SaaS multiusuario construido con:

-   Laravel 12
-   Inertia.js
-   Vue 3
-   JavaScript (NO TypeScript)
-   Pinia
-   Axios
-   Bootstrap 5.3
-   Bootstrap Icons
-   vue-toast-notification
-   Spatie laravel-permission

El objetivo es desarrollar:

-   dashboard administrativo
-   frontend público
-   módulos CRUD reutilizables
-   gestión de usuarios, roles y permisos
-   base escalable para SaaS multiusuario

------------------------------------------------------------------------

## Reglas críticas (obligatorias)

Estas reglas no deben romperse.

1.  Usar Vue 3 con JavaScript. Nunca usar TypeScript.
2.  Usar Composition API con `<script setup>` obligatoriamente.
3.  No usar Options API bajo ninguna circunstancia.
4.  El panel usa Inertia.js. No crear SPA desacoplada ni API
    innecesaria.
5.  UI basada únicamente en Bootstrap 5.3.
6.  No diseñar estilos personalizados.
7.  Íconos únicamente Bootstrap Icons.
8.  Estado global con Pinia solo cuando sea necesario.
9.  Notificaciones con vue-toast-notification.
10. Peticiones HTTP con axios cuando aplique.
11. Código simple, limpio y listo para producción.
12. Entregar código completo listo para copiar y pegar.
13. No usar emojis.

------------------------------------------------------------------------

## Tecnologías permitidas

### Backend

-   Laravel 12
-   Inertia Laravel
-   Spatie Permission

### Frontend

-   Vue 3
-   Pinia
-   Axios
-   Bootstrap 5.3
-   Bootstrap Icons
-   vue-toast-notification
-   vue-notify

------------------------------------------------------------------------

## Tecnologías prohibidas

No usar:

-   TypeScript
-   Tailwind
-   Vuetify
-   Quasar
-   PrimeVue
-   UI frameworks externos
-   librerías innecesarias
-   sistemas de estilos personalizados

------------------------------------------------------------------------

## Reglas Laravel 12

Laravel 12 usa configuración moderna.

No usar `Kernel.php` como referencia.

La configuración central está en:

`bootstrap/app.php`

Middlewares se registran con:

`->withMiddleware()`

------------------------------------------------------------------------

## Spatie Permission

Middleware correcto:

`Spatie\Permission\Middleware\RoleMiddleware`

No usar `Middlewares`.

Agregar `HasRoles` al modelo `User`.

------------------------------------------------------------------------

## Reglas Vue

Todos los componentes deben usar Composition API.

Usar siempre:

`<script setup>`

Prohibido usar:

-   data()
-   methods
-   computed en Options API
-   watch en Options API
-   created()
-   mounted()

Si el agente genera Options API debe reescribir el componente usando
Composition API.

------------------------------------------------------------------------

## Reglas de UI (Bootstrap estricto)

La interfaz debe usar únicamente Bootstrap 5.3.

No diseñar estilos personalizados.

No inventar:

-   colores
-   paletas
-   gradientes
-   sombras personalizadas
-   temas visuales
-   variables CSS
-   estilos globales

Usar únicamente clases estándar de Bootstrap:

-   grid
-   container
-   cards
-   tables
-   forms
-   buttons
-   utilities

------------------------------------------------------------------------

## Estructura del frontend

    resources/js
    ├── app.js
    ├── Pages
    │   ├── Dashboard.vue
    │   ├── Users
    │   ├── Roles
    │   ├── Permissions
    │   └── Front
    ├── Layouts
    │   ├── AdminLayout.vue
    │   └── FrontLayout.vue
    ├── Components
    ├── Stores
    ├── Composables
    └── Services

------------------------------------------------------------------------

## Reglas Inertia

Las páginas se renderizan con:

`Inertia::render(...)`

Las páginas viven en:

`resources/js/Pages`

Blade solo se usa como root view de Inertia.

------------------------------------------------------------------------

## Reglas CRUD

Todos los CRUD deben seguir el mismo patrón visual:

-   PageTitle
-   Breadcrumb
-   Searchbar
-   Formularios consistentes
-   useForm
-   toasts
-   ConfirmDialog
-   card layout

------------------------------------------------------------------------

## Reglas para formularios

1.  Formularios claros y consistentes.
2.  Validaciones visibles.
3.  Evitar duplicación entre create y edit.

------------------------------------------------------------------------

## Reglas para modelos

Antes de crear un modelo nuevo:

Preguntar si el modelo ya existe o si el usuario quiere mostrar su
versión.

No inventar columnas ni relaciones.

------------------------------------------------------------------------

## Dashboard

Debe incluir:

-   navbar o sidebar
-   breadcrumbs
-   tarjetas
-   tablas
-   filtros
-   búsqueda
-   formularios consistentes
-   confirmaciones
-   toasts

Debe verse limpio, rápido y profesional.

------------------------------------------------------------------------

## Frontend público

Debe incluir:

-   home
-   landing
-   páginas informativas
-   formularios
-   login
-   registro

Usar Bootstrap como base visual.

------------------------------------------------------------------------

## Multiusuario

Spatie maneja roles y permisos.

El SaaS debe contemplar:

-   users
-   organizations
-   organization_user

Filtrar datos por organización cuando aplique.

------------------------------------------------------------------------

## Comandos base

### Instalar dependencias

composer install\
npm install

### Desarrollo

npm run dev\
php artisan serve

### Producción

npm run build

### Migraciones

php artisan migrate

### Seeders

php artisan db:seed

------------------------------------------------------------------------

## Prioridad de desarrollo

1.  layout admin
2.  auth
3.  dashboard
4.  users
5.  roles
6.  permissions
7.  organizations
8.  frontend público

------------------------------------------------------------------------

## Objetivo del agente

Generar código que sea:

-   simple
-   claro
-   coherente con Laravel 12
-   compatible con Inertia + Vue
-   listo para producción
-   fácil de extender
-   visualmente profesional


## Reglas de tablas y paginación

La paginación debe hacerse siempre del lado del servidor.

No cargar todos los registros en el frontend para después paginarlos en la tabla.

Reglas obligatorias:
- usar paginación backend con Laravel
- devolver resultados paginados desde el controlador
- usar `paginate()` o `simplePaginate()` según el caso
- aplicar búsqueda, filtros y ordenamiento en la consulta del servidor
- la tabla solo debe renderizar los registros recibidos en la página actual
- conservar parámetros de búsqueda, filtros, orden y página en la URL
- no usar paginación en memoria
- no usar `all()` ni `get()` para listados grandes del dashboard

Para tablas administrativas:
- index debe consultar solo los registros necesarios para la página actual
- la UI debe consumir `links`, `current_page`, `last_page`, `per_page`, `total` y `data` 
- cada cambio de búsqueda, filtro, orden o página debe disparar una nueva petición al servidor

Si el agente genera una tabla cargando todos los registros, debe reescribirla usando paginación del servidor.