# CLAUDE.md — teatromuseo-totem-ci4

Tótem interactivo del Teatro Museo. Aplicación CodeIgniter 4 diseñada para pantallas táctiles verticales (1080×1920) en modo kiosko (Fully Kiosk Browser). Sin base de datos propia — todo el contenido proviene de la API del museo.

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
│              │ • TotemApiInterface │                             │
│              │ • CachedTotemApiService                             │
│              │ • MenuBuilder       │                             │
│              │ • NavBuilder        │                             │
│              └──────────┬──────────┘                             │
│                         │                                        │
│              ┌──────────▼──────────┐                             │
│              │   Presenters        │                             │
│              │ • SchoolPresenter   │                             │
│              │ • BillboardPresenter│                             │
│              │ • MuseumTodayPresenter                             │
│              │ • DatePresenter     │                             │
│              └─────────────────────┘                             │
└─────────────────────────────────────────────────────────────────┘
                                │
                                ▼
                  ┌─────────────────────────┐
                  │  teatromuseo-api-ci4    │
                  │      (:8080)            │
                  └─────────────────────────┘
```

### Principios arquitectónicos

1. **Sin base de datos propia.** El tótem es stateless; consume `/api/v1/totem/*` vía CURL server-side.
2. **Controladores de dominio.** Divididos por funcionalidad: `MainController`, `CollectionController`, `MuseumController`, `SchoolController`, `BillboardController`, `FriendsController`.
3. **BaseTotemController.** Provee helpers comunes (`pageMeta()`, `shellNav()`, `render()`, `totemApi()`) sin lógica de dominio.
4. **Servicios con interfaz.** `TotemApiInterface` permite decoradores como `CachedTotemApiService`.
5. **Fallback repositories.** Datos de contingencia cuando la API no responde (`SchoolFallbackRepository`, `BillboardFallbackRepository`, `MuseumFallbackRepository`).
6. **Presenters.** Lógica de presentación separada en clases dedicadas.
7. **Enums.** `Audience`, `SchoolCategory` reemplazan IDs mágicos.
8. **Idiomas.** ES, EN, FR, PT vía cookie `totem_lang` y sistema `lang()` de CI4.

## Estructura de archivos

```
app/
├── Config/
│   ├── Routes.php              # Rutas nombradas, no usar arrays en to()
│   ├── Services.php            # Registro de TotemApiInterface
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
│   ├── TotemApiInterface.php   # Contrato de servicio API
│   ├── TotemApiService.php     # Implementación base (CURLRequest)
│   ├── CachedTotemApiService.php # Decorador con memoización
│   ├── MenuBuilder.php         # Generador de items de menú
│   ├── NavBuilder.php          # Generador de navegación shell
│   └── SlugResolver.php        # Resolución de IDs desde slugs
├── Presenters/
│   ├── SchoolPresenter.php     # Presentación de cursos/escuela
│   ├── BillboardPresenter.php  # Presentación de cartelera
│   ├── MuseumTodayPresenter.php# Presentación de "hoy en el museo"
│   └── DatePresenter.php       # Formateo de fechas localizado
├── Repositories/               # Datos de fallback/contingencia
│   ├── SchoolFallbackRepository.php
│   ├── BillboardFallbackRepository.php
│   └── MuseumFallbackRepository.php
└── Enums/                      # Reemplazo de IDs mágicos
    ├── Audience.php
    └── SchoolCategory.php
```

## Convenciones de código

### PHP

| Aspecto | Convención | Ejemplo |
|---------|------------|---------|
| Controladores | Sufijo `Controller`, heredan `BaseTotemController` | `CollectionController` |
| Métodos | camelCase, descriptivo | `collectionTechniques()` |
| Servicios | Interfaz + Implementación + Decorador | `TotemApiInterface` → `TotemApiService` → `CachedTotemApiService` |
| Presenters | Sufijo `Presenter`, inmutable | `SchoolPresenter` |
| Repositories | Sufijo `Repository`, datos estáticos | `SchoolFallbackRepository` |
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
php spark serve --port 8086

# Tests y calidad
composer test      # PHPUnit (49 tests)
composer lint      # PHP-CS-Fixer (dry-run)
composer analyse   # PHPStan nivel 8
composer format    # PHP-CS-Fixer (fix)

# CSS
composer build:css # Compila style.css
```

Variables necesarias en `.env`:
```bash
CI_ENVIRONMENT = development
app.baseURL    = 'http://localhost:8086/'
TOTEM_API_URL  = 'http://localhost:8080/api/v1/totem'
TOTEM_API_KEY  = '<clave configurada en el API>'

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

### TotemApiInterface
Contrato que define los métodos disponibles:
- `events(): array` — Eventos de cartelera
- `courses(): array` — Cursos de escuela
- `techniques(): array` — Técnicas de titiritería
- `technique(int $id): array` — Detalle de técnica

### TotemApiService
Implementación base usando CURLRequest. Captura excepciones y retorna arrays vacíos en caso de error.

### CachedTotemApiService
Decorador con memoización por request. Registrado en `Config/Services.php`:
```php
public static function totemApi(): TotemApiInterface
{
    return new CachedTotemApiService(new TotemApiService());
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
- TotemApiInterface + CachedTotemApiService
- Presenters de dominio
- Fallback repositories
- Enums y SlugResolver

### Fase 2 — Vistas y componentes 🔄 EN PROGRESO
- F2-T10: Mock notice parcial completado
- Pendiente: Componentizar iconos, centralizar locales, etc.

### Fase 3 — CSS design system ⏳ PENDIENTE
- Consolidar tokens
- Eliminar CSS muerto
- Componentes base card/panel

### Fase 4 — Observabilidad ⏳ PENDIENTE
- Logs estructurados
- Health check endpoint
- Cache file-based

## Pantallas con contenido en construcción

| Ruta | Vista | Estado |
|------|-------|--------|
| `/museo/coleccion/titeres/exhibicion` | `collection_puppets_exhibit.php` | Mock notice (F2-T10) |
| `/museo/coleccion/mascaras/exhibicion` | `collection_masks_exhibit.php` | Mock notice (F2-T10) |
| `/museo/coleccion/fichas/:id` | `collection_item_detail.php` | Mock notice (F2-T10) |
| `/museo/historia/:slug` | `comic_history_post.php` | Mock notice (F2-T10) |
| `/museo/el-museo/edificio` | `museum_building.php` | Mock notice (F2-T10) |
| `/museo/el-museo/institucion` | `museum_institution.php` | Mock notice (F2-T10) |
| `/extension` | `extension_contact.php` | Mock notice (F2-T10) |

---

> **Para IAs:** Si necesitas modificar este proyecto, lee también `AGENTS.md` para convenciones específicas de agentes.
