# Roadmap de Implementación — Tótem Interactivo (TASKS.md)

Backlog técnico activo para `teatromuseo-totem-ci4`. Las tareas completadas se archivan en [TASKS_ARCHIVE.md](TASKS_ARCHIVE.md).
Seguimiento cross-repo: [`../TASKS.md`](../TASKS.md).

**Estado (2026-08-19):** 23 pantallas navegables, 4 idiomas. Cartelera,
TeatroEscuela y Catálogo consumen el BFF mediante `public-read`; el tótem no
mantiene una base de datos propia.
La conexión histórica al Hub vía `/api/v1/totem/*` queda superseded y no debe
reimplementarse.

## ✅ DEPLOY-ALIGN-01 — Helper de deploy común (2026-08-19)

Cerrada. El Tótem comparte el helper versionado de la flota en
`scripts/deploy_ftp.py` y el wrapper `.deploy/deploy.py`. Se corrigieron los
retornos de error, se añadieron FTPS verificado, health check, backups de
rollback, `--dry-run`, `--yes`, `--only`, `--bootstrap` y `--prune`. Las
credenciales, el estado incremental y los backups quedan fuera de Git.

> **Nota arquitectural — Oleadas 2 y 3:**
> Las rutas `/museo/coleccion/titeres`, `/museo/coleccion/mascaras` y `/museo/coleccion/payasos` están **activas con contenido real** y son el flujo de navegación principal a partir del sprint 13-15/6. La nota anterior de "OBSOLETA" en el plan de colección dejó de ser válida: la decisión de consolidar en `collection_main` queda **aplazada hasta que lleguen los assets definitivos de Coni**. No eliminar estas rutas ni vistas mientras eso no ocurra.

---

## ✅ TOTEM-BFF-08 — Corrección de conectividad y refinamiento de reglas (2026-08-19)

Cerrada el 2026-08-19. `BffTotemClient` construye una `CURLRequest` no
compartida con `baseURI` (camelCase), y el health check usa la misma clave al
consultar `/ready`. La prueba de construcción del cliente confirma que la
base resultante incluye `/api/v1/`.

También quedaron cubiertas por tests las siguientes reglas:

- **Cartelera**: máximo 5 funciones, próximas primero en orden ascendente
  (prioridad a la más inmediata; la función de hoy queda primera de forma
  natural por ser la más próxima), y si faltan para completar 5, se rellena
  con las más recientes ya pasadas en orden descendente.
  `BillboardPresenter::presentList()` confía en `sort=agenda` del BFF (que ya
  ordena exactamente así) y solo aplica el tope de 5 —
  `BffTotemClient::shows()` pide además `last_occurrence_at`
  (`BFF-TOTEM-02` en `teatromuseo-bff/TASKS.md`, antes no expuesto en el
  listado) para poder mostrar la fecha real de los eventos de relleno.
- **TeatroEscuela**: cada tarjeta de curso usa su propia imagen real
  (`entry.featured_image.url`, ya hidratada por el BFF) en vez de repetir
  la misma imagen estática en todos los cursos; solo cae al póster genérico
  si un curso realmente no tiene portada cargada.

`composer quality` queda verde en Tótem: 90 tests y 317 assertions. La
verificación HTTP/e2e contra servidores reales queda pendiente hasta iniciar
el stack local; no se registra aquí como validada.

---

## 🟡 Tótem vía BFF — Cartelera / TeatroEscuela / Catálogo (2026-08-18)

Fuente de verdad:
[`../docs/plan/2026-08-18-plan-totem-via-bff.md`](../docs/plan/2026-08-18-plan-totem-via-bff.md).
La auditoría confirmó que `/api/v1/totem/*` nunca existió en el Hub: el
cliente actual convierte sus 404 y fallos en `[]`, por lo que las pantallas
pueden mostrar contenido hardcodeado como si fuera real. Este track migra
solo Cartelera, TeatroEscuela y Catálogo a los `public-read` del BFF con una
clave dedicada `TOTEM_BFF_API_KEY`; no se implementan rutas nuevas en el Hub.

Las tareas siguientes reemplazan, para estas tres pantallas, las casillas
históricas de “Conexión a BD vía API” que todavía mencionan `/api/v1/totem/*`.
Historia del museo y las tradiciones de máscaras quedan fuera de este plan.

### TOTEM-BFF-01 — Cliente BFF, configuración y resiliencia ✅ Cerrada 2026-08-18

- [x] Implementado `app/Services/BffTotemClient.php`, cableado desde
  `Config/Services.php` con métodos por pantalla: `shows`/`show`,
  `courses`/`course`, `collectionItems`/`collectionItem`, `techniques`/
  `technique`, `catalogCategories`.
- [x] Usa `TOTEM_BFF_BASE_URL`, header `X-App-Key: TOTEM_BFF_API_KEY`;
  `TOTEM_API_URL`/`TOTEM_API_KEY`/`X-Totem-Key` retirados de `.env`,
  `.env.example`, `env` y todo `app/`.
- [x] `TotemApiInterface`/`TotemApiService`/`CachedTotemApiService`/
  `FileCachedTotemApiService` eliminados; una sola clase con
  `TotemApiResult` (`fresh`/`stale`/`unavailable`, `isEmpty()`).
- [x] Caché fresh/stale vía `Config\Services::cache()`, TTLs
  `TOTEM_CACHE_TTL_SECONDS`/`TOTEM_STALE_TTL_SECONDS`; el stale solo avanza
  tras una respuesta 200 real, nunca tras una excepción.
- [x] Pruebas: `BillboardControllerTest`/`SchoolControllerTest`/
  `CollectionControllerTest` cubren fresh, vacío honesto, 404-confirmado y
  fallo de transporte (`FakeBffCurlRequest`, `tests/_support/`).

### TOTEM-BFF-02 — Cartelera dinámica ✅ Cerrada 2026-08-18

- [x] Listado y detalle migrados a `public-read/{locale}/events` y
  `.../events/{idOrSlug}`.
- [x] `BillboardController::billboardDetail()` ya no instancia
  `BillboardFallbackRepository` (eliminado) — consulta el BFF.
- [x] `BillboardPresenter` reescrito: contenido real, vacío honesto
  (`Billboard.no_shows_*`) y `content_unavailable` explícito; detalle por
  slug conservado.

### TOTEM-BFF-03 — TeatroEscuela desde CMS ✅ Cerrada 2026-08-18 (corregida el mismo día — ver nota)

- [x] Listado/detalle migrados a `entries/teatroescuela`
  (`order_by=field:start_date&order_direction=upcoming`), leyendo el bloque
  `teatroescuela_ficha` de cada entry. Cursos limitados a **máximo 3**,
  soonest-first, excluyendo historial (curso "terminado" si su
  `end_date`/`start_date` ya pasó).
- [x] Maestros reconstruidos desde `instructors` (referencias a la colección
  `personas`) de cursos publicados reales — cero nombres inventados. La
  sección se oculta por completo si no hay instructores reales resueltos
  (en vez de mostrar un rail vacío o nombres falsos).
- [x] **Corrección post-cierre (mismo día, feedback de David):** la primera
  versión (a) escondía TODA la pantalla (hero/intro/cifras) detrás del
  estado `unavailable` en vez de solo el bloque de cursos, y (b) convirtió
  las 3 cifras clave estáticas (Cursos/Maestros/Alumnos = 50/20/1000) en
  solo 2 cifras calculadas — rompiendo el grid de 3 columnas fijo en CSS.
  Ninguna de las dos cifras fue nunca dato vivo (venían de
  `SchoolFallbackRepository::section()` sin condicionar al éxito de la API),
  así que restaurarlas como copy editorial estático no reintroduce el
  problema de "maestros inventados" (personas con nombre y biografía
  fabricados) — son categorías distintas. La regresión queda cubierta por
  `SchoolControllerTest` y `SchoolPresenterTest`.
  Ahora solo el bloque `.school-courses` reacciona a `unavailable`; hero,
  intro y las 3 cifras se muestran siempre.

### TOTEM-BFF-04 — Catálogo, técnicas y categorías ✅ Cerrada 2026-08-18

- [x] Piezas, fichas, técnicas y categorías migradas a `public-read`/`public`
  del BFF con locale y slugs reales.
- [x] `with_counts=1` alimenta los toggles `hasClowns`/`hasMasks`; curación
  `show_in_totem` aplicada server-side en el BFF (no por parámetro del
  cliente).
- [x] `CollectionPresenter` creado; shapeo inline y mapas slug↔lang-key
  manuales retirados de `CollectionController`.
- [x] `app/Data/titeres_mock.json`/`tecnicas_mock.json` eliminados junto con
  `app/Data/`; ya no hay lookup "mock primero".

### TOTEM-BFF-05 — Limpieza arquitectónica y documentación ✅ Cerrada 2026-08-18

- [x] `BillboardFallbackRepository`/`SchoolFallbackRepository` eliminados
  (con ellos, `teachers()`/`students()` inventados); `app/Repositories/`
  ya no existe.
- [x] `app/Config/Database.php` ya usaba SQLite `:memory:` (verificado, no
  necesitaba cambio). Test de arquitectura extendido
  (`StatelessArchitectureTest::testNoResidualHubTotemProxyReferencesRemain`)
  prohíbe `TOTEM_API_URL`/`TOTEM_API_KEY`/`X-Totem-Key`/`TotemApiInterface`/
  `TotemApiService` en todo `app/`.
- [x] `.env`/`.env.example`/`env`, `CLAUDE.md` del Tótem actualizados
  (Tótem → BFF `public-read`, `TOTEM_BFF_API_KEY`). Documentación raíz del
  monorepo pendiente — ver TOTEM-BFF-07 abajo.

### TOTEM-BFF-06 — Verificación local y e2e ✅ Cerrada 2026-08-19 (ver evidencia en `TOTEM-BFF-13`)

- [x] `composer quality` verde en Tótem: 90 tests y 317 assertions.
- [x] El cliente real se construye con `baseURI` y una base `/api/v1/`; el
  health check consulta `/ready` con la misma convención.
- [x] Hay cobertura de caché fresh/stale, 404 confirmado, JSON inválido,
  reintentos ante 5xx, cabeceras y fallos de transporte.
- [x] Smoke HTTP ejecutado contra el stack local real (`TOTEM-BFF-13`,
  2026-08-19): Cartelera y TeatroEscuela verificados con datos reales,
  camino stale confirmado matando el BFF real y borrando solo la entrada
  fresh en disco. Catálogo y la curación `show_in_totem` **no verificables
  en este entorno** — `catalog-domain` no tiene filas sembradas en
  `collection_items` en este sandbox (confirmado con `GET collection-items`
  directo, con y sin curación); queda cubierto solo por el test dedicado
  del lado BFF (`TotemCurationTest.php`). No es un bloqueante de código —
  es una limitación de datos de este entorno local, ya documentada desde
  la sesión 2026-08-18/19 anterior.

### TOTEM-BFF-07 — Documentación cross-repo ✅ Cerrada 2026-08-18

- [x] `../CLAUDE.md` (raíz de `teatromuseo/`) actualizado: diagrama, tabla de
  apps y sección de auth ahora dicen "Totem → BFF (`public-read`)",
  `X-App-Key: TOTEM_BFF_API_KEY` en vez de Hub directo/`X-Totem-Key`.

---

## 🟡 Endurecimiento post-BFF — Resiliencia offline y verificación (2026-08-19)

Fuente de verdad:
[`../docs/plan/2026-08-19-plan-totem-endurecimiento-post-bff.md`](../docs/plan/2026-08-19-plan-totem-endurecimiento-post-bff.md).
Auditoría posterior a `TOTEM-BFF-01..08`: confirma que la migración al BFF ya
quedó bien resuelta y acota el trabajo real pendiente frente al objetivo de
resiliencia offline de "horas o días" — persistencia de caché, calentamiento
proactivo y verificación e2e real, no un rediseño.

### TOTEM-BFF-09 — Persistencia de caché, timeout configurable y pase de seguridad ✅ Cerrada 2026-08-19

- [x] `app/Config/Cache.php`: `$handler = 'apcu'` (memoria, se pierde en cada
  restart de PHP-FPM/deploy) → `$handler = 'file'` (disco, mismo patrón que
  `teatromuseo-web`). Es la precondición real para que la caché "stale" de
  24h sobreviva un reinicio — hoy no la sobrevive.
- [x] `app/Services/BffTotemClient.php` y `app/Controllers/HealthController.php`:
  timeout hardcodeado `5` → `getenv('TOTEM_BFF_TIMEOUT_SECONDS')` con guard
  `is_numeric`, mismo patrón que `TOTEM_CACHE_TTL_SECONDS`/`TOTEM_STALE_TTL_SECONDS`.
- [x] `env`/`.env`: agregado `TOTEM_BFF_TIMEOUT_SECONDS` (default 5).
- [x] Pase de seguridad: `BffTotemClient::log()` solo registra
  timestamp/path/duration/status/success/error, nunca la clave ni el header
  `X-App-Key` (verificado leyendo el método `log()` completo). `.gitignore`
  ya cubre `.env*` y `/writable/cache/*` (líneas 44-46, 59-60). El helper de
  deploy (`scripts/deploy_ftp.py`) ya excluye `writable/` de cada subida — el
  contenido de `writable/cache/` nunca viaja al hosting vía FTP y persiste
  localmente entre deploys, reforzando por qué el cambio a `file` es
  efectivo. `fields=` allowlists en `BffTotemClient` (`shows()`, `show()`,
  `courses()`, etc.) solo incluyen columnas ya públicas para el sitio web —
  sin datos sensibles. `composer quality` verde (90 tests, 317 assertions).

### TOTEM-BFF-10 — Calentamiento de caché en background ✅ Cerrada 2026-08-19

- [x] Nuevo `app/Commands/WarmBffCache.php` (`php spark totem:warm-cache`)
  — llama secuencialmente `shows()`, `courses()`, `collectionItems()` por
  categoría (titeres/mascaras/payasos), `techniques(true)`,
  `catalogCategories(true)` en los 4 locales (22 llamadas por corrida). Sin
  métodos de detalle — evita fan-out no acotado.
- [x] Registro del cron: confirmado que `codeigniter4/framework` 4.7.3 (la
  versión instalada) no trae scheduler nativo (`Tasks`/`Scheduler`) —
  documentada la línea de crontab exacta en `RELEASE.md` y
  `docs/ops/offline-fallback-strategy.md`, cada 5 min.
- [x] Delay de 250ms entre llamadas (`WarmBffCache::DELAY_MICROSECONDS`) —
  mitigación contra el throttle de 60 req/60s del BFF. Estrictamente
  secuencial, sin `curl_multi` (ADR-010).
- [x] Nuevo test `tests/unit/Commands/WarmBffCacheTest.php` con
  `FakeBffCurlRequest`: cubre las 22 llamadas exactas por corrida, que
  nunca se llaman métodos de detalle, y que el comando no lanza excepción
  cuando el BFF está completamente inalcanzable. `composer quality` verde
  (93 tests, 399 assertions).
- [x] Documentación actualizada: `README.md` (variables de entorno,
  estructura del proyecto, sección "Resiliencia offline" reescrita —
  describía repositorios de fallback y `TOTEM_API_URL`/`X-Totem-Key` que ya
  no existen) y `docs/ops/offline-fallback-strategy.md` reescrito por
  completo con el mismo hallazgo.

### TOTEM-BFF-11 — Evaluación de endpoint compuesto BFF ✅ Cerrada 2026-08-19 (no-go, diferido)

- [x] Análisis documentado en
  [`../docs/audits/2026-08-19-totem-composite-endpoint-evaluation.md`](../docs/audits/2026-08-19-totem-composite-endpoint-evaluation.md):
  revisadas todas las acciones de `BillboardController`/`SchoolController`/
  `CollectionController` — solo 2 de ~15 hacen 2 llamadas BFF (fichas de
  pieza/técnica + "relacionadas", ambas `LIMIT`-acotadas y baratas), el
  resto hace exactamente 1. Tras `TOTEM-BFF-10` (warm-up), las cargas en
  frío de listados prácticamente no ocurren en operación normal.
- [x] **Decisión: no construir** `public-read/{locale}/totem/screen-resolve/{screen}`
  — no hay caso de uso real que lo justifique; el ahorro sería marginal (un
  round-trip menos en 2 pantallas de detalle) frente al costo de mantener
  un endpoint compuesto nuevo. Criterio de reapertura documentado en el
  audit doc.
- [x] Confirmado descartado, sin excepción: cualquier fan-out paralelo
  cliente→BFF (`curl_multi`, promesas, workers) — ADR-010 lo prohíbe;
  ADR-008 documenta que Web ya lo intentó y lo revirtió. No aplica de
  todos modos: no hay múltiples llamadas por pantalla que paralelizar.

### TOTEM-BFF-12 — Seguridad y optimización de API clients ✅ Cerrada 2026-08-19

- [x] Confirmado `TOTEM_BFF_API_KEY` rotable independientemente de
  `WEB_API_KEY`/`BFF_API_KEY`: `teatromuseo-bff/app/Filters/WebAppKeyRequiredFilter.php`
  mapea cada clave configurada a una identidad de llamador distinta
  (`web`/`totem`) vía `hash_equals`; rotar `TOTEM_BFF_API_KEY` en ambos
  `.env` no afecta al sitio público.
- [x] Abierta recomendación cross-repo en `teatromuseo-bff/TASKS.md`
  ("Soporte de request condicional (`ETag`/`If-None-Match`) en
  `public-read`", 2026-08-19): `meta.source_revision` ya existe pero no
  está conectado a ningún short-circuit 304 — afecta a Web también, no se
  ejecuta desde este repo.
- [x] Documentado como decisión consciente de diferir (no omisión):
  single-flight lock en el tótem. A diferencia de `teatromuseo-web`
  (tráfico orgánico concurrente, de ahí su `SingleFlightLock`), un kiosco
  físico tiene una sola sesión de navegador activa — no hay carga
  concurrente de visitantes que colapsar. Revisar solo si el warm-up
  (`TOTEM-BFF-10`) y una visita real llegan a competir de forma observable
  en producción.
- [x] Staggering de requests ante resync post-apagón: ya cubierto por el
  delay de 250ms entre llamadas de `WarmBffCache` (`TOTEM-BFF-10`) — sin
  trabajo adicional.

### TOTEM-BFF-13 — Pruebas de desconexión y verificación ✅ Cerrada 2026-08-19 (cierra TOTEM-BFF-06)

- [x] **Pase manual real ejecutado** contra BFF (:8188) y Tótem (:8186)
  levantados de verdad (MySQL real vía Docker, datos reales sembrados):
  - Cartelera: `curl http://localhost:8186/cartelera` renderiza "Un buen
    cuento maléfico" (evento real del BFF).
  - TeatroEscuela: renderiza cursos reales (`school-course` presente).
  - Se borró solo la entrada **fresh** en disco
    (`writable/cache/tm_totem_totem_bff_*`) de Cartelera/TeatroEscuela, se
    mató el proceso del BFF real, y se confirmó que ambas pantallas siguen
    sirviendo el contenido real cacheado (HTTP 200, nota
    `Common.content_stale_note` visible) — nunca un error ni una pantalla en
    blanco.
  - Se borró también la entrada **stale** de Cartelera con el BFF aún
    caído: confirmado el estado honesto `content_unavailable.php` (HTTP
    200, "Contenido no disponible"), sin datos inventados.
  - `php spark totem:warm-cache` ejecutado contra el BFF real tras
    restaurarlo: 22/22 llamadas `fresh`, confirmando `TOTEM-BFF-10` funciona
    end-to-end, no solo contra el doble de test.
  - **Catálogo: no verificable con datos reales en este entorno** —
    `catalog-domain` tiene 0 filas en `collection_items` en el sandbox local
    (confirmado con `GET collection-items` directo al BFF, con y sin
    curación `show_in_totem`), igual que documentó la sesión del
    2026-08-18/19 anterior. La curación `show_in_totem=0` tampoco es
    verificable en vivo por la misma razón — cubierta solo por los tests
    unitarios del lado BFF (`teatromuseo-bff/tests/Unit/PublicRead/TotemCurationTest.php`).
  - Entorno restaurado al estado original tras la prueba: se limpiaron
    procesos `spark serve` huérfanos que quedaron reintentando puertos
    (efecto colateral de la sesión de pruebas, no de los cambios de código)
    y se relanzaron `totem`/`bff` en sus paneles `tmux` originales
    (`teatromuseo-dev`), puertos 8186/8188 confirmados sanos.
- [x] Cobertura automatizada nueva en `tests/feature/` (antes vacío salvo
  `.gitkeep`): `BillboardBffResilienceTest.php`, `SchoolBffResilienceTest.php`,
  `CollectionBffResilienceTest.php` con `FeatureTestTrait` +
  `FakeBffCurlRequest`, cubriendo el camino stale (el único no cubierto por
  los tests unitarios existentes de controladores) a nivel de HTML
  renderizado. Hallazgo documentado en `CollectionBffResilienceTest`: a
  diferencia de Cartelera/TeatroEscuela, las pantallas de exhibición de
  Colección no distinguen `unavailable` de vacío confirmado — ver
  `TOTEM-BFF-16`. `composer test:feature`: 6 tests, 23 assertions.
  `composer quality` completo: 99 tests, 422 assertions.
- [x] `TOTEM-BFF-06` cerrada por completo y los 2 checkboxes de
  "Oleada 3" ("simular desconexión total", "asegurar carga graceful desde
  caché"), citando los tests nuevos como evidencia.

### TOTEM-BFF-14 — Re-triage de "Saneamiento arquitectónico" ✅ Cerrada 2026-08-19

- [x] Verificados los 12 ítems de `TOT-01` uno por uno contra el código real
  (ver la sección reescrita arriba, "Saneamiento arquitectónico — re-triado
  2026-08-19"): **9 de 12 ya estaban resueltos** (test:feature, alias de
  scripts, `docker-compose.yml`, matriz de PHP en CI, capa HTTP unificada,
  triple decorador colapsado, repos de fallback ya inexistentes,
  `DatePresenter` con guard, `Database.php` con SQLite `:memory:`,
  `.gitignore` con `.env.*`, hooks de git vía composer). Quedan **2
  genuinamente pendientes** (CI sin `release.yml`/`security.yml`/
  `dependabot.yml`/`composer audit`; taxonomía de secciones históricas sin
  migrar — bajo valor) y **1 sin confirmar** (Tailwind/JS build — puede ser
  simplificación deliberada, no actuar sin confirmar con David).

### TOTEM-BFF-15 — Pantallas huérfanas (fuera del roadmap principal, no iniciar sin autorización explícita)

- [ ] `/museo/historia/:slug`, `/museo/el-museo/edificio`,
  `/museo/el-museo/institucion`, `/extension` siguen con "Mock notice",
  explícitamente fuera del alcance de `TOTEM-BFF-01..08`. "Historia del
  museo" es candidato natural por ser ya una página CMS estándar
  (`public-read/{locale}/pages/...`, mismo patrón que Web). Trabajo de
  contenido nuevo, no de resiliencia/caché — no mezclar con `TOTEM-BFF-09..13`.

### TOTEM-BFF-16 — Colección: distinguir `unavailable` de vacío confirmado ✅ Cerrada 2026-08-19

- [x] Hallazgo de `TOTEM-BFF-13`, corregido a pedido explícito de David: a
  diferencia de Cartelera/TeatroEscuela/`collectionMain()` (que sí llaman
  `TotemApiResult::isAvailable()`), `collectionPuppetsExhibit()`/
  `collectionMasksExhibit()`/`collectionClownsExhibit()`/
  `collectionTechniques()` no distinguían "BFF caído" de "categoría
  genuinamente vacía" — ambas mostraban la misma grilla vacía (o, en
  máscaras/payasos, el mismo fallback curado con CTA). Alineado al mismo
  patrón que ya usan Cartelera/TeatroEscuela:
  - `CollectionController`: las 4 acciones ahora calculan
    `'unavailable' => ! $result->isAvailable()` y lo pasan a la vista.
  - `totem/partials/collection_grid.php`: nuevo parámetro `$unavailable` —
    si es `true`, renderiza `content_unavailable.php`; si la lista está
    genuinamente vacía, renderiza un panel honesto nuevo
    (`Collection.no_items_title`/`no_items_copy`, agregado en `es/en/fr/pt`)
    que antes no existía en absoluto (la grilla vacía no mostraba ningún
    mensaje). Solo el bloque de datos se gatea — header/intro/tabs siempre
    se muestran, mismo principio que `theater_school.php`.
  - `collection_masks_exhibit.php`/`collection_clowns_exhibit.php`: su
    fallback curado con CTA (hero + botones) queda reservado exclusivamente
    para el caso "genuinamente vacío"; un BFF caído ahora muestra
    `content_unavailable.php` en su lugar.
  - `TotemRoutesTest::testPuppetsExhibitRoute`/`testMasksExhibitRoute`
    codificaban el comportamiento viejo (sin BFF) como esperado — corregidos
    para afirmar el estado honesto. Nueva cobertura en
    `CollectionControllerTest`/`CollectionBffResilienceTest` para las 4
    pantallas: unavailable, vacío confirmado y contenido real.
    `composer quality`: 104 tests, 429 assertions.
  - **Conscientemente fuera de este cierre:** las secciones de "piezas
    relacionadas" dentro de `collectionTechnique()`/`collectionItem()`
    (fichas de detalle, ya correctas en su contenido principal) no
    distinguen unavailable en su mini-listado secundario — degradan a lista
    vacía sin mensaje propio, análogo a como Cartelera no tiene
    sub-secciones con fetch propio. Prioridad baja, se abre como oportunidad
    de seguimiento si se vuelve a auditar Colección, no bloquea nada de lo
    anterior.

### TOTEM-BFF-17 — Hidratación progresiva en Cartelera (detalle) ✅ Cerrada 2026-08-19

- [x] Corrección de alcance: el plan original (`TOTEM-BFF-11`) descartó
  paralelismo servidor→BFF citando ADR-010, pero conflacionó eso con
  "hidratación progresiva vía AJAX desde el navegador" — que ADR-010 **no**
  prohíbe (prohíbe `curl_multi`/paralelismo del proceso PHP hacia el BFF, no
  que el navegador haga una segunda request al propio tótem después del
  primer render). A pedido explícito de David, corregido e implementado
  para `cartelera/detalle/{slug}` — la pantalla de detalle que el warm-up
  (`TOTEM-BFF-10`) deliberadamente no cubre (por slug, no acotable), y por
  lo tanto la única realmente "fría" en cada primera visita.
- [x] `BffTotemClient::show()` gana `bool $cacheOnly = false` — cuando es
  `true`, `cached()` nunca hace una llamada de red: revisa fresh, si no hay
  cae a `fallbackToStale()` (stale o `unavailable`), instantáneo siempre.
- [x] `BillboardController::billboardDetail()` ahora llama
  `show(..., cacheOnly: true)`. Con caché (caso común, dado el warm-up de
  listados) renderiza el contenido real de inmediato, sin cambios de
  comportamiento. Sin caché (primera vista de un slug) renderiza el shell
  con un esqueleto de carga (`Common.content_loading_label`, agregado en
  los 4 idiomas) en vez de bloquear la respuesta esperando al BFF.
- [x] Nueva acción `billboardDetailData()` + ruta
  `cartelera/detalle/(:any)/data` (registrada **antes** de la ruta general
  `(:any)` — el placeholder greedy la interceptaba, bug encontrado y
  corregido durante la implementación) — hace el fetch real (con
  reintentos, igual que siempre) y devuelve JSON `{state, html}` con el
  mismo partial (`billboard_detail_content.php`, extraído del contenido que
  antes vivía inline en `billboard_detail.php`) que usa el camino síncrono
  — el markup vive una sola vez.
- [x] Un slug confirmado-inexistente ya no puede ser un 404 HTTP real en
  este camino (el shell ya respondió 200) — se reporta `state: 'not_found'`
  y el navegador renderiza el mismo copy de "página no encontrada" inline
  (`totem/partials/content_not_found.php`, nuevo partial dedicado).
- [x] **Bug encontrado y corregido durante la implementación:** un primer
  intento parametrizó `content_unavailable.php` con `$title`/`$copy`
  opcionales — CI4 persiste los datos de `view()` entre llamadas del mismo
  request (`Config\View::$saveData`), así que ese partial heredaba
  silenciosamente un `$title` de una vista *anterior* no relacionada en vez
  de usar su copy por defecto (confirmado con un test aislado). Corregido
  creando `content_not_found.php` como partial separado, sin parámetros —
  mismo principio que ya usaba `content_unavailable.php` antes del intento
  fallido.
- [x] Verificación e2e real (no solo tests): con BFF/Tótem reales
  levantados, primera visita a un slug real nunca visitado
  (`cartelera/detalle/pionero`) confirmó el esqueleto de carga y
  `data-async-detail-url` correcto, sin el título real aún visible;
  `GET .../data` devolvió el JSON real con el contenido completo; la
  segunda visita al mismo slug ya renderizó todo de forma síncrona (caché
  calentado por la llamada async previa).
- [x] Tests nuevos/corregidos: `BffTotemClientTest` (cacheOnly),
  `BillboardControllerTest` (síncrono con caché tibia, diferido con caché
  fría, `/data` con show real/confirmado-inexistente/inalcanzable). Un test
  de `TotemRoutesTest` corregido por depender implícitamente de "no hay BFF
  alcanzable" — asunción inválida ahora que hay un BFF real corriendo en
  `tmux` junto a la suite. `composer quality`: 107 tests, 444 assertions.
- [x] **Conscientemente fuera de este cierre:** el mismo patrón para las
  fichas de detalle de Colección (`collectionItem`/`collectionTechnique`,
  también excluidas del warm-up) — implementado solo en Cartelera como
  referencia; aplicar el mismo patrón ahí es la continuación natural, no
  bloquea nada de lo anterior.

### TOTEM-BFF-18 — Calentar el detalle de los ítems mostrados en Cartelera ✅ Cerrada 2026-08-19

- [x] A pedido explícito de David: "la cartelera son 5 items y teatroescuela
  3 items, ¿por qué no los cargas en caché?" — protección concreta ante una
  mala señal de internet en el sitio físico del tótem: si los 5 eventos que
  ya se muestran en la grilla de Cartelera también tienen su ficha de
  detalle precalentada, tocar cualquiera de ellos es instantáneo y
  resiliente incluso si el BFF está inalcanzable en ese momento — no solo
  la primera visita "en frío" de `TOTEM-BFF-17` que ya se resolvía con
  hidratación progresiva, sino que ya ni siquiera necesita esa hidratación.
- [x] Investigado primero: **TeatroEscuela no tiene pantalla de detalle**
  — los 3 cursos destacados muestran toda su información inline en la
  grilla (`theater_school.php`); `BffTotemClient::course()` existe pero
  ninguna ruta/controlador lo llama. No hay nada que calentar ahí — no es
  una omisión, es que no existe el caso.
  Catálogo queda fuera a propósito: sus categorías no están acotadas a un
  número fijo como Cartelera/TeatroEscuela, así que calentar "cada pieza
  mostrada" ahí no sería acotado de la misma forma.
- [x] `WarmBffCache::run()`: tras calentar `shows($locale)`, reutiliza
  `BillboardPresenter::presentList()` (la misma lógica de selección que ya
  usa la pantalla — próximos primero, relleno con los más recientes,
  tope de 5) para extraer exactamente los slugs mostrados, y calienta el
  detalle de cada uno (`show($locale, $slug)`). Sigue estrictamente
  secuencial (ADR-010), sigue acotado (5 × 4 idiomas = 20 llamadas
  adicionales, no cientos), sin reimplementar la regla de selección.
- [x] Tests: nuevo test cubre que se calienta exactamente el detalle de los
  eventos destacados (y solo esos); test existente de "nunca detalle por
  slug" ajustado para permitir esta única excepción acotada, documentado en
  su propio docblock. `composer quality`: 108 tests, 431 assertions.
- [x] Verificado en vivo contra BFF/Tótem reales: `php spark totem:warm-cache`
  calentó 42 entradas (22 base + 4 idiomas × 5 eventos reales de Cartelera,
  incluyendo `un-buen-cuento-malefico`, `pionero`, etc.); visitar
  inmediatamente después cualquiera de esos eventos renderizó el contenido
  real de forma 100% síncrona, sin esqueleto de carga — la protección
  funciona de punta a punta.

### TOTEM-BFF-19 — Catálogo completo precalentado (piezas + técnicas) ✅ Cerrada 2026-08-19*

*Cerrada en el sentido de "código completo, verificado hasta donde el
entorno lo permitió" — ver la verificación e2e pendiente al final, bloqueada
por una caída de MySQL/Docker ajena a este trabajo.

- [x] A pedido explícito de David: "el catálogo de elementos del museo creo
  que es fundamental que se encuentre. ¿No podemos dejarlos todos cargados?
  ¿Con un JSON o algo así?" — corrige la exclusión de Catálogo que
  `TOTEM-BFF-18` había dejado fuera por asumir que calentar "cada pieza"
  significaba una llamada HTTP por pieza (genuinamente no acotado a medida
  que crece el catálogo). La solución real no necesitó esa disyuntiva.
- [x] **Hallazgo clave en el BFF**: `CatalogPublicReadController::index()`
  (listado) y `::item()` (detalle) ya comparten el mismo mecanismo de
  `?fields=` con un allowlist propio; `DETAIL_FIELDS` es un superconjunto
  estricto de `LIST_FIELDS`. Bastó con ampliar el allowlist del listado a
  `DETAIL_FIELDS` (el default sigue siendo `LIST_FIELDS` — cero cambio de
  comportamiento para Web u otro llamador que no pida más campos
  explícitamente) para poder pedir, en una sola llamada por categoría, todo
  lo que la ficha de detalle de cada pieza necesita.
  Para técnicas, mejor aún: `CatalogFacetReader::techniques()` (listado) y
  `::technique()` (detalle) ya seleccionan exactamente las mismas columnas
  — el listado *ya es* el detalle completo de cada técnica, sin tocar el
  BFF en absoluto.
- [x] `teatromuseo-bff`: `CatalogPublicReadController::index()` ahora acepta
  `DETAIL_FIELDS` vía `fields=`. Tests nuevos en
  `PublicReadValidationTest.php`: confirma que un campo solo-detalle
  (`gallery_images`, `contenido`) ya no es rechazado en el listado, y que
  el proyecto por defecto (sin `fields=`) sigue siendo exactamente
  `LIST_FIELDS` — sin regresión para Web.
- [x] `teatromuseo-totem-ci4`:
  - `BffTotemClient::collectionItemsDetailed($locale, $category)` — la
    misma llamada de listado que `collectionItems()`, pidiendo el campo
    set de detalle (`CATALOG_DETAIL_FIELDS`, espejo manual de
    `DETAIL_FIELDS` del BFF — duplicación deliberada, aceptada por
    ADR-010, entre un único consumidor acotado y su fuente).
  - `BffTotemClient::seedCollectionItemDetails($locale, $items)` — escribe
    la entrada de caché fresh+stale de cada pieza directamente desde datos
    ya en mano (misma clave que usaría `collectionItem($locale, $idOrSlug)`
    en una visita real), **sin ninguna llamada HTTP adicional por pieza**.
  - `BffTotemClient::seedTechniqueDetails($techniques)` — mismo mecanismo
    para técnicas, sembrado directo desde la respuesta ya obtenida de
    `techniques()` (cero llamadas nuevas, ni siquiera una por categoría).
  - `WarmBffCache::run()`: por cada categoría (titeres/mascaras/payasos) ×
    4 idiomas, agrega la llamada detallada + sembrado; agrega el sembrado
    de técnicas tras la llamada a `techniques()` que ya existía. Sigue
    estrictamente secuencial (ADR-010) — 12 llamadas HTTP adicionales
    (3 categorías × 4 idiomas), acotadas sin importar cuántas piezas tenga
    el museo, exactamente el efecto de un "JSON completo" sin necesitar
    construir uno nuevo.
- [x] Tests: `WarmBffCacheTest` actualizado (34 llamadas base, antes 22 —
  cada categoría ahora se pide dos veces, lean + detallada) más un test
  nuevo que verifica que el detalle de una pieza y una técnica quedan
  cacheados tras el warm-up **sin que se haya hecho ninguna llamada extra**
  para sembrarlos. `composer quality`: 109 tests, 466 assertions, verde.
  BFF: `composer analyse` y `tests/Unit` (213 tests) verdes; el nuevo test
  de `PublicReadValidationTest.php` (Feature, requiere MySQL real) **no se
  pudo ejecutar** — ver siguiente punto.
- [ ] **Verificación e2e pendiente, bloqueada por infraestructura ajena a
  este trabajo**: al intentar verificar en vivo (como en `TOTEM-BFF-13`/
  `-17`/`-18`), se encontró que el contenedor Docker de MySQL de este
  entorno dejó de responder — el puerto 3306 acepta la conexión TCP pero el
  handshake de MySQL nunca completa (confirmado con un `mysqli_connect`
  directo en PHP, sin pasar por el BFF; también confirmado que el propio
  CLI de `docker` está sin responder). No es un problema introducido por
  este cambio — el mismo síntoma bloqueó el test Feature del BFF. No se
  intentó reiniciar Docker/MySQL del usuario (fuera de alcance, riesgo de
  tocar infraestructura que no es mía). Pendiente: re-ejecutar
  `php spark totem:warm-cache` contra BFF/MySQL reales y confirmar que
  tocar una pieza de Catálogo tras el warm-up es instantáneo, igual que ya
  se demostró para Cartelera en `TOTEM-BFF-18`.

---

## 🟡 Saneamiento arquitectónico (auditoría 2026-08-05, re-triado 2026-08-19 — `TOTEM-BFF-14`)

> Los ítems de esta sección describían el estado pre-BFF (2026-08-05). Para
> Cartelera, TeatroEscuela y Catálogo, la fuente de verdad vigente es
> `TOTEM-BFF-01..13`; no reintroducir `TotemApiService`, `X-Totem-Key`,
> fallbacks ni las rutas `/api/v1/totem/*` mencionadas en secciones
> históricas más abajo.

> **Contexto, evidencia y rutas exactas (2026-08-05):** [`../docs/plan/2026-08-05-saneamiento-arquitectonico.md`](../docs/plan/2026-08-05-saneamiento-arquitectonico.md)

### TOT-01 — Alineación con la flota — re-triado 2026-08-19

**Ya resueltos (verificado contra el código real, no contra la checklist vieja):**

- [x] `composer test:feature` ya existe y ya no está vacío — `TOTEM-BFF-13`
  agregó 3 suites reales en `tests/feature/`.
- [x] Alias `cs-check`/`phpstan` ya coexisten con `lint`/`analyse`
  (`composer.json:71-72`) — ambas convenciones disponibles.
- [x] `docker-compose.yml` ya existe en la raíz del repo.
- [x] Matriz de PHP en CI ya existe: `.github/workflows/ci.yml` corre
  `php-version: ['8.2', '8.3', '8.4', '8.5']`.
- [x] Capa HTTP ya unificada: un solo `BffTotemClient` (`Services::curlrequest()`
  con `baseURI` correcto), `HealthController` también usa
  `Services::curlrequest()` — cero `curl_init` crudo. Reintentos en 5xx con
  backoff y propagación de `X-Request-ID` ya implementados
  (`BffTotemClient::fetch()`). Todo camino de error devuelve un
  `TotemApiResult` tipado, nunca un `[]` ambiguo.
- [x] Triple decorador de `TotemApiInterface` ya colapsado en un único
  `BffTotemClient` (`TOTEM-BFF-01`).
- [x] Repositorios de fallback ya no existen (`app/Repositories/` eliminado
  por completo en `TOTEM-BFF-05`) — el ítem sobre registrarlos en
  `Config/Services` quedó sin objeto.
- [x] `DatePresenter` ya usa la guarda `class_exists(IntlDateFormatter::class)`
  antes de instanciar (líneas 32, 111) — verificado por lectura directa.
- [x] `app/Config/Database.php` ya usa SQLite `:memory:` (verificado en
  `TOTEM-BFF-05`, sin cambio necesario).
- [x] `.gitignore` ya cubre `.env.*` (línea 45).
- [x] Hooks de git (`pre-commit`/`pre-push`) ya se instalan automáticamente
  vía `composer.json` (`post-install-cmd`/`post-update-cmd`) — no se
  necesita husky para esto; es un mecanismo válido y ya funcional para una
  app PHP sin framework JS.

**Genuinamente pendientes (confirmado, no resuelto):**

- [ ] CI todavía sin `release.yml`, `security.yml`, `dependabot.yml`, ni
  `composer audit` (ni como script ni inline en `ci.yml`) — sigue siendo el
  CI más débil de la flota en este eje específico, y es la app que se
  despliega a producción.
- [ ] Sin Tailwind/build de JS/`engines`/`packageManager` fijados en
  `package.json` — **no confirmado que sea un gap real**: el tótem no tiene
  framework JS (solo PostCSS para CSS) y puede ser una simplificación
  deliberada dado que es un kiosko sin lógica de cliente compleja. No
  actuar sin confirmar la intención con David.
- [ ] La taxonomía `🔴 En progreso`/`🟡 Próximo`/`✅ Completadas` ya se usa
  en los tracks nuevos (`TOTEM-BFF-*`, `DEPLOY-ALIGN-*`) pero las secciones
  históricas de abajo (`🔌`, `⏳`, `🎨`, `📅`, `🌊`, `🗺️`) no se migraron —
  de bajo valor reescribir retroactivamente contenido histórico ya
  archivable; no se ejecuta aquí.

---

## 🟡 Pendientes técnicos inmediatos — prioridad 2
> David puede hacer estas tareas sin esperar a nadie.

> El plan PublicRead/PageDelivery/Snapshots es prioritario. Este backlog no debe
> cambiar contratos del Hub ni del camino público mientras `QA-01..04` y el
> cutover estén en curso.

### Bug: navegación Payasos → Historia
- [ ] Al ir `Colección → Payasos → Historia` y presionar Atrás, el stack de historial lleva a un lugar incorrecto (no vuelve a Payasos sino a otro punto).
  - **Archivos:** `public/assets/js/app.js` (lógica pushState/popState), `app/Views/totem/collection_clowns.php`
  - **Criterio:** Atrás desde Historia vuelve siempre al origen correcto según la ruta de entrada.

### Exhibición de Máscaras — implementar sistema ahora
- [x] Sistema de listado y ficha migrado al BFF en TOTEM-BFF-04. El botón se
  habilita automáticamente cuando el facet `mascaras.item_count` es mayor que
  cero; una fuente no disponible muestra el estado honesto correspondiente.
  - **Archivos:** `app/Controllers/CollectionController.php`,
    `app/Views/totem/collection_masks_exhibit.php`
  - **Fuente:** `public-read/{locale}/collection-items?category=mascaras`

### Exhibición de Payasos — implementar sistema ahora
- [x] Sistema de listado migrado al BFF en TOTEM-BFF-04. El botón se habilita
  automáticamente cuando el facet `payasos.item_count` es mayor que cero.
  - **Archivos:** `app/Controllers/CollectionController.php`,
    `app/Views/totem/collection_clowns_exhibit.php`
  - **Fuente:** `public-read/{locale}/collection-items?category=payasos`

### Imágenes clicables en Tradiciones de Máscaras
- [ ] Las imágenes de Comedia del Arte y Comedia de los Andes deben ser clicables, no solo los botones de texto.
  - **Archivo:** `app/Views/totem/collection_masks_traditions.php`
  - **Criterio:** Presionar la imagen navega igual que presionar el botón correspondiente.

### Paginador en Títeres
- [ ] El listado de técnicas (14 ítems) y el listado de exhibición de Títeres tienen demasiados ítems en pantalla sin paginar.
  - **Archivos:** `app/Views/totem/collection_techniques.php`, `app/Views/totem/collection_puppets_exhibit.php`
  - **Criterio:** Máximo N ítems por página; botones anterior/siguiente funcionales táctilmente.

### Filtro por fecha en Cartelera
- [ ] El calendario de junio en `/cartelera` no es funcional. Al presionar una fecha debe filtrar o destacar las obras de ese día.
  - **Archivo:** `app/Views/totem/billboard.php`, `public/assets/js/app.js`
  - **Criterio:** Presionar fecha → lista se filtra o la obra del día queda destacada.

### Carrusel de imágenes en detalle de Cartelera
- [ ] Las flechas de galería existen pero no hay lógica de carrusel activa. Implementar para cuando lleguen múltiples imágenes por obra.
  - **Archivos:** `app/Views/totem/billboard_detail.php`, `public/assets/js/app.js`
  - **Criterio:** Con una sola imagen, las flechas se ocultan. Con múltiples, navegan entre ellas.

---

## 🔌 Conexión a BD vía API — miércoles 18/6
> Todo el contenido actual es real pero hardcoded. A partir del 18/6 se conecta al panel de administración.
>
> ⚠️ Para Cartelera, TeatroEscuela, piezas de colección y técnicas, estas
> casillas son históricas y quedan reemplazadas por `TOTEM-BFF-02..04` del
> plan [`2026-08-18-plan-totem-via-bff.md`](../docs/plan/2026-08-18-plan-totem-via-bff.md).
> No implementar las rutas `/api/v1/totem/*` que aparecen abajo.

- [ ] **Histórica / superseded — Cartelera** → `GET /api/v1/totem/shows` (ver `TOTEM-BFF-02`)
- [ ] **Histórica / superseded — Teatro Escuela — Cursos** → `GET /api/v1/totem/courses` (ver `TOTEM-BFF-03`)
- [ ] **Histórica / superseded — Colección Títeres — Exhibición** → `GET /api/v1/totem/collection?group=titeres` + `GET /api/v1/totem/collection/{id}` (ver `TOTEM-BFF-04`)
- [ ] **Histórica / superseded — Técnicas de Títeres** → `GET /api/v1/totem/techniques` + `GET /api/v1/totem/technique/{id}` (ver `TOTEM-BFF-04`)
- [ ] **Historia (posts editoriales)** → endpoint de posts / historia del API (confirmar ruta exacta)
- [ ] **Explora el Museo** → `GET /api/v1/totem/museum` + `GET /api/v1/totem/museum-history/{slug}`
- [ ] **Visitas Guiadas** → `GET /api/v1/totem/guided-visits` (cuando el contenido exista)

**Nota histórica:** El contenido hardcoded de Cartelera, TeatroEscuela y
Catálogo fue reemplazado por lecturas reales del BFF en TOTEM-BFF-02..04.

---

## ⏳ Bloqueados por Coni (diseño)
> David integra cuando llegan los assets. No hay nada que hacer hasta entonces.

- [ ] **B1/A3 — Rediseño de `collection_main`** (`/museo/coleccion`) como pantalla única con navegación directa a Títeres/Payasos/Máscaras. Bloqueado por assets definitivos de Coni con nomenclatura final. Al recibirlos: rediseñar, migrar navegación y evaluar si las subrutas intermedias siguen siendo necesarias.
  - **Archivo:** `app/Views/totem/collection_main.php`
  - **Criterio:** Una sola pantalla concentra los 3 grupos con botones directos; cada bloque tiene ilustración de Coni.

- [ ] **GIF animado splash** — collage del inicio. Cuando llegue: reemplazar imagen estática, ajustar contenedor CSS `#il-splash`.

- [ ] **GIF animado menú principal** — collage inferior animado. Contenedor ya existe.

- [ ] **Collage museo** — los 3 elementos decorativos del submenú Museo (hoy solo hay 1 de 3).

- [ ] **GIF animado Teatro Escuela** — cierre del dossier con ornamentos ilustrados.

- [ ] **Logo Teatro Museo en topbar** — tamaño correcto, colores y subtítulo (pendiente versión final de Coni).

- [ ] **Diseño de Contacto** (`/contacto`) — pantalla completa pendiente de diseño.

- [ ] **Diseño de Amigos** (`/amigos`) — pantalla completa pendiente de diseño.

- [ ] **Pájaro superior splash** — confirmar si va y cuándo llega.

---

## ⏳ Bloqueados por el equipo TeatroMuseo (contenido)
> David integra cuando el equipo provee la información.

- [ ] **Maestros Teatro Escuela** — reemplazar los mockups actuales por fotos y nombres reales. Si no hay datos, ocultar la sección. Definir con equipo.

- [ ] **Título "Historia de la Iglesia" → confirmar** — ¿es "Historia de la Capilla" o se mantiene "Iglesia"? Confirmar con equipo antes de editar vistas y traducciones.

- [ ] **Contenido de "TeatroMuseo Hoy"** (`/museo/el-museo/actualidad`) — el texto actual es una aproximación con IA. Reemplazar por contenido real cuando el equipo lo proporcione.

- [ ] **Definir campos a mostrar por ficha en el tótem** — Javi tiene la última palabra sobre qué datos de cada objeto aparecen en pantalla (nombre, técnica, año, descripción, etc.). Esto afecta el diseño de `collection_item_detail.php` y debe definirse antes de conectar la API.

- [ ] **Fichas de colección de Títeres** — completar información en el Excel oficial de Javi y definir/tomar fotografías finales. Bloqueado hasta definir los campos anteriores.

- [ ] **Fichas de colección de Payasos y Máscaras** — cuando Javi complete las fichas, la Exhibición de Payasos/Máscaras se activa automáticamente (ver tareas de implementación arriba).

- [ ] **Artículos adicionales de Historia** — faltan Historia de Títeres e Historia de Máscaras. Con las mismas 4 rondas de investigación + IA que se usaron para Circo y Payasos.

> **Nota — imágenes de técnicas de títeres:** Las imágenes individuales para las 14 técnicas **NO son responsabilidad de David**. Si el equipo las provee en el formato correcto, se integran. Si no, se mantiene la imagen genérica existente.

---

## 🎨 Mejoras de diseño (en algún momento)
> Sin bloqueante externo, pero no son urgentes.

- [ ] **C1 — Rediseñar `/museo/historia`** para que sea más editorial/dramática. Hoy las 2 opciones (Circo/Payasos) funcionan pero la composición es demasiado textual. La referencia de Coni pide hero fuerte, imagen central protagonista y CTA como bloques.
  - **Archivo:** `app/Views/totem/comic_history_main.php`

- [ ] **D1 — Ajustar detalle Cartelera** (`/cartelera/detalle/{slug}`) al layout editorial de la referencia: imagen y ficha lateral mejor separadas, composición con más dramatismo de afiche.
  - **Archivo:** `app/Views/totem/billboard_detail.php`
  - **Referencia:** `assets/design-refs/cartelera/cartelera-detalle.png`

- [ ] **B2/A4 — Ficha individual de objeto** (`/museo/coleccion/fichas/{id}`) — hay 3 fichas implementadas de Títeres. El layout funciona pero puede refinarse con la referencia definitiva de Coni.
  - **Archivo:** `app/Views/totem/collection_item_detail.php`
  - **Referencia:** `assets/design-refs/museo/coleccion/titeres/ficha-item.webp`

---

## 📅 Post-marcha blanca (nice-to-have)
> Para después de que el tótem esté estable en producción.

- [ ] **F1 — Carrusel táctil nativo** — swipe con `touchstart`/`touchmove`/`touchend` en vanilla JS, sin librerías externas.
- [ ] **F2 — Lazy loading YouTube** — mostrar thumbnail estático con botón Play, inyectar `<iframe>` solo al presionar. (Aplazado: el video de Teatro Escuela no existe aún.)
- [ ] **F3 — Contenedores CSS para swap PNG→GIF/Lottie** — asegurar que los contenedores `#il-*` tengan dimensiones explícitas para evitar CLS cuando lleguen las animaciones finales de Coni.
- [ ] **E1 — Teclado táctil + QR dinámico** en `/contacto` y extensión — `simple-keyboard` + `qrcode.js` con `?utm_source=totem`.
- [ ] **Manual de operaciones** — encendido/apagado, reinicio Fully Kiosk, limpieza de caché, reinicio de servidor.

---

## 🌊 OLEADA 3: Marcha Blanca & Producción (activa desde 15/6)

### Calibración en hardware físico
- [ ] Verificar que Fully Kiosk Browser bloquee gestos externos (barras de scroll, zoom/pinch).
- [ ] Validar áreas táctiles ≥ 48×48px. Ajustar tipografía si hay dificultad de lectura a distancia.
- [ ] Optimizar imágenes `.webp` si la carga genera latencia perceptible.

### Estabilidad técnica & BFF offline
- [x] ✅ 2026-08-19 (`TOTEM-BFF-13`): simulado matando el proceso real del
  BFF con el Tótem real levantado — no queda "colgado", sirve HTML 200 con
  contenido stale o el estado honesto `content_unavailable`, nunca un error
  técnico.
- [x] ✅ 2026-08-19 (`TOTEM-BFF-13`): confirmada carga graceful desde caché
  (contenido real stale) y, al agotarla, la pantalla amigable
  `content_unavailable.php` (multiidioma vía `Common.content_unavailable_*`).

### Assets de Coni (cuando lleguen)
- [ ] Colocar GIFs/Lotties en sus carpetas correspondientes.
- [ ] Verificar rendimiento en el procesador local del tótem (no todos los GIFs pesan igual en ARM).

---

## 🗺️ Mapa de Componentes y Rutas Físicas

| Componente | Ruta |
|---|---|
| Rutas del Tótem | `app/Config/Routes.php` |
| Controlador Principal | `app/Controllers/MainController.php` |
| Vistas de Pantalla | `app/Views/totem/` |
| Layout Base | `app/Views/layouts/MainLayout.php` |
| Servicio BFF | `app/Services/BffTotemClient.php` ✅ |
| CSS compilado | `public/assets/css/style.css` |
| CSS parciales | `public/assets/css/src/` (modificar aquí + `composer build:css`) |
| JS principal | `public/assets/js/app.js` (idle timer, navegación, handlers táctiles) |
| Traducciones i18n | `app/Language/{es\|en\|fr\|pt}/` |
| Scripts de despliegue | `scripts/deploy_ftp.py` + `.deploy/deploy.py` (versionados) |
| Referencias visuales de Coni | `assets/design-refs/` |

### Vistas activas por ruta

| Ruta | Vista |
|---|---|
| `/` | `main_splash.php` |
| `/idioma` | `language_selector.php` (modal overlay) |
| `/menu` | `main_menu.php` |
| `/museo` | `museum_menu.php` |
| `/museo/coleccion` | `collection_main.php` |
| `/museo/coleccion/titeres` | `collection_puppets.php` (activa) |
| `/museo/coleccion/titeres/tecnicas` | `collection_techniques.php` |
| `/museo/coleccion/titeres/exhibicion` | `collection_puppets_exhibit.php` |
| `/museo/coleccion/fichas/{id}` | `collection_item_detail.php` |
| `/museo/coleccion/payasos` | `collection_clowns.php` (activa) |
| `/museo/coleccion/mascaras` | `collection_masks.php` (activa) |
| `/museo/coleccion/mascaras/tradiciones/comedia-arte` | `collection_masks_comedia_arte.php` |
| `/museo/coleccion/mascaras/tradiciones/comedia-andes` | `collection_masks_comedia_andes.php` |
| `/museo/historia` | `comic_history_main.php` |
| `/museo/historia/{slug}` | `comic_history_post.php` |
| `/museo/el-museo` | `museum_info_main.php` |
| `/museo/el-museo/institucion` | `museum_institution.php` |
| `/museo/el-museo/edificio` | `museum_building.php` |
| `/museo/el-museo/actualidad` | `museum_today.php` |
| `/museo/visitas-guiadas` | `museum_visits.php` (placeholder) |
| `/teatro-escuela` | `theater_school.php` o `section.php` |
| `/cartelera` | `billboard.php` |
| `/cartelera/detalle/{slug}` | `billboard_detail.php` |
| `/contacto` | placeholder |
| `/amigos` | placeholder |

---

*TASKS.md — Actualizado 2026-06-15 · Sprint fin de semana completo. Auditorías visuales de planificación archivadas en TASKS-ARCHIVES.md.*
