# Standard Editorial para Museo e Historia

Fecha: 2026-06-14

## Contexto

Hoy hay varias pantallas editoriales en el tótem que cuentan historias del museo, pero no todas usan la misma gramática visual. En particular:

- `museo/el-museo/historia`
- `museo/el-museo/iglesia`
- `museo/el-museo/hoy`
- `museo/historia`
- `museo/historia/:slug`

deben sentirse como una sola familia editorial. La pantalla `hoy` no puede seguir viviendo como layout especial; debe entrar al mismo estándar que las demás.

`cartelera/detalle/*` queda fuera de este estándar. Tiene más complejidad, más densidad y otro patrón de lectura. Lo único que se toma de ahí es la idea de que un componente de media puede ser reusable, no el shell completo.

## Objetivo

Construir un template editorial único para museo e historia que:

1. unifique hero, intro, cuerpo y cierre;
2. funcione con contenido mock basado en `lang()` mientras no llegue la data real;
3. soporte un hero que pueda renderizar:
   - una sola imagen;
   - un video;
   - un carrusel mixto de imágenes y videos;
4. permita reutilizar el componente de media como pieza separada;
5. mantenga consistencia visual entre museo e historia, incluyendo `hoy`;
6. preserve compatibilidad con pantallas editoriales futuras sin acoplarse a `cartelera/detalle/*`.

## Alcance

### En alcance

- `museo/el-museo/historia`
- `museo/el-museo/iglesia`
- `museo/el-museo/hoy`
- `museo/historia`
- `museo/historia/:slug`

### Fuera de alcance

- `cartelera/detalle/*`
- rediseño general de la cartelera
- consumo real de API para estas pantallas
- migraciones de backend

## Principios de diseño

1. **Una sola gramática editorial**
   - Título, introducción, secciones y cierre siguen el mismo orden.
   - La diferencia entre pantallas es contenido, no estructura.

2. **Hero flexible, shell estable**
   - El shell editorial no cambia por el tipo de media.
   - La media del hero se resuelve con un componente independiente.

3. **Mock primero, API después**
   - Mientras no exista data real, todo el contenido debe venir de `lang()` o de presentadores con datos mock.
   - No se debe conservar lógica de legacy para la pantalla `hoy`.

4. **Componentes reutilizables**
   - El hero media debe poder usarse hoy en museo e historia.
   - El componente debe quedar listo para ser consumido por otras pantallas de media-rich en el futuro.

5. **Accesibilidad y kiosko**
   - Controles grandes y simples.
   - Navegación visible y predecible.
   - Si hay carrusel, debe ser operable con botones prev / next y leer correctamente su estado.

## Arquitectura propuesta

### 1) Template editorial común

Crear un shell común para todas las pantallas editoriales de museo e historia.

Responsabilidad:

- renderizar navegación superior;
- renderizar hero;
- renderizar intro principal;
- renderizar secciones editoriales;
- renderizar un bloque de cierre o nota final si aplica;
- mantener la misma estructura visual entre páginas.

Forma de uso:

- cada controlador/página entrega un arreglo de contenido ya preparado;
- la vista del shell solo compone;
- no debe contener lógica de negocio.

### 2) Componente de media para el hero

Crear un componente separado que resuelva el área de media del hero.

Responsabilidad:

- renderizar una sola imagen;
- renderizar un video;
- renderizar un carrusel mixto cuando el arreglo de media tenga más de un item;
- usar el marco visual existente como envoltorio cuando corresponda;
- mantener separación clara entre capa visual y shell editorial.

Contrato mínimo sugerido:

```php
[
    'type' => 'image' | 'video',
    'src' => 'assets/img/...webp' | 'assets/video/...mp4',
    'alt' => '...',
    'poster' => 'assets/img/...webp', // solo para video
]
```

Para carrusel:

```php
[
    'media' => [
        ['type' => 'image', 'src' => '...'],
        ['type' => 'video', 'src' => '...'],
    ],
    'frame' => 'assets/img/ui/marco.webp',
]
```

Comportamiento:

- si hay 1 item, renderizar como media única;
- si hay 2+ items, renderizar carrusel;
- si hay videos, el carrusel debe respetar poster, play state y controles;
- los controles deben ser nativos y visibles, no solo decorativos.

### 3) Presenters / view-models editoriales

Para evitar arrays dispersos en vistas, las pantallas editoriales deben recibir un payload consistente.

Sugerencia de estructura:

- `eyebrow`
- `title`
- `hero`
- `intro`
- `sections`
- `closing`

Esto sirve para:

- `museum_building`
- `museum_institution`
- `museum_today`
- `comic_history_main`
- `comic_history_post`

## Diseño de contenido

### Museo e historia

Las pantallas de `museo/el-museo/*` y `museo/historia/*` deben usar el mismo template editorial. Esto incluye:

- mismo ancho de lectura;
- misma jerarquía de título;
- misma composición hero + texto;
- mismo estilo de secciones;
- mismo tratamiento para módulos de cierre.

### `hoy`

`hoy` debe dejar de ser una pantalla especial y convertirse en una pantalla editorial estándar.

Debe tener:

- un hero con media configurable;
- un texto principal mock completo;
- uno o más bloques editoriales secundarios;
- un cierre que sostenga la misma voz de la sección.

No debe conservar comportamiento legacy ni depender de datos remotos para ser entendible.

### `museo/historia`

`museo/historia` y sus posts editoriales deben adoptar el mismo lenguaje visual del template de museo e historia.

Si un post necesita más densidad, la diferencia debe resolverse dentro del mismo shell:

- más secciones;
- más media;
- más bloques de copy;
- no otro layout base.

## Reglas de implementación

1. **No tocar `cartelera/detalle/*` en esta entrega**
   - El detalle de cartelera queda como caso separado.
   - El componente de media debe quedar reutilizable para que pueda adoptarse después, pero no se rediseña esa vista ahora.

2. **No usar legacy de `hoy`**
   - El contenido de `hoy` se crea como mock nuevo.
   - No se mantiene lógica antigua ni copy heredado de estados previos.

3. **No hardcodear texto en la vista**
   - Los textos visibles deben venir de `lang()`.
   - Si hace falta estructura, debe venir de un presenter o de un helper de contenido.

4. **No mezclar shells**
   - El shell editorial debe ser único para museo e historia.
   - El carrusel/media composer debe ser una pieza aparte.

5. **No introducir un grid editorial distinto por pantalla**
   - La variación debe vivir en el contenido y la media, no en la arquitectura visual.

## Archivos a tocar

### Vistas

- `app/Views/totem/museum_building.php`
- `app/Views/totem/museum_institution.php`
- `app/Views/totem/museum_today.php`
- `app/Views/totem/comic_history_main.php`
- `app/Views/totem/comic_history_post.php`
- nuevo shell común editorial
- nuevo componente de media/carrusel

### Presenters

- reemplazar o extender el presenter de `hoy` para entregar contenido editorial completo
- crear un presenter/editorial view-model común si ayuda a mantener la coherencia

### Idiomas

- `app/Language/es/MuseumInfo.php`
- `app/Language/en/MuseumInfo.php`
- `app/Language/fr/MuseumInfo.php`
- `app/Language/pt/MuseumInfo.php`

### CSS

- agregar estilos del shell común
- agregar estilos del componente de media y del carrusel
- agregar responsividad para desktop y viewport kiosko

## Plan de implementación

### Fase 1: contrato y shell

- definir la estructura de datos común;
- crear el shell editorial común;
- migrar `historia` e `iglesia` al shell;
- migrar `museo/historia` al mismo shell.

### Fase 2: media composer

- construir el componente de media;
- soportar imagen única;
- soportar video único;
- soportar carrusel mixto;
- integrar el marco visual reusable.

### Fase 3: `hoy`

- eliminar la dependencia de legacy;
- crear texto mock completo para `hoy`;
- montar `hoy` en el shell común;
- usar el media composer si la pantalla necesita varias piezas.

### Fase 4: polish y QA

- verificar desktop y viewport kiosko;
- verificar que el contenido editorial no rompa el layout;
- verificar que el carrusel sea operable;
- verificar que el copy largo no genere overflow.

## Criterios de aceptación

1. `museo/el-museo/historia`, `museo/el-museo/iglesia`, `museo/el-museo/hoy`, `museo/historia` y `museo/historia/:slug` se perciben como una sola familia visual.
2. `hoy` ya no se ve como excepción de layout.
3. El hero acepta imagen, video o carrusel mixto sin cambiar el shell.
4. El componente de media queda separado y reutilizable.
5. El contenido visible no depende de legacy.
6. Los textos continúan saliendo de `lang()` mientras se esperan datos reales.
7. La implementación pasa revisión visual en navegador sin clipping, overflow o rupturas del ritmo editorial.

## Riesgos

- Si el carrusel se sobrediseña, puede volverse más pesado que el estándar editorial.
- Si se intenta meter lógica de cartelera en este shell, se pierde la claridad del sistema.
- Si los textos mock son demasiado largos, pueden romper el ritmo vertical en kiosko.

## Decisión abierta

La única decisión aún abierta para la implementación es si el carrusel del hero debe:

- avanzar manualmente solo con prev / next, o
- tener autoavance suave además de controles manuales.

La spec asume que cualquiera de los dos debe seguir siendo accesible y no interferir con la navegación principal.
