# Manual de Soporte — Tótem Interactivo Teatromuseo

> Guía de operaciones para personal de soporte técnico y no técnico.

---

## Tabla de contenidos

1. [Encendido y apagado](#encendido-y-apagado)
2. [Reinicio rápido](#reinicio-rápido)
3. [Limpieza de cache](#limpieza-de-cache)
4. [Diagnóstico de problemas](#diagnóstico-de-problemas)
5. [Contacto de emergencia](#contacto-de-emergencia)

---

## Encendido y apagado

### Encendido del kiosko

1. **Verificar conexión eléctrica**
   - El cable de alimentación debe estar conectado al zócalo trasero
   - LED de power en la pantalla debe estar en verde/naranja

2. **Encender la pantalla táctil**
   - Botón de power generalmente en el lateral derecho
   - Esperar 10-15 segundos para que inicie el sistema

3. **Verificar inicio de Fully Kiosk**
   - El navegador debe cargar automáticamente
   - Si no carga, tocar el icono de "Fully" en el escritorio

4. **Comprobar conectividad**
   - El tótem debe mostrar el splash inicial
   - Si aparece "Sin conexión", verificar WiFi/Ethernet

### Apagado del kiosko

**Para mantenimiento programado:**

1. Notificar al equipo de TI con 24h de anticipación
2. Durante horario de cierre del museo:
   - Mantener presionado el botón de power 5 segundos
   - Esperar a que la pantalla se apague completamente
   - Desconectar el cable de alimentación (solo si es necesario)

**IMPORTANTE:** No apagar durante horario de visitas excepto en emergencias.

---

## Reinicio rápido

### Cuándo reiniciar

- La pantalla está congelada o no responde al tacto
- Las transiciones entre pantallas no funcionan
- El idioma no cambia correctamente
- Aparecen mensajes de error persistentes

### Procedimiento de reinicio

**Opción 1: Reinicio suave (preferido)**

1. Tocar 5 veces seguidas en la esquina superior derecha
2. Esperar menú de administración
3. Seleccionar "Recargar página"
4. Esperar 10 segundos

**Opción 2: Reinicio de Fully Kiosk**

1. Arrastrar desde el borde izquierdo hacia adentro
2. Tocar el icono de "Home" para salir del modo kiosko
3. Cerrar Fully Kiosk (botón X o deslizar hacia arriba)
4. Volver a abrir la app "Fully"

**Opción 3: Reinicio completo (si las anteriores fallan)**

1. Mantener presionado el botón de power 10 segundos
2. Esperar que se apague completamente
3. Esperar 5 segundos
4. Presionar el botón de power nuevamente

---

## Limpieza de cache

### Limpieza de cache de Fully Kiosk

Si el tótem muestra información desactualizada:

1. Salir del modo kiosko (arrastrar desde borde izquierdo)
2. Tocar menú de tres puntos (⋮) en Fully
3. Ir a "Configuración" → "Privacidad"
4. Toque "Borrar datos de navegación"
5. Seleccionar "Cache" y "Cookies"
6. Confirmar con "Borrar datos"
7. Volver al modo kiosko

### Limpieza de cache del servidor

Si hay problemas con los datos de la API:

**Método 1: Vía SSH (requiere acceso técnico)**

```bash
# Conectar al servidor del tótem
ssh usuario@ip-del-totem

# Limpiar cache
cd /var/www/totem
rm -rf writable/cache/totem/*.cache

# Verificar que se limpió
ls writable/cache/totem/
```

**Método 2: Panel de administración (si está disponible)**

1. Acceder a `http://[ip-del-totem]/admin`
2. Login con credenciales de administrador
3. Ir a "Herramientas" → "Cache"
4. Clic en "Limpiar cache de API"

---

## Diagnóstico de problemas

### Problema: Pantalla en blanco

| Causa probable | Solución |
|----------------|----------|
| Fully Kiosk no inició | Reiniciar la app Fully |
| Error de red | Verificar conexión WiFi/Ethernet |
| Servidor caído | Verificar `/health` endpoint |
| Cache corrupto | Limpiar cache del servidor |

**Pasos de diagnóstico:**

1. Verificar si el splash aparece → Si no, problema de red/app
2. Verificar endpoint health: `curl http://[ip]/health`
3. Revisar logs: `tail -f writable/logs/log-$(date +%Y-%m-%d).log`

### Problema: No responde al tacto

| Causa probable | Solución |
|----------------|----------|
| Pantalla bloqueada | Reinicio suave |
| Driver táctil falló | Reinicio completo |
| Calibración perdida | Reconfigurar en Fully Kiosk |

### Problema: Idioma no cambia

1. Ir a pantalla de idioma (`/language`)
2. Seleccionar idioma deseado
3. Si persiste: limpiar cookies de Fully Kiosk
4. Si persiste: limpiar cache del servidor

### Problema: "Sin conexión" persistente

1. Verificar LED de red en la pantalla
2. Probar conexión: `ping 8.8.8.8`
3. Verificar WiFi en configuración de Android/Windows
4. Contactar equipo de red si no hay conectividad

---

## Contacto de emergencia

### Niveles de urgencia

**🔴 CRÍTICO** — Tótem no funciona durante horario de visitas
- Teléfono: [Número de TI de guardia]
- Email: soporte-urgente@teatromuseo.cl
- Slack: #incidentes-totem

**🟡 ALTO** — Funcionalidad degradada pero operativo
- Teléfono: [Número oficina TI]
- Email: soporte@teatromuseo.cl
- Horario: L-V 9:00-18:00

**🟢 BAJO** — Mejoras o ajustes menores
- Email: mejoras@teatromuseo.cl
- Sistema de tickets: [URL del sistema]

### Información a proporcionar

Al reportar un incidente, incluir:

1. **Hora exacta** del problema
2. **Descripción** de lo que ocurre
3. **Pasos** para reproducirlo (si aplica)
4. **Foto** de la pantalla (si muestra error)
5. **Resultado** de `/health` endpoint (si accesible)

---

## Checklist de verificación diaria

- [ ] El tótem muestra el splash al acercarse
- [ ] Las transiciones entre pantallas funcionan
- [ ] El cambio de idioma funciona en todas las pantallas
- [ ] La cartelera muestra eventos
- [ ] No hay mensajes de error visibles

---

## Anexos

### Acceso al endpoint /health

Desde cualquier dispositivo en la misma red:

```bash
curl http://[ip-del-totem]/health
```

Respuestas:
- `{"status":"ok"...}` → Todo funciona correctamente
- `{"status":"error"...}` → Problema de conectividad con API

### Ubicación de logs

- **Logs de aplicación**: `writable/logs/log-YYYY-MM-DD.log`
- **Logs de servidor web**: `/var/log/apache2/` o `/var/log/nginx/`
- **Logs de Fully Kiosk**: Dentro de la app → Configuración → Logs

---

*Documento versionado. Última actualización: 2026-06-12*
