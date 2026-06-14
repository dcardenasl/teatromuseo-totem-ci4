# Roadmap de Implementación — Tótem Interactivo (TASKS.md)

Este archivo sirve como el backlog técnico oficial y guía de desarrollo para implementar la lista completa de mejoras del **Tótem Interactivo** (`teatromuseo-totem-ci4`). Está estructurado en 3 Oleadas de desarrollo para facilitar la entrega continua y el testeo en el hardware Fully Kiosk.

Las tareas ya completadas se van archivando en [TASKS-ARCHIVES.md](/Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/TASKS-ARCHIVES.md). Este archivo queda como backlog vivo y lista de trabajo pendiente.

---

## 🔎 Auditoría visual pendiente contra referencias actualizadas

Esta sección documenta la comparación real entre el tótem en `http://localhost:8086` y las referencias visuales más recientes de Coni, ubicadas en `assets/design-refs/`. Los PDFs de `docs/assets/` quedan como archivo histórico, pero la fuente de verdad visual actual es la carpeta `assets/design-refs/`.

### Cómo leer esta auditoría
- `Referencia visual` = fuente de verdad visual actual.
- `Ruta actual` = pantalla que hoy está sirviendo la app.
- `Brecha` = diferencia concreta entre lo implementado y el diseño.
- `Criterio de aceptación` = qué debe cumplirse para considerar la pantalla sincronizada.

> Nota: esta auditoría conserva el histórico visual del proyecto. Si una pantalla ya quedó implementada en una oleada posterior con `[x]`, esa oleada manda para el estado técnico; la auditoría sólo conserva el gap visual o la deuda de estilo que todavía podría quedar.

### Resumen ejecutivo de pendientes reales

Después del cruce visual y del inventario de assets, las deudas que siguen siendo materialmente relevantes son estas:

- `P1` Exportar o localizar el asset canónico de `assets/design-refs/museo/historia/historia-del-circo.webp`.
- `P1` Exportar las piezas editoriales que faltan para Colección: `ficha-item.webp`, `ficha-tecnica.webp`, `tecnicas.webp`, `ver-todos.webp`, `payasos/ficha.webp`, `payasos/teatro.webp` y `mascaras/tradiciones.webp`.
- `P1` Definir la versión final del afiche de detalle de Cartelera y dejarlo como asset reusable en lugar de depender sólo del fallback actual.
- `P2` Cerrar el dossier de `teatro-escuela` con las ilustraciones secundarias de cierre y validar si hace falta una mejora puntual de composición, no un rediseño completo.
- `P2` Alinear nombres de assets en `/museo/el-museo` para que reflejen el contenido real, aunque hoy la cobertura visual ya existe con equivalencias válidas.

Lo que no aparece aquí ya no debe tratarse como bloqueo de exportación: `inicio`, `menu`, `museo`, `museo/el-museo` y la cartelera base están cubiertos funcionalmente; en esos casos queda deuda de pulido o de nomenclatura, no de pantalla inexistente.

### Fuentes de referencia
- `assets/design-refs/inicio.jpg`
- `assets/design-refs/menu-principal.jpg`
- `assets/design-refs/museo/museo.webp`
- `assets/design-refs/museo/explora-el-museo/landing.webp`
- `assets/design-refs/museo/explora-el-museo/historia.webp`
- `assets/design-refs/museo/explora-el-museo/la-iglesia.webp`
- `assets/design-refs/museo/historia/landing.webp`
- `assets/design-refs/museo/historia/historia-del-circo.webp`
- `assets/design-refs/museo/coleccion/coleccion.webp`
- `assets/design-refs/museo/coleccion/titeres/ficha-item.webp`
- `assets/design-refs/museo/coleccion/titeres/ficha-tecnica.webp`
- `assets/design-refs/museo/coleccion/titeres/tecnicas.webp`
- `assets/design-refs/museo/coleccion/titeres/ver-todos.webp`
- `assets/design-refs/museo/coleccion/mascaras/tradiciones.webp`
- `assets/design-refs/museo/coleccion/payasos/ficha.webp`
- `assets/design-refs/museo/coleccion/payasos/teatro.webp`
- `assets/design-refs/cartelera/cartelera.webp`
- `assets/design-refs/cartelera/cartelera-detalle.png`

### Fuente heredada
- `docs/assets/` conserva los PDFs originales y se usa sólo como respaldo histórico cuando falta una exportación actualizada.

### Matriz de sincronización

| Referencia visual | Ruta actual | Vista principal | Estado | Prioridad |
|---|---|---|---|---|
| `assets/design-refs/menu-principal.jpg` | `/menu` | [app/Views/totem/main_menu.php](/Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/main_menu.php) | Pulido aplicado | Media |
| `assets/design-refs/inicio.jpg` | `/` | [app/Views/totem/main_splash.php](/Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/main_splash.php) | Cubierto | Media |
| `assets/design-refs/museo/museo.webp` | `/museo` | [app/Views/totem/museum_menu.php](/Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/museum_menu.php) | Cubierto | Media |
| `assets/design-refs/museo/explora-el-museo/landing.webp` | `/museo/el-museo` | [app/Views/totem/museum_info_main.php](/Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/museum_info_main.php) | Cubierto funcionalmente | Alta |
| `assets/design-refs/museo/historia/landing.webp` | `/museo/historia` | [app/Views/totem/comic_history_main.php](/Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/comic_history_main.php) | Lejos de la referencia | Alta |
| `assets/design-refs/cartelera/cartelera.webp` | `/cartelera` | [app/Views/totem/billboard.php](/Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/billboard.php) | Cubierto | Media |
| `assets/design-refs/cartelera/cartelera-detalle.png` | `/cartelera/detalle/{slug}` | [app/Views/totem/billboard_detail.php](/Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/billboard_detail.php) | Parcial | Alta |
| `assets/design-refs/museo/coleccion/coleccion.webp` | `/museo/coleccion` | [app/Views/totem/collection_main.php](/Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/collection_main.php) | Pendiente rediseño | Alta |
| `assets/design-refs/museo/coleccion/titeres/ficha-item.webp` | `/museo/coleccion/fichas/{id}` | [app/Views/totem/collection_item_detail.php](/Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/collection_item_detail.php) | Pendiente | Alta |
| `assets/design-refs/museo/coleccion/titeres/tecnicas.webp` | `/museo/coleccion/titeres/tecnicas` | [app/Views/totem/collection_techniques.php](/Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/collection_techniques.php) | Legacy | Media |
| `assets/design-refs/museo/coleccion/mascaras/tradiciones.webp` | `/museo/coleccion/mascaras/tradiciones` | [app/Views/totem/collection_masks_traditions.php](/Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/collection_masks_traditions.php) | Final | Usa `comedia-arte.webp` y `comedia-andes.webp` como piezas correctas. |
| `assets/design-refs/museo/coleccion/payasos/ficha.webp` | `/museo/coleccion/payasos` | [app/Views/totem/collection_clowns.php](/Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/collection_clowns.php) | Legacy | Media |
| `assets/design-refs/museo/explora-el-museo/historia.webp` | `/museo/el-museo/edificio` | [app/Views/totem/museum_building.php](/Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/museum_building.php) | Equivalencia pendiente de nombre | Media |
| `assets/design-refs/museo/explora-el-museo/la-iglesia.webp` | `/museo/el-museo/institucion` | [app/Views/totem/museum_institution.php](/Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/museum_institution.php) | Equivalencia pendiente de nombre | Media |
| `assets/design-refs/museo/historia/historia-del-circo.webp` | `/museo/historia/{slug}` | [app/Views/totem/comic_history_post.php](/Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/comic_history_post.php) | Pendiente exportación equivalente | Alta |
| `assets/design-refs/museo/coleccion/titeres/ver-todos.webp` | `/museo/coleccion/titeres/exhibicion` | [app/Views/totem/collection_puppets_exhibit.php](/Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/collection_puppets_exhibit.php) | Legacy | Baja |
| `assets/design-refs/museo/coleccion/payasos/teatro.webp` | `/museo/coleccion/mascaras/exhibicion` | [app/Views/totem/collection_masks_exhibit.php](/Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/collection_masks_exhibit.php) | Legacy | Baja |

### Criterios globales de sincronización
- Ninguna pantalla referenciada debe quedar con bloques vacíos o placeholders visibles.
- Cada pantalla debe respetar el orden de lectura de la referencia visual: hero, cuerpo principal, CTA y cierre.
- Las imágenes deben sostener el peso visual de la composición; no basta con tener el texto correcto.
- Los textos largos deben estar divididos para lectura táctil, sin scroll confuso ni bloques demasiado densos.
- Las rutas de detalle deben mantener jerarquía de datos consistente, sin mezclar contenido de prueba con contenido editorial.
- Si una pantalla usa mocks temporales, debe marcarse explícitamente como contingencia y no como implementación final.

### A. Menú principal
- Historial completo de esta pantalla archivado en [TASKS-ARCHIVES.md](/Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/TASKS-ARCHIVES.md).

### B. Colección del Museo
> ⚠️ **Cambio de arquitectura (1/6):** `collection_main` es la pantalla principal de Colección. Ahí mismo aparecen Títeres, Payasos y Máscaras con sus botones de navegación inmediatos, y desde cada bloque se entra directo al contenido correspondiente. Las pantallas intermedias `/titeres`, `/mascaras` y `/payasos` siguen existiendo como rutas legacy para compatibilidad, pero no deberían seguir creciendo como flujo principal.

- [ ] **B1 — Rediseñar `collection_main` según la referencia visual actual de Coni** (`/museo/coleccion`)
  * **Referencia visual:** `assets/design-refs/museo/coleccion/coleccion.webp`
  * **Ruta actual:** `/museo/coleccion`
  * **Archivos a tocar:** [app/Views/totem/collection_main.php](/Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/collection_main.php)
  * **Bloqueante:** ⏳ Assets de Coni pendientes o por alinear con la nomenclatura final.
  * **Criterio de aceptación:**
      1. Una sola pantalla concentra Títeres, Payasos y Máscaras con sus botones de navegación visibles.
      2. Cada bloque enlaza directo a su destino final, sin pantalla intermedia.
      3. Cada sección tiene ilustraciones decorativas de Coni (PNG placeholder → swap final).
      4. Las rutas y vistas obsoletas están eliminadas.
- [ ] **B2 — Implementar ficha individual de objeto** (`/museo/coleccion/fichas/{id}`)
  * **Referencia visual:** `assets/design-refs/museo/coleccion/titeres/ficha-item.webp`
  * **Ruta actual:** `/museo/coleccion/fichas/{id}`
  * **Archivos a tocar:** [app/Views/totem/collection_item_detail.php](/Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/collection_item_detail.php)
  * **Bloqueante:** ⏳ Falta exportación o ajuste final del layout para el asset canónico.
  * **Criterio de aceptación:**
      1. La imagen principal debe ser protagonista.
      2. Metadatos claramente separados del cuerpo editorial.
      3. No mostrar el campo `contenido` (excluido de la API del tótem).
      4. Usable sin scroll caótico.

### C. Historia
- [ ] **C1 — Rediseñar `/museo/historia` para que coincida con la referencia visual actual**
  * **Referencia visual:** `assets/design-refs/museo/historia/landing.webp`
  * **Ruta actual:** `/museo/historia`
  * **Archivos a tocar:** [app/Views/totem/comic_history_main.php](/Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/comic_history_main.php)
  * **Brecha visual:** La vista actual se reduce a capítulos genéricos. La referencia pide una portada mucho más dramática, con ilustración central, dos bloques de CTA y una composición narrativa clara.
  * **Criterio de aceptación:**
      1. El encabezado debe tener el peso visual de la referencia.
      2. Debe existir una imagen central protagonista.
      3. Los CTA de navegación deben aparecer como bloques fuertes y no como texto suelto.
      4. El orden de lectura debe ser inmediato y jerarquizado.
- [ ] **C2 — Reemplazar los posts genéricos por contenido editorial real**
  * **Referencia visual:** `assets/design-refs/museo/historia/historia-del-circo.webp`
  * **Ruta actual:** `/museo/historia/{slug}` y alias legacy `/museo/historia-comica/{slug}`
  * **Archivos a tocar:** [app/Controllers/TotemController.php](/Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Controllers/TotemController.php), la vista de detalle asociada
  * **Brecha visual:** El flujo de detalle todavía necesita una capa editorial consistente. No basta con un título y un cuerpo mínimo.
  * **Criterio de aceptación:**
      1. El detalle debe tener una estructura reconocible como artículo o capítulo.
      2. El contenido no debe parecer un placeholder de desarrollo.
      3. Debe ser posible leerlo sin fricción visual.

### D. Cartelera
- [ ] **D1 — Ajustar `/cartelera/detalle/{slug}` al layout editorial de referencia**
  * **Referencia visual:** `assets/design-refs/cartelera/cartelera-detalle.png`
  * **Ruta actual:** `/cartelera/detalle/{slug}`
  * **Archivos a tocar:** [app/Views/totem/billboard_detail.php](/Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/billboard_detail.php), [public/assets/css/src/screens/billboard.css](/Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/public/assets/css/src/screens/billboard.css)
  * **Brecha visual:** La información existe, pero la composición no reproduce la referencia. La maqueta reparte el peso entre imagen, texto descriptivo y fichas de datos; la app actual carga una tarjeta demasiado centrada y simplificada.
  * **Criterio de aceptación:**
      1. Debe recuperarse la sensación editorial de afiche detallado.
      2. La imagen principal debe encajar mejor con la columna de información.
      3. La jerarquía entre título, tags, cuerpo y ficha lateral debe ser evidente.
      4. Los controles de navegación de imagen deben integrarse con el layout, no flotar como un añadido aislado.
- [ ] **D2 — Validar que el contenido de cartelera no dependa solo de mocks**
  * **Referencia visual:** `assets/design-refs/cartelera/cartelera.webp`
  * **Ruta actual:** `/cartelera/detalle/{slug}`
  * **Archivos a tocar:** [app/Controllers/TotemController.php](/Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Controllers/TotemController.php)
  * **Brecha visual:** Parte del contenido aún proviene de datos estáticos de respaldo. Hay que confirmar que la pantalla use el contenido correcto del backend o, si no existe, que el mock quede claramente delimitado.
  * **Criterio de aceptación:**
      1. El detalle debe resolver título, tags, texto, horarios y precio de forma consistente.
      2. No deben aparecer textos de prueba en la salida final.
      3. Si se usa fallback temporal, debe ser visualmente equivalente a la referencia.

### E. Teatro Escuela
- [ ] **E1 — Rediseñar `/teatro-escuela` como dossier largo, no como sección simple**
  * **Referencia visual:** `assets/design-refs/museo/museo.webp` y los assets locales de escuela / teatro
  * **Ruta actual:** `/teatro-escuela`
  * **Archivos a tocar:** [app/Views/totem/section.php](/Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/section.php), o una vista dedicada tipo `theater_school_dossier.php`
  * **Brecha visual:** La composición construye un dossier vertical largo con hero, métricas, maestros, cursos, QR y un cierre ilustrado. La versión actual ya cubre la estructura base, pero todavía puede requerir cierre ornamental y una revisión fina de densidad visual.
  * **Criterio de aceptación:**
      1. Debe existir una zona hero fuerte en la parte superior.
      2. Las cifras principales deben leerse de inmediato.
      3. La sección de maestros debe sentirse como un carrusel o grilla curada, no como lista de prueba.
      4. El bloque de cursos debe parecer una pieza editorial real y no una simple tarjeta informativa.
      5. El cierre debe incluir la composición ilustrada final y el contacto/QR en una ubicación coherente con la referencia, sin asumir que haga falta redibujar toda la pantalla.

### F. Pantallas de museo que siguen en modo placeholder
- Historial completo de esta pantalla archivado en [TASKS-ARCHIVES.md](/Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/TASKS-ARCHIVES.md).

### G. Reglas de sincronización entre referencia y código
- G1 y G2 quedaron archivadas en [TASKS-ARCHIVES.md](/Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/TASKS-ARCHIVES.md). La tabla de equivalencia y los criterios visuales viven en las secciones H, I, J, K y L de este mismo documento.
- [ ] **G3 — Marcar explícitamente las pantallas con contenido mock**
  * **Ruta de Implementación:** Este backlog y, si aplica, comentarios en las vistas o controladores.
  * **Qué debe incluir:** etiqueta visible de contingencia en desarrollo interno y nota en backlog de qué contenido falta reemplazar.
  * **Objetivo:** Que nadie asuma que un placeholder es una pantalla final.

### H. Tabla base de equivalencia de assets

Esta tabla debe mantenerse actualizada cuando se cambie cualquier imagen, ilustración o bloque visual relevante. Si un asset de referencia no existe aún en `public/assets/img/`, debe marcarse como `ausente` y tratarse como pendiente de diseño/importación.

| Referencia visual | Asset real actual | Pantalla | Estado | Observación |
|---|---|---|---|---|
| Menú principal - Museo | `public/assets/img/menu/menu_museo.webp` | `/menu` | final | Coincide bien con la tarjeta de entrada a Museo. |
| Menú principal - Teatro Escuela | `public/assets/img/menu/menu_escuela.webp` | `/menu` | final | Coincide con la tarjeta de escuela. |
| Menú principal - Programación | `public/assets/img/menu/menu_programacion.webp` | `/menu` | final | Coincide con la tarjeta de cartelera. |
| Menú principal - Visitas Guiadas | `public/assets/img/menu/menu_visitas.webp` | `/menu` | final | Coincide con la tarjeta de visitas. |
| Menú principal - Amigos de Teatromuseo | `public/assets/img/menu/menu_amigos.webp` | `/menu` | final | Coincide con la tarjeta de amigos. |
| Menú principal - Collage inferior de edificio / ornamento | `public/assets/img/menu/collage_referencia.webp` | `/menu` | final | Asset específico del menú; coincide con la referencia visual del cierre inferior. |
| Colección - Títeres (imagen principal) | `public/assets/img/museum/coleccion-card.webp` | `/museo/coleccion` | final | Es el asset principal visible para la sección de colección. |
| Colección - Historia | `public/assets/img/museum/historia-card.webp` | `/museo/coleccion` y `/museo/historia` | final | Coincide con el bloque de historia. |
| Colección - Explora el museo | `public/assets/img/museum/explora-el-museo-card.webp` | `/museo/coleccion` y `/museo/el-museo` | final | Coincide con el bloque del museo. |
| Colección - Visitas Guiadas | `public/assets/img/museum/visitas-guiadas-card.webp` | `/museo/coleccion` y `/visitas-guiadas` | final | Coincide con el bloque de visitas. |
| Teatro Escuela - Collage de cierre | `public/assets/img/school/school_collage.webp` | `/teatro-escuela` | parcial | Sirve como base del collage final; faltan ornamentos/ajustes de cierre para cerrar la densidad visual. |
| Cartelera detalle - Imagen principal de obra | `public/assets/img/menu/menu_programacion.webp` | `/cartelera/detalle/{slug}` | provisional | Hoy se usa como placeholder; debe reemplazarse por la imagen real de cada obra. |
| Cartelera detalle - Flechas de galería | `public/assets/img/ui/slider_left.webp` y `public/assets/img/ui/slider_right.webp` | `/cartelera/detalle/{slug}` | final | Cumplen la función visual esperada de navegación lateral. |
| Cartelera detalle - Icono duración | `public/assets/img/ui/icon_duration.webp` | `/cartelera/detalle/{slug}` | final | Coincide con el bloque de duración. |
| Cartelera detalle - Icono ticket | `public/assets/img/ui/icon_ticket.webp` | `/cartelera/detalle/{slug}` | final | Coincide con el bloque de precio/entrada. |
| Fondo / textura papel | `public/assets/img/ui/texture.png` | Todas las pantallas | final | Es la base visual del sistema. |
| Portada de Historia | `assets/design-refs/museo/historia/landing.webp` | `/museo/historia` | provisional | La composición existe en diseño, pero aún falta alinearla del todo con la implementación actual. |
| Hero / escena principal de Teatro Escuela | `public/assets/img/school/school_collage.webp` | `/teatro-escuela` | parcial | Sostiene el hero principal; la deuda real está en el cierre ilustrado, no en la ausencia del bloque base. |
| QR editorial del dossier de Escuela | `public/assets/img/school/teatroescuela-qr.png` | `/teatro-escuela` | final | QR real generado para el enlace público de Teatro Escuela con `?utm_source=totem`. |
| Afiche o foto principal de cartelera por obra | `ausente` | `/cartelera/detalle/{slug}` | ausente | Cada obra debería tener su propia imagen editorial y no reciclar un placeholder genérico. |
| Ilustraciones secundarias de cierre en Escuela | `ausente` | `/teatro-escuela` | ausente | Faltan varios ornamentos finales de la referencia para reproducir la misma densidad visual. |

### I. Checklist de actualización de assets

- [ ] Cuando entre una nueva referencia visual de diseño, registrar su nombre exacto en esta sección.
- [ ] Si aparece una imagen nueva en la referencia, crear la fila correspondiente antes de implementarla.
- [ ] Si una pantalla usa un asset provisional, marcarlo como `provisional` hasta reemplazarlo.
- [ ] Si una pantalla carece del asset necesario, registrar el elemento como `ausente` y no asumir equivalencias.
- [ ] Al cambiar un asset, verificar de nuevo la captura visual de la ruta asociada.

### J. Guía aproximada de medidas y orden de bloques

Esta guía no pretende reemplazar a la referencia visual como fuente de diseño. Sirve para reducir ambigüedad en la implementación y para que cada pantalla conserve el mismo orden de lectura, densidad y respiración visual que la referencia.

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
  - La referencia funciona como catálogo curado, no como lista.
  - Los bloques deben respirarse con márgenes amplios entre sí.
  - No dejar tarjetas planas sin protagonismo visual.

#### J3. Historia
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
  - El cierre con collage debe conservar el carácter más ilustrado de la referencia.

#### J6. Lista de assets faltantes o por exportar desde diseño
- Portada editorial específica para `Historia`.
- Imagen principal por obra para `Cartelera detalle`.
- Ilustraciones secundarias de cierre para `Teatro Escuela`.
- Eventuales variantes específicas de portada para cada capítulo de `Historia`.
- Eventuales recortes o versiones de fondo para el menú principal si la referencia usa una composición distinta a la actual.

#### J7. Criterios para cerrar una pantalla como sincronizada
- La pantalla debe respetar el orden de lectura de la referencia visual.
- La jerarquía visual debe ser reconocible a simple vista.
- No deben quedar placeholders visibles.
- Los assets principales deben corresponder a la referencia o a una equivalencia documentada.
- La pantalla debe verse correcta en 1080x1920 y no romperse en scroll vertical.

### K. Tabla rígida de validación por pantalla

Esta tabla define el estado mínimo esperado para considerar una pantalla alineada con la referencia visual. Si un ítem no cumple, la pantalla debe permanecer en estado `provisional`.

| Pantalla | Tamaño objetivo | Bloque 1 | Bloque 2 | Bloque 3 | Bloque 4 | Asset principal | Check final |
|---|---|---|---|---|---|---|---|
| `/menu` | 1080x1920 vertical | Topbar | Título central | Grilla de accesos | Ornamento inferior | `public/assets/img/menu/*.webp` | Debe caber sin cortes y conservar la composición de la referencia. |
| `/museo/coleccion` | 1080x1920 vertical | Topbar | Título | Bloque Títeres | Bloque Payasos | `public/assets/img/museum/*.webp` | Debe incluir los botones de navegación inmediatos dentro de cada bloque y no depender de pantallas intermedias. |
| `/museo/coleccion/titeres` | 1080x1920 vertical | Legacy | Legacy | Legacy | Legacy | `public/assets/img/museum/coleccion-card.webp` | Ruta mantenida para compatibilidad, pero no es el flujo principal. |
| `/museo/coleccion/mascaras` | 1080x1920 vertical | Legacy | Legacy | Legacy | Legacy | `public/assets/img/museum/*.webp` | Ruta mantenida para compatibilidad, pero no es el flujo principal. |
| `/museo/coleccion/payasos` | 1080x1920 vertical | Legacy | Legacy | Legacy | Legacy | `public/assets/img/museum/*.webp` | Ruta mantenida para compatibilidad, pero no es el flujo principal. |
| `/museo/historia` | 1080x1920 vertical | Topbar | Título | Composición central | Segundo bloque / CTA | `assets/design-refs/museo/historia/landing.webp` | Debe reproducir el dramatismo de la referencia actual. |
| `/museo/historia-comica` | 1080x1920 vertical | Topbar | Título | Composición central | Segundo bloque / CTA | `assets/design-refs/museo/historia/landing.webp` | Alias heredado para QR/deep links. |
| `/cartelera/detalle/{slug}` | 1080x1920 vertical | Topbar | Título + tags | Imagen o galería | Ficha lateral + cierre | `assets/design-refs/cartelera/cartelera-detalle.png` | Debe conservar legibilidad y jerarquía editorial. |
| `/teatro-escuela` | 1080x1920 vertical | Topbar | Hero | Métricas | Cursos + cierre | `public/assets/img/school/school_collage.webp` | Debe sentirse como dossier y no como ficha simple; faltan ornamentos/cierre fino para igualar la referencia. |
| `/museo/el-museo/actualidad` | 1080x1920 vertical | Topbar | Título | Contenido real | Cierre / CTA | `assets/design-refs/museo/museo.webp` | No debe quedar en mock visible. |

#### K1. Regla de uso de la tabla
- [ ] Antes de tocar una pantalla, revisar su fila en esta tabla.
- [ ] Si el asset principal figura como `ausente`, resolver primero el asset o aceptar explícitamente un placeholder temporal.
- [ ] Si el check final no puede cumplirse, la pantalla no debe marcarse como final.
- [ ] Si se agregan bloques nuevos a la referencia, actualizar la fila correspondiente antes de implementar CSS.
- [ ] Si el tamaño objetivo cambia, verificar de nuevo la captura visual en navegador real.

### L. Plan operativo por pantalla

Esta lista convierte la auditoría visual en pasos accionables. Si se quiere retomar el tótem en una sesión futura, este es el bloque más útil para arrancar sin reabrir toda la investigación.

| Pantalla | Bloque principal a resolver | Archivo principal | Estado actual | Próxima acción concreta |
|---|---|---|---|---|
| `/menu` | Ajuste fino de composición | [app/Views/totem/main_menu.php](/Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/main_menu.php) | cercano | Revisar espaciado, escala y cierre inferior comparando captura contra la referencia actual. |
| `/museo/coleccion` | Pantalla única con navegación directa a Títeres / Payasos / Máscaras | [app/Views/totem/collection_main.php](/Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/collection_main.php) | Pendiente rediseño | ⏳ Bloqueado por assets de Coni. Al recibirlos: rediseñar, eliminar subrutas y vistas obsoletas. |
| ~~`/museo/coleccion/titeres`~~ | ~~Técnicas~~ | ~~collection_techniques.php~~ | **OBSOLETA** | Eliminar ruta y vista al implementar rediseño de collection_main. |
| ~~`/museo/coleccion/mascaras`~~ | ~~Tradiciones~~ | ~~collection_masks.php~~ | **OBSOLETA** | Eliminar ruta y vista al implementar rediseño de collection_main. |
| ~~`/museo/coleccion/payasos`~~ | ~~Editorial payasos~~ | ~~collection_clowns.php~~ | **OBSOLETA** | Eliminar ruta y vista al implementar rediseño de collection_main. |
| `/museo/coleccion/fichas/{id}` | Ficha individual de objeto | [app/Views/totem/collection_item_detail.php](/Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/collection_item_detail.php) | Pendiente diseño | ⏳ Falta cerrar el asset y el layout final. |
| `/museo/historia` | Portada / línea de tiempo | [app/Views/totem/comic_history_main.php](/Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/comic_history_main.php) | lejos | Redibujar la pantalla como pieza editorial fuerte con composición central. |
| `/museo/historia-comica` | Alias legacy | [app/Views/totem/comic_history_main.php](/Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/comic_history_main.php) | compatibilidad | Mantener sólo para QR/deep links heredados. |
| `/museo/historia/{slug}` | Detalle de capítulo | vista asociada a controlador | parcial | Alinear el detalle con una estructura de artículo real. |
| `/museo/historia-comica/{slug}` | Alias legacy | vista asociada a controlador | compatibilidad | Mantener sólo para QR/deep links heredados. |
| `/cartelera/detalle/{slug}` | Afiche editorial | [app/Views/totem/billboard_detail.php](/Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/billboard_detail.php) | parcial | Separar claramente imagen, descripción y ficha lateral. |
| `/teatro-escuela` | Dossier largo | [app/Views/totem/section.php](/Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/section.php) | parcial | Mantener el dossier base y cerrar sólo ornamentos/ajustes finos de composición, si el repaso visual los confirma. |
| `/museo/el-museo/actualidad` | Contenido real | [app/Views/totem/museum_today.php](/Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/museum_today.php) | real / fallback explícito | Reemplazar stub por una composición editorial activa, con respuesta robusta si la API no entrega bloques. |

#### L1. Cómo usar este plan operativo
- [ ] Elegir una sola pantalla por sesión.
- [ ] Comparar primero contra la fila de la tabla rígida.
- [ ] Revisar luego la equivalencia de assets antes de tocar CSS o HTML.
- [ ] Implementar el bloque principal a resolver.
- [ ] Hacer captura en navegador y volver a validar contra la referencia.
- [ ] Sólo marcar como final cuando la fila quede sin observaciones pendientes.

### M. Backlog priorizado derivado del audit de Coni

> Orden de trabajo:
> - `P1` = exportar assets faltantes que bloquean la fidelidad visual.
> - `P2` = renombrar y reutilizar assets ya existentes para cerrar deuda de coherencia.
> - `P3` = rediseñar pantallas que todavía no alcanzan la estructura del diseño.

#### M1. Assets que hay que exportar sí o sí
- [ ] Exportar cada referencia faltante como asset final `.webp`, con nombres representativos y sin sufijos genéricos.
- [ ] Exportar `assets/design-refs/museo/historia/historia-del-circo.webp` para dejar `/museo/historia` con una pieza visual canónica.
- [ ] Exportar `assets/design-refs/museo/coleccion/titeres/ficha-item.webp` para la ficha individual de objetos.
- [ ] Exportar `assets/design-refs/museo/coleccion/titeres/ficha-tecnica.webp` para el bloque técnico de títeres.
- [ ] Exportar `assets/design-refs/museo/coleccion/titeres/tecnicas.webp` para la pantalla de técnicas fijas.
- [ ] Exportar `assets/design-refs/museo/coleccion/titeres/ver-todos.webp` para el CTA de navegación total.
- [ ] Exportar `assets/design-refs/museo/coleccion/payasos/ficha.webp` para la entrada editorial de payasos.
- [ ] Exportar `assets/design-refs/museo/coleccion/payasos/teatro.webp` para el bloque editorial de payasos.
- [x] Exportar `assets/design-refs/museo/coleccion/mascaras/tradiciones.webp` para la pantalla de tradiciones.
- [ ] Exportar `assets/design-refs/cartelera/cartelera-detalle.png` como equivalente final en `.webp` para `/cartelera/detalle/{slug}`.
- [ ] Si Coni no entrega una exportación específica para la vista de `actualidad`, conservar `public/assets/img/museo/el-museo/collage-historia-actual.webp` como fallback documentado en lugar de tratarla como bloqueo.

#### M2. Assets que hay que renombrar y reutilizar
- [ ] Renombrar `public/assets/img/museo/el-museo/collage-nuestra-historia.webp` a un nombre representativo del contenido real y reutilizarlo en `/museo/el-museo/edificio`.
- [ ] Renombrar `public/assets/img/museo/el-museo/collage-san-judas.webp` a un nombre representativo de la Iglesia y reutilizarlo en `/museo/el-museo/institucion`.
- [ ] Reutilizar `public/assets/img/museo/el-museo/explora-el-museo.webp` como portada real de `/museo/el-museo` si esa composición sigue siendo la correcta.
- [ ] Reutilizar `public/assets/img/museo/historia/historia-editorial.webp` como portada o bloque de entrada fuerte donde historia necesite peso editorial.
- [x] Reutilizar `public/assets/img/museo/coleccion/titeres/titere.webp`, `public/assets/img/museo/coleccion/payasos/payaso.webp`, `public/assets/img/museo/coleccion/mascaras/mascara.webp`, `public/assets/img/museo/coleccion/mascaras/comedia-arte.webp` y `public/assets/img/museo/coleccion/mascaras/comedia-andes.webp` antes de duplicar nuevas piezas.
- [ ] Aplicar la regla de nombres `lowercase-kebab-case.webp`, evitando `landing`, `main`, `v1`, `temp` y nombres genéricos.

#### M3. Pantallas que todavía necesitan rediseño estructural
- [ ] Rediseñar `/museo/historia` para que deje de leerse como una vista más textual y pase a una composición editorial con hero dominante y jerarquía clara.
- [ ] Rediseñar `/museo/coleccion` para que la navegación de colección quede apoyada en las 12 técnicas fijas y en accesos directos coherentes.
- [ ] Rediseñar `/museo/coleccion/fichas/{id}` para que tenga una ficha individual real, no una extensión de listado.
- [ ] Revisar `/museo/coleccion/payasos` y `/museo/coleccion/mascaras` para que, si siguen existiendo, tengan entrada editorial propia y no solo texto estructurado.
- [ ] Rediseñar `/cartelera/detalle/{slug}` como afiche editorial completo, con imagen, ficha, tags y cierre bien jerarquizados.
- [ ] Revisar `/teatro-escuela` como dossier vertical completo y cerrar sólo los ornamentos o ajustes visuales que el repaso final confirme como pendientes.

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
> Histórico completado y archivado en [TASKS-ARCHIVES.md](/Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/TASKS-ARCHIVES.md).

---

## 🌊 OLEADA 2: Cobertura Completa de Contenidos
> **Estado:** Bloques B, C y D archivados ✅ — Bloque A pendiente (A3/A4), bloque G parcialmente pendiente (G3) y bloques E/F pendientes.
> **Objetivo:** Desarrollar las pantallas y vistas de navegación profunda detalladas en el plano de la v1.0, incorporando los assets definitivos de diseño y las decisiones del equipo.
>
> ⚠️ **Decisión de arquitectura (1/6):** La navegación de Colección quedó concentrada en una sola pantalla (`/museo/coleccion`). Ahí se muestran Títeres, Payasos y Máscaras con sus botones de navegación inmediatos; desde cada bloque se salta directo al contenido final. Las rutas intermedias `/coleccion/titeres`, `/coleccion/mascaras` y `/coleccion/payasos` quedaron obsoletas, al igual que las vistas `collection_techniques.php`, `collection_masks.php` y `collection_clowns.php`.

### 1. Bloque A: Colección del Museo (Estructura de Navegación)
- Historial completo de los hitos ya cerrados de esta sección archivado en [TASKS-ARCHIVES.md](/Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/TASKS-ARCHIVES.md).
- [ ] **A3 — Rediseño de `collection_main` según diseño aprobado de Coni** (`/museo/coleccion`)
  *   **Ruta de Implementación:** [app/Views/totem/collection_main.php](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/collection_main.php)
  *   **Bloqueante:** ⏳ Faltan assets definitivos de Coni exportados en `.webp` y con nombres representativos para cada bloque.
  *   **Instrucciones:**
      1. Rediseñar `collection_main` como pantalla única que contiene Títeres, Payasos y Máscaras con navegación directa desde cada bloque.
      2. Cada bloque enlaza directo a su destino final, sin pantalla intermedia.
      3. Eliminar rutas `/coleccion/titeres`, `/coleccion/mascaras`, `/coleccion/payasos` de `Routes.php`.
      4. Eliminar vistas `collection_techniques.php`, `collection_masks.php`, `collection_clowns.php`.
- [ ] **A4 — Ficha Individual de Objeto** (`/museo/coleccion/fichas/{id}`)
  *   **Ruta de Implementación:** [app/Views/totem/collection_item_detail.php](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/collection_item_detail.php)
  *   **Bloqueante:** ⏳ Falta la ficha canónica con su asset definitivo en `.webp` y el layout final corregido.
  *   **Instrucciones cuando llegue el diseño:**
      1. Consumir `GET /api/v1/totem/collection/{id}`.
      2. Implementar layout según la referencia corregida de Coni.
      3. No mostrar el campo `contenido` (excluido de la respuesta del tótem).

### 2. Bloque B: Historia
- Historial completo de este bloque archivado en [TASKS-ARCHIVES.md](/Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/TASKS-ARCHIVES.md).

### 3. Bloque C: Explora el Museo
- Historial completo de este bloque archivado en [TASKS-ARCHIVES.md](/Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/TASKS-ARCHIVES.md).

### 4. Bloque D: Teatro Escuela (Rediseño a Dossier)
- Historial completo de este bloque archivado en [TASKS-ARCHIVES.md](/Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/TASKS-ARCHIVES.md).

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
- Historial de la ambigüedad del enlace `/historia` archivado en [TASKS-ARCHIVES.md](/Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/TASKS-ARCHIVES.md).

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
