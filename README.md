# Teatro Museo — Interactive Kiosk Totem

This repository contains the interactive kiosk totem frontend application for **Teatromuseo del Títere y el Payaso** built on **CodeIgniter 4**.

## Architecture & Layout Overview
- **Device Port:** Runs locally on port `8086`.
- **Target Resolution:** Designed specifically for a high-definition vertical touch display (`1080x1920` / viewport ratio `9:16`).
- **Layout Model:** Uses fluid typography (`cqi` units), container query context (`@container kiosk`), and safe touch zones (≥44px min-height targets).
- **Offline & Hardcoded Data Resilience:** Contains a private local data repository inside the controller so the kiosk can run completely autonomous without external APIs or in offline environments.

---

## Design System & Tokens
All style tokens are structured under CSS Custom Properties in `public/assets/css/src/00-tokens.css`:

- **Official Color Palette:**
  - Background (Paper): `#f8f5ec` (`--paper`)
  - Typography / Details (Ink): `#353430` (`--ink`)
  - Accent / Actions (Orange): `#de5928` (`--accent`)
  - Secondary Grids (Vibrant):
    - Light Blue: `#8fa6f0` (`--grid-blue`)
    - Dark Purple: `#693592` (`--grid-purple`)
    - Crimson Wine: `#880b2e` (`--grid-wine`)
- **Interactive States:** Uses tactile `:active` scale transformation feedback and clear accessibility outlines for `:focus-visible`.

---

## Styling Architecture
We use a modular stylesheet pattern where separate files in `public/assets/css/src/` are compiled into the production output `public/assets/css/style.css`. 

> [!WARNING]
> Do NOT edit `public/assets/css/style.css` directly! It is overwritten upon every compilation. Always edit partials in `public/assets/css/src/` instead.

### Build CSS Command
Whenever you modify files under `public/assets/css/src/`, recompile using:
```bash
composer build:css
# or directly:
bash bin/build-css.sh
```

---

## Local Development & Setup

### Requirements
- PHP 8.2 or higher
- Composer

### Installation
1. Install dependencies:
   ```bash
   composer install
   ```
2. Copy environmental variables:
   ```bash
   cp env .env
   ```
3. Start the server:
   ```bash
   php spark serve --port 8086
   ```
4. Access in your browser: `http://localhost:8086`

---

## Folder Structure
```
├── app/
│   ├── Controllers/
│   │   └── TotemController.php      # Main controller with views and offline data
│   └── Views/
│       ├── layouts/
│       │   └── MainLayout.php       # Shell layouts, HTML heads, asset loaders
│       └── totem/
│           ├── splash.php           # Landing touch to begin screen
│           ├── language.php         # Multi-language selector page
│           ├── main_menu.php        # Primary content modules selection
│           └── section.php          # Dynamic section views (Museum, History, etc.)
├── bin/
│   └── build-css.sh                 # High performance CSS build/concat pipeline
└── public/
    └── assets/
        ├── css/
        │   ├── src/                 # Development CSS partial modules
        │   └── style.css            # Compiled production style sheet
        ├── fonts/                   # Lato Typographic families
        └── js/
            └── app.js               # Multi-language switcher dictionary client
```

---

## Secure Deployment Exclusions
Deployment scripts in `.deploy/` exclude local sensitive configurations:
- Excludes `.env`, `composer.json`, `composer.lock`, `.deploy/` and macOS metadata files.
- Minimizes package sizes to optimize totem sync speeds in 1 second.
