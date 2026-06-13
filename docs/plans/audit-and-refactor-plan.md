# Plan de Implementación — Auditoría y Mejora del Tótem Interactivo

> **Contexto:** Aplicación CodeIgniter 4 para kiosko táctil vertical (1080×1920) del Teatro Museo. Stateless, consume API propia, sin base de datos.
> **Decisión del equipo:** Refactor profundo (4–6 semanas), progresivo sin romper funcionalidad actual, priorizando robustez y calidad técnica sobre nuevas features.
> **Rama de trabajo:** `dev` (sin ramas feature adicionales). Cada fase se entrega con commits convencionales siguiendo el patrón actual del proyecto.
> **Ubicación del plan:** `docs/plans/audit-and-refactor-plan.md`.
> **Sistema de tareas:** `docs/plans/TASKS.md` con tareas ejecutables referenciadas a este plan.

---

## 1. Diagnóstico ejecutivo

### 1.1 Problemas críticos encontrados

| Área | Problema | Impacto |
|------|----------|---------|
| **Backend PHP** | `TotemController` es un *God Controller* de ~852 líneas con 38 métodos que mezcla routing, lógica de negocio, presentación, mocks y utilidades. | Difícil de mantener, testear y escalar. Cualquier cambio en una pantalla puede afectar a otras. |
| **Backend PHP** | `TotemApiService::get()` traga cualquier error (4xx, 5xx, JSON inválido, red) y retorna `[]` sin distinción. | Imposible diagnosticar fallas en producción; el kiosko puede mostrar datos de fallback sin que nadie se entere. |
| **Backend PHP** | Datos de contingencia/mocks hardcodeados en el controlador (profesores, alumnos, obras, slugs, textos, imágenes). | Contenido no actualizable sin desplegar código; difícil de testear. |
| **Backend PHP** | `BaseController` crea un cliente HTTP con `API_BASE_URL` que nadie usa. Código muerto. | Confusión y dependencia de variable de entorno no documentada. |
| **Backend PHP** | Múltiples bugs lógicos: clase `event-card--adult` nunca se aplica; `visitsSection()` nunca se usa; `collectionTechnique()` hace 2 llamadas API innecesarias. | Comportamiento incorrecto o ineficiente. |
| **Vistas PHP** | Bug funcional en `theater_school.php`: el bucle de cursos usa `$section['...']` en vez de `$course['...']`, repitiendo el mismo contenido. | **La pantalla de Teatro Escuela muestra cursos duplicados/erróneos.** |
| **Vistas PHP** | Lógica de negocio embebida en vistas (`collection_main.php`, `splash.php`, `language.php`). | Mezcla de concerns, dificulta mantenimiento. |
| **Vistas PHP** | JavaScript inline en `splash.php`, `language.php`, `theater_school.php`, `collection_main.php`. | Dificulta CSP, testing, caching y limpieza de estado en navegación SPA. |
| **Vistas PHP** | Hack de escape en `page_shell.php` que reintroduce HTML después de `esc()`. | Riesgo de XSS si el título contiene entidades inesperadas. |
| **CSS** | ~27–40 % del CSS es código muerto confirmado (`hero.css`, `detail.css`, bloques no usados en `section.css` y `school.css`). | Bundle de 148 KB innecesariamente grande, difícil de mantener. |
| **CSS** | Inconsistencia de nombres: controlador/JS usan `pill-button--lang`, CSS define `pill-button--language`. | Deuda técnica clara; estilos específicos no aplican. |
| **CSS** | Colores, sombras, z-index y tipografía no tokenizados; 76 tamaños de fuente distintos, 62 sombras distintas, 37 border-radius distintos. | Diseño inconsistente y difícil de evolucionar. |
| **CSS** | Selectores usados en vistas sin estilos (`collection-heading__eyebrow/copy`, `.totem-button`). | Elementos se renderizan sin estilo. |
| **JS** | Navegación SPA clona y re-ejecuta scripts inline sin cleanup universal. Acumulación de listeners/intervalos. | Fugas de memoria y comportamiento errático tras múltiples navegaciones. |
| **JS** | `mousemove` en idle timer genera eventos constantes innecesarios. | Carga del main thread. |
| **Configuración** | `.env` activo define `API_BASE_URL` en lugar de `TOTEM_API_URL` y no define `TOTEM_API_KEY`. | En producción el tótem apuntaría a localhost y no enviaría API key. |
| **Configuración** | `App.php` tiene `baseURL` hardcodeado a `http://localhost:8080/`, `forceGlobalSecureRequests = false`, `CSPEnabled = false`, timezone `UTC`. | Riesgos de seguridad y comportamiento regional incorrecto. |
| **Seguridad** | `Filters.php` tiene CSRF, honeypot, invalidchars y secureheaders desactivados. | Superficie de ataque mayor; el futuro formulario de `/extension` quedará desprotegido. |
| **Seguridad** | Cookie `totem_lang` seteada desde JS sin `Secure` ni `SameSite`. | Vulnerable a manipulación en tránsito. |
| **Seguridad** | `.deploy/.env.deploy` con credenciales FTP tiene permisos 644 y usa FTP puerto 21 (texto plano). | Exposición de credenciales y tráfico no cifrado. |
| **Tests** | Solo 16 tests: 2 de health + 14 smoke tests de rutas. Cobertura real ~0 %. Sin PHPStan, CS-Fixer, PHPCS ni CI/CD. | Imposible garantizar calidad en refactor profundo. |
| **Tooling** | Build CSS es solo `cat` de parciales, sin minificación, source map, autoprefixer ni linter. | Riesgo de errores de sintaxis y bundle grande. |

### 1.2 Fortalezas a preservar

- Arquitectura stateless y resiliencia ante fallos de red.
- Convenciones de nombres claras (kebab-case español para rutas, BEM para CSS, snake_case para vistas).
- Separación inicial entre layouts, partials y vistas de pantalla.
- Uso consistente de `esc()` en vistas (salvo excepciones señaladas).
- Sistema de i18n con 4 idiomas.

---

## 2. Visión y principios del refactor

### 2.1 Objetivos

1. **Eliminar el God Controller**: dividir responsabilidades en controladores de dominio, presenters/view models, servicios y repositorios de fallback.
2. **Hacer el código testeable**: inyección de dependencias, interfaces, tests unitarios e integración con cobertura real.
3. **Mejorar observabilidad**: el servicio API debe distinguir éxito, vacío, error de red y error HTTP; todo debe quedar logueado.
4. **Eliminar código muerto y duplicado**: vistas, métodos, CSS y JS no usados deben desaparecer.
5. **Refactorizar CSS hacia un design system**: tokens consolidados, componentes reutilizables, código muerto eliminado.
6. **Estabilizar la navegación SPA**: cleanup de listeners/intervalos, scripts inline externos, detección robusta de enlaces.
7. **Cerrar brechas de seguridad**: CSP, cookies seguras, HTTPS, validación de entradas, CSRF para formularios.
8. **Corregir variables de entorno y despliegue**: usar `TOTEM_API_URL`/`TOTEM_API_KEY`, proteger credenciales, preferir SFTP/FTPS.
9. **Agregar tooling de calidad**: PHPStan nivel 8+, PHP-CS-Fixer, scripts composer y CI/CD mínimo.

### 2.2 Principios no negociables

- **No romper la funcionalidad actual**: cada fase debe ser desplegable y validable en el kiosko real.
- **Mantener la resiliencia offline**: el kiosko debe seguir funcionando (con fallback amigable) si la API no responde.
- **Mínima intrusión**: cambios quirúrgicos; no reescribir por reescribir.
- **Tests antes o junto al cambio**: cada refactor significativo va acompañado de tests.

---

## 3. Fases de implementación

### Fase 0 — Fundamentos y seguridad (semana 1)

> Objetivo: estabilizar lo que está roto antes de tocar arquitectura.

#### Backend
- [ ] **Corregir variables de entorno**:
  - Actualizar `.env`, `env` y documentación para usar `TOTEM_API_URL` y `TOTEM_API_KEY`.
  - Eliminar `API_BASE_URL` o consolidar todo en `TOTEM_API_URL`.
  - Remover `$this->apiClient` muerto de `BaseController`.
- [ ] **Corregir bug crítico en `theater_school.php`**: usar `$course[...]` dentro del `foreach`.
- [ ] **Corregir bug de clase `event-card--adult`**: usar `match` sobre `audience_id`.
- [ ] **Eliminar código muerto inmediato**: `visitsSection()`, `TotemApiService::collectionItem()` si no se usa, rama `strlen()` muerta en `excerptMuseumBlockContent()`.
- [ ] **Validar `$from` en `language()`**: regex de ruta interna.

#### Configuración y seguridad
- [ ] **Proteger credenciales de despliegue**:
  - Cambiar permisos de `.deploy/.env.deploy` a `600`.
  - Documentar que el archivo no debe commitearse.
  - Planificar migración a SFTP/FTPS o pipeline CI/CD con secretos.
- [ ] **Seguridad de aplicación**:
  - Timezone a `America/Santiago`.
  - `forceGlobalSecureRequests = true` para producción vía `.env`.
  - Preparar CSP: activar `CSPEnabled`, mover inline scripts a archivos externos o usar nonces.
  - Configurar cookie `totem_lang` con `Secure; SameSite=Lax` desde JS y considerar setcookie PHP complementario.
  - Preparar CSRF para futuro formulario `/extension`.
  - Habilitar `secureheaders` filter en producción.

#### Tooling
- [ ] Instalar y configurar:
  - `phpstan/phpstan` nivel 8.
  - `friendsofphp/php-cs-fixer` con PSR-12.
- [ ] Agregar scripts en `composer.json`: `lint`, `analyse`, `format`, `test` con `--no-coverage` por defecto.
- [ ] Actualizar CodeIgniter 4.7.2 → 4.7.3.
- [ ] Crear pipeline CI/CD mínima (GitHub Actions) para tests, lint y análisis estático.

#### Tests
- [ ] Hacer que `composer test` termine limpio:
  - Ejecutar con `--no-coverage` o instalar PCOV/Xdebug.
  - Limpiar plantillas de ejemplo en `tests/_support/`.
- [ ] Tests unitarios para `TotemApiService` mockeando `CURLRequest`.
- [ ] Tests de controlador para métodos críticos afectados en esta fase.

**Entregables:**
- `.env` corregido y documentado.
- Bugs críticos corregidos.
- Tooling de calidad instalado y funcionando.
- CI/CD básico verde.

---

### Fase 1 — Arquitectura backend: separar responsabilidades (semanas 2–3)

> Objetivo: eliminar el God Controller y crear una capa de aplicación limpia.

#### Controladores
- [ ] Dividir `TotemController` en controladores de dominio:
  - `MainController`: splash, language, main menu.
  - `MuseumController`: museo, edificio, institución, actualidad, historia cómica.
  - `CollectionController`: colección, técnicas, exhibición, fichas.
  - `SchoolController`: teatro escuela.
  - `BillboardController`: cartelera y detalle.
  - `FriendsController`: amigos y extension/contacto (futuro).
- [ ] Crear `BaseTotemController` con helpers comunes (`pageMeta`, `shellNav`, `render`).
- [ ] Mover `menuItem()` y `shellNav()` a un `MenuBuilder` / `NavBuilder` reutilizable.

#### Servicios y API
- [ ] Definir interfaz `TotemApiInterface`.
- [ ] Refactorizar `TotemApiService` para inyección de dependencias (constructor recibe `CURLRequest` o config).
- [ ] Mejorar manejo de errores en `TotemApiService::get()`:
  - Loggear status no-2xx y JSON inválido.
  - Distinguir `NetworkException`, `NotFoundException`, respuesta vacía.
  - Ofrecer método `getOrFallback()` para el modo resiliencia del kiosko.
- [ ] Crear `CachedTotemApiService` decorador con memoización por request para evitar llamadas repetidas a `museum()`, `techniques()`, etc.
- [ ] Registrar servicio en `app/Config/Services.php` como `Services::totemApi()`.

#### Presenters y ViewModels
- [ ] Crear presenters de dominio:
  - `SchoolPresenter`: transforma cursos, profesores, alumnos.
  - `BillboardPresenter`: transforma shows a eventos + meses, maneja clases CSS por audiencia.
  - `MuseumTodayPresenter`: lógica de `buildMuseumTodayContext()` + `normalizeMuseumTodayBlocks()`.
  - `DatePresenter` / `LocalizedDate`: formateo de fechas por locale usando `IntlDateFormatter`.
- [ ] Extraer datos de contingencia/mock a `Config/Totem.php` o repositorios dedicados (`SchoolFallbackRepository`, `BillboardFallbackRepository`).

#### Value Objects y Enums
- [ ] Reemplazar magic numbers con Enums:
  - `SchoolCategory` (1, 2, 3).
  - `Audience` (1, 2, 3, 4).
- [ ] Crear `ImageAsset` value object para centralizar prefijos `assets/img/` y validar existencia.
- [ ] Crear `SlugResolver` para resolver slugs a IDs de API sin N+1.

#### Rutas
- [ ] Actualizar `Routes.php` para apuntar a nuevos controladores.
- [ ] Eliminar rutas obsoletas de colección (`/museo/coleccion/titeres`, `/mascaras`, `/payasos`) y sus vistas.
- [ ] Añadir `set404Override()` con handler multiidioma amigable.

#### Tests
- [ ] Tests unitarios para cada presenter.
- [ ] Tests de integración para cada controlador usando mocks del API.
- [ ] Tests de fallback offline.

**Entregables:**
- Backend reestructurado sin God Controller.
- Servicio API robusto y observable.
- Presenters testeados.
- Rutas limpias.

---

### Fase 2 — Refactorización de vistas y componentes (semanas 3–4)

> Objetivo: separar lógica de presentación, eliminar JS inline y consolidar componentes.

#### Lógica de vistas
- [ ] Mover arrays hardcodeados de vistas a controladores/presenters:
  - `$sections` de `collection_main.php`.
  - Locales de `splash.php` y `language.php` → config/helper `totem_locales()`.
- [ ] Internacionalizar textos faltantes (nombres de idioma en `language.php`, fallback de `<title>`, etc.).
- [ ] Reemplazar hack de escape en `page_shell.php` por helper `safe_title()` con whitelist estricta o separar título visual/semántico.

#### Componentes reutilizables
- [ ] Crear `app/Views/totem/partials/icons/` con partials SVG:
  - `icon_language.php`, `icon_back.php`, `icon_home.php`, `icon_close.php`, `transition_overlay.php`.
- [ ] Refactorizar `topbar.php` para recibir iconos por nombre de partial.
- [ ] Refactorizar `card.php` para evitar duplicación de markup enabled/disabled.
- [ ] Crear `collection_pill.php` para eliminar duplicación en `collection_band.php`.
- [ ] Crear `mock_notice.php` para vistas placeholder.

#### JavaScript
- [ ] Extraer scripts inline a módulos externos:
  - `assets/js/splash.js` (rotación de idiomas).
  - `assets/js/language-selector.js`.
  - `assets/js/school-modal.js`.
  - `assets/js/collection-main.js`.
- [ ] Implementar **registry de cleanup** en `app.js`: `window.__totemCleanup`. Antes de `commitFetchedPage`, ejecutar todos los cleanup registrados.
- [ ] Mejorar detección de enlaces externos: manejar `//example.com`, `mailto:`, `tel:`, `data:`.
- [ ] Eliminar `mousemove` de `IDLE_ACTIVITY_EVENTS` o reemplazarlo por eventos menos frecuentes.
- [ ] Evitar re-ejecución incontrolada de scripts inline; cargar módulos ES6 con `type="module"`.
- [ ] Revisar escape de URLs en vistas (`card.php`, `billboard.php`): usar `esc(..., 'url')` o escapar URL completa.

#### Vistas placeholder
- [ ] Decidir si se mantienen mocks para desarrollo o se eliminan.
- [ ] Si se mantienen, usar `mock_notice.php` y claves de idioma.
- [ ] Completar o eliminar vistas vacías (`collection_puppets_exhibit.php`, `collection_masks_exhibit.php`).

#### Tests
- [ ] Tests de vistas (smoke + contenido esperado).
- [ ] Tests de JS con herramienta ligera si es viable, o al menos validación manual documentada.

**Entregables:**
- Vistas limpias sin lógica de negocio embebida.
- JS modularizado con cleanup.
- Componentes reutilizables consolidados.

---

### Fase 3 — Rediseño y consolidación de CSS (semanas 4–5)

> Objetivo: convertir el CSS en un design system mantenible y eliminar código muerto.

#### Tokens
- [ ] Mover `--paper-texture` de inline en `MainLayout.php` a `00-tokens.css`.
- [ ] Añadir tokens faltantes:
  - Superficies: `--surface`, `--surface-solid`.
  - Bordes: `--border-subtle`, `--border-strong`.
  - Sombras: `--shadow-sm`, `--shadow-md`, `--shadow-lg`.
  - Tipografía: `--text-xs`, `--text-sm`, `--text-base`, `--text-lg`, `--text-xl`, `--text-2xl`.
  - Z-index: `--z-base`, `--z-header`, `--z-modal`, `--z-idle`, `--z-orientation`, `--z-transition`.
  - Touch target: `--touch-target: 44px`.

#### Limpieza de código muerto
- [ ] Eliminar archivos/bloques confirmados como no usados:
  - `shared/hero.css` completo.
  - `screens/detail.css` completo.
  - Bloques `.collection-hero`, `.collection-route`, `.collection-pdf`, `.collection-hotspot`, `.collection-archive` de `screens/section.css`.
  - Bloque `.school-dossier` de `screens/school.css`.
  - `.language-card__button*` de `screens/language.css`.
  - `.menu-title__copy/eyebrow/line`, `.menu-card__hint` de `screens/menu.css`.
  - `.footer-logos`, `.logo-badge` de `shared/utils.css`.

#### Componentes base
- [ ] Crear `.card` y `.panel` en `shared/components.css`.
- [ ] Reemplazar `.event-card`, `.course-card`, `.collection-stat`, `.museum-today__stat`, `.stat-card`, `.detail-stat`, `.school-stat-card` con modifiers de `.card`.

#### Nomenclatura y consistencia
- [ ] Unificar `pill-button--lang` vs `pill-button--language` en controlador, JS y CSS.
- [ ] Definir estilos para `.collection-heading__eyebrow` y `.collection-heading__copy` o eliminar esos elementos.
- [ ] Decidir sobre `partials/button.php` (`.totem-button`): usarlo o eliminarlo.

#### Responsive
- [ ] Migrar media queries sueltas a `@container kiosk (...)`.
- [ ] Eliminar duplicación entre `language.css @media (max-width: 640px)` y `99-responsive.css`.

#### Build pipeline
- [ ] Mejorar `bin/build-css.sh` con:
  - Validación de sintaxis.
  - Opcional: PostCSS + autoprefixer + cssnano + source map.
- [ ] Agregar pre-commit hook para compilar CSS.
- [ ] Considerar PurgeCSS conservador con safelist para clases dinámicas PHP.

#### Accesibilidad
- [ ] Garantizar targets táctiles ≥44px.
- [ ] Completar `prefers-reduced-motion` en todas las animaciones.

#### Tests
- [ ] Validación visual en kiosko real 1080×1920 para cada pantalla afectada.
- [ ] Tests de regresión visual mínimos (capturas comparativas si es viable).

**Entregables:**
- CSS limpio, tokenizado y basado en componentes.
- Build pipeline mejorado.
- Bundle reducido (~35–40 % menos).

---

### Fase 4 — Observabilidad, robustez y marcha blanca (semana 6)

> Objetivo: preparar el sistema para producción y operación continua.

#### Observabilidad
- [ ] Logs estructurados de llamadas API: endpoint, tiempo de respuesta, status, error.
- [ ] Endpoint de health check (`/health`) para monitoreo del kiosko.
- [ ] Dashboard o alerta mínima si la API no responde.

#### Robustez offline
- [ ] Definir estrategia de fallback clara:
  - Datos de contingencia por dominio.
  - Pantalla amigable multiidioma si no hay datos ni fallback.
- [ ] Cachear respuestas API en archivo con TTL corto para superar cortes de red.

#### Documentación
- [ ] Actualizar `README.md` con estructura real.
- [ ] Crear `AGENTS.md` con convenciones específicas para agentes de código.
- [ ] Documentar flujo de fallback/offline.
- [ ] Crear manual de soporte de operaciones (encendido/apagado, reinicio, limpieza de caché).

#### Despliegue
- [ ] Evaluar migración de FTP a SFTP/FTPS o pipeline CI/CD con secretos.
- [ ] Automatizar compilación de CSS en el pipeline.
- [ ] Eliminar `sync-css.py` si es legacy.
- [ ] Limpiar `totem-prod.zip` del workspace.

#### Tests finales
- [ ] Suite completa de tests debe estar verde.
- [ ] Tests de carga/rendimiento básicos.
- [ ] Pruebas de tolerancia a fallas de red.
- [ ] Pruebas en hardware real (Fully Kiosk).

**Entregables:**
- Sistema observable y robusto.
- Documentación actualizada.
- Despliegue más seguro.
- Validación en hardware real.

---

## 4. Cronograma resumido

| Fase | Semana(s) | Enfoque | Hitos |
|------|-----------|---------|-------|
| 0 | 1 | Fundamentos, seguridad, tooling | `.env` corregido, bugs críticos corregidos, CI verde, PHPStan/CS-Fixer instalados. |
| 1 | 2–3 | Arquitectura backend | God Controller eliminado, servicios/presenters testeados, rutas limpias. |
| 2 | 3–4 | Vistas y JS | Lógica movida a backend, JS modularizado, componentes consolidados. |
| 3 | 4–5 | CSS design system | Código muerto eliminado, tokens, build pipeline mejorado. |
| 4 | 6 | Observabilidad, robustez, despliegue | Health check, fallback, documentación, pruebas en hardware. |

---

## 5. Criterios de aceptación globales

- [ ] `composer test`, `composer lint` y `composer analyse` pasan sin errores.
- [ ] Cobertura de tests ≥60 % en backend (presenters, servicios, controladores).
- [ ] No hay código muerto detectado por análisis estático o búsqueda manual.
- [ ] CSS compilado reduce tamaño en ≥25 %.
- [ ] Navegación SPA no acumula listeners/intervalos (validado manualmente).
- [ ] Kiosko funciona correctamente con API disponible y con API caída.
- [ ] Pruebas en hardware real 1080×1920 exitosas.
- [ ] Variables de entorno y credenciales protegidas.

---

## 6. Riesgos y mitigaciones

| Riesgo | Probabilidad | Impacto | Mitigación |
|--------|--------------|---------|------------|
| Regresiones en pantallas durante refactor de controladores | Media | Alto | Tests de integración por controlador; despliegues parciales; validación visual en cada fase. |
| CSS roto tras eliminar código muerto | Media | Alto | Revisión manual clase por clase; PurgeCSS conservador; validación visual en kiosko. |
| Scripts inline externos rompen navegación SPA | Media | Medio | Registry de cleanup; pruebas de navegación secuencial en todas las pantallas. |
| Cambio de variables de entorno rompe despliegue | Baja | Alto | Documentación clara; migración gradual; verificación en staging. |
| Tests tardan más de lo esperado | Media | Medio | Priorizar tests de integración sobre unitarios puros; usar mocks. |
| Hardware Fully Kiosk no disponible para pruebas | Baja | Alto | Validación en navegador con viewport 1080×1920 como mínimo; reservar ventana de prueba real. |

---

## 7. Convención de commits

Cada entrega se registra con commits convencionales siguiendo el patrón actual del proyecto. Ejemplos:

```
fix(config): use TOTEM_API_URL and TOTEM_API_KEY in .env
fix(views): use $course instead of $section in theater_school loop
fix(controller): correct audience_id to CSS class mapping
chore(tools): install phpstan and php-cs-fixer
refactor(controllers): split TotemController into domain controllers
refactor(services): add TotemApiInterface and CachedTotemApiService
refactor(views): extract inline JS to external modules
refactor(css): remove dead code and consolidate tokens
feat(monitoring): add /health endpoint and structured API logs
docs(ops): add support manual and update README
```

La rama de trabajo es `dev`. No se crean ramas feature adicionales. Cada commit debe dejar el kiosko navegable y los tests verdes.

---

## 8. Próximos pasos inmediatos

1. Guardar este plan en `docs/plans/audit-and-refactor-plan.md`.
2. Crear `docs/plans/TASKS.md` con el sistema de tareas ejecutables referenciadas a este plan.
3. Comenzar Fase 0 en `dev` con corrección de `.env`, bugs críticos e instalación de tooling.
4. Validar que `composer test` pase limpio antes de avanzar a Fase 1.
