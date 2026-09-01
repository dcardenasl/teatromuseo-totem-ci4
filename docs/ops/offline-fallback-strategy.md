# Estrategia de Resiliencia Offline — Tótem Interactivo

> Documento técnico que describe el comportamiento del kiosko ante fallos de conectividad con `teatromuseo-bff`.
>
> Reescrito 2026-08-19 tras la migración a BFF (`TOTEM-BFF-01..08`) y el
> endurecimiento de persistencia de caché (`TOTEM-BFF-09..10`) — la versión
> anterior de este documento describía repositorios de fallback y una
> conexión directa al Hub (`/api/v1/totem/*`) que ya no existen en el
> código. Ver `teatromuseo-totem-ci4/TASKS.md` y
> `../../docs/plan/2026-08-19-plan-totem-endurecimiento-post-bff.md` para el
> historial completo.

---

## Resumen ejecutivo

El tótem interactivo funciona **sin base de datos propia**, consumiendo toda
la información desde `teatromuseo-bff` (seam `public-read`) vía un único
cliente HTTP, `App\Services\BffTotemClient`. No existen repositorios de
fallback ni contenido mock: cuando no hay datos reales disponibles (ni
frescos ni en caché), el tótem muestra un estado honesto, nunca contenido
inventado.

---

## Arquitectura de resiliencia

```
┌─────────────────────────────────────────────────────────────┐
│                  CAPA 1: BFF (online)                         │
│  • Datos frescos desde teatromuseo-bff (public-read)          │
│  • Reintentos con backoff exponencial en 5xx/timeout          │
└──────────────────────┬──────────────────────────────────────┘
                       │ (cache-miss)
                       ▼
┌─────────────────────────────────────────────────────────────┐
│         CAPA 2: Caché fresh (TTL corto, en disco)             │
│  • Última respuesta real, TOTEM_CACHE_TTL_SECONDS (60s)       │
│  • Handler de caché: file (persiste a través de restarts      │
│    de PHP-FPM y deploys — no apcu)                            │
└──────────────────────┬──────────────────────────────────────┘
                       │ (fresh expiró o el BFF falló)
                       ▼
┌─────────────────────────────────────────────────────────────┐
│        CAPA 3: Caché stale (TTL largo, en disco)              │
│  • Última respuesta real EXITOSA, TOTEM_STALE_TTL_SECONDS     │
│    (24h) — solo avanza tras un 200 real, nunca tras un fallo  │
│  • Calentada proactivamente por `php spark totem:warm-cache`  │
│    (cron ~5 min) además de por el tráfico normal del kiosco   │
└──────────────────────┬──────────────────────────────────────┘
                       │ (no hay stale, o expiró)
                       ▼
┌─────────────────────────────────────────────────────────────┐
│              CAPA 4: Estado honesto "no disponible"           │
│  • `totem/partials/content_unavailable.php`                   │
│  • Acotado SOLO al bloque dependiente de esa fuente — el      │
│    diseño estático (hero/intro/cifras) de la pantalla nunca   │
│    se oculta                                                   │
└─────────────────────────────────────────────────────────────┘
```

Cada llamada de `BffTotemClient` devuelve un `TotemApiResult` tipado
(`data`, `state: fresh|stale|unavailable`) — nunca un array ambiguo. Un
`404` confirmado del BFF (p. ej. una ficha que no existe) se trata como
respuesta válida y nunca cae a stale; solo un fallo de transporte, un 5xx o
un JSON inválido activa el camino stale/unavailable.

---

## Comportamiento por pantalla

| Pantalla | `fresh`/`stale` con datos | `fresh`/`stale` vacío confirmado | `unavailable` |
|---|---|---|---|
| Cartelera | Eventos reales (máx. 5, próximos primero) | "Sin funciones programadas" | `content_unavailable`, solo en el listado |
| TeatroEscuela | Cursos reales (máx. 3 próximos) | "Sin cursos abiertos" | `content_unavailable`, solo en `.school-courses` — hero/intro/cifras evergreen siempre visibles |
| Catálogo (piezas/técnicas/categorías) | Fichas y listados reales, curados por `show_in_totem` | Mensaje "sin piezas" del grupo correspondiente | `content_unavailable`, solo en el bloque de datos |

No existe una pantalla "Museo Hoy" ni "Historia del museo" alimentada por
esta capa todavía — ver `TASKS.md` (`TOTEM-BFF-15`, fuera de este roadmap).

---

## Configuración

```bash
# Conexión al BFF
TOTEM_BFF_BASE_URL=http://localhost:8188
TOTEM_BFF_API_KEY=your-app-key-here

# Caché fresh+stale (BffTotemClient), handler = file (app/Config/Cache.php)
TOTEM_CACHE_TTL_SECONDS=60
TOTEM_STALE_TTL_SECONDS=86400

# Timeout del cliente HTTP hacia el BFF
TOTEM_BFF_TIMEOUT_SECONDS=5
```

---

## Calentamiento proactivo de caché

`app/Commands/WarmBffCache.php` (`php spark totem:warm-cache`) refresca
secuencialmente los métodos de listado/faceta de cada pantalla (Cartelera,
TeatroEscuela, Catálogo por categoría, técnicas y categorías con conteo) en
los 4 idiomas, sin depender de que un visitante dispare la primera request.
No llama métodos de detalle por slug — evita cualquier fan-out no acotado.
Estrictamente secuencial: el tótem, como el resto de la flota, no usa
`curl_multi` ni paralelismo (ver ADR-010 en el monorepo raíz).

Registrar en crontab del hosting de producción (ver
`.deploy/README.md`), cada ~5 minutos:

```cron
*/5 * * * * cd /ruta/al/totem && php spark totem:warm-cache >> writable/logs/warm-cache.log 2>&1
```

---

## Monitoreo

### Health Check

```bash
curl https://totem.example.com/health
```

Respuesta cuando el BFF está disponible:
```json
{
  "status": "ok",
  "api": "reachable",
  "timestamp": "2026-06-12T23:02:17-04:00"
}
```

Respuesta cuando el BFF no está disponible:
```json
{
  "status": "error",
  "api": "unreachable",
  "timestamp": "2026-06-12T23:02:17-04:00"
}
```

### Logs estructurados

Cada llamada al BFF genera un log JSON vía `BffTotemClient::log()`:

```json
{
  "timestamp": "2026-06-12T23:02:17-04:00",
  "service": "bff_totem_client",
  "path": "public-read/es/entries/teatroescuela",
  "duration": 145,
  "status": 200,
  "success": true
}
```

Nunca incluye la clave (`TOTEM_BFF_API_KEY`) ni el header `X-App-Key`. Los
logs están en `writable/logs/log-<YYYY-MM-DD>.log`.

---

## Limpieza de caché

Para forzar actualización inmediata de datos:

```bash
# Eliminar todo el contenido de la caché (fresh y stale)
rm -rf writable/cache/*
```

No existe un comando `spark cache:clear` propio del tótem — el driver
`file` de CI4 no lo requiere para uso normal; `TOTEM_CACHE_TTL_SECONDS`
(60s) ya garantiza que los datos frescos se refrescan solos.

---

## Checklist de resiliencia

Ante un corte de red confirmado con el BFF, verificar:

- [ ] Las pantallas de menú principal siguen funcionando
- [ ] Cartelera/TeatroEscuela/Catálogo muestran contenido real (stale), no
  un error, si había caché previa
- [ ] Tras agotar el TTL stale sin BFF, aparece `content_unavailable`
  acotado solo al bloque de datos — el diseño estático de cada pantalla
  sigue visible
- [ ] `/health` reporta `"api": "unreachable"`
- [ ] Ningún dato inventado aparece en pantalla

Cobertura automatizada de estos escenarios: `tests/feature/*ResilienceTest.php`
(`TOTEM-BFF-13`).

---

## Referencias

- Cliente único: `app/Services/BffTotemClient.php`
- Resultado tipado: `app/Services/TotemApiResult.php`
- Calentamiento en background: `app/Commands/WarmBffCache.php`
- Configuración de caché: `app/Config/Cache.php`
- Plan de endurecimiento: `../../docs/plan/2026-08-19-plan-totem-endurecimiento-post-bff.md`
