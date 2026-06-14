# TASKS Archives

Este archivo conserva el histórico de tareas ya terminadas en `TASKS.md`.

Fecha de corte: `2026-06-14`.

## 1. Auditoría visual cerrada

### A. Menú principal
- [x] **A1 — Pulido fino del menú principal para igualar la referencia visual**
  * **Referencia visual:** `assets/design-refs/menu-principal.jpg`
  * **Ruta actual:** `/menu`
  * **Archivos a tocar:** [app/Views/totem/main_menu.php](/Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/main_menu.php), [public/assets/css/src/screens/menu.css](/Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/public/assets/css/src/screens/menu.css)
  * **Brecha visual:** La composición general sí coincide, pero el render actual todavía puede afinar el espaciado vertical, la jerarquía de tipografías y la posición de los ornamentos inferiores para clavar la maqueta.
  * **Criterio de aceptación:**
      1. El título central debe quedar visualmente centrado en el área segura del tótem.
      2. Las 5 tarjetas deben mantenerse en el ritmo visual de la referencia, sin desbalance entre filas.
      3. Los ornamentos inferiores no deben tapar el contenido ni generar sensación de corte brusco.
      4. El header debe conservar la misma lógica de navegación y tamaño de botones que la referencia.

### F. Pantallas de museo que siguieron en modo placeholder
- [x] **F1 — Completar `museum_today.php` con contenido real de actualidad**
  * **Referencia visual:** `assets/design-refs/museo/museo.webp` y la estructura de referencia interna del módulo museo
  * **Ruta actual:** `/museo/el-museo/actualidad`
  * **Archivos a tocar:** [app/Views/totem/museum_today.php](/Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/museum_today.php)
  * **Brecha visual:** La vista actual sigue como “Detalle actualidad (mock)”. Eso no alcanza un estándar de producción ni de sincronización con diseño.
  * **Criterio de aceptación:**
      1. Debe dejar de mostrarse como stub.
      2. Debe tener contenido real o una composición explícitamente temporal.
      3. Debe mantener el mismo lenguaje visual del resto del módulo museo.

## 2. Oleada 1 - Demo & Fundación Técnica

> **Estado:** completada.

### 1. UI & UX Correcciones Críticas (Frontend)
- [x] **P1 — Menú principal: 6 items no caben sin scroll**
  *   **Ruta de Implementación:** Modificar [public/assets/css/src/screens/menu.css](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/public/assets/css/src/screens/menu.css)
  *   **Instrucciones:**
      1. Ajustar el grid o flexbox de `.menu-card` para reducir la altura (`height`) de las tarjetas.
      2. Reducir padding interno y achicar el `font-size` de los títulos para que las 6 tarjetas quepan por completo en una pantalla de proporción vertical 1080x1920 (viewport de Full HD vertical).
      3. Ejecutar `composer build:css` para compilar los cambios.
- [x] **P2 — Botón de idioma no muestra el idioma activo**
  *   **Ruta de Implementación:** Modificar [public/assets/js/app.js](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/public/assets/js/app.js)
  *   **Instrucciones:**
      1. En el handler `DOMContentLoaded`, leer la cookie `totem_lang` o en su defecto `localStorage.getItem('totem_lang')` (fallback 'es').
      2. Mapear los valores ('es', 'en', 'fr', 'pt') a etiquetas visibles ('ESP', 'ENG', 'FRA', 'POR').
      3. Seleccionar el contenedor en el DOM (`.pill-button--lang span:last-child`) y actualizar su `textContent` dinámicamente al cargar la página.
- [x] **P3 — Idle timer: sin aviso visual antes del reset**
  *   **Ruta de Implementación:**
      *   Modificar [public/assets/js/app.js](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/public/assets/js/app.js)
      *   Modificar [app/Views/layouts/MainLayout.php](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/layouts/MainLayout.php)
      *   Crear [public/assets/css/src/screens/idle.css](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/public/assets/css/src/screens/idle.css)
  *   **Instrucciones:**
      1. En `MainLayout.php`, agregar el markup HTML para un overlay `#idle-overlay` que empiece oculto.
      2. En `app.js`, crear constantes `IDLE_LIMIT = 120` e `WARN_AT = 105`.
      3. Al llegar a 105 segundos de inactividad, remover la clase oculta del overlay, iniciar un countdown de 15 a 0 mostrando el tiempo restante en pantalla.
      4. Incorporar textos multiidioma traducidos para la advertencia.
      5. Si el usuario presiona "Continuar" u otra parte del DOM, restaurar `idleTime = 0` y ocultar el overlay.
      6. Evitar que el timer corra o redirija cuando la URL sea la raíz (`/` o `/index.php/`).
- [x] **P4 — Traducciones de textos hardcodeados en subpáginas**
  *   **Ruta de Implementación:**
      *   Modificar [app/Language/es/Totem.php](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Language/es/Totem.php) (y directorios `/en/`, `/fr/`, `/pt/`)
      *   Modificar [app/Views/totem/museum_menu.php](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/museum_menu.php)
      *   Modificar [app/Views/totem/billboard.php](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/billboard.php)
      *   Modificar [app/Views/totem/billboard_detail.php](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/billboard_detail.php)
  *   **Instrucciones:**
      1. Extraer los textos e eyebrows estáticos que están en español (e.g. "Recorre la colección viva", "Programación", "Cartelera", "Duración aproximada") y agregarlos a los diccionarios de traducción de cada idioma.
      2. Reemplazar los textos rígidos en las vistas por la función `lang('Totem.clave')`.
 
### 2. Integración con la API Backend
- [x] **Crear Servicio de API del Tótem (`TotemApiService`)**
  *   **Ruta de Implementación:** Crear [app/Services/TotemApiService.php](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Services/TotemApiService.php)
  *   **Instrucciones:**
      1. Definir variables `baseUrl` (tomando `TOTEM_API_URL` del entorno) y `apiKey` (tomando `TOTEM_API_KEY`).
      2. Implementar un método genérico de petición `get(string $path, array $params = [])` usando la librería `CURLRequest` de CodeIgniter.
      3. Añadir de manera predeterminada el header `X-Totem-Key` en las solicitudes.
      4. Establecer un timeout máximo de 5 segundos.
      5. Envolver las llamadas en bloques `try/catch` para capturar fallas de red y retornar arreglos vacíos de forma graceful sin gatillar excepciones PHP al usuario final.
- [x] **Conectar Cartelera a la API Real**
  *   **Ruta de Implementación:** Modificar [app/Controllers/TotemController.php](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Controllers/TotemController.php)
  *   **Instrucciones:**
      1. Instanciar `TotemApiService`.
      2. Modificar el método `billboard()` para invocar a `$apiService->shows()` en lugar de utilizar el arreglo estático.
      3. Validar que la vista renderice dinámicamente las tarjetas según el payload de la API.
- [x] **Conectar Teatro Escuela a la API de Cursos**
  *   **Ruta de Implementación:** Modificar [app/Controllers/TotemController.php](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Controllers/TotemController.php)
  *   **Instrucciones:**
      1. Modificar el método `theaterSchool()` para consumir `$apiService->courses()`.
      2. Reemplazar los datos estáticos de cursos activos por el set dinámico devuelto por la API.

## 3. Oleada 2 - Cobertura Completa de Contenidos

> **Estado:** Bloques A, B y C completados.

### 1. Bloque A: Colección del Museo (Estructura de Navegación)
- [x] **Rutas dinámicas en CodeIgniter 4**
  *   **Ruta de Implementación:** Modificar [app/Config/Routes.php](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Config/Routes.php)
  *   **Instrucciones:** La navegación activa de Colección usa sólo la ruta de ficha. Las rutas intermedias quedaron obsoletas y no deben registrarse de nuevo:
      ```php
      $routes->get('museo/coleccion/fichas/(:num)', 'TotemController::collectionItem/$1');
      ```
- [x] **A1 — Vista Principal de Colección creada** (`/museo/coleccion`) ✅
  *   **Ruta de Implementación:** [app/Views/totem/collection_main.php](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/collection_main.php)
  *   **Estado:** Vista estructurada y funcional. Pendiente de rediseño final con assets de Coni (ver A3).
- [x] **A2 — Rutas y vistas intermedias por tipo creadas** ~~(`/museo/coleccion/titeres`, `/mascaras`, `/payasos`)~~ ⚠️ OBSOLETAS
  *   **Nota:** Estas vistas (`collection_techniques.php`, `collection_masks.php`, `collection_clowns.php`) y sus rutas fueron creadas pero quedan obsoletas por el cambio de arquitectura del 1/6. Serán eliminadas al implementar A3.

### 2. Bloque B: Historia
- [x] **Rutas dinámicas en CodeIgniter 4**
  *   **Ruta de Implementación:** Modificar [app/Config/Routes.php](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Config/Routes.php)
  *   **Instrucciones:** Añadir: `$routes->get('museo/historia/(:segment)', 'TotemController::museumHistoryPost/$1');` y mantener el alias legacy `museo/historia-comica/(:segment)` para compatibilidad.
- [x] **B1 — Pantalla Principal de Historia (Timeline)** (`/museo/historia`)
  *   **Ruta de Implementación:** Crear [app/Views/totem/comic_history_main.php](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/comic_history_main.php)
  *   **Instrucciones:** Consumir `GET /api/v1/totem/museum-history/historia-comica`. Crear un layout interactivo de línea de tiempo vertical. Habilitar scroll exclusivamente en el contenedor del timeline.
- [x] **B2 — Post Editorial Individual** (`/museo/historia/{slug}` y alias legacy `/museo/historia-comica/{slug}`)
  *   **Ruta de Implementación:** Crear [app/Views/totem/comic_history_post.php](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/comic_history_post.php)
  *   **Instrucciones:** Consumir `GET /api/v1/totem/museum-history/{slug}` y pintar la información/fotos de manera elegante y legible.

### 3. Bloque C: Explora el Museo
- [x] **Rutas estáticas en CodeIgniter 4**
  *   **Ruta de Implementación:** Modificar [app/Config/Routes.php](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Config/Routes.php)
  *   **Instrucciones:** Registrar las siguientes subpáginas:
      ```php
      $routes->get('museo/el-museo/edificio', 'TotemController::museumBuilding');
      $routes->get('museo/el-museo/institucion', 'TotemController::museumInstitution');
      $routes->get('museo/el-museo/actualidad', 'TotemController::museumToday');
      ```
- [x] **C1 — Sub-menú Explora el Museo** (`/museo/el-museo`)
  *   **Ruta de Implementación:** Crear [app/Views/totem/museum_info_main.php](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/museum_info_main.php)
  *   **Instrucciones:** Menu de 3 grandes opciones táctiles (Edificio, Institución, Actualidad) de acuerdo con los diseños recibidos el 2/6.
- [x] **C2 — Historia del Edificio** (`/museo/el-museo/edificio`)
  *   **Ruta de Implementación:** Crear [app/Views/totem/museum_building.php](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/museum_building.php)
  *   **Instrucciones:** Consumir `GET /api/v1/totem/museum`. Filtrar los datos de la Iglesia San Judas Tadeo y pintar el texto e imágenes.
- [x] **C3 — Historia de la Institución** (`/museo/el-museo/institucion`)
  *   **Ruta de Implementación:** Crear [app/Views/totem/museum_institution.php](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/museum_institution.php)
  *   **Instrucciones:** Consumir `GET /api/v1/totem/museum` y renderizar misión, visión y estructura interna del teatro.
- [x] **C4 — Historia en la Actualidad** (`/museo/el-museo/actualidad`)
  *   **Ruta de Implementación:** Crear [app/Views/totem/museum_today.php](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/museum_today.php)
  *   **Instrucciones:** Consumir `GET /api/v1/totem/museum` y mostrar logros más representativos (e.g. FMIM 2024).

### 4. Bloque D: Teatro Escuela (Rediseño a Dossier)
- [x] **D1 — Rediseño del Layout del Teatro Escuela** (`/teatro-escuela`)
  *   **Ruta de Implementación:** Modificar [app/Views/totem/section.php](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/section.php) (o crear una vista dedicada `theater_school_dossier.php`)
  *   **Instrucciones:**
      1. Integrar sección superior para embed de YouTube (silenciado e interactivo).
      2. Crear bloque con cifras y datos clave del teatro (stats).
      3. Añadir carrusel de tarjetas compactas de Maestros y Alumnos Destacados.
      4. Incluir bloque de cursos dinámicos vinculados a la API.

### 6. Bloque F: Lógicas e Interactividades de UX
- [x] **F4 — Ambigüedad del enlace `/historia`** ✅ Resuelta implícitamente
  *   **Estado:** La ruta `/historia` nunca llegó a existir en `Routes.php`. Historia vive en `/museo/historia` y conserva el alias legacy `/museo/historia-comica`. No requiere acción.

### 7. Bloque G: Reglas de sincronización entre referencia y código
- [x] **G1 — Crear una tabla de equivalencia entre assets de referencia y assets reales del tótem**
  * **Ruta de Implementación:** Añadir una subsección en este mismo `TASKS.md` o en un documento complementario dentro de `docs/`
  * **Qué debía incluir:**
      1. Nombre del asset en la referencia visual.
      2. Archivo real usado en `public/assets/img/`.
      3. Pantalla donde aparece.
      4. Estado: final, provisional o ausente.
  * **Resultado:** La equivalencia quedó materializada en `TASKS.md` dentro de la sección H "Tabla base de equivalencia de assets" y la matriz de sincronización inicial.
- [x] **G2 — Definir criterios de aceptación visual por pantalla antes de tocar CSS**
  * **Ruta de Implementación:** Añadir checklist por pantalla en este mismo documento.
  * **Qué debía incluir:**
      1. Jerarquía de títulos.
      2. Orden de bloques.
      3. Presencia de imágenes principales.
      4. Presencia de CTA.
      5. Comportamiento del scroll.
  * **Resultado:** Los criterios quedaron incorporados en `TASKS.md` dentro de las secciones I, J, K y L, que ahora sirven como guía visual y operativa.

## 4. Archivo visual y de implementación ya cerrado

- Pulido fino del menú principal para igualar la referencia visual.
- Completar `museum_today.php` con contenido real de actualidad.

## 5. Fundación técnica y arquitectura ya integradas

- Crear BaseTotemController.
- Crear MenuBuilder y NavBuilder.
- Dividir TotemController en controladores de dominio.
- Crear TotemApiInterface y refactorizar TotemApiService.
- Crear CachedTotemApiService.
- Crear presenters de dominio.
- Extraer datos de contingencia a repositorios/config.
- Crear Enums y Value Objects.

## Nota

Las tareas nuevas o pendientes deben seguir registrándose en [TASKS.md](/Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/TASKS.md).
