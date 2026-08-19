# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added

- **Background BFF cache warm-up (TOTEM-BFF-10)** — new `php spark totem:warm-cache`
  command proactively refreshes the fresh/stale cache for every kiosk screen across all
  4 locales, instead of relying solely on visitor traffic to populate it. Meant to run on
  a ~5 min cron (see `RELEASE.md`).
- **Interactive museum collection content** — connected collection pages to the Totem API and
  added the clowns exhibit content.
- **Billboard interactions** — added day filter chips and a multi-image slider to billboard views.
- **Deployment and environment tooling** — added non-interactive environment initialization,
  production Docker parity, and deployment-oriented project setup.
- **Favicon and app-icon set** — added a full favicon/manifest set (SVG, ICO, PNG sizes,
  apple-touch-icon, web app manifest) with cache-busted asset URLs and long-lived
  Cache-Control headers.

### Changed

- **Cache backend persistence (TOTEM-BFF-09)** — switched the primary cache handler from
  `apcu` (in-process memory, wiped on PHP-FPM restart/deploy) to `file` (disk-backed), so the
  fresh/stale BFF cache survives restarts and deploys instead of silently losing hours of
  offline resilience. Added `TOTEM_BFF_TIMEOUT_SECONDS` env var so the BFF HTTP client timeout
  is configurable instead of hardcoded.
- **Fleet configuration** — aligned base URL and Totem API URL with the fleet port conventions.
- **Quality workflow** — aligned Composer scripts and Git hooks with workspace conventions.
- **Collection and school view contracts** — updated presenters and views to expose
  `featuredCourse` and prevent request-to-request view context leakage.

### Fixed

- **Collection and Totem PHPStan issues** — resolved pre-existing type errors in collection,
  story, and Totem API helpers.
- **Totem cache placeholder** — restored the missing writable cache index placeholder.
- **Content assertions** — synchronized stale tests with the current views and data.
