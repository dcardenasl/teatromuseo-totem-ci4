# Roadmap de Implementación — Tótem Interactivo (TASKS.md)

Este archivo sirve como el backlog técnico oficial y guía de desarrollo para implementar la lista completa de mejoras del **Tótem Interactivo** (`teatromuseo-totem-ci4`). Está estructurado en 3 Oleadas de desarrollo para facilitar la entrega continua y el testeo en el hardware Fully Kiosk.

---

## 🔎 Auditoría visual pendiente contra PDFs de referencia

Esta sección documenta la comparación real entre el tótem en `http://localhost:8086` y los PDFs de diseño ubicados en `docs/assets/`. La idea es que este bloque funcione como una guía de sincronización visual, no solo como una lista de tareas sueltas.

### Cómo leer esta auditoría
- `PDF de referencia` = fuente de verdad visual.
- `Ruta actual` = pantalla que hoy está sirviendo la app.
- `Brecha` = diferencia concreta entre lo implementado y el diseño.
- `Criterio de aceptación` = qué debe cumplirse para considerar la pantalla sincronizada.

### Fuentes de referencia
- `docs/assets/1 MENU PRINCIPAL/MENU PRINCIPAL.pdf`
- `docs/assets/1.1 MUSEO/1.1.1 COLECCION/COLECCION.pdf`
- `docs/assets/1.1 MUSEO/1.1.3 HISTORIA COMICA/HISTORIA COMICA.pdf`
- `docs/assets/1.2 CARTELERA/1.2.2 DETALLE OBRA/CARTELERA DETALLE.pdf`
- `docs/assets/1.3 ESCUELA/TEATROESCUELA .pdf`

### Matriz de sincronización

| PDF de referencia | Ruta actual | Vista principal | Estado | Prioridad |
|---|---|---|---|---|
| `MENU PRINCIPAL.pdf` | `/menu` | [app/Views/totem/main_menu.php](/Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/main_menu.php) | Pulido aplicado | Media |
| `COLECCION.pdf` | `/museo/coleccion` (pantalla única con scroll — ver decisión arquitectura 1/6) | [app/Views/totem/collection_main.php](/Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/collection_main.php) | Pendiente rediseño — bloqueado por assets de Coni | Alta |
| `HISTORIA COMICA.pdf` | `/museo/historia-comica` | [app/Views/totem/comic_history_main.php](/Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/comic_history_main.php) | Lejos del PDF | Alta |
| `CARTELERA DETALLE.pdf` | `/cartelera/detalle/{slug}` | [app/Views/totem/billboard_detail.php](/Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/billboard_detail.php) | Parcial | Alta |
| `TEATROESCUELA.pdf` | `/teatro-escuela` | [app/Views/totem/section.php](/Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/section.php) | Lejos del PDF | Alta |

### Criterios globales de sincronización
- Ninguna pantalla referenciada debe quedar con bloques vacíos o placeholders visibles.
- Cada pantalla debe respetar el orden de lectura del PDF: hero, cuerpo principal, CTA, y cierre.
- Las imágenes deben sostener el peso visual de la composición; no basta con tener el texto correcto.
- Los textos largos deben estar divididos para lectura táctil, sin scroll confuso ni bloques demasiado densos.
- Las rutas de detalle deben mantener jerarquía de datos consistente, sin mezclar contenido de prueba con contenido editorial.
- Si una pantalla usa mocks temporales, debe marcarse explícitamente como contingencia y no como implementación final.

### A. Menú principal
- [x] **A1 — Pulido fino del menú principal para igualar el PDF**
  * **PDF de referencia:** `MENU PRINCIPAL.pdf`
  * **Ruta actual:** `/menu`
  * **Archivos a tocar:** [app/Views/totem/main_menu.php](/Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/main_menu.php), [public/assets/css/src/screens/menu.css](/Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/public/assets/css/src/screens/menu.css)
  * **Brecha visual:** La composición general sí coincide, pero el render actual todavía puede afinar el espaciado vertical, la jerarquía de tipografías y la posición de los ornamentos inferiores para clavar la maqueta.
  * **Criterio de aceptación:**
      1. El título central debe quedar visualmente centrado en el área segura del tótem.
      2. Las 5 tarjetas deben mantenerse en el ritmo visual del PDF, sin desbalance entre filas.
      3. Los ornamentos inferiores no deben tapar el contenido ni generar sensación de corte brusco.
      4. El header debe conservar la misma lógica de navegación y tamaño de botones que el PDF.

### B. Colección del Museo
> ⚠️ **Cambio de arquitectura (1/6):** La navegación se simplificó. `collection_main` es ahora la única pantalla de colección — muestra Títeres, Payasos y Máscaras con scroll, navegando directo a fichas. Las subrutas `/titeres`, `/mascaras`, `/payasos` y sus vistas quedan obsoletas y serán eliminadas.

- [ ] **B1 — Rediseñar `collection_main` según diseño aprobado de Coni** (`/museo/coleccion`)
  * **PDF de referencia:** `COLECCION.pdf`
  * **Ruta actual:** `/museo/coleccion`
  * **Archivos a tocar:** [app/Views/totem/collection_main.php](/Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/collection_main.php)
  * **Bloqueante:** ⏳ Assets de Coni pendientes (reunión 4/6).
  * **Criterio de aceptación:**
      1. Una sola pantalla con scroll muestra Títeres, Payasos y Máscaras como secciones.
      2. Cada sección tiene ilustraciones decorativas de Coni (PNG placeholder → swap final).
      3. Los links van directo a `/museo/coleccion/fichas/{id}`, sin pantalla intermedia.
      4. Las rutas y vistas obsoletas están eliminadas.
- [ ] **B2 — Implementar ficha individual de objeto** (`/museo/coleccion/fichas/{id}`)
  * **PDF de referencia:** `COLECCION.pdf` (versión corregida — no recibida aún)
  * **Ruta actual:** `/museo/coleccion/fichas/{id}`
  * **Archivos a tocar:** [app/Views/totem/collection_item_detail.php](/Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/collection_item_detail.php)
  * **Bloqueante:** ⏳ Coni mostró diseño el 27/5 con corrección pendiente (layout muy espaciado). PDF corregido no recibido (reunión 4/6).
  * **Criterio de aceptación:**
      1. La imagen principal debe ser protagonista.
      2. Metadatos claramente separados del cuerpo editorial.
      3. No mostrar el campo `contenido` (excluido de la API del tótem).
      4. Usable sin scroll caótico.

### C. Historia Cómica
- [ ] **C1 — Rediseñar `/museo/historia-comica` para que coincida con el PDF**
  * **PDF de referencia:** `HISTORIA COMICA.pdf`
  * **Ruta actual:** `/museo/historia-comica`
  * **Archivos a tocar:** [app/Views/totem/comic_history_main.php](/Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/comic_history_main.php)
  * **Brecha visual:** La vista actual se reduce a capítulos genéricos. El PDF pide una portada mucho más dramática, con ilustración central, dos bloques de CTA y una composición narrativa clara.
  * **Criterio de aceptación:**
      1. El encabezado debe tener el peso visual del PDF.
      2. Debe existir una imagen central protagonista.
      3. Los CTA de navegación deben aparecer como bloques fuertes y no como texto suelto.
      4. El orden de lectura debe ser inmediato y jerarquizado.
- [ ] **C2 — Reemplazar los posts genéricos por contenido editorial real**
  * **PDF de referencia:** `HISTORIA COMICA.pdf`
  * **Ruta actual:** `/museo/historia-comica/{slug}`
  * **Archivos a tocar:** [app/Controllers/TotemController.php](/Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Controllers/TotemController.php), la vista de detalle asociada
  * **Brecha visual:** El flujo de detalle todavía necesita una capa editorial consistente. No basta con un título y un cuerpo mínimo.
  * **Criterio de aceptación:**
      1. El detalle debe tener una estructura reconocible como artículo o capítulo.
      2. El contenido no debe parecer un placeholder de desarrollo.
      3. Debe ser posible leerlo sin fricción visual.

### D. Cartelera
- [ ] **D1 — Ajustar `/cartelera/detalle/{slug}` al layout editorial del PDF**
  * **PDF de referencia:** `CARTELERA DETALLE.pdf`
  * **Ruta actual:** `/cartelera/detalle/{slug}`
  * **Archivos a tocar:** [app/Views/totem/billboard_detail.php](/Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/billboard_detail.php), [public/assets/css/src/screens/billboard.css](/Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/public/assets/css/src/screens/billboard.css)
  * **Brecha visual:** La información existe, pero la composición no reproduce el PDF. La maqueta de referencia reparte el peso entre imagen, texto descriptivo y fichas de datos; la app actual carga una tarjeta demasiado centrada y simplificada.
  * **Criterio de aceptación:**
      1. Debe recuperarse la sensación editorial de afiche detallado.
      2. La imagen principal debe encajar mejor con la columna de información.
      3. La jerarquía entre título, tags, cuerpo y ficha lateral debe ser evidente.
      4. Los controles de navegación de imagen deben integrarse con el layout, no flotar como un añadido aislado.
- [ ] **D2 — Validar que el contenido de cartelera no dependa solo de mocks**
  * **PDF de referencia:** `CARTELERA DETALLE.pdf`
  * **Ruta actual:** `/cartelera/detalle/{slug}`
  * **Archivos a tocar:** [app/Controllers/TotemController.php](/Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Controllers/TotemController.php)
  * **Brecha visual:** Parte del contenido aún proviene de datos estáticos de respaldo. Hay que confirmar que la pantalla use el contenido correcto del backend o, si no existe, que el mock quede claramente delimitado.
  * **Criterio de aceptación:**
      1. El detalle debe resolver título, tags, texto, horarios y precio de forma consistente.
      2. No deben aparecer textos de prueba en la salida final.
      3. Si se usa fallback temporal, debe ser visualmente equivalente a la referencia.

### E. Teatro Escuela
- [ ] **E1 — Rediseñar `/teatro-escuela` como dossier largo, no como sección simple**
  * **PDF de referencia:** `TEATROESCUELA .pdf`
  * **Ruta actual:** `/teatro-escuela`
  * **Archivos a tocar:** [app/Views/totem/section.php](/Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/section.php), o una vista dedicada tipo `theater_school_dossier.php`
  * **Brecha visual:** Esta es la pantalla con mayor diferencia respecto del PDF. La referencia construye un dossier vertical largo con hero, métricas, maestros, cursos, QR y un cierre muy ilustrado. La versión actual se percibe demasiado genérica.
  * **Criterio de aceptación:**
      1. Debe existir una zona hero fuerte en la parte superior.
      2. Las cifras principales deben leerse de inmediato.
      3. La sección de maestros debe sentirse como un carrusel o grilla curada, no como lista de prueba.
      4. El bloque de cursos debe parecer una pieza editorial real y no una simple tarjeta informativa.
      5. El cierre debe incluir la composición ilustrada final y el contacto/QR en una ubicación coherente con el PDF.

### F. Pantallas de museo que siguen en modo placeholder
- [x] **F1 — Completar `museum_today.php` con contenido real de actualidad**
  * **PDF de referencia:** `TEATROESCUELA .pdf` y la estructura de referencia interna del módulo museo
  * **Ruta actual:** `/museo/el-museo/actualidad`
  * **Archivos a tocar:** [app/Views/totem/museum_today.php](/Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/museum_today.php)
  * **Brecha visual:** La vista actual sigue como “Detalle actualidad (mock)”. Eso no alcanza un estándar de producción ni de sincronización con diseño.
  * **Criterio de aceptación:**
      1. Debe dejar de mostrarse como stub.
      2. Debe tener contenido real o una composición explícitamente temporal.
      3. Debe mantener el mismo lenguaje visual del resto del módulo museo.

### G. Reglas de sincronización entre PDF y código
- [ ] **G1 — Crear una tabla de equivalencia entre assets del PDF y assets reales del tótem**
  * **Ruta de Implementación:** Añadir una subsección en este mismo `TASKS.md` o en un documento complementario dentro de `docs/`
  * **Qué debe incluir:**
      1. Nombre del asset en PDF.
      2. Archivo real usado en `public/assets/img/`.
      3. Pantalla donde aparece.
      4. Estado: final, provisional o ausente.
  * **Objetivo:** Evitar que futuras implementaciones reinterpreten el diseño en vez de reproducirlo.
- [ ] **G2 — Definir criterios de aceptación visual por pantalla antes de tocar CSS**
  * **Ruta de Implementación:** Añadir checklist por pantalla en este mismo documento.
  * **Qué debe incluir:**
      1. Jerarquía de títulos.
      2. Orden de bloques.
      3. Presencia de imágenes principales.
      4. Presencia de CTA.
      5. Comportamiento del scroll.
  * **Objetivo:** Que cada ajuste de estilo pueda validarse contra una lista de chequeo objetiva.
- [ ] **G3 — Marcar explícitamente las pantallas con contenido mock**
  * **Ruta de Implementación:** Este backlog y, si aplica, comentarios en las vistas o controladores.
  * **Qué debe incluir:** etiqueta visible de contingencia en desarrollo interno y nota en backlog de qué contenido falta reemplazar.
  * **Objetivo:** Que nadie asuma que un placeholder es una pantalla final.

### H. Tabla base de equivalencia de assets

Esta tabla debe mantenerse actualizada cuando se cambie cualquier imagen, ilustración o bloque visual relevante. Si un asset del PDF no existe aún en `public/assets/img/`, debe marcarse como `ausente` y tratarse como pendiente de diseño/importación.

| PDF / Asset esperado | Asset real actual | Pantalla | Estado | Observación |
|---|---|---|---|---|
| Menú principal - Museo | `public/assets/img/menu/menu_museo.webp` | `/menu` | final | Coincide bien con la tarjeta de entrada a Museo. |
| Menú principal - Teatro Escuela | `public/assets/img/menu/menu_escuela.webp` | `/menu` | final | Coincide con la tarjeta de escuela. |
| Menú principal - Programación | `public/assets/img/menu/menu_programacion.webp` | `/menu` | final | Coincide con la tarjeta de cartelera. |
| Menú principal - Visitas Guiadas | `public/assets/img/menu/menu_visitas.webp` | `/menu` | final | Coincide con la tarjeta de visitas. |
| Menú principal - Amigos de Teatromuseo | `public/assets/img/menu/menu_amigos.webp` | `/menu` | final | Coincide con la tarjeta de amigos. |
| Menú principal - Collage inferior de edificio / ornamento | `public/assets/img/menu/collage_referencia.webp` | `/menu` | provisional | Usado como referencia visual del cierre inferior, revisar si el PDF tiene recorte o composición distinta. |
| Colección - Títeres (imagen principal) | `public/assets/img/museum/cat_coleccion.webp` | `/museo/coleccion` y `/museo/coleccion/titeres` | final | Es el asset principal visible para la sección de colección. |
| Colección - Historia Cómica | `public/assets/img/museum/cat_historia_comica.webp` | `/museo/coleccion` y `/museo/historia-comica` | final | Coincide con el bloque de historia cómica. |
| Colección - El Museo | `public/assets/img/museum/cat_el_museo.webp` | `/museo/coleccion` y `/museo/el-museo` | final | Coincide con el bloque del museo. |
| Colección - Visitas Guiadas | `public/assets/img/museum/cat_visitas_guiadas.webp` | `/museo/coleccion` y `/visitas-guiadas` | final | Coincide con el bloque de visitas. |
| Teatro Escuela - Collage de cierre | `public/assets/img/school/school_collage.webp` | `/teatro-escuela` | final | Sirve como base del collage final, aunque el PDF tiene una composición más rica. |
| Cartelera detalle - Imagen principal de obra | `public/assets/img/menu/menu_programacion.webp` | `/cartelera/detalle/{slug}` | provisional | Hoy se usa como placeholder; debe reemplazarse por la imagen real de cada obra. |
| Cartelera detalle - Flechas de galería | `public/assets/img/ui/slider_left.webp` y `public/assets/img/ui/slider_right.webp` | `/cartelera/detalle/{slug}` | final | Cumplen la función visual esperada de navegación lateral. |
| Cartelera detalle - Icono duración | `public/assets/img/ui/icon_duration.webp` | `/cartelera/detalle/{slug}` | final | Coincide con el bloque de duración. |
| Cartelera detalle - Icono ticket | `public/assets/img/ui/icon_ticket.webp` | `/cartelera/detalle/{slug}` | final | Coincide con el bloque de precio/entrada. |
| Fondo / textura papel | `public/assets/img/ui/texture.png` | Todas las pantallas | final | Es la base visual del sistema. |
| Portada de Historia Cómica | `ausente` | `/museo/historia-comica` | ausente | El PDF usa una composición editorial fuerte que hoy no tiene un asset equivalente explícito en `public/assets/img/`. |
| Hero / escena principal de Teatro Escuela | `public/assets/img/school/school_collage.webp` | `/teatro-escuela` | provisional | Ya sostiene el hero principal; todavía falta la composición más rica del PDF. |
| QR editorial del dossier de Escuela | `public/assets/img/school/teatroescuela-qr.png` | `/teatro-escuela` | final | QR real generado para el enlace público de Teatro Escuela con `?utm_source=totem`. |
| Afiche o foto principal de cartelera por obra | `ausente` | `/cartelera/detalle/{slug}` | ausente | Cada obra debería tener su propia imagen editorial y no reciclar un placeholder genérico. |
| Ilustraciones secundarias de cierre en Escuela | `ausente` | `/teatro-escuela` | ausente | Faltan varios ornamentos finales del PDF para reproducir la misma densidad visual. |

### I. Checklist de actualización de assets

- [ ] Cuando entre un nuevo PDF de diseño, registrar su nombre exacto en esta sección.
- [ ] Si aparece una imagen nueva en el PDF, crear la fila correspondiente antes de implementarla.
- [ ] Si una pantalla usa un asset provisional, marcarlo como `provisional` hasta reemplazarlo.
- [ ] Si una pantalla carece del asset necesario, registrar el elemento como `ausente` y no asumir equivalencias.
- [ ] Al cambiar un asset, verificar de nuevo la captura visual de la ruta asociada.

### J. Guía aproximada de medidas y orden de bloques

Esta guía no pretende reemplazar al PDF como fuente de diseño. Sirve para reducir ambigüedad en la implementación y para que cada pantalla conserve el mismo orden de lectura, densidad y respiración visual que la referencia.

#### J1. Menú principal
- **Orden visual esperado:**
  1. Topbar con navegación.
  2. Título central en 2 líneas.
  3. Tarjetas en grilla de 2 columnas.
  4. Ornamento / collage inferior.
- **Medidas aproximadas:**
  - Topbar: ocupación visual de 10% a 12% del alto.
  - Título: bloque centrado en el tercio superior.
  - Tarjetas: ancho equivalente a 40% del viewport cada una, con separación uniforme.
  - Ornamento inferior: anclado al borde inferior, sin invadir la zona de CTA.
- **Notas de sincronización:**
  - Mantener el fondo tipo papel.
  - Evitar que el título se vea demasiado comprimido verticalmente.
  - Los accesos deben seguir sintiéndose táctiles, no como menú de escritorio.

#### J2. Colección del Museo
- **Orden visual esperado:**
  1. Topbar.
  2. Título principal.
  3. Bloque `TÍTERES`.
  4. Bloque `PAYASOS`.
  5. Bloque `MÁSCARAS`.
  6. Ornamentos de cierre.
- **Medidas aproximadas:**
  - Cada bloque principal debe ocupar un tramo claro de scroll vertical.
  - La imagen o ilustración principal debe ser más grande que el texto.
  - Los CTA deben quedar alineados bajo el bloque de contenido, no arriba ni pegados al borde.
- **Notas de sincronización:**
  - El PDF funciona como catálogo curado, no como lista.
  - Los bloques deben respirarse con márgenes amplios entre sí.
  - No dejar tarjetas planas sin protagonismo visual.

#### J3. Historia Cómica
- **Orden visual esperado:**
  1. Topbar.
  2. Título grande.
  3. Ilustración o composición central.
  4. Botón / CTA principal.
  5. Segunda composición visual.
  6. Segundo CTA.
  7. Footer con identidad visual.
- **Medidas aproximadas:**
  - La composición central debe ser dominante y ocupar la mayor atención en el primer pliegue.
  - Los CTA deben ser anchos y de lectura inmediata.
  - El footer debe quedar visible al final del recorrido, no oculto por exceso de contenido intermedio.
- **Notas de sincronización:**
  - Evitar convertir la pantalla en una lista de capítulos.
  - El efecto debe ser editorial y no archivístico plano.

#### J4. Cartelera detalle
- **Orden visual esperado:**
  1. Topbar.
  2. Título de obra.
  3. Tags.
  4. Bloque editorial principal.
  5. Imagen / galería principal.
  6. Cuerpo de texto.
  7. Ficha lateral con fecha, hora, duración y precio.
  8. Cierre con collage, QR y logos.
- **Medidas aproximadas:**
  - Título y tags deben quedar arriba, antes del bloque visual principal.
  - La imagen central debe tener suficiente aire alrededor para no sentirse embebida en una tarjeta pequeña.
  - La ficha lateral debe leerse sin competir con el cuerpo principal.
- **Notas de sincronización:**
  - Separar claramente texto de venta, texto descriptivo y datos prácticos.
  - Si existe carrusel, la navegación debe ser evidente pero discreta.

#### J5. Teatro Escuela
- **Orden visual esperado:**
  1. Topbar.
  2. Título principal.
  3. Hero visual.
  4. Resumen corto.
  5. Métricas o cifras.
  6. Maestros / equipo.
  7. Cursos.
  8. Cierre con collage y QR.
- **Medidas aproximadas:**
  - El hero debe ocupar la mayor masa visual del primer tramo.
  - Las métricas deben estar alineadas en una sola lectura rápida.
  - La grilla de maestros debe sentirse compacta pero no apretada.
  - El bloque de cursos necesita aire suficiente para leer título, inicio y copy.
- **Notas de sincronización:**
  - La pantalla debe sentirse como dossier, no como ficha informativa.
  - El cierre con collage debe conservar el carácter más ilustrado del PDF.

#### J6. Lista de assets faltantes o por exportar desde diseño
- Portada editorial específica para `Historia Cómica`.
- Hero/imagen principal definitiva para `Teatro Escuela`.
- QR editorial final para `Teatro Escuela`.
- Imagen principal por obra para `Cartelera detalle`.
- Ilustraciones secundarias de cierre para `Teatro Escuela`.
- Eventuales variantes específicas de portada para cada capítulo de `Historia Cómica`.
- Eventuales recortes o versiones de fondo para el menú principal si el PDF usa una composición distinta a la actual.

#### J7. Criterios para cerrar una pantalla como sincronizada
- La pantalla debe respetar el orden de lectura del PDF.
- La jerarquía visual debe ser reconocible a simple vista.
- No deben quedar placeholders visibles.
- Los assets principales deben corresponder a la referencia o a una equivalencia documentada.
- La pantalla debe verse correcta en 1080x1920 y no romperse en scroll vertical.

### K. Tabla rígida de validación por pantalla

Esta tabla define el estado mínimo esperado para considerar una pantalla alineada con el PDF. Si un ítem no cumple, la pantalla debe permanecer en estado `provisional`.

| Pantalla | Tamaño objetivo | Bloque 1 | Bloque 2 | Bloque 3 | Bloque 4 | Asset principal | Check final |
|---|---|---|---|---|---|---|---|
| `/menu` | 1080x1920 vertical | Topbar | Título central | Grilla de accesos | Ornamento inferior | `public/assets/img/menu/*.webp` | Debe caber sin cortes y conservar la composición del PDF. |
| `/museo/coleccion` | 1080x1920 vertical | Topbar | Título | Bloque Títeres | Bloque Payasos | `public/assets/img/museum/cat_*.webp` | Debe verse como portada editorial, no como lista de textos. |
| `/museo/coleccion/titeres` | 1080x1920 vertical | Topbar | Título | Grid de técnicas | CTA / navegación secundaria | `public/assets/img/museum/cat_coleccion.webp` | Debe sostenerse visualmente como catálogo táctil. |
| `/museo/coleccion/mascaras` | 1080x1920 vertical | Topbar | Título | Acceso 1 | Acceso 2 | `public/assets/img/museum/cat_*.webp` | Debe tener peso visual suficiente para no sentirse vacío. |
| `/museo/coleccion/payasos` | 1080x1920 vertical | Topbar | Título | Narrativa editorial | CTA / enlace a historia | `ausente` | Debe dejar de ser placeholder. |
| `/museo/historia-comica` | 1080x1920 vertical | Topbar | Título | Composición central | Segundo bloque / CTA | `ausente` | Debe reproducir el dramatismo del PDF. |
| `/cartelera/detalle/{slug}` | 1080x1920 vertical | Topbar | Título + tags | Imagen o galería | Ficha lateral + cierre | `ausente` por obra | Debe conservar legibilidad y jerarquía editorial. |
| `/teatro-escuela` | 1080x1920 vertical | Topbar | Hero | Métricas | Cursos + cierre | `public/assets/img/school/school_collage.webp` + ausentes | Debe sentirse como dossier y no como ficha simple. |
| `/museo/el-museo/actualidad` | 1080x1920 vertical | Topbar | Título | Contenido real | Cierre / CTA | `ausente` | No debe quedar en mock visible. |

#### K1. Regla de uso de la tabla
- [ ] Antes de tocar una pantalla, revisar su fila en esta tabla.
- [ ] Si el asset principal figura como `ausente`, resolver primero el asset o aceptar explícitamente un placeholder temporal.
- [ ] Si el check final no puede cumplirse, la pantalla no debe marcarse como final.
- [ ] Si se agregan bloques nuevos al PDF, actualizar la fila correspondiente antes de implementar CSS.
- [ ] Si el tamaño objetivo cambia, verificar de nuevo la captura visual en navegador real.

### L. Plan operativo por pantalla

Esta lista convierte la auditoría visual en pasos accionables. Si se quiere retomar el tótem en una sesión futura, este es el bloque más útil para arrancar sin reabrir toda la investigación.

| Pantalla | Bloque principal a resolver | Archivo principal | Estado actual | Próxima acción concreta |
|---|---|---|---|---|
| `/menu` | Ajuste fino de composición | [app/Views/totem/main_menu.php](/Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/main_menu.php) | cercano | Revisar espaciado, escala y cierre inferior comparando captura contra PDF. |
| `/museo/coleccion` | Pantalla única con scroll (Títeres + Payasos + Máscaras) | [app/Views/totem/collection_main.php](/Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/collection_main.php) | Pendiente rediseño | ⏳ Bloqueado por assets de Coni. Al recibirlos: rediseñar, eliminar subrutas y vistas obsoletas. |
| ~~`/museo/coleccion/titeres`~~ | ~~Técnicas~~ | ~~collection_techniques.php~~ | **OBSOLETA** | Eliminar ruta y vista al implementar rediseño de collection_main. |
| ~~`/museo/coleccion/mascaras`~~ | ~~Tradiciones~~ | ~~collection_masks.php~~ | **OBSOLETA** | Eliminar ruta y vista al implementar rediseño de collection_main. |
| ~~`/museo/coleccion/payasos`~~ | ~~Editorial payasos~~ | ~~collection_clowns.php~~ | **OBSOLETA** | Eliminar ruta y vista al implementar rediseño de collection_main. |
| `/museo/coleccion/fichas/{id}` | Ficha individual de objeto | [app/Views/totem/collection_item_detail.php](/Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/collection_item_detail.php) | Pendiente diseño | ⏳ Bloqueado por PDF corregido de Coni (reunión 4/6). |
| `/museo/historia-comica` | Portada / línea de tiempo | [app/Views/totem/comic_history_main.php](/Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/comic_history_main.php) | lejos | Redibujar la pantalla como pieza editorial fuerte con composición central. |
| `/museo/historia-comica/{slug}` | Detalle de capítulo | vista asociada a controlador | parcial | Alinear el detalle con una estructura de artículo real. |
| `/cartelera/detalle/{slug}` | Afiche editorial | [app/Views/totem/billboard_detail.php](/Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/billboard_detail.php) | parcial | Separar claramente imagen, descripción y ficha lateral. |
| `/teatro-escuela` | Dossier largo | [app/Views/totem/section.php](/Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/section.php) | lejos | Crear vista dedicada o reestructurar sección con hero, métricas, maestros y cursos. |
| `/museo/el-museo/actualidad` | Contenido real | [app/Views/totem/museum_today.php](/Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/museum_today.php) | real / fallback explícito | Reemplazar stub por una composición editorial activa, con respuesta robusta si la API no entrega bloques. |

#### L1. Cómo usar este plan operativo
- [ ] Elegir una sola pantalla por sesión.
- [ ] Comparar primero contra la fila de la tabla rígida.
- [ ] Revisar luego la equivalencia de assets antes de tocar CSS o HTML.
- [ ] Implementar el bloque principal a resolver.
- [ ] Hacer captura en navegador y volver a validar contra el PDF.
- [ ] Sólo marcar como final cuando la fila quede sin observaciones pendientes.

## 🗺️ Mapa de Componentes y Rutas Físicas del Proyecto

Para cualquier desarrollo en este módulo, guíate por este mapa de archivos clave:

*   **Rutas del Tótem:** `app/Config/Routes.php` (declaración de URLs limpias)
*   **Controlador Principal:** `app/Controllers/TotemController.php` (lógica de servidor y fetch de datos)
*   **Vistas de Pantalla:** `app/Views/totem/` (layouts HTML)
*   **Layout Base del Tótem:** `app/Views/layouts/MainLayout.php` (estructura global de HTML/CSS/JS)
*   **Servicio Cliente API:** `app/Services/TotemApiService.php` ✅ Implementado
*   **Estilos CSS compilados:** `public/assets/css/style.css` (generado compilando los parciales en `/src/`)
*   **Hojas de Estilo Parciales:** `public/assets/css/src/` (modificar aquí, y luego correr `composer build:css`)
*   **Lógica de Interacción JS:** `public/assets/js/app.js` (idle timer, cookies, handlers táctiles)
*   **Traducciones (i18n):** `app/Language/{es|en|fr|pt}/Totem.php` (textos, eyebrows, placeholders)

---

## 🌊 OLEADA 1: Demo & Fundación Técnica (Urgente - Pre-reunión 1/6)
> **Objetivo:** Resolver problemas críticos de UX/UI y establecer la infraestructura de comunicación con la API real para contar con un tótem 100% navegable en la demo de software.
> **Estado:** ¡100% Completada! ✅

### 1. UI & UX Correcciones Críticas (Frontend)
- [x] **P1 — Menú principal: 6 items no caben sin scroll**
  *   **Ruta de Implementación:** Modificar [public/assets/css/src/screens/menu.css](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/public/assets/css/src/screens/menu.css)
  *   **Instrucciones:**
      1. Ajustar el grid o flexbox de `.menu-card` para reducir la altura (`height`) de las tarjetas.
      2. Reducir padding interno y achicar el `font-size` de los títulos para que las 6 tarjetas quepan por completo en una pantalla de proporción vertical 1080x1920 (viewport de Full HD vertical).
      3. Ejecutar `composer build:css` para compilar los cambios.
- [x] **P2 — Botón de idioma no muestra el idioma activo**
  *   **Ruta de Implementación:** Modificar [public/assets/js/app.js](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/public/assets/js/app.js)
  *   **Instrucciones:**
      1. En el handler `DOMContentLoaded`, leer la cookie `totem_lang` o en su defecto `localStorage.getItem('totem_lang')` (fallback 'es').
      2. Mapear los valores ('es', 'en', 'fr', 'pt') a etiquetas visibles ('ESP', 'ENG', 'FRA', 'POR').
      3. Seleccionar el contenedor en el DOM (`.pill-button--lang span:last-child`) y actualizar su `textContent` dinámicamente al cargar la página.
- [x] **P3 — Idle timer: sin aviso visual antes del reset**
  *   **Ruta de Implementación:**
      *   Modificar [public/assets/js/app.js](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/public/assets/js/app.js)
      *   Modificar [app/Views/layouts/MainLayout.php](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/layouts/MainLayout.php)
      *   Crear [public/assets/css/src/screens/idle.css](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/public/assets/css/src/screens/idle.css)
  *   **Instrucciones:**
      1. En `MainLayout.php`, agregar el markup HTML para un overlay `#idle-overlay` que empiece oculto.
      2. En `app.js`, crear constantes `IDLE_LIMIT = 120` e `WARN_AT = 105`.
      3. Al llegar a 105 segundos de inactividad, remover la clase oculta del overlay, iniciar un countdown de 15 a 0 mostrando el tiempo restante en pantalla.
      4. Incorporar textos multiidioma traducidos para la advertencia.
      5. Si el usuario presiona "Continuar" u otra parte del DOM, restaurar `idleTime = 0` y ocultar el overlay.
      6. Evitar que el timer corra o redirija cuando la URL sea la raíz (`/` o `/index.php/`).
- [x] **P4 — Traducciones de textos hardcodeados en subpáginas**
  *   **Ruta de Implementación:**
      *   Modificar [app/Language/es/Totem.php](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Language/es/Totem.php) (y directorios `/en/`, `/fr/`, `/pt/`)
      *   Modificar [app/Views/totem/museum_menu.php](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/museum_menu.php)
      *   Modificar [app/Views/totem/billboard.php](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/billboard.php)
      *   Modificar [app/Views/totem/billboard_detail.php](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/billboard_detail.php)
  *   **Instrucciones:**
      1. Extraer los textos e eyebrows estáticos que están en español (e.g. "Recorre la colección viva", "Programación", "Cartelera", "Duración aproximada") y agregarlos a los diccionarios de traducción de cada idioma.
      2. Reemplazar los textos rígidos en las vistas por la función `lang('Totem.clave')`.
 
### 2. Integración con la API Backend
- [x] **Crear Servicio de API del Tótem (`TotemApiService`)**
  *   **Ruta de Implementación:** Crear [app/Services/TotemApiService.php](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Services/TotemApiService.php)
  *   **Instrucciones:**
      1. Definir variables `baseUrl` (tomando `TOTEM_API_URL` del entorno) y `apiKey` (tomando `TOTEM_API_KEY`).
      2. Implementar un método genérico de petición `get(string $path, array $params = [])` usando la librería `CURLRequest` de CodeIgniter.
      3. Añadir de manera predeterminada el header `X-Totem-Key` en las solicitudes.
      4. Establecer un timeout máximo de 5 segundos.
      5. Envolver las llamadas en bloques `try/catch` para capturar fallas de red y retornar arreglos vacíos de forma graceful sin gatillar excepciones PHP al usuario final.
- [x] **Conectar Cartelera a la API Real**
  *   **Ruta de Implementación:** Modificar [app/Controllers/TotemController.php](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Controllers/TotemController.php)
  *   **Instrucciones:**
      1. Instanciar `TotemApiService`.
      2. Modificar el método `billboard()` para invocar a `$apiService->shows()` en lugar de utilizar el arreglo estático.
      3. Validar que la vista renderice dinámicamente las tarjetas según el payload de la API.
- [x] **Conectar Teatro Escuela a la API de Cursos**
  *   **Ruta de Implementación:** Modificar [app/Controllers/TotemController.php](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Controllers/TotemController.php)
  *   **Instrucciones:**
      1. Modificar el método `theaterSchool()` para consumir `$apiService->courses()`.
      2. Reemplazar los datos estáticos de cursos activos por el set dinámico devuelto por la API.

---

## 🌊 OLEADA 2: Cobertura Completa de Contenidos
> **Estado:** Bloques A, B y C completados ✅ — Bloques D, E y F pendientes.
> **Objetivo:** Desarrollar todas las nuevas pantallas y vistas de navegación profunda detalladas en el plano de la v1.0, incorporando los assets definitivos de diseño y las decisiones del equipo.
>
> ⚠️ **Decisión de arquitectura (1/6):** La navegación de Colección fue simplificada. El diseño de Coni unifica Títeres, Payasos y Máscaras en una sola pantalla con scroll (`/museo/coleccion`), desde donde se navega directo a fichas individuales. Las rutas intermedias `/coleccion/titeres`, `/coleccion/mascaras` y `/coleccion/payasos` quedan obsoletas. Las vistas `collection_techniques.php`, `collection_masks.php` y `collection_clowns.php` serán reemplazadas al implementar el rediseño de `collection_main.php`.

### 1. Bloque A: Colección del Museo (Estructura de Navegación)
- [x] **Rutas dinámicas en CodeIgniter 4**
  *   **Ruta de Implementación:** Modificar [app/Config/Routes.php](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Config/Routes.php)
  *   **Instrucciones:** Registrar las nuevas rutas dinámicas para fichas, técnicas y colecciones:
      ```php
      $routes->get('museo/coleccion/titeres', 'TotemController::collectionTechniques');
      $routes->get('museo/coleccion/titeres/(:segment)', 'TotemController::collectionTechnique/$1');
      $routes->get('museo/coleccion/mascaras', 'TotemController::collectionMasks');
      $routes->get('museo/coleccion/mascaras/(:segment)', 'TotemController::collectionMaskTradition/$1');
      $routes->get('museo/coleccion/payasos', 'TotemController::collectionClowns');
      $routes->get('museo/coleccion/fichas/(:num)', 'TotemController::collectionItem/$1');
      ```
- [x] **A1 — Vista Principal de Colección creada** (`/museo/coleccion`) ✅
  *   **Ruta de Implementación:** [app/Views/totem/collection_main.php](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/collection_main.php)
  *   **Estado:** Vista estructurada y funcional. Pendiente de rediseño final con assets de Coni (ver A3).
- [x] **A2 — Rutas y vistas intermedias por tipo creadas** ~~(`/museo/coleccion/titeres`, `/mascaras`, `/payasos`)~~ ⚠️ OBSOLETAS
  *   **Nota:** Estas vistas (`collection_techniques.php`, `collection_masks.php`, `collection_clowns.php`) y sus rutas fueron creadas pero quedan obsoletas por el cambio de arquitectura del 1/6. Serán eliminadas al implementar A3.
- [ ] **A3 — Rediseño de `collection_main` según diseño aprobado de Coni** (`/museo/coleccion`)
  *   **Ruta de Implementación:** [app/Views/totem/collection_main.php](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/collection_main.php)
  *   **Bloqueante:** ⏳ Requiere assets PNG de Coni (ilustraciones decorativas por sección). Pendiente reunión 4/6.
  *   **Instrucciones:**
      1. Rediseñar `collection_main` como pantalla única con scroll: sección Títeres → sección Payasos → sección Máscaras.
      2. Cada sección enlaza directo a `/museo/coleccion/fichas/{id}`.
      3. Eliminar rutas `/coleccion/titeres`, `/coleccion/mascaras`, `/coleccion/payasos` de `Routes.php`.
      4. Eliminar vistas `collection_techniques.php`, `collection_masks.php`, `collection_clowns.php`.
- [ ] **A4 — Ficha Individual de Objeto** (`/museo/coleccion/fichas/{id}`)
  *   **Ruta de Implementación:** [app/Views/totem/collection_item_detail.php](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/collection_item_detail.php)
  *   **Bloqueante:** ⏳ Coni mostró diseño el 27/5 con corrección pendiente (layout muy espaciado). PDF corregido no recibido. Pendiente reunión 4/6.
  *   **Instrucciones cuando llegue el diseño:**
      1. Consumir `GET /api/v1/totem/collection/{id}`.
      2. Implementar layout según PDF corregido de Coni.
      3. No mostrar el campo `contenido` (excluido de la respuesta del tótem).

### 2. Bloque B: Historia Cómica
- [x] **Rutas dinámicas en CodeIgniter 4**
  *   **Ruta de Implementación:** Modificar [app/Config/Routes.php](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Config/Routes.php)
  *   **Instrucciones:** Añadir: `$routes->get('museo/historia-comica/(:segment)', 'TotemController::museumHistoryPost/$1');`
- [x] **B1 — Pantalla Principal de Historia Cómica (Timeline)** (`/museo/historia-comica`)
  *   **Ruta de Implementación:** Crear [app/Views/totem/comic_history_main.php](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/comic_history_main.php)
  *   **Instrucciones:** Consumir `GET /api/v1/totem/museum-history/historia-comica`. Crear un layout interactivo de línea de tiempo vertical. Habilitar scroll exclusivamente en el contenedor del timeline.
- [x] **B2 — Post Editorial Individual** (`/museo/historia-comica/{slug}`)
  *   **Ruta de Implementación:** Crear [app/Views/totem/comic_history_post.php](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/comic_history_post.php)
  *   **Instrucciones:** Consumir `GET /api/v1/totem/museum-history/{slug}` y pintar la información/fotos de manera elegante y legible.

### 3. Bloque C: Explora el Museo
- [x] **Rutas estáticas en CodeIgniter 4**
  *   **Ruta de Implementación:** Modificar [app/Config/Routes.php](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Config/Routes.php)
  *   **Instrucciones:** Registrar las siguientes subpáginas:
      ```php
      $routes->get('museo/el-museo/edificio', 'TotemController::museumBuilding');
      $routes->get('museo/el-museo/institucion', 'TotemController::museumInstitution');
      $routes->get('museo/el-museo/actualidad', 'TotemController::museumToday');
      ```
- [x] **C1 — Sub-menú Explora el Museo** (`/museo/el-museo`)
  *   **Ruta de Implementación:** Crear [app/Views/totem/museum_info_main.php](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/museum_info_main.php)
  *   **Instrucciones:** Menu de 3 grandes opciones táctiles (Edificio, Institución, Actualidad) de acuerdo con los diseños recibidos el 2/6.
- [x] **C2 — Historia del Edificio** (`/museo/el-museo/edificio`)
  *   **Ruta de Implementación:** Crear [app/Views/totem/museum_building.php](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/museum_building.php)
  *   **Instrucciones:** Consumir `GET /api/v1/totem/museum`. Filtrar los datos de la Iglesia San Judas Tadeo y pintar el texto e imágenes.
- [x] **C3 — Historia de la Institución** (`/museo/el-museo/institucion`)
  *   **Ruta de Implementación:** Crear [app/Views/totem/museum_institution.php](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/museum_institution.php)
  *   **Instrucciones:** Consumir `GET /api/v1/totem/museum` y renderizar misión, visión y estructura interna del teatro.
- [x] **C4 — Historia en la Actualidad** (`/museo/el-museo/actualidad`)
  *   **Ruta de Implementación:** Crear [app/Views/totem/museum_today.php](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/museum_today.php)
  *   **Instrucciones:** Consumir `GET /api/v1/totem/museum` y mostrar logros más representativos (e.g. FMIM 2024).

### 4. Bloque D: Teatro Escuela (Rediseño a Dossier)
- [x] **D1 — Rediseño del Layout del Teatro Escuela** (`/teatro-escuela`)
  *   **Ruta de Implementación:** Modificar [app/Views/totem/section.php](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/section.php) (o crear una vista dedicada `theater_school_dossier.php`)
  *   **Instrucciones:**
      1. Integrar sección superior para embed de YouTube (silenciado e interactivo).
      2. Crear bloque con cifras y datos clave del teatro (stats).
      3. Añadir carrusel de tarjetas compactas de Maestros y Alumnos Destacados.
      4. Incluir bloque de cursos dinámicos vinculados a la API.

### 5. Bloque E: Extensión & Formulario de Contacto
- [ ] **E1 — Teclado Táctil e Integración de QR Dinámico** (`/extension`)
  *   **Estado:** La ruta `/extension`, el redirect desde `/visitas-guiadas` y la vista `extension_contact.php` ya existen ✅. Lo pendiente es el teclado táctil y el QR dinámico.
  *   **Ruta de Implementación:** [app/Views/totem/extension_contact.php](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/extension_contact.php)
  *   **Instrucciones:**
      1. Cargar e inicializar la librería `simple-keyboard`. Desplegar teclado virtual en la zona inferior cuando un input obtenga el foco táctil.
      2. Integrar `qrcode.js` para generar un QR dinámico que apunte a la sección de contacto del sitio web con `?utm_source=totem`.

### 6. Bloque F: Lógicas e Interactividades de UX
- [ ] **F1 — Carrusel Táctil en Detalle de Obra**
  *   **Ruta de Implementación:** Modificar [public/assets/js/app.js](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/public/assets/js/app.js)
  *   **Instrucciones:** Desarrollar en vanilla JS un interceptor de deslizamientos (`touchstart`, `touchmove`, `touchend`) para transicionar imágenes en galerías con total fluidez táctil y sin librerías externas que sobrecarguen el kiosk.
- [ ] **F2 — Lazy Loading de YouTube Video Iframes**
  *   **Ruta de Implementación:**
      *   Modificar [app/Views/totem/billboard_detail.php](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/billboard_detail.php)
      *   Modificar [public/assets/js/app.js](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/public/assets/js/app.js)
  *   **Instrucciones:**
      1. Mostrar inicialmente una imagen del thumbnail del video con botón de Play estilizado en alto contraste.
      2. Sólo tras presionar el botón, inyectar el `<iframe>` dinámicamente forzando los parámetros `autoplay=1&mute=1&cc_load_policy=1`.
- [ ] **F3 — Preparación de Contenedores de Ilustraciones**
  *   **Ruta de Implementación:** Modificar parciales CSS en [public/assets/css/src/screens/](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/public/assets/css/src/screens/)
  *   **Instrucciones:** Asegurar que los contenedores CSS con IDs como `#il-payaso` tengan altura y anchura explícitas para evitar saltos bruscos en el layout (*CLS*) cuando se intercambien las imágenes PNG por GIFs o Lotties animados finales.
- [x] **F4 — Ambigüedad del enlace `/historia`** ✅ Resuelta implícitamente
  *   **Estado:** La ruta `/historia` nunca llegó a existir en `Routes.php`. El acceso a Historia está bajo `/museo/historia-comica`. No requiere acción.

---

## 🌊 OLEADA 3: Marcha Blanca & Producción (15/6 en adelante)
> **Objetivo:** Asegurar la estabilidad final del tótem en las pantallas de Fully Kiosk dentro del teatro, optimizar rendimiento y preparar material de contingencia.

### 1. Calibración en Hardware Físico
- [ ] **Pruebas de Usabilidad Táctil e Interfaz**
  *   **Instrucciones:**
      1. Instalar la versión compilada en el hardware vertical real del teatro.
      2. Verificar que el Fully Kiosk Browser bloquee adecuadamente gestos externos del navegador, barras de scroll y gestos de zoom/pinch.
      3. Validar áreas de click/tap que midan al menos 48x48px y ajustar márgenes de tipografía si es difícil leer a la distancia esperada.
- [ ] **Optimización y caché**
  *   **Instrucciones:** Monitorear el tiempo de carga de las imágenes y optimizarlas en formato `.webp` si causan latencia perceptible en la red local.

### 2. Estabilidad Técnica & Fallback Offline
- [ ] **Pruebas de Tolerancia a Fallas de Red**
  *   **Instrucciones:**
      1. Simular la desconexión total a Internet del tótem.
      2. Validar que la interfaz no quede "colgada" ni muestre alertas de error técnico en pantalla.
      3. Asegurar que cargue las últimas variables del caché de forma graceful o presente la pantalla amigable de error multiidioma.
- [ ] **Carga de Assets de Ilustración Definitivos**
  *   **Instrucciones:** Colocar y renombrar todos los GIFs/Lotties JSON enviados por Coni en sus respectivas carpetas y comprobar su óptimo rendimiento en el procesador local del tótem.

### 3. Operación
- [ ] **Creación del Manual de Soporte de Operaciones**
  *   **Instrucciones:** Crear un documento técnico breve en la carpeta del tótem detallando los pasos de encendido/apagado, reinicio rápido, limpieza de caché de Fully Kiosk y reinicio de servidores locales.
