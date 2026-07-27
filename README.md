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
# Conexión API (obligatorio)
TOTEM_API_URL=http://localhost:8180/api/v1/totem
TOTEM_API_KEY=your-api-key-here

# Feature flags
TOTEM_ENABLE_TRANSITIONS=true
TOTEM_ENABLE_ANIMATIONS=true
TOTEM_ENABLE_FILE_CACHE=true

# Cache TTL (segundos)
TOTEM_CACHE_TTL_SECONDS=60

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
│   ├── TotemApiInterface.php   # Contrato de API
│   ├── TotemApiService.php     # Implementación base
│   ├── CachedTotemApiService.php      # Memoización por request
│   ├── FileCachedTotemApiService.php  # Cache en archivo
│   ├── MenuBuilder.php         # Generador de menú
│   └── NavBuilder.php          # Generador de navegación
├── Presenters/
│   ├── SchoolPresenter.php
│   ├── BillboardPresenter.php
│   ├── MuseumTodayPresenter.php
│   └── DatePresenter.php
├── Repositories/
│   ├── SchoolFallbackRepository.php
│   ├── BillboardFallbackRepository.php
│   └── MuseumFallbackRepository.php
└── Enums/
    ├── Audience.php
    └── SchoolCategory.php

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

El tótem implementa 4 capas de resiliencia:

1. **API online**: Datos frescos desde la API
2. **File Cache**: Última respuesta válida (TTL configurable)
3. **Fallback Repositories**: Datos estáticos embebidos
4. **Pantalla amigable**: Mensaje multiidioma si no hay datos

Ver: `docs/ops/offline-fallback-strategy.md`

---

## Despliegue

### FTP/FTPS (actual)

```bash
# Configurar credenciales en .deploy/.env.deploy
# FTP_HOST, FTP_USER, FTP_PASS, FTP_REMOTE_PATH

# Ejecutar despliegue
python3 .deploy/deploy.py
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
