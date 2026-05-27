# CSS source (split)

`public/assets/css/style.css` se **genera** concatenando los parciales de esta
carpeta. **No edites `style.css` a mano** — edita el parcial correspondiente y
regenera:

```bash
bash bin/build-css.sh
# o:
composer build:css
```

## Estructura

```
src/
├── 00-tokens.css                 :root + variables (--paper, --ink, --accent…)
├── 01-base.css                   reset, html/body
├── 02-shell.css                  .kiosk-shell, .totem-stage, .totem-page, .screen
├── 99-responsive.css             @container queries (responsive del tótem)
├── shared/
│   ├── header.css                .totem-header, .totem-brand, .pill-button
│   ├── screen.css                .screen__panel, .screen-page
│   ├── hero.css                  .hero (eyebrow, title, cta, ornaments)
│   ├── utils.css                 .footer-logos, .logo-badge, .rule, .sr-only
│   └── footer-ornament.css       .footer-ornament, .footer-cloud, .footer-stage--*
└── screens/
    ├── splash.css                splash.php (decoración + collage)
    ├── language.css              language.php
    ├── menu.css                  main_menu.php
    ├── section.css               section.php — section-*, course-*, school
    ├── section-extras.css        section.php — content-panel, stat-card, visual overrides
    ├── billboard.css             billboard.php — billboard-*, event-*, month-*, date-chip
    └── detail.css                billboard_detail.php
```

## Convenciones

- **Tokens (colores, sombras, radios)** → solo en `00-tokens.css`. Si necesitas
  un valor nuevo, añádelo como variable CSS, no como hex suelto.
- **BEM por pantalla**: `.<screen>-<block>__<element>--<modifier>`
  (`.splash-bird`, `.menu-card__art`, `.event-card__title--compact`).
- **Compartido entre ≥ 2 pantallas** → vive en `shared/`. **Específico de una
  sola** → vive en `screens/<nombre>.css`.
- **No editar `style.css` directamente** — el build lo sobrescribe.

## Orden en el build

`bin/build-css.sh` concatena los parciales en este orden, que respeta la
cascada original:

1. `00-tokens.css`
2. `01-base.css`
3. `shared/header.css`, `screen.css`, `hero.css`
4. `screens/*.css` (splash, language, menu, section, section-extras, billboard, detail)
5. `shared/utils.css`
6. `02-shell.css` *(redeclara `.screen` — debe ir después de `shared/screen.css`)*
7. `shared/footer-ornament.css`
8. `99-responsive.css`

Si agregas un parcial, añádelo al array `FILES` en `bin/build-css.sh` en el
lugar correcto.

## Workflow

1. Editar parcial en `src/`.
2. `composer build:css` (regenera `style.css`).
3. Refrescar el tótem en el navegador.
