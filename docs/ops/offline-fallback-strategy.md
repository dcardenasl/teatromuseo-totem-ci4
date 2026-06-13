# Estrategia de Fallback Offline — Tótem Interactivo

> Documento técnico que describe el comportamiento del kiosko ante fallos de conectividad con la API.

---

## Resumen ejecutivo

El tótem interactivo está diseñado para funcionar **sin base de datos propia**, consumiendo toda la información desde una API REST externa. Sin embargo, incluye múltiples capas de resiliencia para garantizar que el kiosko siga operativo incluso cuando la API no esté disponible.

---

## Arquitectura de resiliencia (capas)

```
┌─────────────────────────────────────────────────────────────┐
│                    CAPA 1: API (online)                      │
│  • Datos frescos desde teatromuseo-api-ci4                   │
│  • Latencia ~50-200ms                                        │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼ (si falla)
┌─────────────────────────────────────────────────────────────┐
│              CAPA 2: File Cache (TTL 60s)                    │
│  • Última respuesta válida almacenada en disco               │
│  • Persistente entre requests                                │
│  • TTL configurable vía TOTEM_CACHE_TTL_SECONDS              │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼ (si no hay cache o expiró)
┌─────────────────────────────────────────────────────────────┐
│              CAPA 3: Fallback Repositories                   │
│  • Datos estáticos embebidos en código                       │
│  • Cursos, eventos, información del museo                    │
│  • Siempre disponibles, sin dependencias externas            │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼ (si no hay datos)
┌─────────────────────────────────────────────────────────────┐
│              CAPA 4: Pantalla amigable offline               │
│  • Mensaje multiidioma explicando situación                  │
│  • Navegación básica mantenida                               │
└─────────────────────────────────────────────────────────────┘
```

---

## Comportamiento por dominio

### 1. Cartelera (Billboard)

| Escenario | Comportamiento |
|-----------|----------------|
| API disponible | Muestra eventos reales con fechas dinámicas |
| Cache disponible | Usa última cartelera conocida |
| Fallback | Muestra eventos estáticos de `BillboardFallbackRepository` |
| Sin datos | Pantalla "Próximamente" con mensaje explicativo |

### 2. Teatro Escuela (School)

| Escenario | Comportamiento |
|-----------|----------------|
| API disponible | Muestra cursos activos con profesores/alumnos |
| Cache disponible | Usa última lista de cursos conocida |
| Fallback | Muestra cursos estáticos de `SchoolFallbackRepository` |
| Sin datos | Vista informativa sobre la escuela (sin cursos específicos) |

### 3. Museo Hoy (Museum Today)

| Escenario | Comportamiento |
|-----------|----------------|
| API disponible | Muestra horarios, tarifas y actividades del día |
| Cache disponible | Usa última información conocida |
| Fallback | Datos estáticos de `MuseumFallbackRepository` |
| Sin datos | Horario estándar de funcionamiento |

### 4. Colección (Collection)

| Escenario | Comportamiento |
|-----------|----------------|
| API disponible | Fichas técnicas dinámicas desde API |
| Cache disponible | Fichas cacheadas disponibles |
| Fallback | Información general sobre técnicas de titiritería |
| Sin datos | Mensaje "Contenido en construcción" |

---

## Configuración

Las siguientes variables de entorno controlan el comportamiento offline:

```bash
# Habilitar/deshabilitar cache en archivo (default: true)
TOTEM_ENABLE_FILE_CACHE=true

# Tiempo de vida del cache en segundos (default: 60)
TOTEM_CACHE_TTL_SECONDS=60

# Directorio de cache (relativo a writable/)
# Por defecto: writable/cache/totem/
```

---

## Monitoreo

### Health Check

El endpoint `/health` permite verificar el estado de conectividad:

```bash
curl https://totem.example.com/health
```

Respuesta cuando API está disponible:
```json
{
  "status": "ok",
  "api": "reachable",
  "timestamp": "2026-06-12T23:02:17-04:00"
}
```

Respuesta cuando API no está disponible:
```json
{
  "status": "error",
  "api": "unreachable",
  "timestamp": "2026-06-12T23:02:17-04:00"
}
```

### Logs estructurados

Cada llamada API genera un log en formato JSON:

```json
{
  "timestamp": "2026-06-12T23:02:17-04:00",
  "service": "totem_api",
  "endpoint": "courses",
  "duration": 145,
  "status": 200,
  "success": true
}
```

Los logs están en `writable/logs/log-<YYYY-MM-DD>.log`.

---

## Limpieza de cache

Para forzar actualización inmediata de datos:

```bash
# Eliminar todos los archivos de cache
rm -rf writable/cache/totem/*.cache

# O usando el servicio (si se implementa endpoint admin)
php spark cache:clear totem
```

---

## Checklist de resiliencia

Ante un corte de red confirmado, verificar:

- [ ] Las pantallas de menú principal siguen funcionando
- [ ] La cartelera muestra eventos (cache o fallback)
- [ ] La escuela muestra información de cursos
- [ ] El museo muestra horarios de atención
- [ ] No hay errores visibles al usuario
- [ ] El log registra `api: unreachable` en `/health`

---

## Referencias

- Implementación: `app/Services/FileCachedTotemApiService.php`
- Fallbacks: `app/Repositories/*FallbackRepository.php`
- Configuración: `app/Config/Totem.php`
- Health check: `app/Controllers/HealthController.php`
