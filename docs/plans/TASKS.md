# Tareas ejecutables — Auditoría y Mejora del Tótem Interactivo

> Plan detallado: [`docs/plans/audit-and-refactor-plan.md`](./audit-and-refactor-plan.md)
> Rama de trabajo: `dev`
> Convención de commits: conventional commits, un commit por tarea o grupo de tareas relacionadas.

---

## Cómo usar este documento

- Cada tarea tiene un código `F0-T1`, `F1-T3`, etc. (Fase-Tarea).
- Marcar con `[x]` solo cuando la tarea esté implementada, testeada y commiteada en `dev`.
- Si una tarea se bloquea, agregar una nota bajo el ítem con fecha y razón.
- Antes de comenzar una fase, validar que la fase anterior esté completa.

---

## Fase 0 — Fundamentos y seguridad

> Objetivo: estabilizar lo que está roto antes de tocar arquitectura.

### F0-T1 — Corregir variables de entorno
- **Referencia:** Plan §3 Fase 0 → Backend → Corregir variables de entorno.
- **Archivos:** `.env`, `env`, `README.md`, `CLAUDE.md`.
- **Descripción:**
  - Reemplazar `API_BASE_URL` por `TOTEM_API_URL` y `TOTEM_API_KEY`.
  - Documentar ambas variables en `env` y README.
  - Validar que `TotemApiService` las lea correctamente.
- **Criterio de aceptación:**
  - `env` contiene `TOTEM_API_URL` y `TOTEM_API_KEY` documentadas.
  - `.env` local apunta a valores correctos.
  - `composer test` sigue pasando.
- **Commit sugerido:** `fix(config): use TOTEM_API_URL and TOTEM_API_KEY in .env`

### F0-T2 — Remover cliente HTTP muerto de BaseController
- **Referencia:** Plan §3 Fase 0 → Backend → Corregir variables de entorno.
- **Archivos:** `app/Controllers/BaseController.php`.
- **Descripción:** Eliminar `$this->apiClient` y su inicialización con `API_BASE_URL`.
- **Criterio de aceptación:** No queda referencia a `API_BASE_URL` ni `$apiClient` en `BaseController`.
- **Commit sugerido:** `refactor(controllers): remove unused apiClient from BaseController`

### F0-T3 — Corregir bug de cursos en theater_school.php
- **Referencia:** Plan §3 Fase 0 → Backend → Corregir bug crítico en theater_school.php.
- **Archivos:** `app/Views/totem/theater_school.php`.
- **Descripción:** En el bucle `foreach ($courses as $course)`, reemplazar `$section['courseImage']`, `$section['courseTitle']`, `$section['courseTag']`, `$section['courseStart']`, `$section['courseCopy']`, `$section['courseQrUrl']`, `$section['courseQrImage']`, `$section['courseQrLabel']`, `$section['courseContactLabel']`, `$section['courseContact']` por las claves correspondientes de `$course`.
- **Criterio de aceptación:**
  - La vista renderiza cada curso con sus datos propios.
  - Se valida visualmente en `/teatro-escuela`.
- **Commit sugerido:** `fix(views): use $course data inside theater_school course loop`

### F0-T4 — Corregir mapeo de audience_id a clase CSS
- **Referencia:** Plan §3 Fase 0 → Backend → Corregir bug de clase event-card--adult.
- **Archivos:** `app/Controllers/TotemController.php`.
- **Descripción:** Reemplazar el `if/elseif` actual por una expresión `match` que asigne `event-card--national`, `event-card--international`, `event-card--kids`, `event-card--adult` según `audience_id`.
- **Criterio de aceptación:**
  - Cada `audience_id` genera la clase CSS correcta.
  - Tests actualizados o nuevos cubren el mapeo.
- **Commit sugerido:** `fix(controller): correct audience_id to event-card class mapping`

### F0-T5 — Eliminar código muerto inmediato
- **Referencia:** Plan §3 Fase 0 → Backend → Eliminar código muerto inmediato.
- **Archivos:** `app/Controllers/TotemController.php`, `app/Services/TotemApiService.php`.
- **Descripción:**
  - Eliminar `visitsSection()` del controlador.
  - Eliminar `TotemApiService::collectionItem()` si no tiene referencias.
  - Eliminar rama `strlen()` muerta en `excerptMuseumBlockContent()`.
- **Criterio de aceptación:**
  - No hay referencias a los métodos/vías eliminadas.
  - `composer test` y `composer analyse` pasan.
- **Commit sugerido:** `refactor(controller): remove dead code visitsSection, collectionItem and strlen branch`

### F0-T6 — Validar parámetro `from` en language()
- **Referencia:** Plan §3 Fase 0 → Backend → Validar `$from` en `language()`.
- **Archivos:** `app/Controllers/TotemController.php`, `app/Views/totem/language.php`.
- **Descripción:** Validar que `$from` sea una ruta interna válida (regex `^[a-z0-9/\-]+$`). Si no, asignar cadena vacía.
- **Criterio de aceptación:**
  - URLs maliciosas o extrañas en `?from=` no se propagan.
  - Tests de controlador cubren casos válidos e inválidos.
- **Commit sugerido:** `fix(controller): validate from parameter in language action`

### F0-T7 — Proteger credenciales de despliegue
- **Referencia:** Plan §3 Fase 0 → Configuración y seguridad → Proteger credenciales de despliegue.
- **Archivos:** `.deploy/.env.deploy`, `.deploy/deploy.py`, `.gitignore`.
- **Descripción:**
  - Cambiar permisos de `.deploy/.env.deploy` a `600`.
  - Verificar que `.deploy/.env.deploy` esté en `.gitignore`.
  - Agregar validación en `deploy.py` para abortar si el archivo de credenciales tiene permisos débiles.
- **Criterio de aceptación:**
  - `ls -l .deploy/.env.deploy` muestra `-rw-------`.
  - `deploy.py` rechaza ejecutarse si los permisos no son 600.
- **Commit sugerido:** `fix(deploy): restrict credentials file permissions to 600`

### F0-T8 — Configurar timezone y seguridad base
- **Referencia:** Plan §3 Fase 0 → Configuración y seguridad → Seguridad de aplicación.
- **Archivos:** `app/Config/App.php`, `.env`, `env`.
- **Descripción:**
  - Cambiar `appTimezone` a `America/Santiago` (o documentar override en `.env`).
  - Configurar `forceGlobalSecureRequests = true` para producción vía `.env`.
  - Preparar `CSPEnabled` para activación futura (documentar pasos).
- **Criterio de aceptación:**
  - `appTimezone` refleja zona horaria de Chile.
  - `.env` permite sobreescribir `forceGlobalSecureRequests`.
- **Commit sugerido:** `fix(config): set America/Santiago timezone and prepare secure production flags`

### F0-T9 — Asegurar cookie totem_lang
- **Referencia:** Plan §3 Fase 0 → Configuración y seguridad → Seguridad de aplicación.
- **Archivos:** `app/Views/totem/language.php`, `app/Config/Cookie.php`.
- **Descripción:**
  - Al escribir `totem_lang` desde JS, agregar `Secure; SameSite=Lax` (condicional a producción si es necesario).
  - Considerar setcookie PHP complementario con flags de `Config\Cookie`.
- **Criterio de aceptación:**
  - La cookie incluye `SameSite=Lax` y `Secure` en producción.
  - El splash sigue pudiendo leer el idioma activo.
- **Commit sugerido:** `fix(security): set Secure and SameSite flags on totem_lang cookie`

### F0-T10 — Instalar PHPStan y PHP-CS-Fixer
- **Referencia:** Plan §3 Fase 0 → Tooling.
- **Archivos:** `composer.json`, `phpstan.neon.dist`, `.php-cs-fixer.dist.php`.
- **Descripción:**
  - Instalar `phpstan/phpstan` y `friendsofphp/php-cs-fixer` como dev dependencies.
  - Configurar PHPStan nivel 8.
  - Configurar PHP-CS-Fixer con reglas PSR-12.
  - Agregar scripts `lint`, `analyse`, `format`.
- **Criterio de aceptación:**
  - `composer analyse` ejecuta PHPStan.
  - `composer lint` ejecuta PHP-CS-Fixer en modo dry-run.
  - `composer format` ejecuta PHP-CS-Fixer en modo fix.
- **Commit sugerido:** `chore(tools): install phpstan and php-cs-fixer`

### F0-T11 — Actualizar CodeIgniter 4.7.2 → 4.7.3
- **Referencia:** Plan §3 Fase 0 → Tooling.
- **Archivos:** `composer.json`, `composer.lock`.
- **Descripción:** Ejecutar `composer update codeigniter4/framework` a la última patch version 4.7.x.
- **Criterio de aceptación:**
  - `composer outdated --direct` no muestra patch pendiente de CI4.
  - `composer test` sigue pasando.
- **Commit sugerido:** `chore(deps): update codeigniter4/framework to 4.7.3`

### F0-T12 — Crear pipeline CI/CD mínima
- **Referencia:** Plan §3 Fase 0 → Tooling.
- **Archivos:** `.github/workflows/ci.yml`.
- **Descripción:**
  - Workflow que instala dependencias, ejecuta `composer lint`, `composer analyse` y `composer test` en PHP 8.2.
  - Ejecutar en push y PR a `dev`.
- **Criterio de aceptación:**
  - El workflow se dispara en GitHub.
  - Todos los jobs pasan en la primera ejecución.
- **Commit sugerido:** `ci(github): add lint, analyse and test workflow`

### F0-T13 — Limpiar tests y hacer composer test limpio
- **Referencia:** Plan §3 Fase 0 → Tests.
- **Archivos:** `composer.json`, `phpunit.xml.dist`, `tests/_support/`.
- **Descripción:**
  - Cambiar script `test` a `phpunit --no-coverage` o instalar PCOV/Xdebug.
  - Eliminar plantillas de ejemplo en `tests/_support/` que no se usan.
  - Actualizar `phpunit.xml.dist` si es necesario.
- **Criterio de aceptación:**
  - `composer test` termina con exit code 0.
  - No quedan clases `Example*` en `tests/_support/`.
- **Commit sugerido:** `test(tooling): clean up example support files and make composer test pass cleanly`

### F0-T14 — Tests unitarios para TotemApiService
- **Referencia:** Plan §3 Fase 0 → Tests.
- **Archivos:** `tests/unit/Services/TotemApiServiceTest.php`.
- **Descripción:**
  - Mockear `CURLRequest`.
  - Probar respuesta 200 con `data`, respuesta 200 sin `data`, respuesta 404, excepción de red, JSON inválido.
- **Criterio de aceptación:**
  - Todos los casos de éxito y error están cubiertos.
  - Los tests pasan.
- **Commit sugerido:** `test(services): add unit tests for TotemApiService error handling`

### F0-T15 — Tests de controlador para acciones críticas
- **Referencia:** Plan §3 Fase 0 → Tests.
- **Archivos:** `tests/unit/Controllers/TotemControllerTest.php`.
- **Descripción:**
  - Tests para `language()`, `mainMenu()`, `theaterSchool()` y `billboard()`.
  - Mockear `TotemApiService` si es necesario.
- **Criterio de aceptación:**
  - Cobertura mínima de las acciones corregidas en Fase 0.
  - Tests pasan.
- **Commit sugerido:** `test(controllers): add controller tests for language, menu, school and billboard`

---

## Fase 1 — Arquitectura backend

> **Estado:** completada. Todos los cambios están en `dev` y el kiosko sigue navegable.

### F1-T1 — Crear BaseTotemController
- [x] **Completado.** Creado `BaseTotemController` con `pageMeta`, `shellNav`, `menuItem` y `render`; heredado por todos los controladores de dominio.
- **Commit:** `refactor(controllers): add BaseTotemController with shared render helpers`

### F1-T2 — Crear MenuBuilder y NavBuilder
- [x] **Completado.** `MenuBuilder` y `NavBuilder` extraídos a `app/Services/`; `BaseTotemController` delega en ellos.
- **Commit:** `refactor(services): extract MenuBuilder and NavBuilder`

### F1-T3 — Dividir TotemController en controladores de dominio
- [x] **Completado.** Creados `MainController`, `MuseumController`, `CollectionController`, `SchoolController`, `BillboardController` y `FriendsController`; `TotemController.php` eliminado; `Routes.php` actualizado.
- **Commit:** `refactor(controllers): split TotemController into domain controllers`

### F1-T4 — Crear TotemApiInterface y refactorizar TotemApiService
- [x] **Completado.** Creada `TotemApiInterface`; `TotemApiService` implementa la interfaz, acepta `CURLRequest` por constructor y loguea status no-2xx / JSON inválido.
- **Commit:** `refactor(services): add TotemApiInterface, refactor TotemApiService and add CachedTotemApiService`

### F1-T5 — Crear CachedTotemApiService
- [x] **Completado.** Creado `CachedTotemApiService` decorador con memoización por request; registrado en `Config\Services::totemApi()`.
- **Commit:** `refactor(services): add TotemApiInterface, refactor TotemApiService and add CachedTotemApiService`

### F1-T6 — Crear presenters de dominio
- [x] **Completado.** Creados `SchoolPresenter`, `BillboardPresenter`, `MuseumTodayPresenter` y `DatePresenter`; `getMonthName()` reemplazado por `IntlDateFormatter`.
- **Commit:** `refactor(presenters): add School, Billboard, MuseumToday and Date presenters`

### F1-T7 — Extraer datos de contingencia a repositorios/config
- [x] **Completado.** Creados `Config\Totem`, `SchoolFallbackRepository`, `BillboardFallbackRepository` y `MuseumFallbackRepository`; datos estáticos eliminados de controladores.
- **Commit:** `refactor(fallback): extract static mock data to fallback repositories and Totem config`

### F1-T8 — Crear Enums y Value Objects
- [x] **Completado.** Creados `SchoolCategory`, `Audience`, `ImageAsset` y `SlugResolver`; IDs mágicos eliminados de controladores.
- **Commit:** `refactor(domain): add SchoolCategory and Audience enums, ImageAsset and SlugResolver`

### F1-T9 — Limpiar rutas obsoletas de colección
- [x] **Completado.** Eliminada ruta/vista de payasos; añadido `set404Override('App\Controllers\MainController::notFound')` con vista multiidioma.
- **Commit:** `refactor(routes): remove obsolete collection routes and add friendly 404 handler`

### F1-T10 — Tests de integración para controladores
- [x] **Completado.** Tests por dominio en `tests/unit/Controllers/`; tests unitarios para presenters y `CachedTotemApiService`.
- **Commits:**
  - `test(controllers): add integration tests for domain controllers`
  - `test(presenters): add unit tests for domain presenters`
  - `test(services): add CachedTotemApiService tests`

---

## Fase 2 — Vistas y componentes

### F2-T1 — Mover lógica hardcodeada de vistas a backend
- **Referencia:** Plan §3 Fase 2 → Lógica de vistas.
- **Archivos:** `app/Views/totem/collection_main.php`, `app/Controllers/CollectionController.php`.
- **Descripción:** Mover array `$sections` de `collection_main.php` al controlador/presenter.
- **Criterio de aceptación:**
  - `collection_main.php` solo itera datos recibidos.
  - No hay rutas ni imágenes hardcodeadas en la vista.
- **Commit sugerido:** `refactor(views): move collection_main sections data to controller`

### F2-T2 — Centralizar configuración de locales
- [x] **Completado.** Creado `app/Helpers/locale_helper.php` con `totem_locales()` y `totem_locale_codes()`. Actualizadas vistas `splash.php`, `language.php` y `MainLayout.php`. Agregadas claves `locale_*` en archivos de idioma.
- **Commit:** `refactor(i18n): centralize supported locales in helper`

### F2-T3 — Internacionalizar textos faltantes
- **Referencia:** Plan §3 Fase 2 → Lógica de vistas.
- **Archivos:** `app/Language/*/Totem.php`, `app/Views/totem/language.php`, `app/Views/layouts/MainLayout.php`.
- **Descripción:**
  - Nombres de idioma en archivos de idioma.
  - Fallback del `<title>`.
- **Criterio de aceptación:**
  - No hay textos en español hardcodeados en vistas.
  - Claves existen en los 4 idiomas.
- **Commit sugerido:** `fix(i18n): add missing language keys for language selector and title fallback`

### F2-T4 — Reemplazar hack de escape en page_shell.php
- **Referencia:** Plan §3 Fase 2 → Lógica de vistas.
- **Archivos:** `app/Views/totem/partials/page_shell.php`, `app/Helpers/title_helper.php`.
- **Descripción:**
  - Crear helper `safe_title()` con whitelist estricta (`<br>`, `<strong>`).
  - O separar título visual/semántico.
- **Criterio de aceptación:**
  - No se reintroduce HTML después de `esc()` de forma insegura.
  - Tests de helper verifican whitelist.
- **Commit sugerido:** `fix(security): replace title escape hack with safe_title helper`

### F2-T5 — Componentizar iconos SVG
- **Referencia:** Plan §3 Fase 2 → Componentes reutilizables.
- **Archivos:** `app/Views/totem/partials/icons/*.php`, `app/Views/totem/partials/topbar.php`.
- **Descripción:**
  - Crear partials para iconos.
  - Refactorizar `topbar.php` para usar nombres de icono en lugar de strings mágicos.
- **Criterio de aceptación:**
  - No hay SVG inline masivo en `topbar.php`.
  - Se pueden agregar iconos nuevos fácilmente.
- **Commit sugerido:** `refactor(views): extract SVG icons to reusable partials`

### F2-T6 — Refactorizar card.php y collection_band.php
- **Referencia:** Plan §3 Fase 2 → Componentes reutilizables.
- **Archivos:** `app/Views/totem/partials/card.php`, `app/Views/totem/partials/collection_band.php`, `app/Views/totem/partials/collection_pill.php`.
- **Descripción:**
  - Unificar markup enabled/disabled en `card.php`.
  - Crear `collection_pill.php` para eliminar duplicación.
- **Criterio de aceptación:**
  - Reducción de duplicación medible en líneas.
  - Visualmente idéntico.
- **Commit sugerido:** `refactor(views): deduplicate card and collection_band markup`

### F2-T7 — Extraer scripts inline a módulos JS
- **Referencia:** Plan §3 Fase 2 → JavaScript.
- **Archivos:** `public/assets/js/splash.js`, `public/assets/js/language-selector.js`, `public/assets/js/school-modal.js`, `public/assets/js/collection-main.js`, vistas afectadas.
- **Descripción:**
  - Mover cada script inline a su archivo.
  - Registrar funciones de cleanup para cada módulo.
- **Criterio de aceptación:**
  - Ninguna vista contiene `<script>` inline con lógica.
  - Los módulos se desmontan correctamente al navegar.
- **Commit sugerido:** `refactor(js): extract inline scripts to external modules with cleanup`

### F2-T8 — Implementar registry de cleanup en app.js
- **Referencia:** Plan §3 Fase 2 → JavaScript.
- **Archivos:** `public/assets/js/app.js`.
- **Descripción:**
  - `window.__totemCleanup` array de funciones.
  - Ejecutar todos los cleanup antes de `commitFetchedPage`.
- **Criterio de aceptación:**
  - Navegación repetida no acumula listeners/intervalos.
  - Validado manualmente con `getEventListeners` en DevTools.
- **Commit sugerido:** `fix(js): add cleanup registry to prevent memory leaks in SPA navigation`

### F2-T9 — Mejorar escape de URLs en vistas
- **Referencia:** Plan §3 Fase 2 → JavaScript.
- **Archivos:** `app/Views/totem/partials/card.php`, `app/Views/totem/billboard.php`.
- **Descripción:** Usar `esc(..., 'url')` o escapar la URL completa en atributos `href`/`src`.
- **Criterio de aceptación:**
  - URLs con caracteres especiales funcionan correctamente.
  - No hay XSS por URLs.
- **Commit sugerido:** `fix(views): use url context escaping for image and detail links`

### F2-T10 — Gestionar vistas placeholder
- **Estado:** En progreso — partial creado, claves de idioma agregadas, vistas actualizadas.
- **Notas:**
  - Se mantienen los mocks para la presentación del lunes 15/06.
  - Las vistas vacías que forman parte de la navegación NO se eliminan; serán pobladas con los diseños de listados próximamente.
  - Vistas actualizadas: `collection_puppets_exhibit.php`, `collection_masks_exhibit.php`, `museum_building.php`, `museum_institution.php`, `extension_contact.php`, `comic_history_post.php`, `collection_item_detail.php`.
- **Referencia:** Plan §3 Fase 2 → Vistas placeholder.
- **Archivos:** `app/Views/totem/partials/mock_notice.php`, vistas placeholder.
- **Descripción:**
  - Crear partial `mock_notice.php`.
  - Reemplazar textos de placeholder por el partial + claves de idioma.
  - Eliminar vistas vacías no necesarias.
- **Criterio de aceptación:**
  - Placeholders visibles son consistentes y multilenguaje.
- **Commit sugerido:** `refactor(views): standardize mock notices with partial and i18n`

---

## Fase 3 — CSS design system

### F3-T1 — Consolidar tokens en 00-tokens.css
- **Referencia:** Plan §3 Fase 3 → Tokens.
- **Archivos:** `public/assets/css/src/00-tokens.css`, `app/Views/layouts/MainLayout.php`.
- **Descripción:**
  - Mover `--paper-texture` de inline a tokens.
  - Agregar tokens de surface, border, shadow, typography, z-index y touch target.
- **Criterio de aceptación:**
  - No hay variables inline en `MainLayout.php`.
  - Todos los tokens nuevos se usan en al menos un parcial.
- **Commit sugerido:** `refactor(css): consolidate design tokens in 00-tokens.css`

### F3-T2 — Eliminar CSS muerto confirmado
- **Referencia:** Plan §3 Fase 3 → Limpieza de código muerto.
- **Archivos:** `public/assets/css/src/shared/hero.css`, `public/assets/css/src/screens/detail.css`, `public/assets/css/src/screens/section.css`, `public/assets/css/src/screens/school.css`, `public/assets/css/src/screens/language.css`, `public/assets/css/src/screens/menu.css`, `public/assets/css/src/shared/utils.css`, `bin/build-css.sh`.
- **Descripción:**
  - Eliminar archivos y bloques muertos.
  - Actualizar `bin/build-css.sh` si es necesario.
- **Criterio de aceptación:**
  - `style.css` reduce tamaño en ≥25 %.
  - Ninguna vista afectada visualmente.
- **Commit sugerido:** `refactor(css): remove dead CSS files and unused blocks`

### F3-T3 — Crear componentes base .card y .panel
- **Referencia:** Plan §3 Fase 3 → Componentes base.
- **Archivos:** `public/assets/css/src/shared/components.css`, parciales afectados.
- **Descripción:**
  - Crear `.card` y `.panel`.
  - Reemplazar tarjetas similares con modifiers.
- **Criterio de aceptación:**
  - Consistencia visual.
  - Reducción de duplicación CSS.
- **Commit sugerido:** `refactor(css): add card and panel base components`

### F3-T4 — Unificar nomenclatura de botón de idioma
- **Referencia:** Plan §3 Fase 3 → Nomenclatura y consistencia.
- **Archivos:** `app/Controllers/MainController.php` o `NavBuilder`, `public/assets/js/app.js`, `public/assets/css/src/shared/header.css`.
- **Descripción:** Decidir entre `pill-button--lang` o `pill-button--language` y aplicar en todos lados.
- **Criterio de aceptación:**
  - Un solo nombre en controlador, JS y CSS.
  - Botón de idioma estilizado consistentemente.
- **Commit sugerido:** `fix(css): unify language button modifier class`

### F3-T5 — Corregir selectores sin estilos
- **Referencia:** Plan §3 Fase 3 → Nomenclatura y consistencia.
- **Archivos:** `app/Views/totem/comic_history_main.php`, `public/assets/css/src/screens/section.css`, `app/Views/totem/partials/button.php`.
- **Descripción:**
  - Definir estilos para `.collection-heading__eyebrow/copy` o eliminar elementos.
  - Decidir si `partials/button.php` se usa o se elimina.
- **Criterio de aceptación:**
  - Todos los selectores usados en vistas tienen estilos definidos.
- **Commit sugerido:** `fix(css): add missing styles or remove unused selectors`

### F3-T6 — Unificar responsive bajo @container kiosk
- **Referencia:** Plan §3 Fase 3 → Responsive.
- **Archivos:** `public/assets/css/src/screens/section.css`, `public/assets/css/src/screens/museum-today.css`, `public/assets/css/src/screens/language.css`, `public/assets/css/src/99-responsive.css`.
- **Descripción:** Migrar `@media (max-width: ...)` a `@container kiosk (...)`.
- **Criterio de aceptación:**
  - No quedan media queries sueltas en parciales de pantalla.
  - Visualmente idéntico en 1080×1920.
- **Commit sugerido:** `refactor(css): migrate media queries to container queries`

### F3-T7 — Mejorar build CSS
- **Referencia:** Plan §3 Fase 3 → Build pipeline.
- **Archivos:** `bin/build-css.sh`, `package.json` (opcional), `.github/workflows/ci.yml`.
- **Descripción:**
  - Validación de sintaxis antes de concatenar.
  - Opcional: PostCSS + autoprefixer + cssnano + source map.
  - Agregar compilación CSS al CI.
- **Criterio de aceptación:**
  - `composer build:css` falla si hay error de sintaxis.
  - `style.css` compilado es más pequeño.
- **Commit sugerido:** `chore(build): improve CSS build pipeline with syntax check and minification`

### F3-T8 — Garantizar accesibilidad táctil y reduced-motion
- **Referencia:** Plan §3 Fase 3 → Accesibilidad.
- **Archivos:** Parciales CSS con targets <44px o animaciones sin fallback.
- **Descripción:**
  - Targets táctiles ≥44px.
  - `prefers-reduced-motion` y `totem-animations-disabled` consistentes.
- **Criterio de aceptación:**
  - No quedan targets <44px.
  - Animaciones respetan preferencias del usuario.
- **Commit sugerido:** `fix(css): ensure touch targets and reduced motion support`

### F3-T9 — Validación visual en kiosko
- **Referencia:** Plan §3 Fase 3 → Tests.
- **Archivos:** N/A (validación manual).
- **Descripción:** Recorrer todas las rutas en viewport 1080×1920 o hardware real.
- **Criterio de aceptación:**
  - Capturas comparativas o checklist firmado.
  - No hay regresiones visuales.
- **Commit sugerido:** `docs(css): add visual validation checklist for design system`

---

## Fase 4 — Observabilidad, robustez y marcha blanca

### F4-T1 — Logs estructurados de llamadas API
- **Referencia:** Plan §3 Fase 4 → Observabilidad.
- **Archivos:** `app/Services/TotemApiService.php` o decorador.
- **Descripción:**
  - Loguear endpoint, tiempo de respuesta, status, error.
  - Estructura JSON consistente.
- **Criterio de aceptación:**
  - Cada llamada API deja entrada en log.
  - Logs parseables.
- **Commit sugerido:** `feat(monitoring): add structured logging for API calls`

### F4-T2 — Endpoint de health check
- **Referencia:** Plan §3 Fase 4 → Observabilidad.
- **Archivos:** `app/Controllers/HealthController.php`, `app/Config/Routes.php`.
- **Descripción:** Crear `/health` que verifique conectividad con la API y estado básico.
- **Criterio de aceptación:**
  - `/health` retorna JSON con status.
  - Tests de controlador cubren éxito y fallo de API.
- **Commit sugerido:** `feat(monitoring): add /health endpoint`

### F4-T3 — Definir estrategia de fallback offline
- **Referencia:** Plan §3 Fase 4 → Robustez offline.
- **Archivos:** `app/Config/Totem.php`, fallback repositories.
- **Descripción:**
  - Documentar y consolidar fallback por dominio.
  - Pantalla amigable si no hay datos.
- **Criterio de aceptación:**
  - Kiosko funciona sin API usando fallback.
  - Documentación clara.
- **Commit sugerido:** `docs(ops): document offline fallback strategy`

### F4-T4 — Cachear respuestas API en archivo
- **Referencia:** Plan §3 Fase 4 → Robustez offline.
- **Archivos:** `app/Services/CachedTotemApiService.php` o nuevo `FileCachedTotemApiService.php`.
- **Descripción:**
  - Cachear respuestas en `writable/cache/totem/` con TTL 60s.
  - Usar cache si la API falla.
- **Criterio de aceptación:**
  - Corte de red no deja pantallas en blanco (usa cache reciente).
  - Tests de tolerancia a fallos pasan.
- **Commit sugerido:** `feat(services): add file-based API cache for offline resilience`

### F4-T5 — Actualizar README.md
- **Referencia:** Plan §3 Fase 4 → Documentación.
- **Archivos:** `README.md`.
- **Descripción:** Reflejar estructura real, variables de entorno, convenciones y build.
- **Criterio de aceptación:** README actualizado y útil para nuevos desarrolladores.
- **Commit sugerido:** `docs(readme): update project structure and setup instructions`

### F4-T6 — Crear AGENTS.md
- **Referencia:** Plan §3 Fase 4 → Documentación.
- **Archivos:** `AGENTS.md`.
- **Descripción:** Convenciones específicas para agentes de código.
- **Criterio de aceptación:** Documento con convenciones de commits, arquitectura, CSS y tests.
- **Commit sugerido:** `docs(agents): add AGENTS.md with coding conventions`

### F4-T7 — Crear manual de soporte de operaciones
- **Referencia:** Plan §3 Fase 4 → Documentación.
- **Archivos:** `docs/ops/support-manual.md`.
- **Descripción:** Encendido/apagado, reinicio rápido, limpieza de caché de Fully Kiosk, reinicio de servidores.
- **Criterio de aceptación:** Manual breve y accionable para soporte no técnico.
- **Commit sugerido:** `docs(ops): add support manual`

### F4-T8 — Evaluar y mejorar despliegue
- **Referencia:** Plan §3 Fase 4 → Despliegue.
- **Archivos:** `.deploy/deploy.py`, `.deploy/sync-css.py`.
- **Descripción:**
  - Evaluar migración a SFTP/FTPS o CI/CD con secretos.
  - Eliminar `sync-css.py` si es legacy.
  - Limpiar `totem-prod.zip` del workspace.
- **Criterio de aceptación:**
  - Proceso de despliegue documentado.
  - Artefactos innecesarios eliminados.
- **Commit sugerido:** `chore(deploy): evaluate secure deployment and remove legacy scripts`

### F4-T9 — Tests finales y pruebas en hardware
- **Referencia:** Plan §3 Fase 4 → Tests finales.
- **Archivos:** Suite completa de tests.
- **Descripción:**
  - Ejecutar suite completa.
  - Pruebas de carga/rendimiento básicas.
  - Pruebas de tolerancia a fallas de red.
  - Pruebas en hardware Fully Kiosk 1080×1920.
- **Criterio de aceptación:**
  - `composer test`, `composer lint`, `composer analyse` pasan.
  - Cobertura ≥60 %.
  - Checklist de pruebas en hardware firmado.
- **Commit sugerido:** `test(e2e): finalize hardware validation and coverage checks`

---

## Historial de cambios

| Fecha | Autor | Cambio |
|-------|-------|--------|
| 2026-06-12 | Kimi Code | Creación del plan y sistema de tareas. |
