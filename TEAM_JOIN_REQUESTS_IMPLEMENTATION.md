# 📋 Sistema de Solicitudes de Unión a Equipos - Implementación Completada

## ✅ Completado en esta rama `feature/team-join-requests`

### 1. **Base de Datos**
- ✅ Migration: `create_solicitudes_equipo_table`
  - Tabla con campos: equipo_id, participante_id, mensaje, estado, respondida_por_participante_id, respondida_en
  - Estados: pendiente, aceptada, rechazada

- ✅ Migration: `fix_solicitudes_unique_constraint`
  - Cambio de UNIQUE GLOBAL a UNIQUE CONDICIONAL
  - UNIQUE INDEX solo para estado='pendiente'
  - Permite múltiples solicitudes con diferentes estados
  - Habilita rejoin después de salir del equipo

### 2. **Modelos**
- ✅ **SolicitudEquipo** (Nuevo)
  - Relaciones: equipo(), participante(), respondidaPor()
  - Scopes: pendiente(), aceptada(), rechazada()

- ✅ **Equipo** (Actualizado)
  - Relaciones: solicitudes(), solicitudesPendientes()
  - Método: getLider()

- ✅ **Participante** (Actualizado)
  - Relaciones: solicitudes(), solicitudesRecibidas()
  - Método: esLiderDe()

### 3. **Eventos**
- ✅ **SolicitudEquipoEnviada**: Cuando participante envía solicitud
- ✅ **SolicitudEquipoAceptada**: Cuando líder acepta solicitud
- ✅ **SolicitudEquipoRechazada**: Cuando líder rechaza solicitud

### 4. **Controlador**
- ✅ **SolicitudEquipoController** con métodos:
  - `crearSolicitud()`: Participante envía solicitud
  - `verSolicitudesEquipo()`: Líder ve solicitudes pendientes
  - `misSolicitudes()`: Participante ve historial
  - `aceptar()`: Líder acepta y agrega al equipo
    - ✅ **AUTO-RECHAZO**: Rechaza automáticamente otras solicitudes pendientes
  - `rechazar()`: Líder rechaza solicitud

### 5. **Rutas**
```
POST   /participante/solicitudes/{equipo}/crear           → crearSolicitud
GET    /participante/solicitudes/mis-solicitudes          → misSolicitudes
GET    /participante/solicitudes/equipo/{equipo}          → verSolicitudesEquipo
POST   /participante/solicitudes/{solicitud}/aceptar      → aceptar
POST   /participante/solicitudes/{solicitud}/rechazar     → rechazar
```

### 6. **Vistas**
- ✅ **mis-solicitudes.blade.php**: Panel para participante
  - Ver todas sus solicitudes (pendientes, aceptadas, rechazadas)
  - Status badge de cada solicitud
  - Información del líder que respondió

- ✅ **equipo-solicitudes.blade.php**: Panel para líder
  - Ver solicitudes pendientes
  - Información del participante (nombre, email, no. control, carrera)
  - Botones para aceptar/rechazar

---

## ⏳ Próximos Pasos (TODO)

### 1. **Implementar Notificaciones por Email**
- [ ] Crear Mailable para SolicitudEquipoEnviada
- [ ] Crear Mailable para SolicitudEquipoAceptada
- [ ] Crear Mailable para SolicitudEquipoRechazada
- [ ] Configurar EventServiceProvider para listeners

### 2. **Mejorar Vista join.blade.php**
- [ ] Modificar modal para elegir entre:
  - Unirse directamente (si el equipo permite)
  - Enviar solicitud con mensaje
- [ ] Agregar validaciones en cliente

### 3. **Agregar Notificaciones en Tiempo Real**
- [ ] Badge en header con número de solicitudes pendientes
- [ ] Notificación visual cuando hay nuevas solicitudes

### 4. **Agregar Validaciones Adicionales**
- [ ] Validar que el equipo no esté completo (máx 5)
- [ ] Validar que sea durante el evento activo
- [ ] Validar que no haya rechazos previos del mismo participante

### 5. **Agregar Tests**
- [ ] Test para crear solicitud
- [ ] Test para aceptar solicitud
- [ ] Test para rechazar solicitud
- [ ] Test de permisos

### 6. **Documentación**
- [ ] API endpoint documentation
- [ ] User flow diagram
- [ ] Email template documentation

---

## 🔄 Commits Realizados

1. `chore: Add participant users and seed database refresh`
   - Creados 4 usuarios participantes de prueba

2. `feat(solicitudes): Implement team join requests system`
   - Migration, Model, Events, Controller, Routes

3. `feat(views): Add team join request views`
   - Vistas para participante y líder

---

## 🚀 Cómo Usar (Base)

### Para un Participante:
1. Ir a `/participante/equipos/join` (Explorar Equipos)
2. Encontrar un equipo que le interese
3. Enviar solicitud con mensaje opcional
4. Ir a `/participante/solicitudes/mis-solicitudes` para ver estado

### Para un Líder:
1. Recibir notificación de nueva solicitud
2. Ir a `/participante/solicitudes/equipo/{id}` para ver solicitudes
3. Aceptar o rechazar
4. Si acepta, el participante se agrega al equipo automáticamente

---

## 📝 Notas Técnicas

- Los eventos se disparan correctamente pero NO envían email (configurado en log)
- Cambiar `MAIL_MAILER` en `.env` a `smtp` para enviar reales
- Sistema funciona correctamente con la estructura actual del proyecto
- Compatible con roles y permisos existentes

---

## 🔐 Validaciones y Protecciones

### **1. Triple Validación en Cada Paso**

```
EquipoController.join()
├─ ¿Está en otro equipo? → Error
├─ ¿Equipo completo (5 miembros)? → Error
└─ ¿Hay solicitud pendiente? → Error

SolicitudEquipoController.crearSolicitud()
├─ ¿Está en este equipo? → Error
├─ ¿Está en otro equipo? → Error
└─ ¿Hay solicitud pendiente? → Error

Base de Datos
└─ UNIQUE INDEX (equipo_id, participante_id) WHERE estado='pendiente'
```

### **2. Auto-Rechazo Automático**

Cuando un líder ACEPTA una solicitud:
```php
// 1. Marcar como aceptada
$solicitud->update(['estado' => 'aceptada']);

// 2. Agregar participante al equipo
$equipo->participantes()->attach($participante_id, ['perfil_id' => 1]);

// 3. AUTO-RECHAZO de todas las otras PENDIENTES
SolicitudEquipo::where('participante_id', $participante_id)
    ->where('estado', 'pendiente')
    ->where('id', '!=', $solicitud->id)
    ->update(['estado' => 'rechazada']);
```

### **3. UNIQUE Condicional en BD**

**Permite:**
- Equipo A: ACEPTADA (participante en equipo)
- Equipo A: NUEVA PENDIENTE (si se sale y reintenía)

**Previene:**
- Equipo A: 2 PENDIENTES (de la misma persona)

---

## 🧪 Verificación de Funcionalidad

**Comando disponible:**
```bash
php artisan solicitudes:verificar
```

**Muestra:**
- ✅ Todas las solicitudes en BD con su estado
- ✅ Lo que VE cada líder en su dashboard
- ✅ Estadísticas globales

**Ejemplo:**
```
=== TODAS LAS SOLICITUDES ===
[PENDIENTE] Equipo 12 (DevcITO): juan
[ACEPTADA] Equipo 11 (Eslabon Programado): juan

=== QUÉ VE CADA LÍDER ===
📋 Pablo Lider (Líder de DevcITO):
   Solicitudes pendientes: 1
   • juan

Tellez NO VE NADA (su solicitud está ACEPTADA, no PENDIENTE)
```

