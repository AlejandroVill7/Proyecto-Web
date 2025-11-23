# PROJECT_CONTEXT.md - Sistema de Gestión de Proyectos Académicos

## 1. ESTADO DEL PROYECTO (22/11/2025)

Versión: 0.8.0 (Beta - Admin Module Complete)  
Rama Actual: main (merged from feat/admin-gestion-total)

Módulos Completados

- Arquitectura Base: Autenticación por Roles (Middleware), redirecciones en login, base de datos normalizada.
- Módulo Administrador (Completo):
  - Dashboard: Métricas con Chart.js y calendario de eventos.
  - Usuarios: CRUD con asignación de roles y filtros.
  - Eventos: Gestión completa + definición de criterios de evaluación ponderados.
  - Equipos: Supervisión, edición forzada y gestión de miembros (buscador integrado con Alpine.js).
  - Proyectos: Cálculo automático de rankings y puntajes.
  - Resultados: Tabla de ganadores y generación de constancias PDF.
- Módulo Juez (Parcial):
  - Dashboard con lista de pendientes.
  - Interfaz de evaluación (sliders) funcional.
- UX/UI:
  - Modo oscuro: soporte completo (toggle en navegación, estilos Tailwind dark:).
  - Navegación: menús dinámicos según rol.

## 2. REGLAS DE NEGOCIO & STACK TÉCNICO

Stack Tecnológico

- Backend: Laravel 11, PHP 8.2+.
- Base de Datos: MySQL (Eloquent ORM).
- Frontend: Blade, Alpine.js, Tailwind CSS.

Librerías Clave

- barryvdh/laravel-dompdf: para diplomas y constancias.
- Chart.js: para gráficos de métricas.

Lógica de Evaluación (Implementada)

- Puntaje por criterio: (Promedio de jueces para el criterio X) * (Ponderación del criterio X) / 100.
- Nota final: suma de puntajes ponderados por criterio.
- Ganadores: ordenamiento descendente por sumatoria total de puntos ponderados.
- Escala: 0 a 100.

## 3. RESUMEN DEL ESQUEMA DE DATOS (Fuente de verdad)

- Tablas maestras: users, roles, perfiles, carreras, eventos.
- Entidades principales: participantes (extiende users), equipos, proyectos, avances.
- Tablas pivote: user_rol, equipo_participante.
- Evaluación: criterio_evaluacion, calificaciones (unique: [proyecto_id, juez_user_id, criterio_id]).
- Constancias: constancias con archivo y código QR.

## 4. SIGUIENTES PASOS (ROADMAP)

Foco: Módulo Participante

- Registro extendido obligatorio al primer login (/registro-participante) para capturar No. Control y Carrera.
- Gestión de equipos (alumno):
  - Crear equipo (validación: mínimo 2 integrantes).
  - Unirse a equipo (mediante código o invitación).
  - Subir repositorio/información del proyecto.
- Mis Resultados:
  - Vista para descargar constancias una vez cerrado el evento.
- Integraciones a mediano plazo:
  - Mejoras en reportes y exportes CSV/PDF.
  - Notificaciones internas (email/laravel notifications).

## 5. GUÍAS DE DESARROLLO (AI AGENTS)

- Estilo de código: mantener controladores delgados ("Skinny Controllers"). Aplicar principios SOLID y responsabilidad única.
- Validaciones: usar FormRequests para todas las entradas que escriben en BD.
- Vistas: usar componentes Blade reutilizables (<x-input>, <x-table>, etc.). Todos los nuevos componentes deben incluir soporte dark: con utilidades de Tailwind.
- JS: preferir Alpine.js para interactividad ligera (modales, dropdowns, toggles). Evitar jQuery salvo necesidad justificada.
- Tests: agregar pruebas unitarias y de integración para lógica de negocio crítica (cálculo de puntajes, reglas de equipos).
- Seguridad: validar permisos con middleware y Gates/Policies; proteger rutas administrativas y de juez.
- Performance: usar eager loading donde corresponda y paginación para listados grandes.
- Documentación: actualizar README/migrations cuando se añadan nuevas relaciones o pivotes.

## 6. NOTAS PARA DESARROLLADORES / IA

- Al crear nuevas relaciones N:M, especificar siempre el nombre de la tabla pivote en la definición belongsToMany.
- Usar User::getDashboardRouteName() para resolver la ruta de inicio del usuario en la navegación.
- Mantener convenciones de nombres en español para tablas y modelos cuando el dominio del proyecto lo requiera.
- Añadir withTimestamps() en relaciones pivot que requieran marcas temporales.
- Registrar y documentar cambios en migraciones y seeders para facilitar despliegues.

Fecha de actualización: 22 de Noviembre, 2025  
Versión: 0.8.0 (Beta - Admin Module Complete)

# PROJECT_CONTEXT.md - Sistema de Gestión de Proyectos Académicos

## 1. ESTADO DEL PROYECTO (23/11/2025)

**Versión:** 0.9.0 (RC - Módulos Admin y Participante Completos)  
**Rama Actual:** main (merged from feature/modulo-participante-registro)

### Módulos Completados

* **Arquitectura Base:** Autenticación por Roles (Middleware), redirecciones en login, base de datos normalizada.
* **Módulo Administrador (Completo):**
    * Gestión total de Usuarios, Eventos, Equipos y Criterios.
    * Dashboard métrico y generación de reportes globales.
* **Módulo Participante (Completo):**
    * **Registro:** Flujo forzoso para capturar No. Control, Carrera y Teléfono.
    * **Dashboard:** Vista horizontal con Gráfico de Radar (Chart.js) mostrando desempeño por criterio, Calendario de eventos próximos y accesos directos.
    * **Gestión de Equipos:**
        * *Creación:* Transaccional (Equipo + Proyecto + Asignación Líder).
        * *Unirse:* Buscador con filtros y validación de cupos.
        * *Gestión:* Interfaz con Alpine.js para buscar alumnos disponibles y administrar miembros.
    * **Bitácora:** Sistema de timeline para registrar avances del proyecto.
    * **Resultados:** Descarga autónoma de Constancias (PDF) con cálculo de ranking en tiempo real (1ro, 2do, 3er lugar o Participación).
* **Módulo Juez (Parcial):**
    * Dashboard básico.
    * Interfaz de evaluación funcional (sliders).

### UX/UI
* **Modo Oscuro:** Soporte completo en todas las vistas nuevas (Gráficos adaptativos, tablas, dropdowns).
* **Interactividad:** Alpine.js utilizado para buscadores asíncronos y menús.

## 2. REGLAS DE NEGOCIO & STACK TÉCNICO

### Stack Tecnológico
* **Backend:** Laravel 11, PHP 8.2+.
* **Base de Datos:** MySQL/PostgreSQL.
* **Frontend:** Blade, Alpine.js, Tailwind CSS.
* **Librerías Clave:** `barryvdh/laravel-dompdf` (PDF), `Chart.js` (Gráficos Radar/Barras).

### Lógica de Evaluación
* **Puntaje:** `(Promedio Jueces × Ponderación Criterio) / 100`.
* **Ranking:** Dinámico basado en la sumatoria total.
* **Constancias:** El sistema determina automáticamente el tipo de diploma basado en la posición del equipo al momento de la descarga.

## 3. ESQUEMA DE DATOS (Actualizado)

* **Tablas maestras:** `users`, `roles`, `perfiles`, `carreras`, `eventos`.
* **Participantes:** `participantes` (extiende users, incluye teléfono y no_control).
* **Proyectos:** `proyectos` (repo_url, descripción). Relación 1:1 con Equipos.
* **Avances:** `avances` (Tabla nueva: proyecto_id, descripcion, fecha).
* **Evaluación:** `criterio_evaluacion`, `calificaciones`.

## 4. SIGUIENTES PASOS (ROADMAP)

**Foco Inmediato: Módulo Juez (Fase Final)**

1.  **Listado de Proyectos:** Vista para que el juez vea qué equipos tiene asignados o disponibles para evaluar.
2.  **Detalle de Evaluación:** Mejorar la vista de sliders para mostrar el cálculo en tiempo real.
3.  **Feedback:** Permitir al juez dejar comentarios de retroalimentación (opcional).

**Mantenimiento:**
* Refactorizar vistas duplicadas (ej. PDF de constancias) para usar componentes compartidos.
* Pruebas de estrés con múltiples jueces calificando simultáneamente.

## 5. GUÍAS DE DESARROLLO

* **Chart.js:** Al implementar gráficos, configurar siempre los colores de texto y rejilla para soportar Dark Mode (usar variables condicionales JS).
* **PDF:** Usar `stream()` para previsualización en navegador en lugar de `download()`.
* **Validaciones:** Mantener lógica de negocio (ej. "Mínimo 2 integrantes") en Controladores o FormRequests, no solo en vista.

---
*Fecha de actualización: 23 de Noviembre, 2025*