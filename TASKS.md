# Roadmap de Implementación — Tótem Interactivo (TASKS.md)

Backlog técnico activo para `teatromuseo-totem-ci4`. Las tareas completadas se archivan en [TASKS_ARCHIVE.md](TASKS_ARCHIVE.md).
Seguimiento cross-repo: [`../TASKS.md`](../TASKS.md).

**Estado (2026-08-05):** 23 pantallas navegables, 4 idiomas. Conectado al hub vía `/api/v1/totem/*`.
La marcha blanca y el hito de "conexión a BD desde el 18/6" quedaron atrás — este encabezado estuvo
~7 semanas obsoleto hasta la auditoría del 2026-08-05.

> **Nota arquitectural — Oleadas 2 y 3:**
> Las rutas `/museo/coleccion/titeres`, `/museo/coleccion/mascaras` y `/museo/coleccion/payasos` están **activas con contenido real** y son el flujo de navegación principal a partir del sprint 13-15/6. La nota anterior de "OBSOLETA" en el plan de colección dejó de ser válida: la decisión de consolidar en `collection_main` queda **aplazada hasta que lleguen los assets definitivos de Coni**. No eliminar estas rutas ni vistas mientras eso no ocurra.

---

## 🟡 Saneamiento arquitectónico (auditoría 2026-08-05)

> **Contexto, evidencia y rutas exactas:** [`../docs/plan/2026-08-05-saneamiento-arquitectonico.md`](../docs/plan/2026-08-05-saneamiento-arquitectonico.md)
>
> **Decisión tomada: alineación completa con la flota** (manteniendo el despliegue FTP).
> Esta app quedó fuera de línea respecto de las otras siete en casi todos los ejes de tooling.
> Empezar por `TOT-02`, que es el único bug funcional.

### TOT-02 — Puerto obsoleto (bug funcional) — ✅ COMPLETADA (verificado 2026-08-07)

- [x] **El fallback apuntaba al puerto 8080; el hub corre en 8180.** Corregido en
  `app/Services/TotemApiService.php:24` y `app/Helpers/title_helper.php:42` — ambos ahora
  usan `http://localhost:8180/api/v1/totem` por defecto (commit `31e0513`). Grep de `8080` en
  todo `*.php` solo devuelve una línea comentada de ejemplo en `Config/Cors.php`, inerte.
- [x] **`.env.example` creado** (commit `766006c`, 42 líneas) documentando las 6 variables
  `TOTEM_*` realmente leídas por la app (`TOTEM_API_URL`, `TOTEM_API_KEY`,
  `TOTEM_ENABLE_FILE_CACHE`, `TOTEM_CACHE_TTL_SECONDS`, `TOTEM_ENABLE_TRANSITIONS`,
  `TOTEM_ENABLE_ANIMATIONS`) — más completo que las 4 originalmente pedidas aquí.

### TOT-01 — Alineación con la flota (parcial — ver ítems marcados)

- [x] **Subida a PHPUnit 11** (`^11.0` en `composer.json`, resuelto 11.5.56) y PHPStan
  `^2.1.56` (resuelto 2.2.8) — verificado en vivo: 66 tests / 251 assertions, todos ✅
  (commits `6657897`/`271b886`).
- [x] **`phpstan-baseline.neon` vaciado** (`ignoreErrors: []`, confirmado con
  `vendor/bin/phpstan analyse` → "No errors") y `app/Config` excluido, alineado con el resto
  de la flota (commit `4ed76c9`).
- [ ] **Añadir `test:feature`** (falta) y unificar los alias de scripts: aquí son `lint`/`analyse`,
  en la familia API son `cs-check`/`phpstan`.
- [ ] **Completar el CI:** faltan `release.yml`, `security.yml` y `dependabot.yml`, y no hay matriz
  de PHP (una sola versión, declarando `"php": "^8.2"`). Tampoco hay `composer audit`. Es la app que
  se despliega a producción y tiene el CI más débil de la flota.
- [ ] **Añadir `docker-compose.yml`.** Hay `Dockerfile` pero no compose: se puede construir pero no
  levantar en el stack local documentado.
- [ ] **Incorporar a la cadena de build compartida:** hoy sin Tailwind, sin build de JS, sin husky,
  sin `engines` ni `packageManager` fijados — a diferencia de admin y web.
- [ ] **Unificar la capa HTTP.** Hay **dos modismos distintos dentro de la misma app**:
  `Services::curlrequest()` en `app/Services/TotemApiService.php:42` y `curl_init` crudo en
  `app/Controllers/HealthController.php:57`. Faltan además: reintentos en 5xx (el admin reintenta
  los GET dos veces con backoff), propagación de `X-Request-ID`, y — lo más grave — **todos los
  caminos de error devuelven `[]`** (no-2xx, JSON inválido, excepción, y hasta el fallo al construir
  el cliente), así que una caída total del upstream es **indistinguible de contenido vacío**.
- [ ] **Colapsar el triple decorador manual de `TotemApiInterface`.**
  `TotemApiService` (256 l) + `CachedTotemApiService` (154 l) + `FileCachedTotemApiService` (220 l):
  cada método de la interfaz está reescrito tres veces a mano. Añadir un endpoint obliga a tocar
  cuatro archivos.
- [ ] **Registrar los repositorios de respaldo en `Config/Services`** en vez de instanciarlos en
  línea como valores por defecto de constructor. `app/Controllers/BillboardController.php:37` hace
  `new \App\Repositories\BillboardFallbackRepository()` **directamente**, además de usar el
  presenter que ya lo tiene.
- [ ] **Unificar el formateo de fecha localizada con `teatromuseo-web`.**
  `app/Presenters/DatePresenter.php:19,36` usa `IntlDateFormatter` **sin la guarda `class_exists`**
  que sí tiene `web/app/Common.php:309` (`format_localized_date()`), más patrones `sprintf` por
  idioma escritos a mano (`Section.school_start_en|fr|pt|es`). Ambas apps renderizan los mismos
  eventos y cursos para los mismos 4 idiomas.
- [ ] **Blindar `app/Config/Database.php`.** Arrastra el grupo por defecto de CI4
  (`'hostname' => 'localhost'`, `'DBDriver' => 'MySQLi'`) pese a que la app es stateless. Adoptar el
  patrón del BFF (`:memory:` + SQLite3 + comentario explicando que no hay BD propia).
- [ ] **Añadir el glob `.env.*` a `.gitignore`** (solo esta app y `teatromuseo-web` no lo tienen).
- [x] **`DEAD-02` — `app/Repositories/MuseumFallbackRepository.php` eliminado** (commit
  `ff7db92`); confirmado sin referencias remanentes en `app/`/`tests/`.
- [ ] **`DOC-01` — Migrar este tracker a la taxonomía del resto de la flota**
  (`🔴 En progreso` / `🟡 Próximo` / `✅ Completadas`) en vez de "Pendientes técnicos inmediatos",
  y triar las 45 casillas abiertas de abajo: varias pueden estar ya resueltas.

---

## 🔴 Pendientes técnicos inmediatos
> David puede hacer estas tareas sin esperar a nadie.

### Bug: navegación Payasos → Historia
- [ ] Al ir `Colección → Payasos → Historia` y presionar Atrás, el stack de historial lleva a un lugar incorrecto (no vuelve a Payasos sino a otro punto).
  - **Archivos:** `public/assets/js/app.js` (lógica pushState/popState), `app/Views/totem/collection_clowns.php`
  - **Criterio:** Atrás desde Historia vuelve siempre al origen correcto según la ruta de entrada.

### Exhibición de Máscaras — implementar sistema ahora
- [ ] Implementar estructura completa de Exhibición para Máscaras (listado + ficha detalle), idéntica a la de Títeres. El botón queda desactivado/oculto hasta que existan fichas — pero el sistema debe estar listo para activarse automáticamente cuando lleguen.
  - **Archivos:** `app/Controllers/TotemController.php`, `app/Views/totem/collection_masks.php`, nueva vista `collection_masks_exhibit.php` (o reutilizar `collection_masks_exhibit.php` si ya existe)
  - **API:** `GET /api/v1/totem/collection?group=mascaras`
  - **Criterio:** Cuando haya fichas en la BD con `group=mascaras`, el botón se habilita y la pantalla muestra el listado sin cambios en el código.

### Exhibición de Payasos — implementar sistema ahora
- [ ] Ídem que Máscaras. Botón desactivado hasta que existan fichas, sistema listo para activarse.
  - **Archivos:** `app/Controllers/TotemController.php`, `app/Views/totem/collection_clowns.php`
  - **API:** `GET /api/v1/totem/collection?group=payasos`
  - **Criterio:** Misma lógica que Títeres y Máscaras.

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

- [ ] **Cartelera** → `GET /api/v1/totem/shows` (reemplazar array hardcoded en controlador)
- [ ] **Teatro Escuela — Cursos** → `GET /api/v1/totem/courses`
- [ ] **Colección Títeres — Exhibición** → `GET /api/v1/totem/collection?group=titeres` + `GET /api/v1/totem/collection/{id}`
- [ ] **Técnicas de Títeres** → `GET /api/v1/totem/techniques` + `GET /api/v1/totem/technique/{id}`
- [ ] **Historia (posts editoriales)** → endpoint de posts / historia del API (confirmar ruta exacta)
- [ ] **Explora el Museo** → `GET /api/v1/totem/museum` + `GET /api/v1/totem/museum-history/{slug}`
- [ ] **Visitas Guiadas** → `GET /api/v1/totem/guided-visits` (cuando el contenido exista)

**Nota:** Después de conectar la API, el contenido hardcoded que hoy existe (cartelera real, cursos reales, fichas de Javi, textos editoriales en 4 idiomas) debe seguir apareciendo — ahora sirviéndose desde la BD.

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

### Estabilidad técnica & fallback offline
- [ ] Simular desconexión total a internet y verificar que el tótem no quede "colgado" ni muestre errores técnicos.
- [ ] Asegurar carga graceful desde caché o pantalla de error amigable multiidioma.

### Assets de Coni (cuando lleguen)
- [ ] Colocar GIFs/Lotties en sus carpetas correspondientes.
- [ ] Verificar rendimiento en el procesador local del tótem (no todos los GIFs pesan igual en ARM).

---

## 🗺️ Mapa de Componentes y Rutas Físicas

| Componente | Ruta |
|---|---|
| Rutas del Tótem | `app/Config/Routes.php` |
| Controlador Principal | `app/Controllers/TotemController.php` |
| Vistas de Pantalla | `app/Views/totem/` |
| Layout Base | `app/Views/layouts/MainLayout.php` |
| Servicio API | `app/Services/TotemApiService.php` ✅ |
| CSS compilado | `public/assets/css/style.css` |
| CSS parciales | `public/assets/css/src/` (modificar aquí + `composer build:css`) |
| JS principal | `public/assets/js/app.js` (idle timer, navegación, handlers táctiles) |
| Traducciones i18n | `app/Language/{es\|en\|fr\|pt}/Totem.php` |
| Scripts de despliegue | `.deploy/` (ignorado por git) |
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
