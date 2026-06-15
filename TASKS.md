# Roadmap de Implementación — Tótem Interactivo (TASKS.md)

Backlog técnico activo para `teatromuseo-totem-ci4`. Las tareas completadas se archivan en [TASKS-ARCHIVES.md](TASKS-ARCHIVES.md).

**Estado actual (2026-06-15):** 23 pantallas navegables, 4 idiomas, contenido real hardcoded. Marcha blanca activa. Conexión a BD desde el miércoles 18/6.

> **Nota arquitectural — Oleadas 2 y 3:**
> Las rutas `/museo/coleccion/titeres`, `/museo/coleccion/mascaras` y `/museo/coleccion/payasos` están **activas con contenido real** y son el flujo de navegación principal a partir del sprint 13-15/6. La nota anterior de "OBSOLETA" en el plan de colección dejó de ser válida: la decisión de consolidar en `collection_main` queda **aplazada hasta que lleguen los assets definitivos de Coni**. No eliminar estas rutas ni vistas mientras eso no ocurra.

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

- [ ] **Fichas de colección de Payasos y Máscaras** — solo hay 3 fichas completas de Títeres (Javi). Cuando el equipo complete más fichas, la Exhibición de Payasos/Máscaras se activa automáticamente (ver tareas de implementación arriba).

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
