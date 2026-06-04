# CLAUDE.md — teatromuseo-totem-ci4

Tótem interactivo del Teatro Museo. Aplicación CI4 diseñada para pantallas táctiles verticales
(1080×1920) en modo kiosko (Fully Kiosk Browser). Sin base de datos propia — todo el contenido
proviene de la API del museo.

## Arquitectura

```
Kiosko → TotemController → TotemApiService → teatromuseo-api-ci4 (:8080)
```

- **Sin DB ni migraciones.** El tótem es stateless; consume `/api/v1/totem/*` vía CURL server-side.
- **Autenticación:** cabecera `X-Totem-Key` (variable `TOTEM_API_KEY` en `.env`).
- **Idiomas:** ES, EN, FR, PT vía cookie `totem_lang` y sistema de idiomas de CI4 (`lang()`).
- **Resiliencia:** `TotemApiService::get()` captura excepciones y retorna `[]` — las vistas muestran
  datos de contingencia (mocks) cuando la API no responde.

## Desarrollo local

```bash
php spark serve --port 8086   # servidor de desarrollo
composer test                 # unit tests (phpunit)
```

Variables necesarias en `.env`:
```
CI_ENVIRONMENT = development
app.baseURL    = 'http://localhost:8086/'
TOTEM_API_URL  = 'http://localhost:8080/api/v1/totem'
TOTEM_API_KEY  = '<clave configurada en el API>'
```

## CSS

El CSS se compila con **concatenación de parciales**. Nunca editar `style.css` directamente.

```
public/assets/css/
├── style.css          ← compilado, NO editar a mano
└── src/
    ├── 00-tokens.css  ← variables CSS (colores, radios, tipografía)
    ├── 01-base.css    ← reset, fuentes (@font-face)
    ├── 02-shell.css   ← contenedor kiosko
    ├── 99-responsive.css
    ├── shared/        ← header, screen, hero, footer-ornament, transitions, utils…
    └── screens/       ← un parcial por pantalla
        ├── section.css    ← secciones genéricas + colección
        ├── school.css     ← pantalla Teatro Escuela (teacher-card, school-*, course-card)
        ├── billboard.css  ← cartelera
        ├── menu.css       ← menú principal y submenús
        └── …
```

**Ciclo de trabajo:**
1. Editar el parcial en `src/`
2. `composer build:css` (o `bash bin/build-css.sh`)
3. Refrescar el navegador

Para agregar un nuevo parcial: añadirlo en el orden correcto dentro del array `FILES` en
`bin/build-css.sh`.

## Convenciones de nombres

| Ámbito | Convención | Ejemplo |
|---|---|---|
| Rutas URL | español, kebab-case | `/museo/coleccion/titeres` |
| Métodos del controlador | inglés, camelCase | `collectionTechniques()` |
| Vistas | inglés, snake_case | `collection_techniques.php` |
| Clases CSS | BEM inglés | `.teacher-card--amber` |
| Claves de idioma | inglés, PascalCase.snake_key | `Collection.masks_title` |

## Despliegue

El despliegue a producción es FTP incremental (solo archivos modificados desde el último deploy):

```bash
# Requiere credenciales en .deploy/.env.deploy (no committed)
python3 .deploy/deploy.py
```

Los archivos excluidos del despliegue: `.env`, `vendor/`, `tests/`, `composer.*`, `.git/`,
`writable/`.

El CSS compilado (`style.css`) **sí se sube** — debe compilarse antes de hacer deploy.

## Estado de las waves

| Wave | Estado | Descripción |
|---|---|---|
| Wave 1 | ✅ Completo | Shell, splash, menú, idiomas, cartelera, historia, escuela, colección |
| Wave 2 | 🔄 Parcial | Rediseño colección (bloqueado por assets de Coni), detalle de fichas |
| Wave 3 | ⏳ Pendiente | Integración BFF, contacto interactivo, analytics |

## Pantallas con contenido en construcción

Estas rutas son accesibles pero muestran contenido de contingencia o shell vacío:

| Ruta | Vista | Estado |
|---|---|---|
| `/museo/coleccion/titeres/exhibicion` | `collection_puppets_exhibit.php` | Shell vacío, esperando assets |
| `/museo/coleccion/mascaras/exhibicion` | `collection_masks_exhibit.php` | Shell vacío, esperando assets |
| `/museo/coleccion/fichas/:id` | `collection_item_detail.php` | Mock — esperando diseño de ficha |
| `/museo/historia/:slug` | `comic_history_post.php` | Mock — esperando contenido CMS |
| `/cartelera/detalle/:slug` | `billboard_detail.php` | Datos hardcodeados — esperando endpoint API |
| `/museo/el-museo/edificio` | `museum_building.php` | Texto placeholder — esperando diseño |
| `/museo/el-museo/institucion` | `museum_institution.php` | Texto placeholder — esperando diseño |
| `/extension` | `extension_contact.php` | Mock — formulario pendiente (Wave 3) |

## Rutas legacy mantenidas

`/museo/historia-comica` y `/museo/historia-comica/:slug` existen para no romper QR codes
físicos ya impresos. Ambas apuntan a los mismos handlers que `/museo/historia`.
