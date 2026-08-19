# AGENTS.md — Convenciones para `teatromuseo-totem-ci4`

## Alcance

El tótem es una aplicación CodeIgniter 4 stateless para una pantalla táctil
vertical de 1080×1920. Se ejecuta localmente en el puerto `8186` y consume el
BFF `teatromuseo-bff` (`8188`) mediante su seam `public-read` y una clave
dedicada `TOTEM_BFF_API_KEY` enviada como `X-App-Key`.

- No tiene base de datos propia.
- No emite ni valida JWTs.
- La API se consume server-side; nunca desde las vistas con JavaScript.
- El cliente conserva respuestas fresh y stale para tolerar caídas del BFF,
  pero no inventa contenido: una fuente no disponible se muestra como tal.
- La producción se despliega por FTP con los scripts de `.deploy/`; no usar
  Docker como mecanismo de despliegue de producción.

Lee `CLAUDE.md` y `TASKS.md` antes de modificar código. Revisa primero
`git status --short --branch` y conserva cambios ajenos.

## Arquitectura

```text
Routes → Controllers → BffTotemClient → BFF public-read
Controllers → Presenters → Views
                       ↘ TotemApiResult (fresh/stale/unavailable)
```

### Ubicaciones importantes

- `app/Controllers/BaseTotemController.php` — helpers comunes de renderizado,
  navegación y acceso al cliente BFF.
- `app/Controllers/` — `Main`, `Collection`, `Museum`, `School`, `Billboard`,
  `Friends` y `Health`.
- `app/Services/BffTotemClient.php` — CURL server-side, `X-App-Key`,
  normalización, logs estructurados, reintentos y caché fresh/stale.
- `app/Services/TotemApiResult.php` — resultado tipado con estados
  `fresh`, `stale` y `unavailable`.
- `app/Presenters/` — transformación de datos para las vistas.
- No hay `app/Repositories/` ni `app/Data/*.json`: no agregar mocks ni
  repositorios de contenido inventado.
- `app/Config/Routes.php` — rutas del tótem y `/health`.
- `app/Config/Totem.php` — feature flags y TTL de caché.

## Reglas de código

### Controladores

- Usar `declare(strict_types=1);`, `final class` y docblocks coherentes.
- Heredar de `BaseTotemController` y retornar la vista como `string`.
- Orquestar servicios, presenters y metadatos; no implementar lógica de negocio.
- Usar `$this->request` para entrada HTTP; nunca `$_GET` ni `$_POST` directamente.
- Usar `$this->totemApi()`; no crear clientes CURL en controladores.
- No usar `echo` ni `print`.

### Servicios y presenters

- Si se agrega una lectura, agregar un método explícito a
  `BffTotemClient` y devolver `TotemApiResult`; distinguir una respuesta
  vacía confirmada de una fuente no disponible.
- Mantener el logging de BFF: timestamp, endpoint, duración en milisegundos,
  status, `success` y error cuando corresponda.
- Usar un Presenter cuando exista transformación no trivial para la vista.
- No introducir fallback de contenido: los estados `unavailable` y `stale`
  deben llegar a la vista de forma explícita.
- No colocar reglas de negocio en las vistas.

### Vistas

- Extender `layouts/MainLayout` y usar el partial `totem/partials/page_shell`
  cuando corresponda.
- Usar `lang()` para todo texto visible; no hardcodear español.
- Escapar output dinámico con `esc()` y generar enlaces con `base_url()`.
- No llamar servicios API ni definir datasets de negocio en una vista.

### Rutas

Este proyecto conserva handlers CI4 en formato string. Mantener el estilo
existente:

```php
$routes->get('museo/coleccion', 'CollectionController::collectionMain');
$routes->get('health', 'HealthController::index');
$routes->addRedirect('visitas-guiadas', 'extension');
```

Para enlaces internos usar `base_url('ruta')`, como hacen `MenuBuilder`,
`NavBuilder` y las vistas existentes. No usar arrays como destino de
`route()`/`to()` ni hardcodear el host.

## Internacionalización

- Los idiomas soportados son `es`, `en`, `fr` y `pt`, resueltos por el sistema
  de idioma de CI4 y la cookie `totem_lang`.
- Los archivos viven en `app/Language/{es,en,fr,pt}/` y usan nombres PascalCase
  (`Collection.php`, `MuseumInfo.php`, etc.).
- Las claves se escriben en `snake_case` y se consultan como
  `lang('Collection.techniques_title')`.
- Toda nueva clave visible debe agregarse en los cuatro idiomas.
- Usar `sprintf()` para interpolar valores dinámicos.

## Assets y CSS

- Para assets nuevos, usar nombres descriptivos en `lowercase-kebab-case.webp` y
  una carpeta funcional estable dentro de `public/assets/img/`.
- No editar `public/assets/css/style.css` directamente; es un archivo generado.
- Editar `public/assets/css/src/` y ejecutar:

  ```bash
  composer build:css
  ```

- Usar los tokens existentes (`--accent`, `--radius-card`, `--shadow-card`,
  `--touch-target-min`) y la convención BEM para componentes nuevos.

## Desarrollo, tests y despliegue

```bash
php spark serve --port 8186

composer test
composer test:unit
composer lint             # dry-run de PHP-CS-Fixer
composer analyse         # PHPStan nivel 8
composer format           # aplica PHP-CS-Fixer
composer quality

composer build:css
python3 .deploy/sync-css.py
python3 .deploy/deploy.py
```

La configuración local mínima es:

```dotenv
app.baseURL = 'http://localhost:8186/'
TOTEM_BFF_BASE_URL = 'http://localhost:8188'
TOTEM_BFF_API_KEY = '<clave dedicada configurada en el BFF>'
TOTEM_CACHE_TTL_SECONDS = 60
TOTEM_STALE_TTL_SECONDS = 86400
```

El endpoint `/health` consulta `/ready` del BFF, devuelve JSON y `503` cuando
el BFF no es alcanzable. La caché se gestiona mediante el servicio de caché de
CI4; no crear una segunda caché de archivos para este flujo.

## Checklist antes de cerrar un cambio

- [ ] El controlador conserva la separación Controller → Service → Presenter → View.
- [ ] El cliente usa `BffTotemClient` y propaga `fresh`, `stale` o `unavailable`.
- [ ] Los textos nuevos existen en `es`, `en`, `fr` y `pt`.
- [ ] Los enlaces usan `base_url()` y el output dinámico usa `esc()`.
- [ ] CSS fuente compilado con `composer build:css` si hubo cambios visuales.
- [ ] Tests y `composer quality` ejecutados.
- [ ] No se añadieron secretos, DB propia ni llamadas API desde el navegador.

## Prohibido

1. Crear modelos, migraciones o una base de datos local para el tótem.
2. Emitir/validar JWTs o duplicar la autenticación del Hub.
3. Acceder directamente a la API desde vistas o JavaScript.
4. Ocultar una caída del BFF simulando contenido o convirtiéndola en un vacío confirmado.
5. Editar `style.css` compilado o hardcodear español.
6. Omitir `declare(strict_types=1)` en código nuevo.
