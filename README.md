# Teatro Museo — Tótem Interactivo

Aplicación de kiosko interactivo para **Teatromuseo del Títere y el Payaso**, construida con **CodeIgniter 4**. Diseñada para pantallas táctiles verticales (1080×1920) en modo kiosko.

[![CI](https://github.com/davidcardenas/teatromuseo-totem-ci4/actions/workflows/ci.yml/badge.svg)](https://github.com/davidcardenas/teatromuseo-totem-ci4/actions/workflows/ci.yml)

---

## Características principales

- **Stateless**: Sin base de datos propia, consume API REST externa
- **Resiliente**: Funciona offline con cache en archivo y datos de fallback
- **Multiidioma**: Español, inglés, francés y portugués
- **Accesible**: Targets táctiles ≥44px, soporte para `prefers-reduced-motion`
- **Observable**: Logs estructurados y endpoint de health check

---

## Arquitectura

```
┌─────────────────────────────────────────────────────────────┐
│  Vistas (Views)        →  Extend MainLayout                  │
│  Controladores         →  Heredan BaseTotemController        │
│  Servicios             →  TotemApiInterface + Decoradores    │
│  Presenters            →  Transforman datos para vistas      │
│  Fallback Repositories →  Datos de contingencia offline      │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
                    ┌─────────────────┐
                    │  API REST       │
                    │  (teatromuseo)  │
                    └─────────────────┘
```

---

## Requisitos

- PHP 8.2+
- Composer
- Node.js 18+ (para build de CSS)

---

## Instalación

```bash
# 1. Clonar repositorio
git clone https://github.com/davidcardenas/teatromuseo-totem-ci4.git
cd teatromuseo-totem-ci4

# 2. Instalar dependencias PHP
composer install

# 3. Instalar dependencias Node (para CSS)
npm install

# 4. Configurar variables de entorno
cp env .env
# Editar .env con tus valores

# 5. Compilar CSS
composer build:css

# 6. Iniciar servidor de desarrollo
php spark serve --port 8186
```

Acceder en: `http://localhost:8186`

---

## Variables de entorno

```bash
# Conexión al BFF (obligatorio) — teatromuseo-bff, seam public-read
TOTEM_BFF_BASE_URL=http://localhost:8188
TOTEM_BFF_API_KEY=your-app-key-here

# Feature flags
TOTEM_ENABLE_TRANSITIONS=true
TOTEM_ENABLE_ANIMATIONS=true

# Cache fresh+stale (BffTotemClient) y timeout HTTP, en segundos
TOTEM_CACHE_TTL_SECONDS=60
TOTEM_STALE_TTL_SECONDS=86400
TOTEM_BFF_TIMEOUT_SECONDS=5

# App
app.baseURL=http://localhost:8186/
app.appTimezone=America/Santiago
CI_ENVIRONMENT=development
```

---

## Estructura del proyecto

```
app/
├── Config/
│   ├── Routes.php              # Rutas nombradas
│   ├── Services.php            # Registro de servicios
│   └── Totem.php               # Configuración del tótem
├── Commands/
│   └── WarmBffCache.php        # totem:warm-cache — calentamiento de caché en background
├── Controllers/
│   ├── BaseTotemController.php # Controlador base con helpers
│   ├── MainController.php      # Splash, menú, idioma, 404
│   ├── CollectionController.php# Colección: técnicas, títeres, máscaras
│   ├── MuseumController.php    # Museo: hoy, edificio, institución
│   ├── SchoolController.php    # Teatro escuela
│   ├── BillboardController.php # Cartelera
│   ├── FriendsController.php   # Amigos, extensión
│   └── HealthController.php    # Health check /health
├── Services/
│   ├── BffTotemClient.php      # Único cliente HTTP — teatromuseo-bff (public-read)
│   ├── TotemApiResult.php      # Resultado tipado: {data, state: fresh|stale|unavailable}
│   ├── MenuBuilder.php         # Generador de menú
│   ├── NavBuilder.php          # Generador de navegación
│   └── SlugResolver.php        # Resolución de IDs desde slugs
├── Presenters/
│   ├── SchoolPresenter.php     # Cursos/escuela (TotemApiResult → vista)
│   ├── BillboardPresenter.php  # Cartelera (TotemApiResult → vista)
│   ├── CollectionPresenter.php # Piezas/técnicas/categorías (TotemApiResult → vista)
│   ├── MuseumTodayPresenter.php
│   └── DatePresenter.php
└── Enums/
    ├── Audience.php
    └── SchoolCategory.php
```

No hay `app/Repositories/` ni `app/Data/*.json`: no existe contenido de relleno en este proyecto — ver sección "Resiliencia offline" abajo.

```
public/assets/css/
├── style.css          # ← Compilado (NO editar)
└── src/               # ← Editar aquí
    ├── 00-tokens.css  # Variables CSS
    ├── 01-base.css
    ├── 02-shell.css
    ├── shared/        # Componentes reutilizables
    └── screens/       # Estilos por pantalla
```

---

## Comandos disponibles

```bash
# Tests
composer test        # PHPUnit

# Calidad de código
composer lint        # PHP-CS-Fixer (dry-run)
composer format      # PHP-CS-Fixer (fix)
composer analyse     # PHPStan nivel 8
composer quality      # format:check + analyse + test

# CSS
composer build:css   # Compilar CSS con PostCSS

# Calentamiento de caché en background (TOTEM-BFF-10)
php spark totem:warm-cache
```

Los hooks de git (`pre-commit`, `pre-push`) se instalan automáticamente al correr
`composer install`/`composer update`.

---

## Health Check

```bash
curl http://localhost:8186/health
```

Respuesta:
```json
{
  "status": "ok",
  "api": "reachable",
  "timestamp": "2026-06-12T23:02:17-04:00"
}
```

---

## Resiliencia offline

El tótem no mantiene base de datos propia y consume `teatromuseo-bff` (seam
`public-read`) a través de `App\Services\BffTotemClient`, el único cliente
HTTP de la app. Cada llamada devuelve un `TotemApiResult` tipado
(`fresh`/`stale`/`unavailable`) — nunca un array ambiguo que confunda
"fuente caída" con "genuinamente vacío":

1. **Fresh**: caché con TTL corto (`TOTEM_CACHE_TTL_SECONDS`, 60s) — la
   respuesta real más reciente.
2. **Stale**: caché en disco con TTL largo (`TOTEM_STALE_TTL_SECONDS`,
   24h) — la última respuesta real exitosa, servida cuando el BFF falla
   (timeout, 5xx, JSON inválido). Solo avanza tras un éxito real, nunca
   tras una excepción. El handler de caché es `file` (persiste a través de
   reinicios de PHP-FPM y deploys — ver `app/Config/Cache.php`).
3. **Calentamiento proactivo**: `php spark totem:warm-cache`
   (`app/Commands/WarmBffCache.php`) refresca las listas/facetas de cada
   pantalla en background, sin depender de que un visitante dispare la
   primera request. Ejecutar por cron cada ~5 min (ver `.deploy/README.md`
   para la línea exacta en el hosting de producción).
4. **Pantalla amigable**: cuando ni fresh ni stale tienen datos
   (`unavailable`), se muestra `totem/partials/content_unavailable.php` —
   solo en el bloque dependiente de esa fuente, nunca el diseño estático
   (hero/intro/cifras) de la pantalla.

**No existen repositorios de fallback ni contenido mock.** `app/Repositories/`
y `app/Data/*.json` no existen — una pantalla sin datos reales muestra un
estado honesto, nunca datos inventados. Esto está reforzado por un test de
arquitectura (`tests/unit/Architecture/StatelessArchitectureTest.php`) que
falla el build si reaparece cualquier referencia al proxy histórico del Hub
(`TOTEM_API_URL`, `X-Totem-Key`, `TotemApiService`, etc.).

---

## Despliegue

### FTP/FTPS (actual)

```bash
# Configurar credenciales en .deploy/.env.deploy (chmod 600)
# FTP_PROTOCOL=ftps, FTP_HOST, FTP_USER, FTP_PASS, FTP_REMOTE_DIR

# Revisar y ejecutar despliegue
python3 .deploy/deploy.py --dry-run
python3 .deploy/deploy.py --yes

# Restaurar un release guardado
python3 .deploy/deploy.py --rollback <release-id>
```

### CI/CD (recomendado)

El proyecto incluye workflow de GitHub Actions en `.github/workflows/ci.yml`.

---

## Documentación adicional

- [Convenciones para desarrolladores](AGENTS.md)
- [Estrategia offline](docs/ops/offline-fallback-strategy.md)
- [Manual de soporte](docs/ops/support-manual.md)
- [Plan de auditoría](docs/plans/audit-and-refactor-plan.md)

---

## Licencia

Proprietary - Teatromuseo del Títere y el Payaso
