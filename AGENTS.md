# AGENTS.md — Convenciones para Agentes de Código

Este documento define las convenciones específicas para agentes de IA trabajando en el proyecto teatromuseo-totem-ci4. Lee esto antes de hacer cualquier cambio arquitectural.

## Principios no negociables

### 1. Arquitectura por capas

```
┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│   Vistas    │────▶│ Controlador │────▶│   Servicio  │────▶ API externa
│  (View)     │◀────│ (Controller)│◀────│ (Service)   │
└─────────────┘     └──────┬──────┘     └─────────────┘
                           │
              ┌────────────┼────────────┐
              ▼            ▼            ▼
        Presenter   Fallback Repo   Enums/VOs
```

**REGLAS:**
- Las vistas NO tienen lógica de negocio (solo `foreach` e `if` simples)
- Los controladores ORQUESTAN, no implementan lógica
- Los servicios ACCEDEN a datos (API, fallback, caché)
- Los presenters TRANSFORMAN datos para visualización

### 2. Observabilidad y resiliencia (Fase 4)

#### Logs estructurados

Todas las llamadas API generan logs en formato JSON:

```json
{
  "timestamp": "2026-06-12T23:02:17-04:00",
  "service": "totem_api",
  "endpoint": "courses",
  "duration": 145,
  "status": 200,
  "success": true
}
```

Los logs se escriben en `writable/logs/log-YYYY-MM-DD.log`.

#### Health Check

Endpoint `/health` para monitoreo:

```bash
curl http://localhost:8086/health
# {"status":"ok","api":"reachable","timestamp":"..."}
```

#### Cache en archivo

El servicio `FileCachedTotemApiService` proporciona cache persistente:

- TTL configurable vía `TOTEM_CACHE_TTL_SECONDS` (default: 60s)
- Ubicación: `writable/cache/totem/`
- Se activa/desactiva con `TOTEM_ENABLE_FILE_CACHE`
- Limpieza manual: `rm writable/cache/totem/*.cache`

---

### 3. Patrones establecidos

#### Controladores

```php
<?php

declare(strict_types=1);

namespace App\Controllers;

/**
 * Descripción de qué maneja este controlador.
 */
final class ExampleController extends BaseTotemController
{
    public function exampleAction(): string
    {
        // 1. Obtener datos del servicio
        $data = $this->totemApi()->exampleMethod();
        
        // 2. Usar presenter si hay lógica de presentación
        $presenter = new ExamplePresenter($data);
        
        // 3. Renderizar con metadatos de página
        return $this->render('totem/view_name', [
            'nav' => $this->shellNav(base_url('ruta/padre')),
            'items' => $presenter->items(),
        ], lang('File.title_key'));
    }
}
```

**SIEMPRE:**
- Heredar de `BaseTotemController`
- Usar `declare(strict_types=1);`
- Usar `final class`
- Documentar con docblock
- Retornar `string` (la vista renderizada)

**NUNCA:**
- Acceder a `$_GET`, `$_POST` directamente (usar `$this->request`)
- Hacer echo/print en controladores
- Llamar a la API directamente (usar `$this->totemApi()`)

#### Vistas

```php
<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
    <?php ob_start(); ?>
        <!-- contenido aquí -->
        <?= view('totem/partials/mock_notice', [
            'titleKey' => 'Totem.mock_notice_title',
            'copyKey'  => 'Totem.mock_notice_copy',
        ]) ?>
    <?php $content = ob_get_clean(); ?>

    <?= view('totem/partials/page_shell', [
        'title'   => lang('File.title_key'),
        'content' => $content,
        'nav'     => $nav ?? [],
    ]) ?>
<?= $this->endSection() ?>
```

**SIEMPRE:**
- Extender `MainLayout`
- Usar `page_shell` para consistencia
- Usar `lang()` para textos (nunca hardcodear español)
- Usar `esc()` para output dinámico

**NUNCA:**
- Definir arrays de datos en vistas (van en controladores)
- Llamar a métodos de API desde vistas
- Hardcodear URLs o rutas

#### Servicios API

Jerarquía de servicios:

```
FileCachedTotemApiService  ← Cache en archivo (TTL 60s)
    ↓
CachedTotemApiService      ← Memoización por request
    ↓
TotemApiService            ← Cliente HTTP base
```

Si necesitas agregar un método al API:

```php
// 1. Agregar a la interfaz
interface TotemApiInterface
{
    public function newMethod(): array;
}

// 2. Implementar en TotemApiService
public function newMethod(): array
{
    return $this->get('endpoint');
}

// 3. Los decoradores lo heredan automáticamente
```

#### Presenters

Usar cuando hay lógica de transformación de datos:

```php
<?php

declare(strict_types=1);

namespace App\Presenters;

/**
 * Presenter para transformar datos de X para visualización.
 */
final readonly class ExamplePresenter
{
    /**
     * @param array<string, mixed> $data
     */
    public function __construct(private array $data) {}

    /**
     * @return list<array<string, mixed>>
     */
    public function items(): array
    {
        // Transformar $this->data a formato de vista
        return array_map(fn($item) => [
            'title' => $item['name'],
            'image' => ImageAsset::fromArray($item['image'] ?? [])?->url(),
        ], $this->data['items'] ?? []);
    }
}
```

### 3. Rutas

En `app/Config/Routes.php`:

```php
// ✅ CORRECTO: arrays para handler, string para nombre
$routes->get('ruta/amigable', [Controller::class, 'method'], ['as' => 'route_name']);

// ✅ CORRECTO: generar URLs
base_url('ruta/amigable');     // URL completa
route('route_name');           // Usando nombre
site_url('ruta/amigable');     // Con index.php si aplica

// ❌ INCORRECTO
$routes->get('ruta', 'Controller::method');  // string viejo
$routes->get('ruta', ['Controller', 'method']); // array sin class constant
```

### 4. Internacionalización

**Estructura de archivos:**
```
app/Language/
├── es/
│   ├── Common.php      # Textos compartidos
│   ├── Totem.php       # Textos del tótem (mock notices)
│   ├── Collection.php  # Colección
│   ├── School.php      # Escuela
│   └── ...
├── en/
├── fr/
└── pt/
```

**Convenciones:**
- Nombre de archivo en PascalCase (igual que el namespace)
- Claves en snake_case
- Siempre agregar las 4 traducciones (es, en, fr, pt)
- Usar `sprintf()` para valores dinámicos

```php
// ✅ CORRECTO
return [
    'welcome_title' => 'Bienvenido',
    'item_count'    => 'Hay %d ítems',
];

// Uso
lang('File.welcome_title');
sprintf(lang('File.item_count'), $count);
```

### 5. CSS

**NUNCA editar `style.css` directamente.**

**Flujo de trabajo:**
1. Editar en `public/assets/css/src/`
2. Ejecutar `composer build:css`
3. Verificar cambios

**Convenciones de nombrado:**
```css
/* BEM - Bloque__Elemento--Modificador */
.card                    /* bloque */
.card__title            /* elemento */
.card--featured         /* modificador */
.card--featured__title  /* elemento de modificador */
```

**Tokens disponibles en `00-tokens.css`:**
```css
var(--color-brand)           /* #de5928 */
var(--color-brand-dark)      /* #b5451e */
var(--radius-card)           /* 16px */
var(--shadow-card)           /* sombra estándar */
```

### 6. Tests

```bash
# Ejecutar todos los tests
composer test

# Tests específicos
vendor/bin/phpunit tests/unit/Controllers/ExampleControllerTest.php
```

**Estructura de test:**
```php
<?php

namespace Tests\Unit\Controllers;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * @internal
 */
final class ExampleControllerTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    public function testRouteRendersCorrectly(): void
    {
        $result = $this->get('ruta/amigable');
        
        $result->assertStatus(200);
        $result->assertSee('Texto esperado');
    }
}
```

### 7. Commits

Usar conventional commits:

```
feat(controllers): add new domain controller for X
fix(views): correct title escaping in page_shell
refactor(services): extract ApiClient to interface
docs(config): update env template
style(css): fix indentation in tokens file
test(presenters): add unit tests for DatePresenter
chore(deps): update codeigniter4/framework
```

**Ámbitos comunes:** `controllers`, `views`, `services`, `presenters`, `config`, `css`, `i18n`, `tests`, `routes`

## Checklist antes de cambios

### Si vas a agregar un endpoint API nuevo:
- [ ] Agregué método a `TotemApiInterface`
- [ ] Implementé en `TotemApiService`
- [ ] Verifiqué que funciona con cache deshabilitado
- [ ] Los errores quedan en logs estructurados

### Si vas a crear un controlador nuevo:
- [ ] Hereda de `BaseTotemController`
- [ ] Tiene `declare(strict_types=1)`
- [ ] Es `final class`
- [ ] Métodos retornan `string`
- [ ] Rutas registradas en `Routes.php` con nombre
- [ ] Tests básicos creados

### Si vas a crear una vista nueva:
- [ ] Extiende `MainLayout`
- [ ] Usa `page_shell` como contenedor
- [ ] Todos los textos usan `lang()`
- [ ] Output dinámico usa `esc()`
- [ ] No tiene lógica de negocio (solo presentación)

### Si vas a tocar CSS:
- [ ] Edité el archivo fuente en `css/src/`, no `style.css`
- [ ] Ejecuté `composer build:css`
- [ ] Uso las variables de `00-tokens.css`
- [ ] Sigo BEM para nomenclatura

### Si vas a agregar texto:
- [ ] Agregué la clave en los 4 idiomas (es, en, fr, pt)
- [ ] Usé el archivo de idioma apropiado
- [ ] No hardcodeé español en vistas

## Referencias rápidas

| Necesito... | Voy a... |
|-------------|----------|
| Agregar una pantalla nueva | `app/Controllers/`, `app/Config/Routes.php`, `app/Views/totem/` |
| Consumir un endpoint API | `TotemApiInterface`, `TotemApiService` |
| Transformar datos para vista | Crear `ExamplePresenter` en `app/Presenters/` |
| Agregar texto traducible | `app/Language/*/File.php` |
| Estilos nuevos | `public/assets/css/src/screens/` o `shared/` |
| Datos de fallback si API falla | `app/Repositories/FallbackRepository.php` |
| Configuración del totem | `app/Config/Totem.php` + `.env` |
| Verificar salud del sistema | Endpoint `/health` o `HealthController` |
| Cache en archivo | `FileCachedTotemApiService` o `writable/cache/totem/` |
| Logs de API | `writable/logs/log-YYYY-MM-DD.log` |

## Prohibido

1. **NO crear modelos de base de datos.** Este proyecto no usa DB.
2. **NO usar arrays en `route()` o `to()` de router.** Usar nombres de ruta.
3. **NO hardcodear español en vistas.** Usar sistema de idiomas.
4. **NO editar `style.css` directamente.** Usar build system.
5. **NO quitar `declare(strict_types=1)`.** Tipado estricto obligatorio.
6. **NO crear controladores que no hereden de `BaseTotemController`.**
7. **NO acceder API directamente desde vistas o controladores.** Usar servicios.
8. **NO romper la resiliencia offline.** Siempre manejar el caso de API caída con fallback amigable.

## Dudas?

Si algo no está claro:
1. Busca ejemplos existentes en el código (ej: `CollectionController`)
2. Revisa `docs/plans/audit-and-refactor-plan.md` para contexto
3. Pregunta antes de inventar un nuevo patrón
