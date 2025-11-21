# PROJECT_CONTEXT.md - Sistema de Gestión de Proyectos Académicos

## 1. PERFIL DEL AGENTE Y STACK TECNOLÓGICO
**Rol:** Arquitecto de Software Senior y Desarrollador Fullstack Laravel.
**Objetivo:** Construir un sistema robusto, escalable y seguro siguiendo principios SOLID.

### Stack Tecnológico
* **Backend:** Laravel 11+ (PHP 8.2+).
* **Base de Datos:** MySQL (con Eloquent ORM).
* **Frontend:** Blade Templates + Alpine.js (Interactividad ligera) + Tailwind CSS.
* **Autenticación:** Laravel Breeze (Blade Stack).
* **Extras:** Chart.js (Gráficos), DomPDF (Reportes).

---

## 2. ESQUEMA DE BASE DE DATOS (FUENTE DE VERDAD)
*Las migraciones ya incluyen SoftDeletes e Integridad Referencial.*

### Tablas Maestras
* **`users`**: Autenticación central.
* **`roles`**: `id`, `nombre` (Admin, Juez, Participante).
* **`perfiles`**: `id`, `nombre` (Roles técnicos: Programador, Diseñador, etc.).
* **`carreras`**: `id`, `nombre`, `clave`.
* **`eventos`**: `id`, `nombre`, `descripcion`, `fecha_inicio`, `fecha_fin`.

### Entidades Principales
* **`participantes`**: Extiende `users`. Cols: `user_id`, `carrera_id`, `no_control`.
* **`equipos`**: `id`, `nombre`.
* **`proyectos`**: `id`, `equipo_id` (Unique), `evento_id`, `nombre`, `descripcion`, `repositorio_url`.
* **`avances`**: `id`, `proyecto_id`, `descripcion`, `fecha`.

### Tablas Pivote & Relaciones
* **`user_rol`**: `[user_id, rol_id]`. (Un usuario puede tener múltiples roles).
* **`equipo_participante`**: `[equipo_id, participante_id, perfil_id]`. (Asigna participante a equipo con un rol técnico).

### Evaluación y Resultados
* **`criterio_evaluacion`**: `id`, `evento_id`, `nombre`, `ponderacion`.
* **`calificaciones`**: `id`, `proyecto_id`, `juez_user_id`, `criterio_id`, `puntuacion`.
    * *Constraint:* Unique `[proyecto_id, juez_user_id, criterio_id]`.
* **`constancias`**: `id`, `participante_id`, `evento_id`, `tipo`, `archivo_path`, `codigo_qr`.

---

## 3. REGLAS DE NEGOCIO (LOGICA DURA)

### Reglas de Equipos
1.  **Multidisciplinarios:** Se debe validar que los integrantes sean de distintas carreras (deseable) o roles técnicos variados.
2.  **Restricción de Creación:** Un participante **NO** puede crear un equipo estando solo. Mínimo 2 integrantes iniciales.
3.  **Inmutabilidad:** No se pueden eliminar equipos una vez que `evento->fecha_inicio` ha pasado.

### Reglas de Evaluación
1.  **Cálculo:** La nota final es el promedio ponderado de todas las calificaciones de los jueces.
2.  **Escala:** 0 a 100.
3.  **Visualización:** Los participantes solo ven gráficas de avance si tienen equipo asignado.

### Reglas de Acceso (Roles)
* **Admin:** Acceso total. Puede crear Jueces y Admins.
* **Juez:** Solo ve proyectos del evento asignado. Puede editar equipos (nombres/miembros) si es necesario.
* **Participante:** Solo puede editar su perfil y su equipo (si es líder/creador).

---

## 4. FLUJO DE VISTAS Y UX (REQUERIMIENTOS DE PANTALLA)

### A. Vistas Generales (Layouts)
* Login debe permitir seleccionar contexto implícito o redirigir según rol.
* **Componentes Globales:** `<x-calendario>`, `<x-carrusel-eventos>`, `<x-grafico-avance>`.

### B. Rol: Administrador (`/admin`)
1.  **Dashboard:** Calendario y métricas generales.
2.  **Usuarios:** CRUD completo de Jueces y Admins.
3.  **Equipos:** Supervisión, edición forzada de miembros.
4.  **Proyectos:** Vista de lista, asignación de criterios de evaluación.

### C. Rol: Participante (`/participante`)
1.  **Registro Especial (`/registro-participante`):** Formulario extendido (Carrera, No. Control) obligatorio al primer login.
2.  **Dashboard:**
    * *Sin Equipo:* Muestra opciones para unirse/crear.
    * *Con Equipo:* Muestra Gráfico de Avance del proyecto.
3.  **Mi Equipo:** Gestión de miembros, asignar roles (perfiles), subir repositorio.
4.  **Constancias:** Vista de logros (Participación, 1er/2do/3er lugar) con descarga PDF y QR.

### D. Rol: Juez (`/juez`)
1.  **Dashboard:** Eventos activos asignados.
2.  **Sala de Evaluación:**
    * Lista de equipos.
    * Vista de detalle de proyecto.
    * **Formulario de Calificación:** Interfaz para puntuar criterios (1-100) con feedback visual.
3.  **Gestión Técnica:** Puede editar criterios de evaluación y modificar equipos (fix names/members).

---

## 5. ESTRUCTURA DE DIRECTORIOS SUGERIDA (BLADE)

```text
resources/views/
├── layouts/
│   ├── app.blade.php (Base)
│   ├── navigation.blade.php
│   ├── admin.blade.php
│   ├── juez.blade.php
│   └── participante.blade.php
├── components/ (Blade Components)
│   ├── ui/ (Botones, Cards, Modales)
│   └── widgets/ (Calendario, Graficos)
├── admin/
│   ├── dashboard.blade.php
│   ├── usuarios/
│   └── ...
├── juez/
│   ├── dashboard.blade.php
│   ├── evaluar/
│   └── ...
└── participante/
    ├── dashboard.blade.php
    ├── registro/
    ├── equipo/
    └── constancias/