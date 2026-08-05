# AGENTS.md — Convenciones para `teatromuseo-totem-ci4`

## Alcance

El tótem es una aplicación CodeIgniter 4 stateless para una pantalla táctil
vertical de 1080×1920. Se ejecuta localmente en el puerto `8186` y consume el
Hub `teatromuseo-api` (`8180`) mediante `/api/v1/totem/*` y el header
`X-Totem-Key`.

- No tiene base de datos propia.
- No emite ni valida JWTs.
- La API se consume server-side; nunca desde las vistas con JavaScript.
- La caché y los datos de fallback son parte del funcionamiento offline y no
  deben eliminarse al refactorizar.
- La producción se despliega por FTP con los scripts de `.deploy/`; no usar
  Docker como mecanismo de despliegue de producción.

Lee `CLAUDE.md` y `TASKS.md` antes de modificar código. Revisa primero
`git status --short --branch` y conserva cambios ajenos.

## Arquitectura

```text
Routes → Controllers → TotemApiInterface → TotemApiService
                         ↓
              CachedTotemApiService → FileCachedTotemApiService (opcional)
Controllers → Presenters → Views
                       ↘ Repositories de fallback
```

### Ubicaciones importantes

- `app/Controllers/BaseTotemController.php` — helpers comunes de renderizado,
  navegación y acceso al servicio API.
- `app/Controllers/` — `Main`, `Collection`, `Museum`, `School`, `Billboard`,
  `Friends` y `Health`.
- `app/Services/TotemApiInterface.php` — contrato del cliente API.
- `app/Services/TotemApiService.php` — CURL, header `X-Totem-Key`, normalización,
  logs estructurados y manejo seguro de errores.
- `app/Services/CachedTotemApiService.php` — memoización por request.
- `app/Services/FileCachedTotemApiService.php` — caché persistente opcional.
- `app/Presenters/` — transformación de datos para las vistas.
- `app/Repositories/*FallbackRepository.php` — datos de contingencia.
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

- Si se agrega un endpoint, agregar primero el método a
  `TotemApiInterface` y luego implementarlo en `TotemApiService`; los
  decoradores deben seguir funcionando.
- Mantener el logging de API: timestamp, endpoint, duración en milisegundos,
  status, `success` y error cuando corresponda.
- Usar un Presenter cuando exista transformación no trivial para la vista.
- Mantener los fallback de `School`, `Billboard` y `Museum` funcionando cuando
  la API devuelve error o datos vacíos.
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
TOTEM_API_URL = 'http://localhost:8180/api/v1/totem'
TOTEM_API_KEY = '<clave configurada en el Hub>'
TOTEM_ENABLE_FILE_CACHE = true
TOTEM_CACHE_TTL_SECONDS = 60
```

El endpoint `/health` devuelve JSON y `503` cuando la API no es alcanzable.
La caché persistente vive en `writable/cache/totem/`; para limpiarla, usar
`rm writable/cache/totem/*.cache` únicamente dentro de ese directorio.

## Checklist antes de cerrar un cambio

- [ ] El controlador conserva la separación Controller → Service → Presenter → View.
- [ ] La API tiene contrato en `TotemApiInterface` y fallback cuando aplica.
- [ ] Los textos nuevos existen en `es`, `en`, `fr` y `pt`.
- [ ] Los enlaces usan `base_url()` y el output dinámico usa `esc()`.
- [ ] CSS fuente compilado con `composer build:css` si hubo cambios visuales.
- [ ] Tests y `composer quality` ejecutados.
- [ ] No se añadieron secretos, DB propia ni llamadas API desde el navegador.

## Prohibido

1. Crear modelos, migraciones o una base de datos local para el tótem.
2. Emitir/validar JWTs o duplicar la autenticación del Hub.
3. Acceder directamente a la API desde vistas o JavaScript.
4. Eliminar la caché o el fallback como "simplificación".
5. Editar `style.css` compilado o hardcodear español.
6. Omitir `declare(strict_types=1)` en código nuevo.
