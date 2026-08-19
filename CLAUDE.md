# CLAUDE.md — teatromuseo-totem-ci4

Tótem interactivo del Teatro Museo. Aplicación CodeIgniter 4 diseñada para pantallas táctiles verticales (1080×1920) en modo kiosko (Fully Kiosk Browser). Sin base de datos propia — todo el contenido de Cartelera (shows), TeatroEscuela (cursos) y Catálogo (piezas/técnicas) proviene del BFF (`teatromuseo-bff`, seam `public-read`), igual que `teatromuseo-web`. La planificación de esta migración está en [`../docs/plan/2026-08-18-plan-totem-via-bff.md`](../docs/plan/2026-08-18-plan-totem-via-bff.md): las rutas `/api/v1/totem/*` que este proyecto llamaba antes **nunca se implementaron en el Hub** — el tótem corría 100% sobre contenido fantasma hasta esta migración.

## Arquitectura

```
┌─────────────────────────────────────────────────────────────────┐
│                         KIOSKO (1080×1920)                       │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────────────────┐  │
│  │MainController│  │Collection   │  │SchoolController         │  │
│  │             │  │Controller   │  │BillboardController      │  │
│  │Museum       │  │Friends      │  │                         │  │
│  │Controller   │  │Controller   │  │  (Domain Controllers)    │  │
│  └──────┬──────┘  └──────┬──────┘  └────────────┬────────────┘  │
│         └─────────────────┴──────────────────────┘                │
│                         │                                        │
│              ┌──────────▼──────────┐                             │
│              │ BaseTotemController │                             │
│              │  (Shared helpers)   │                             │
│              └──────────┬──────────┘                             │
│                         │                                        │
│              ┌──────────▼──────────┐                             │
│              │   Services          │                             │
│              │ • BffTotemClient    │                             │
│              │ • TotemApiResult    │                             │
│              │ • MenuBuilder       │                             │
│              │ • NavBuilder        │                             │
│              └──────────┬──────────┘                             │
│                         │                                        │
│              ┌──────────▼──────────┐                             │
│              │   Presenters        │                             │
│              │ • SchoolPresenter   │                             │
│              │ • BillboardPresenter│                             │
│              │ • CollectionPresenter                              │
│              │ • MuseumTodayPresenter                             │
│              │ • DatePresenter     │                             │
│              └─────────────────────┘                             │
└─────────────────────────────────────────────────────────────────┘
                                │
                                ▼
                  ┌─────────────────────────┐
                  │   teatromuseo-bff       │
                  │   (public-read, :8188)  │
                  └─────────────────────────┘
```

### Principios arquitectónicos

1. **Sin base de datos propia.** El tótem es stateless; consume el BFF (`teatromuseo-bff`, seam `public-read`) vía CURL server-side, con clave dedicada `TOTEM_BFF_API_KEY` (nunca la del sitio público).
2. **Controladores de dominio.** Divididos por funcionalidad: `MainController`, `CollectionController`, `MuseumController`, `SchoolController`, `BillboardController`, `FriendsController`.
3. **BaseTotemController.** Provee helpers comunes (`pageMeta()`, `shellNav()`, `render()`, `totemApi()`) sin lógica de dominio.
4. **Un solo cliente HTTP.** `App\Services\BffTotemClient` — un método por necesidad de pantalla, caché fresh+stale-on-failure vía el cache service de CI4. Cada llamada devuelve `TotemApiResult` (`state: fresh|stale|unavailable`), nunca un `[]` ambiguo que confunda "fuente caída" con "genuinamente vacío".
5. **Sin contenido de relleno.** No existen repositorios de fallback ni JSON mock: una pantalla sin datos muestra un estado honesto (`totem/partials/content_unavailable`, o el copy "sin funciones/cursos" propio de cada pantalla) — nunca datos inventados.
6. **Presenters.** Lógica de presentación separada en clases dedicadas (`BillboardPresenter`, `SchoolPresenter`, `CollectionPresenter`), cada una recibe un `TotemApiResult` y decide fresh/stale/unavailable/vacío.
7. **Enums.** `Audience`, `SchoolCategory` — uso histórico; `Audience` ya no aplica a Cartelera (el dominio de eventos no tiene campo de público objetivo).
8. **Idiomas.** ES, EN, FR, PT vía cookie `totem_lang` y sistema `lang()` de CI4.

## Estructura de archivos

```
app/
├── Config/
│   ├── Routes.php              # Rutas nombradas, no usar arrays en to()
│   ├── Services.php            # Registro de BffTotemClient
│   └── Totem.php               # Config + env vars (TOTEM_ENABLE_*)
├── Controllers/
│   ├── BaseTotemController.php # Helpers comunes para todos los controladores
│   ├── MainController.php      # Splash, menú principal, 404, idioma
│   ├── CollectionController.php# Colección: técnicas, títeres, máscaras
│   ├── MuseumController.php    # El museo: hoy, edificio, institución
│   ├── SchoolController.php    # Teatro escuela: cursos, técnicas
│   ├── BillboardController.php # Cartelera: eventos, detalles
│   └── FriendsController.php   # Amigos, extensión
├── Services/
│   ├── BffTotemClient.php      # Cliente único al BFF (public-read), caché fresh+stale
│   ├── TotemApiResult.php      # Resultado tipado: {data, state: fresh|stale|unavailable}
│   ├── MenuBuilder.php         # Generador de items de menú
│   ├── NavBuilder.php          # Generador de navegación shell
│   └── SlugResolver.php        # Resolución de IDs desde slugs
├── Presenters/
│   ├── SchoolPresenter.php     # Presentación de cursos/escuela (TotemApiResult → vista)
│   ├── BillboardPresenter.php  # Presentación de cartelera (TotemApiResult → vista)
│   ├── CollectionPresenter.php # Presentación de piezas/técnicas/categorías (TotemApiResult → vista)
│   ├── MuseumTodayPresenter.php# Presentación de "hoy en el museo"
│   └── DatePresenter.php       # Formateo de fechas localizado (con fallback si falta `intl`)
└── Enums/                      # Reemplazo de IDs mágicos
    ├── Audience.php
    └── SchoolCategory.php
```

No hay `app/Repositories/` ni `app/Data/*.json`: no existe contenido de relleno en este proyecto. Una pantalla sin datos reales muestra `totem/partials/content_unavailable` (fuente inalcanzable) o un copy "sin funciones/cursos" propio de la pantalla (fuente confirma vacío) — nunca datos inventados.

## Convenciones de código

### PHP

| Aspecto | Convención | Ejemplo |
|---------|------------|---------|
| Controladores | Sufijo `Controller`, heredan `BaseTotemController` | `CollectionController` |
| Métodos | camelCase, descriptivo | `collectionTechniques()` |
| Servicio HTTP | Un cliente único, sin decoradores | `App\Services\BffTotemClient` |
| Presenters | Sufijo `Presenter`, reciben `TotemApiResult` | `SchoolPresenter` |
| Enums | PascalCase, casos PascalCase | `Audience::NationalTour` |
| Vistas | snake_case, carpeta `totem/` | `collection_techniques.php` |

### Rutas (app/Config/Routes.php)

```php
// ✅ CORRECTO: nombre de ruta como string
$routes->get('museo/coleccion', [CollectionController::class, 'collectionMain'], ['as' => 'collection_main']);

// ✅ CORRECTO: helper route() con nombre
route('collection_main')

// ❌ INCORRECTO: arrays en to() o route() con arrays
$routes->get('path', ['Controller', 'method']); // NO USAR
route(['Controller', 'method']); // NO USAR
```

### Vistas

```php
// ✅ CORRECTO: extender MainLayout, usar page_shell
<?= $this->extend('layouts/MainLayout') ?>
<?= $this->section('content') ?>
    <?= view('totem/partials/page_shell', [
        'title' => lang('Collection.techniques_title'),
        'content' => $content,
        'nav' => $nav ?? []
    ]) ?>
<?= $this->endSection() ?>
```

### Internacionalización (i18n)

- Archivos en `app/Language/{es,en,fr,pt}/`
- Claves en PascalCase para archivos, snake_case para claves
- Siempre usar `lang()` con clave completa: `lang('Collection.techniques_title')`
- Nunca hardcodear español en vistas

## CSS

```
public/assets/css/
├── style.css          ← compilado, NO editar a mano
└── src/
    ├── 00-tokens.css  ← variables CSS (colores, radios, tipografía)
    ├── 01-base.css    ← reset, fuentes (@font-face)
    ├── 02-shell.css   ← contenedor kiosko
    ├── 99-responsive.css
    ├── shared/        ← componentes reutilizables
    │   ├── header.css
    │   ├── footer-ornament.css
    │   ├── transitions.css  ← transiciones entre pantallas
    │   └── ...
    └── screens/       ← un parcial por pantalla
        ├── section.css
        ├── school.css
        ├── billboard.css
        ├── menu.css
        └── ...
```

**Ciclo de trabajo:**
1. Editar el parcial en `src/`
2. `composer build:css` (o `bash bin/build-css.sh`)
3. Refrescar el navegador

## Desarrollo local

```bash
# Servidor de desarrollo
php spark serve --port 8186

# Tests y calidad
composer test      # PHPUnit (49 tests)
composer lint      # PHP-CS-Fixer (dry-run), alias: composer cs-check / format:check
composer analyse   # PHPStan nivel 8, alias: composer phpstan
composer format    # PHP-CS-Fixer (fix), alias: composer cs-fix
composer quality   # format:check + analyse + test — igual al resto de apps del workspace

# CSS
composer build:css # Compila style.css
```

Los hooks `pre-commit`/`pre-push` se instalan automáticamente en `.git/hooks/` vía
`composer install`/`composer update` (scripts `post-install-cmd`/`post-update-cmd`),
igual que en el resto de apps del workspace. `Dockerfile` existe para paridad local
con las demás apps — la app no se despliega vía Docker en producción, sino por FTP
(ver `RELEASE.md`).

Variables necesarias en `.env`:
```bash
CI_ENVIRONMENT     = development
app.baseURL        = 'http://localhost:8186/'
TOTEM_BFF_BASE_URL = 'http://localhost:8188'
TOTEM_BFF_API_KEY  = '<clave dedicada, debe existir también como TOTEM_BFF_API_KEY en teatromuseo-bff/.env>'

# Caché fresh+stale (BffTotemClient)
TOTEM_CACHE_TTL_SECONDS  = 60      # respuesta "fresh"
TOTEM_STALE_TTL_SECONDS  = 86400   # última respuesta real, para outages del BFF

# Feature flags
TOTEM_ENABLE_TRANSITIONS = true   # transiciones entre pantallas
TOTEM_ENABLE_ANIMATIONS  = true   # animaciones no esenciales
```

## Despliegue

```bash
# Compilar CSS antes de deploy
composer build:css

# Deploy FTP incremental (requiere .deploy/.env.deploy)
python3 .deploy/deploy.py
```

Archivos excluidos: `.env`, `vendor/`, `tests/`, `composer.*`, `.git/`, `writable/`.

## Controladores de dominio

### MainController
- `index()` — Splash/idle screen
- `mainMenu()` — Menú principal
- `language()` — Selector de idioma
- `notFound()` — Página 404 amigable

### CollectionController
- `collectionMain()` — Landing de colección
- `collectionTechniques()` — Técnicas de titiritería
- `collectionTechnique($slug)` — Detalle de técnica
- `collectionPuppetsExhibit()` — Exhibición de títeres
- `collectionMasksExhibit()` — Exhibición de máscaras
- `collectionMasksTraditions()` — Tradiciones de máscaras
- `collectionMaskTradition($slug)` — Detalle de tradición
- `collectionItem($id)` — Ficha de ítem

### MuseumController
- `museumToday()` — Hoy en el museo
- `museumInfo()` — Menú de información
- `museumBuilding()` — El edificio
- `museumInstitution()` — La institución

### SchoolController
- `theaterSchool()` — Teatro escuela (cursos, horarios)

### BillboardController
- `billboard()` — Cartelera de eventos
- `billboardDetail($slug)` — Detalle de evento

### FriendsController
- `friendsSection()` — Amigos de Teatromuseo
- `extensionContact()` — Extensión y contacto

## Servicios API

### BffTotemClient
Único cliente HTTP del tótem — un método por necesidad de pantalla, todos contra el BFF (`teatromuseo-bff`, seam `public-read`):
- `shows(locale)` / `show(locale, $idOrSlug)` — Cartelera (eventos)
- `courses(locale)` / `course(locale, $slug)` — TeatroEscuela (entries CMS `teatroescuela`)
- `collectionItems(locale, category?, technique?)` / `collectionItem(locale, $idOrSlug)` — Catálogo (piezas)
- `techniques($withCounts)` / `technique($idOrSlug)` — Catálogo (técnicas)
- `catalogCategories($withCounts)` — Catálogo (categorías, para toggles como "hay payasos/máscaras")

Cada método devuelve `TotemApiResult` (`data`, `state: fresh|stale|unavailable`) — nunca un array vacío ambiguo. Caché fresh+stale vía el cache service de CI4 (`TOTEM_CACHE_TTL_SECONDS`/`TOTEM_STALE_TTL_SECONDS`), reintentos con backoff en 5xx/timeout, propagación de `X-Request-ID`, header `X-App-Key: TOTEM_BFF_API_KEY`. Registrado en `Config/Services.php`:
```php
public static function totemApi(bool $getShared = true): BffTotemClient
{
    if ($getShared) {
        return static::getSharedInstance('totemApi');
    }

    return new BffTotemClient(static::cache());
}
```

## Estado actual del proyecto

### Fase 0 — Fundamentos ✅ COMPLETA
- Variables de entorno corregidas
- Bugs críticos resueltos
- PHPStan + PHP-CS-Fixer instalados
- CI/CD pipeline activa

### Fase 1 — Arquitectura backend ✅ COMPLETA
- BaseTotemController con helpers compartidos
- MenuBuilder y NavBuilder extraídos
- Controladores divididos por dominio
- BffTotemClient + TotemApiResult
- Presenters de dominio
- Estados explícitos fresh/stale/unavailable
- Enums y SlugResolver

### Fase 2 — Vistas y componentes ✅ COMPLETA
- F2-T1: Lógica movida de collection_main.php al controlador
- F2-T2: Locales centralizados en helper
- F2-T3: Textos faltantes internacionalizados
- F2-T4: Helper safe_title() creado
- F2-T5: Iconos SVG componentizados
- F2-T6: card.php y collection_band.php refactorizados
- F2-T7: Scripts inline extraídos a módulos JS
- F2-T8: Cleanup registry verificado
- F2-T9: Escape de URLs mejorado
- F2-T10: Mock notice parcial completado

### Fase 3 — CSS design system ✅ COMPLETA
- F3-T1: Tokens consolidados en 00-tokens.css ✅
- F3-T2: CSS muerto eliminado (detail.css) ✅
- F3-T3: Componentes base .card/.panel creados ✅
- F3-T4: Nomenclatura de botón de idioma unificada ✅
- F3-T5: Selectores sin estilos corregidos ✅
- F3-T6: Container queries migradas ✅
- F3-T7: Build pipeline mejorado con PostCSS ✅
- F3-T8: Accesibilidad táctil y reduced-motion ✅

### Fase 4 — Observabilidad ✅ COMPLETA
- F4-T1: Logs estructurados de API ✅
- F4-T2: Health check endpoint `/health` ✅
- F4-T3: Estrategia histórica de fallback offline documentada ✅
- F4-T4: Cache file-based histórico, reemplazado por fresh/stale en CI4 ✅
- F4-T5: README.md actualizado ✅
- F4-T6: AGENTS.md actualizado ✅
- F4-T7: Manual de soporte creado ✅
- F4-T8: Evaluación de despliegue completada ✅
- F4-T9: Tests finales (59 tests, 167 assertions) ✅

### Fase 5 — Migración a datos reales vía BFF ✅ COMPLETA (2026-08-18)
- El proyecto descubrió que `/api/v1/totem/*` **nunca existió en el Hub**: Cartelera,
  TeatroEscuela y gran parte de Catálogo corrían 100% sobre contenido inventado
  (repositorios fallback y JSON mock), indistinguible de contenido real para el
  visitante. Ver [`../docs/plan/2026-08-18-plan-totem-via-bff.md`](../docs/plan/2026-08-18-plan-totem-via-bff.md).
- `TotemApiInterface`/`TotemApiService`/`CachedTotemApiService`/`FileCachedTotemApiService`
  (triple decorador, TOT-01) colapsados en un único `App\Services\BffTotemClient` contra
  `teatromuseo-bff` (seam `public-read`), con `TotemApiResult` (`fresh`/`stale`/`unavailable`)
  reemplazando el `[]` ambiguo que confundía "fuente caída" con "genuinamente vacío".
- Repositorios de fallback y JSON mock eliminados por completo — `app/Repositories/` y
  `app/Data/` ya no existen. Estados honestos (`content_unavailable`, copy "sin
  funciones/cursos") en su lugar.
- `collection_items.show_in_totem` (ya existía en el esquema, sin uso) activado
  server-side en el BFF para curar qué piezas ve el tótem — el sitio público sigue
  viendo todo lo publicado.
- `composer quality` verde (PHPStan, CS-Fixer, 90 tests y 317 assertions).

## Pantallas con contenido en construcción

Las tres filas de Catálogo que antes decían "Mock notice" (F2-T10) ya no aplican —
`collection_puppets_exhibit.php`, `collection_masks_exhibit.php` y
`collection_item_detail.php` consumen datos reales del BFF desde la Fase 5. Las
filas que siguen abajo son pantallas fuera del alcance de esa migración, sin backend
propio todavía:

| Ruta | Vista | Estado |
|------|-------|--------|
| `/museo/historia/:slug` | `comic_history_post.php` | Mock notice (F2-T10) |
| `/museo/el-museo/edificio` | `museum_building.php` | Mock notice (F2-T10) |
| `/museo/el-museo/institucion` | `museum_institution.php` | Mock notice (F2-T10) |
| `/extension` | `extension_contact.php` | Mock notice (F2-T10) |

---

> **Para IAs:** Si necesitas modificar este proyecto, lee también `AGENTS.md` para convenciones específicas de agentes.
