# Audit Logbook: Totem shell centralization

## Objective
- Document the shared shell that wraps every totem screen and centralizes the global content padding used by the app.
- Clarify where responsive spacing tokens live so future screen work changes one system instead of patching each view.

## Environment
- Project: `teatromuseo-totem-ci4`
- Local URL: `http://localhost:8086`
- Screens verified during this audit: menu, cartelera detail, and the shared shell path used by all totem screens.

## Shell Architecture
- `app/Views/layouts/MainLayout.php` defines the outer application frame and injects the shared totem chrome.
- `app/Views/totem/partials/page_shell.php` wraps each screen in `totem-page-shell` and places the visible content inside `<main class="page-content">`.
- `.kiosk-shell` in `public/assets/css/src/02-shell.css` constrains the whole app to the kiosk aspect ratio and keeps the full-screen panel centered.
- `.page-content` in `public/assets/css/src/02-shell.css` is the single global content container where screen padding is applied.
- Individual screen views, including `app/Views/totem/billboard_detail.php`, should not define their own global page padding unless they have a very specific exception.

## Global Spacing Tokens
- `public/assets/css/src/00-tokens.css` defines the shared spacing tokens:
  - `--content-padding-inline`
  - `--content-padding-top`
  - `--content-padding-bottom`
- `public/assets/css/src/99-responsive.css` updates those tokens by kiosk breakpoint:
  - `max-width: 920px` for tablet-style screens
  - `max-width: 640px` for compact/mobile screens
- The current token defaults are:
  - Desktop / large kiosk: `18px` top, `16px` inline, `14px` bottom
  - Tablet: `16px` top, `14px` inline, `14px` bottom
  - Mobile: `14px` top, `12px` inline, `12px` bottom

## Usage Rules
- Use the global shell tokens first when adjusting page breathing room.
- Only override padding inside a specific screen when the layout genuinely needs a local exception.
- Keep screen-specific tweaks limited to content structure, not the app-wide frame.
- When the app feels too tight or too loose, change the shared tokens instead of editing each screen view.

## Files To Review First
- `app/Views/layouts/MainLayout.php`
- `app/Views/totem/partials/page_shell.php`
- `public/assets/css/src/02-shell.css`
- `public/assets/css/src/00-tokens.css`
- `public/assets/css/src/99-responsive.css`

## Evidence
- Verified in the browser that both menu and cartelera detail render through the same shell.
- Verified that the `page-content` container receives the padding token values and changes them by breakpoint.
- Verified that removing per-screen padding from cartelera detail restored the shared spacing system.

## Pending Work
- Decide whether the default desktop token set should be slightly more generous for very large portrait kiosks.
- Consider adding a tiny inline comment near `page-content` in `02-shell.css` if future contributors need a quick reminder that it is the global spacing anchor.

## Final Summary
- The totem now has one shared spacing shell instead of per-screen padding hacks.
- Future layout tuning should happen in the global shell tokens, not inside individual screen views.
