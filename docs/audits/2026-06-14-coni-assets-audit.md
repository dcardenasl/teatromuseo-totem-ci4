# Audit: Coni screen proposals vs live assets

## Objective

Revisar en profundidad las propuestas de pantallas entregadas por Coni en `assets/design-refs/` y compararlas con las pantallas que hoy están funcionando en el tótem, para detectar:

- assets que faltan por exportar,
- assets que ya existen pero todavía no se usan,
- assets que están mal nombrados o duplicados,
- pantallas que tienen cobertura visual parcial.

## Environment

- Repo: `teatromuseo-totem-ci4`
- Fecha: `2026-06-14`
- Pantallas analizadas: `/menu`, `/museo`, `/museo/el-museo`, `/museo/historia`, `/museo/coleccion`, `/cartelera`, `/cartelera/detalle/{slug}`, `/teatro-escuela`
- Nuevos refs incorporados: `assets/design-refs/inicio.jpg`, `assets/design-refs/menu-principal.jpg`

## Process Log

1. Se inventariaron las referencias visuales de Coni en `assets/design-refs/`.
2. Se inventariaron las vistas activas en `app/Views/totem/` y los assets físicos en `public/assets/img/`.
3. Se compararon los nombres de archivo usados por controladores y vistas contra las referencias de diseño.
4. Se inspeccionaron visualmente los archivos clave para distinguir equivalencias reales de simples nombres parecidos.

## Findings

### 1) `/menu`

Referencia de diseño: `assets/design-refs/menu-principal.jpg`

Estado: cubierto.

- Usa `public/assets/img/menu/menu_museo.webp`, `menu_escuela.webp`, `menu_programacion.webp`, `menu_visitas.webp`, `menu_amigos.webp` y `menu/collage_referencia.webp`.
- La composición general sí coincide con la propuesta de Coni: título central, cinco accesos y cierre ornamental inferior.
- `collage_referencia.webp` sigue siendo el elemento menos específico, pero ya funciona como equivalencia aceptable del cierre visual.

### 1.1) `/` splash / inicio

Referencia de diseño: `assets/design-refs/inicio.jpg`

Estado: cubierto.

- La pantalla actual `public/assets/img/splash/collage-inicio.webp` reproduce la lógica de la propuesta: título `DESCUBRE TEATROMUSEO`, botón central y composición ornamental inferior.
- No veo un asset faltante crítico para esta vista.
- El sistema ya está razonablemente alineado con la referencia.

### 2) `/museo`

Referencia de diseño: `assets/design-refs/museo/museo.webp`

Estado: mayormente cubierto, con un card incorrecto en `Colección`.

- La grilla principal ya quedó alineada con los cuatro accesos esperados: colección, el museo, historia y visitas.
- Los assets actuales `public/assets/img/museum/coleccion-card.webp`, `explora-el-museo-card.webp`, `historia-card.webp` y `visitas-guiadas-card.webp` cubren la pantalla base en términos de layout.
- Sin embargo, el primer card de `Colección` no está usando la composición editorial de Coni; hoy se renderiza con una pieza simplificada tipo marioneta sola.
- Eso lo vuelve visualmente distinto de la referencia de `Colección` dentro del módulo Museo.

### 3) `/museo/el-museo`

Referencia de diseño: `assets/design-refs/museo/explora-el-museo/landing.webp`

Estado: cubierto funcionalmente, pero con deuda de naming y reutilización.

- La página está montada con tres accesos internos: historia de Teatromuseo, historia de la Iglesia y actualidad.
- Existen assets equivalentes en `public/assets/img/museo/el-museo/`:
  - `collage-nuestra-historia.webp`
  - `collage-san-judas.webp`
  - `collage-historia-actual.webp`
  - `explora-el-museo.webp`
- El problema principal no es ausencia total, sino desalineación entre nombres, destino y uso real.
- `explora-el-museo.webp` está disponible pero no se usa en la pantalla principal del módulo.

### 4) `/museo/el-museo/edificio`

Referencia de diseño: `assets/design-refs/museo/explora-el-museo/historia.webp`

Estado: equivalente visual presente, nombre no alineado.

- El sistema usa `public/assets/img/museo/el-museo/collage-nuestra-historia.webp`.
- No existe un archivo con el nombre de referencia `historia.webp` en `public/assets/img/museo/el-museo/`.
- Conclusión: la pieza existe, pero está exportada bajo un nombre distinto, lo que dificulta rastreo y mantenimiento.

### 5) `/museo/el-museo/institucion`

Referencia de diseño: `assets/design-refs/museo/explora-el-museo/la-iglesia.webp`

Estado: equivalente visual presente, nombre no alineado.

- El sistema usa `public/assets/img/museo/el-museo/collage-san-judas.webp`.
- No existe un archivo con el nombre de referencia `la-iglesia.webp`.
- Igual que en la pantalla anterior: la pieza visual está, pero el nombre no representa la referencia de Coni.

### 6) `/museo/el-museo/actualidad`

Referencia de diseño: no encontré una exportación directa con ese nombre en `assets/design-refs/`.

Estado: cubierto con asset propio, pero sin coincidencia explícita de diseño.

- Usa `public/assets/img/museo/el-museo/collage-historia-actual.webp`.
- No encontré en `assets/design-refs/` un archivo claramente equivalente con ese mismo contenido y nombre.
- Si Coni tiene una exportación específica para esta sección, todavía no está integrada como asset dedicado.

### 7) `/museo/historia` y alias `/museo/historia-comica`

Referencias de diseño:

- `assets/design-refs/museo/historia/landing.webp`
- `assets/design-refs/museo/historia/historia-del-circo.webp`

Estado: parcial.

- La pantalla actual funciona, pero es más textual y menos editorial que la referencia.
- En `public/assets/img/museo/historia/` existen:
  - `collage-circo.webp`
  - `collage-teatro.webp`
  - `historia-editorial.webp`
- Falta una exportación local explícita equivalente a `historia-del-circo.webp`.
- `historia-editorial.webp` está presente pero no está conectado a la pantalla de historia; hoy es más bien una pieza suelta que sirve como portada del acceso en el módulo `/museo`.

### 8) `/museo/coleccion`

Referencia de diseño: `assets/design-refs/museo/coleccion/coleccion.webp`

Estado: bien encaminado, pero faltan varias piezas de detalle.

- El sistema ya tiene los assets base de portada:
  - `public/assets/img/museo/coleccion/titeres/titere.webp`
  - `public/assets/img/museo/coleccion/payasos/payaso.webp`
  - `public/assets/img/museo/coleccion/mascaras/mascara.webp`
- Faltan los assets que Coni propuso para la capa de detalle y navegación:
  - `ficha-item.webp`
  - `ficha-tecnica.webp`
  - `tecnicas.webp`
  - `ver-todos.webp`
  - `payasos/ficha.webp`
  - `payasos/teatro.webp`
  - `mascaras/tradiciones.webp`
- Resultado: la colección base existe, pero aún no alcanza la densidad visual de la propuesta original en sus subpantallas.

### 8.1) Reutilización detectada en colección

- `public/assets/img/museo/coleccion/titeres/titere.webp` equivale visualmente al card `coleccion-card.webp`.
- `public/assets/img/museo/coleccion/payasos/payaso.webp` equivale al card de payasos.
- `public/assets/img/museo/coleccion/mascaras/mascara.webp` equivale al card de máscaras.
- `public/assets/img/museo/coleccion/mascaras/comedia-arte.webp` y `comedia-andes.webp` ya cubren las dos tradiciones visibles en la pantalla de máscaras.

### 9) `/museo/coleccion/titeres/exhibicion`

Referencia de diseño: `assets/design-refs/museo/coleccion/titeres/ficha-item.webp`

Estado: sin asset dedicado equivalente.

- La vista actual de exhibición sigue dependiendo de la estructura de listado.
- No hay un export local con la composición de tarjeta/ficha de Coni para este caso.

### 10) `/museo/coleccion/titeres/tecnicas`

Referencia de diseño: `assets/design-refs/museo/coleccion/titeres/tecnicas.webp`

Estado: sin asset dedicado equivalente.

- La pantalla actual es una grilla textual.
- Falta el export visual específico que Coni diseñó para técnicas.

### 11) `/museo/coleccion/mascaras/tradiciones`

Referencia de diseño: `assets/design-refs/museo/coleccion/mascaras/tradiciones.webp`

Estado: sin asset dedicado equivalente.

- La pantalla actual lista las tradiciones con texto.
- Existen assets relacionados para las dos tradiciones (`comedia-arte.webp`, `comedia-andes.webp`), pero no una composición de portada/entrada equivalente a la propuesta.

### 12) `/museo/coleccion/payasos`

Referencias de diseño:

- `assets/design-refs/museo/coleccion/payasos/ficha.webp`
- `assets/design-refs/museo/coleccion/payasos/teatro.webp`

Estado: sin assets dedicados equivalentes.

- El sistema tiene los assets del payaso base y de las tarjetas, pero no las piezas editoriales de ficha/teatro que Coni mostró.

### 13) `/cartelera` y `/cartelera/detalle/{slug}`

Referencias de diseño:

- `assets/design-refs/cartelera/cartelera.webp`
- `assets/design-refs/cartelera/cartelera-detalle.png`

Estado: parcial.

- La cartelera de listado está cubierta.
- La vista de detalle funciona con `la-malattia-di-nogasto-poster.webp` y `la-malattia-di-nogasto-collage.webp`, pero no existe un asset local que represente de forma canónica la composición de `cartelera-detalle.png`.
- En otras palabras: hay contenido suficiente para producir la vista, pero falta el export específico de esa portada editorial como asset reusable.

### 14) `/teatro-escuela`

Estado: funcional, pero no hay referencia directa en `assets/design-refs/`.

- Esta pantalla depende de assets ya existentes:
  - `menu/menu_escuela.webp`
  - `menu/menu_programacion.webp`
  - `school/teatroescuela-qr.webp`
  - `teatro-escuela/collage.webp`
  - `logos/ministerio_culturas_chile.webp`
- No encontré una carpeta de Coni en `assets/design-refs/` para esta pantalla, así que no pude hacer contraste directo.

## Browser Verification

Se hizo una pasada viva sobre `http://localhost:8086/` con el navegador integrado para validar que el render actual sí está usando los assets correctos en las pantallas que ya están cubiertas.

### Pantallas que sí calzan con Coni

- `/` splash:
  - Coincide con `assets/design-refs/inicio.jpg`.
  - Usa `assets/img/splash/collage-inicio.webp`, `assets/img/ui/texture.webp`, `assets/animations/logo.gif`, `assets/fonts/corporate-a/Corporate%20A%20Light%20Regular.otf`, `assets/fonts/Lato/Lato-Regular.ttf` y `assets/fonts/Lato/Lato-Bold.ttf`.
- `/menu`:
  - Coincide con `assets/design-refs/menu-principal.jpg`.
  - Usa `assets/img/menu/menu_museo.webp`, `menu_programacion.webp`, `menu_escuela.webp`, `menu_visitas.webp`, `menu_amigos.webp`, `menu/collage_referencia.webp` y `logos/ministerio_culturas_chile.webp`.
- `/museo`:
  - Coincide con `assets/design-refs/museo/museo.webp`.
  - Usa `assets/img/museo/coleccion/titeres/titere.webp`, `assets/img/museo/el-museo/explora-el-museo.webp`, `assets/img/museo/historia/historia-editorial.webp` y `assets/img/menu/menu_visitas.webp`.
  - Ojo: el card de `Colección` aquí no es el de la lámina `assets/design-refs/museo/coleccion/coleccion.webp`; es una marioneta sola.
- `/museo/coleccion`:
  - Coincide con `assets/design-refs/museo/coleccion/coleccion.webp`.
  - Usa los tres assets base correctos: `assets/img/museo/coleccion/titeres/titere.webp`, `assets/img/museo/coleccion/payasos/payaso.webp` y `assets/img/museo/coleccion/mascaras/mascara.webp`.

### Pantallas donde aún faltan assets o integración

- `/museo/coleccion/titeres/exhibicion`:
  - Hoy es un placeholder de "contenido en preparación".
  - Coni sí dejó `assets/design-refs/museo/coleccion/titeres/ficha-item.webp` como composición de referencia.
- `/museo/coleccion/titeres/tecnicas`:
  - Hoy es una grilla textual sin la composición editorial de Coni.
  - Falta integrar `assets/design-refs/museo/coleccion/titeres/tecnicas.webp`.
- `/museo/coleccion/mascaras/tradiciones`:
  - Hoy muestra solo texto y dos links.
  - Falta integrar `assets/design-refs/museo/coleccion/mascaras/tradiciones.webp`.
- `/museo/coleccion/payasos`:
  - La ruta no resuelve; hoy devuelve "Página no encontrada".
  - Coni sí entregó referencias para esa rama en `assets/design-refs/museo/coleccion/payasos/ficha.webp` y `assets/design-refs/museo/coleccion/payasos/teatro.webp`.

### Conclusión rápida

- Las pantallas base ya están bien alineadas con Coni.
- La deuda real está en las subpantallas de Colección y en la ausencia de la ruta base de Payasos.
- También sigue faltando la materialización de varias piezas editoriales en assets reutilizables, aunque la referencia visual ya existe.

## App-wide Summary

### En buen estado

- `/`
- `/menu`
- `/museo`
- `/museo/el-museo`
- `/museo/historia`
- `/teatro-escuela`
- `/cartelera`
- `/cartelera/detalle/{slug}`
- `/amigos-de-teatromuseo`

### Parciales o pendientes

- `/extension` sigue en modo `Contenido en preparación`.
- `/museo/coleccion/titeres/exhibicion` y `/museo/coleccion/mascaras/exhibicion` también están en modo placeholder.
- `/museo/coleccion/mascaras/tradiciones` funciona, pero todavía no expresa la composición visual de Coni.
- `/museo/coleccion/titeres/tecnicas` funciona, pero la vista es más funcional que editorial.

### Roturas reales

- `/museo/coleccion/payasos` responde 404.
- El card de `Colección` en `/museo` usa una marioneta aislada en vez de la lámina editorial de referencia.
- `/cartelera` y `/cartelera/detalle/{slug}` funcionan, pero el diseño todavía no calza con las láminas de Coni.
  - En el listado, los cards son cajas textuales con miniatura vacía y no usan la composición imagen + bloque de color de la referencia.
  - En el detalle, la jerarquía de título, chips, foto principal y cierre no reproduce la composición editorial exacta; se ve más técnico y menos editorial que la lámina `cartelera-detalle.png`.

### Qué significa esto

- La app no está “rota” en bloque.
- Sí tiene una deuda clara en la rama de colección y en un par de pantallas stub.
- El resto del sitio está razonablemente estable y navegable.

## Corrections Applied During This Audit

- Se renombraron los assets de museo a nombres más descriptivos:
  - `coleccion-card.webp`
  - `explora-el-museo-card.webp`
  - `historia-card.webp`
  - `visitas-guiadas-card.webp`
- Se agregó una regla de nomenclatura en `AGENTS.md`:
  - `lowercase-kebab-case.webp`
  - contenido primero
  - evitar `cat`, `landing`, `main`, `v1`, `temp`
- Se actualizó la documentación de tareas para reflejar los nombres y pantallas más coherentes.

## Evidence

- Inspección visual directa de:
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
- Inspección de las vistas y controladores que consumen assets en:
  - `app/Controllers/MuseumController.php`
  - `app/Controllers/CollectionController.php`
  - `app/Controllers/BillboardController.php`
  - `app/Repositories/SchoolFallbackRepository.php`
  - `app/Views/totem/*.php`

## Pending Work

- Exportar o reutilizar explícitamente los assets faltantes de colección:
  - `ficha-item.webp`
  - `ficha-tecnica.webp`
  - `tecnicas.webp`
  - `ver-todos.webp`
  - `payasos/ficha.webp`
  - `payasos/teatro.webp`
  - `mascaras/tradiciones.webp`
- Alinear el módulo `/museo/el-museo` con nombres de assets que reflejen la referencia original.
- Crear o localizar el asset equivalente de `historia-del-circo.webp`.
- Decidir si `cartelera-detalle.png` debe exportarse a un asset local canónico para dejar de depender de combinaciones de fallback.
- Revisar si `collage_referencia.webp` debe sustituirse por un cierre final real o conservarse como provisional.

## Final Summary

El sistema ya cubre bien la base de `/`, `/menu`, `/museo`, parte de `/museo/el-museo` y parte de `/cartelera`, pero la deuda visual más clara sigue estando en:

1. colección de detalle,
2. historia como pantalla editorial,
3. cartelera detalle como composición canónica,
4. limpieza de nombres para assets equivalentes que hoy existen pero no están nombrados como Coni los diseñó.

La diferencia principal ya no es “no hay imagen”, sino “hay imagen pero todavía no está bien asignada, nombrada o reutilizada”. Eso es una buena señal porque el trabajo pendiente ahora es de orden y cobertura, no de reconstrucción total.
