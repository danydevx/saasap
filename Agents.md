# AGENTS.md

Este proyecto es un SaaS Starter construido con:

Laravel 12  
Inertia.js  
Vue 3  
JavaScript (NO TypeScript)  
Bootstrap 5.3  
Bootstrap Icons  
Pinia  
Axios  
Spatie laravel-permission  
Stripe  

El objetivo es proporcionar una base reusable para construir múltiples aplicaciones SaaS.

---

# Reglas obligatorias

1. Usar Vue 3 con JavaScript.
2. Usar `<script setup>` obligatoriamente.
3. Usar Composition API.
4. No usar Options API.
5. No usar TypeScript.
6. El frontend usa Inertia.js (no crear SPA separada).
7. Usar Bootstrap 5.3 para UI.
8. Usar Bootstrap Icons para iconos.

---

# Arquitectura del proyecto

El sistema tiene tres áreas principales.

Admin  
Panel de administración del SaaS.

Member  
Área del usuario registrado.

Public  
Login, registro y recuperación de contraseña.

---

# Convenciones de rutas

Admin

/admin/*

Member

/member/*

Public

/login  
/register  

---

# Convención de permisos

Formato:

module.action

Ejemplos:

users.view  
users.create  
users.update  
users.delete  

---

# Convención de módulos

Los módulos del sistema se controlan mediante:

system_modules

Ejemplos de módulos:

users  
billing  
support  
media  
exports  
api  
webhooks  
automations  
legal  
notifications  

Si un módulo está desactivado:

- no debe aparecer en el menú
- sus rutas no deben ejecutarse
- sus funcionalidades deben ignorarse

---

# Feature Flags

Las features se controlan mediante feature flags.

No mezclar:

Settings  
Feature Flags  
Modules

---

# Settings

Los settings globales deben obtenerse mediante un servicio central.

No hardcodear valores del sistema.

Ejemplos:

app_name  
support_email  
billing_enabled  

---

# Permisos y seguridad

Siempre validar en backend:

auth  
rol  
permiso  
policy  
ownership  

No confiar en frontend.

---

# Convenciones frontend

Usar:

Layout Admin  
Layout Member  
Layout Auth

Componentes comunes:

PageTitle  
Breadcrumb  
Searchbar  
ConfirmDialog  

---

# CRUD

Los CRUD deben seguir estructura consistente:

Index  
Create  
Edit  

Usar:

useForm  
validaciones  
toast notifications  

---

# Nuevos módulos

Cuando el agente cree un módulo debe:

1 crear rutas  
2 crear controlador  
3 crear vistas Inertia  
4 crear permisos  
5 agregar menú  
6 respetar módulos activos  
7 respetar permisos  
8 documentar si es necesario  

---

# No hacer

No refactorizar arquitectura completa.  
No mover carpetas masivamente.  
No crear librerías nuevas innecesarias.  
No usar TypeScript.

---

# Objetivo

Mantener el proyecto consistente, seguro y reusable como SaaS starter.