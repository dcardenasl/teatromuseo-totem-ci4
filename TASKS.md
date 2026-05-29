# Roadmap de Implementación — Tótem Interactivo (TASKS.md)

Este archivo sirve como el backlog técnico oficial y guía de desarrollo para implementar la lista completa de mejoras del **Tótem Interactivo** (`teatromuseo-totem-ci4`). Está estructurado en 3 Oleadas de desarrollo para facilitar la entrega continua y el testeo en el hardware Fully Kiosk.

---

## 🗺️ Mapa de Componentes y Rutas Físicas del Proyecto

Para cualquier desarrollo en este módulo, guíate por este mapa de archivos clave:

*   **Rutas del Tótem:** `app/Config/Routes.php` (declaración de URLs limpias)
*   **Controlador Principal:** `app/Controllers/TotemController.php` (lógica de servidor y fetch de datos)
*   **Vistas de Pantalla:** `app/Views/totem/` (layouts HTML)
*   **Layout Base del Tótem:** `app/Views/layouts/MainLayout.php` (estructura global de HTML/CSS/JS)
*   **Servicio Cliente API:** `app/Services/TotemApiService.php` *(Nuevo a implementar)*
*   **Estilos CSS compilados:** `public/assets/css/style.css` (generado compilando los parciales en `/src/`)
*   **Hojas de Estilo Parciales:** `public/assets/css/src/` (modificar aquí, y luego correr `composer build:css`)
*   **Lógica de Interacción JS:** `public/assets/js/app.js` (idle timer, cookies, handlers táctiles)
*   **Traducciones (i18n):** `app/Language/{es|en|fr|pt}/Totem.php` (textos, eyebrows, placeholders)

---

## 🌊 OLEADA 1: Demo & Fundación Técnica (Urgente - Pre-reunión 1/6)
> **Objetivo:** Resolver problemas críticos de UX/UI y establecer la infraestructura de comunicación con la API real para contar con un tótem 100% navegable en la demo de software.
> **Estado:** ¡100% Completada! ✅

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
      2. Reemplazar los datos estáticos de cursos activos por el set dinámico devuelto por la API.r los datos estáticos de cursos activos por el set dinámico devuelto por la API.

---

## 🌊 OLEADA 2: Cobertura Completa de Contenidos (Semana 2/6 a 8/6)
> **Objetivo:** Desarrollar todas las nuevas pantallas y vistas de navegación profunda detalladas en el plano de la v1.0, incorporando los assets definitivos de diseño y las decisiones del equipo.

### 1. Bloque A: Colección del Museo (Estructura de Navegación)
- [ ] **Rutas dinámicas en CodeIgniter 4**
  *   **Ruta de Implementación:** Modificar [app/Config/Routes.php](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Config/Routes.php)
  *   **Instrucciones:** Registrar las nuevas rutas dinámicas para fichas, técnicas y colecciones:
      ```php
      $routes->get('museo/coleccion/titeres', 'TotemController::collectionTechniques');
      $routes->get('museo/coleccion/titeres/(:segment)', 'TotemController::collectionTechnique/$1');
      $routes->get('museo/coleccion/mascaras', 'TotemController::collectionMasks');
      $routes->get('museo/coleccion/mascaras/(:segment)', 'TotemController::collectionMaskTradition/$1');
      $routes->get('museo/coleccion/payasos', 'TotemController::collectionClowns');
      $routes->get('museo/coleccion/fichas/(:num)', 'TotemController::collectionItem/$1');
      ```
- [ ] **A1 — Vista Principal de Colección** (`/museo/coleccion`)
  *   **Ruta de Implementación:** Crear [app/Views/totem/collection_main.php](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/collection_main.php)
  *   **Instrucciones:** Diseñar una vista limpia con 3 tarjetas táctiles de grandes dimensiones (Títeres, Máscaras, Payasos) usando las clases de CSS correspondientes.
- [ ] **A2 — Pantalla de Técnicas de Títeres** (`/museo/coleccion/titeres`)
  *   **Ruta de Implementación:** Crear [app/Views/totem/collection_techniques.php](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/collection_techniques.php)
  *   **Instrucciones:** Consumir el endpoint `GET /api/v1/totem/techniques` a través de `TotemApiService` y pintar el listado en un grid.
- [ ] **A3 — Pantalla de Técnica Individual** (`/museo/coleccion/titeres/{slug}`)
  *   **Ruta de Implementación:** Crear [app/Views/totem/collection_technique_detail.php](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/collection_technique_detail.php)
  *   **Instrucciones:** Consumir el endpoint `GET /api/v1/totem/technique/{id}`. Mostrar cabecera editorial y grid de 3 columnas para las fichas de objetos que pertenezcan a esa técnica.
- [ ] **A4 — Ficha Individual de Objeto (Títere / Máscara)** (`/museo/coleccion/fichas/{id}`)
  *   **Ruta de Implementación:** Crear [app/Views/totem/collection_item_detail.php](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/collection_item_detail.php)
  *   **Instrucciones:**
      1. Consumir `GET /api/v1/totem/collection/{id}`.
      2. Diseñar layout de 2 columnas: izquierda para carrusel táctil de fotos, derecha para metadatos (Origen, Período, Técnica) y descripción editorial.
      3. Asegurar que no se procese ni muestre el campo pesado `contenido`.
- [ ] **A5 — Pantalla Máscaras: Tradiciones** (`/museo/coleccion/mascaras`)
  *   **Ruta de Implementación:** Crear [app/Views/totem/collection_masks.php](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/collection_masks.php)
  *   **Instrucciones:** Navegación pura con 2 grandes accesos: Comedia del Arte / Comedia del Andes.
- [ ] **A6 — Tradición Individual (Máscaras)** (`/museo/coleccion/mascaras/{slug}`)
  *   **Ruta de Implementación:** Crear [app/Views/totem/collection_mask_tradition_detail.php](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/collection_mask_tradition_detail.php)
  *   **Instrucciones:** Consumir `GET /api/v1/totem/collection?category=mascaras&tradition={slug}` y renderizar el catálogo de máscaras de dicha tradición.
- [ ] **A7 — Pantalla Payasos** (`/museo/coleccion/payasos`)
  *   **Ruta de Implementación:** Crear [app/Views/totem/collection_clowns.php](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/collection_clowns.php)
  *   **Instrucciones:** Diseñar una vista enriquecida de lectura y enlazar de forma directa a hitos de la línea de tiempo de Historia Cómica.

### 2. Bloque B: Historia Cómica
- [ ] **Rutas dinámicas en CodeIgniter 4**
  *   **Ruta de Implementación:** Modificar [app/Config/Routes.php](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Config/Routes.php)
  *   **Instrucciones:** Añadir: `$routes->get('museo/historia-comica/(:segment)', 'TotemController::museumHistoryPost/$1');`
- [ ] **B1 — Pantalla Principal de Historia Cómica (Timeline)** (`/museo/historia-comica`)
  *   **Ruta de Implementación:** Crear [app/Views/totem/comic_history_main.php](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/comic_history_main.php)
  *   **Instrucciones:** Consumir `GET /api/v1/totem/museum-history/historia-comica`. Crear un layout interactivo de línea de tiempo vertical. Habilitar scroll exclusivamente en el contenedor del timeline.
- [ ] **B2 — Post Editorial Individual** (`/museo/historia-comica/{slug}`)
  *   **Ruta de Implementación:** Crear [app/Views/totem/comic_history_post.php](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/comic_history_post.php)
  *   **Instrucciones:** Consumir `GET /api/v1/totem/museum-history/{slug}` y pintar la información/fotos de manera elegante y legible.

### 3. Bloque C: Explora el Museo
- [ ] **Rutas estáticas en CodeIgniter 4**
  *   **Ruta de Implementación:** Modificar [app/Config/Routes.php](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Config/Routes.php)
  *   **Instrucciones:** Registrar las siguientes subpáginas:
      ```php
      $routes->get('museo/el-museo/edificio', 'TotemController::museumBuilding');
      $routes->get('museo/el-museo/institucion', 'TotemController::museumInstitution');
      $routes->get('museo/el-museo/actualidad', 'TotemController::museumToday');
      ```
- [ ] **C1 — Sub-menú Explora el Museo** (`/museo/el-museo`)
  *   **Ruta de Implementación:** Crear [app/Views/totem/museum_info_main.php](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/museum_info_main.php)
  *   **Instrucciones:** Menu de 3 grandes opciones táctiles (Edificio, Institución, Actualidad) de acuerdo con los diseños recibidos el 2/6.
- [ ] **C2 — Historia del Edificio** (`/museo/el-museo/edificio`)
  *   **Ruta de Implementación:** Crear [app/Views/totem/museum_building.php](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/museum_building.php)
  *   **Instrucciones:** Consumir `GET /api/v1/totem/museum`. Filtrar los datos de la Iglesia San Judas Tadeo y pintar el texto e imágenes.
- [ ] **C3 — Historia de la Institución** (`/museo/el-museo/institucion`)
  *   **Ruta de Implementación:** Crear [app/Views/totem/museum_institution.php](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/museum_institution.php)
  *   **Instrucciones:** Consumir `GET /api/v1/totem/museum` y renderizar misión, visión y estructura interna del teatro.
- [ ] **C4 — Historia en la Actualidad** (`/museo/el-museo/actualidad`)
  *   **Ruta de Implementación:** Crear [app/Views/totem/museum_today.php](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/museum_today.php)
  *   **Instrucciones:** Consumir `GET /api/v1/totem/museum` y mostrar logros más representativos (e.g. FMIM 2024).

### 4. Bloque D: Teatro Escuela (Rediseño a Dossier)
- [ ] **D1 — Rediseño del Layout del Teatro Escuela** (`/teatro-escuela`)
  *   **Ruta de Implementación:** Modificar [app/Views/totem/section.php](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/section.php) (o crear una vista dedicada `theater_school_dossier.php`)
  *   **Instrucciones:**
      1. Integrar sección superior para embed de YouTube (silenciado e interactivo).
      2. Crear bloque con cifras y datos clave del teatro (stats).
      3. Añadir carrusel de tarjetas compactas de Maestros y Alumnos Destacados.
      4. Incluir bloque de cursos dinámicos vinculados a la API.

### 5. Bloque E: Extensión & Formulario de Contacto
- [ ] **E1 — Teclado Táctil e Integración de QR Dinámico** (`/extension`)
  *   **Ruta de Implementación:**
      *   Modificar [app/Config/Routes.php](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Config/Routes.php) (Registrar `/extension` y configurar redirect 301/302 de `/visitas-guiadas` a `/extension`)
      *   Crear [app/Views/totem/extension_contact.php](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/extension_contact.php)
  *   **Instrucciones:**
      1. Cargar e inicializar la librería `simple-keyboard` en la pantalla. Desplegar teclado virtual en la zona inferior cuando un input de texto o email obtenga el foco táctil.
      2. Integrar `qrcode.js` para generar de manera dinámica un código QR que sirva como alternativa si el WiFi falla. La URL debe apuntar a la sección de contacto del sitio web agregando `?utm_source=totem`.

### 6. Bloque F: Lógicas e Interactividades de UX
- [ ] **F1 — Carrusel Táctil en Detalle de Obra**
  *   **Ruta de Implementación:** Modificar [public/assets/js/app.js](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/public/assets/js/app.js)
  *   **Instrucciones:** Desarrollar en vanilla JS un interceptor de deslizamientos (`touchstart`, `touchmove`, `touchend`) para transicionar imágenes en galerías con total fluidez táctil y sin librerías externas que sobrecarguen el kiosk.
- [ ] **F2 — Lazy Loading de YouTube Video Iframes**
  *   **Ruta de Implementación:**
      *   Modificar [app/Views/totem/billboard_detail.php](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Views/totem/billboard_detail.php)
      *   Modificar [public/assets/js/app.js](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/public/assets/js/app.js)
  *   **Instrucciones:**
      1. Mostrar inicialmente una imagen del thumbnail del video con botón de Play estilizado en alto contraste.
      2. Sólo tras presionar el botón, inyectar el `<iframe>` dinámicamente forzando los parámetros `autoplay=1&mute=1&cc_load_policy=1`.
- [ ] **F3 — Preparación de Contenedores de Ilustraciones**
  *   **Ruta de Implementación:** Modificar parciales CSS en [public/assets/css/src/screens/](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/public/assets/css/src/screens/)
  *   **Instrucciones:** Asegurar que los contenedores CSS con IDs como `#il-payaso` tengan altura y anchura explícitas para evitar saltos bruscos en el layout (*CLS*) cuando se intercambien las imágenes PNG por GIFs o Lotties animados finales.
- [ ] **F4 — Solución a la Ambigüedad del Enlace `/historia`**
  *   **Ruta de Implementación:** Modificar [app/Controllers/TotemController.php](file:///Users/davidcardenas/Developer/PHP/teatromuseo/teatromuseo-totem-ci4/app/Controllers/TotemController.php)
  *   **Instrucciones:** Tras la reunión del 1/6 con Paulina, aplicar la resolución (e.g. eliminar o configurar redirect 302 hacia `/museo/el-museo`).

---

## 🌊 OLEADA 3: Marcha Blanca & Producción (15/6 en adelante)
> **Objetivo:** Asegurar la estabilidad final del tótem en las pantallas de Fully Kiosk dentro del teatro, optimizar rendimiento y preparar material de contingencia.

### 1. Calibración en Hardware Físico
- [ ] **Pruebas de Usabilidad Táctil e Interfaz**
  *   **Instrucciones:**
      1. Instalar la versión compilada en el hardware vertical real del teatro.
      2. Verificar que el Fully Kiosk Browser bloquee adecuadamente gestos externos del navegador, barras de scroll y gestos de zoom/pinch.
      3. Validar áreas de click/tap que midan al menos 48x48px y ajustar márgenes de tipografía si es difícil leer a la distancia esperada.
- [ ] **Optimización y caché**
  *   **Instrucciones:** Monitorear el tiempo de carga de las imágenes y optimizarlas en formato `.webp` si causan latencia perceptible en la red local.

### 2. Estabilidad Técnica & Fallback Offline
- [ ] **Pruebas de Tolerancia a Fallas de Red**
  *   **Instrucciones:**
      1. Simular la desconexión total a Internet del tótem.
      2. Validar que la interfaz no quede "colgada" ni muestre alertas de error técnico en pantalla.
      3. Asegurar que cargue las últimas variables del caché de forma graceful o presente la pantalla amigable de error multiidioma.
- [ ] **Carga de Assets de Ilustración Definitivos**
  *   **Instrucciones:** Colocar y renombrar todos los GIFs/Lotties JSON enviados por Coni en sus respectivas carpetas y comprobar su óptimo rendimiento en el procesador local del tótem.

### 3. Operación
- [ ] **Creación del Manual de Soporte de Operaciones**
  *   **Instrucciones:** Crear un documento técnico breve en la carpeta del tótem detallando los pasos de encendido/apagado, reinicio rápido, limpieza de caché de Fully Kiosk y reinicio de servidores locales.
