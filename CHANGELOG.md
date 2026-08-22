# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added

- **Animated collage assets across kiosk closing sections** — Cartelera (main and detail),
  TeatroEscuela, the main menu coda, the museum info topics, and the splash screen now show
  a lightweight animated collage instead of a static image. The Cartelera and TeatroEscuela
  closing sections were also restructured into a shared, full-bleed stacked layout (QR/contact
  above the collage) instead of the previous side-by-side grid.
- **`totem:warm-cache` guarded against overlapping runs** — the command now acquires a
  non-blocking file lock (`App\Libraries\CommandLock`) before warming the BFF cache and skips
  the run if another invocation already holds it, instead of letting two overlapping cron
  invocations race against the same cache.
- **Full museum catalog pre-warmed, pieces and techniques (TOTEM-BFF-19)** — `totem:warm-cache`
  now seeds every collection item's and every technique's own detail cache directly from a single
  bounded bulk listing call per category (BFF's listing endpoint now accepts the detail field set
  via `fields=`), instead of one HTTP call per piece. Tapping any piece in Catálogo is instant and
  resilient to a bad connection at the kiosk's location, regardless of how large the museum's
  catalog grows — no per-item network fan-out either way.
- **Warm cache for Cartelera's featured event details (TOTEM-BFF-18)** — `totem:warm-cache` now
  also pre-fetches the detail page for each of the up-to-5 events currently shown in Cartelera
  (reusing `BillboardPresenter`'s own selection, still bounded), so tapping any displayed event is
  instant and resilient to a bad connection at the kiosk's location — not just protected by the
  progressive-hydration fallback from `TOTEM-BFF-17`.
- **Progressive hydration for cold billboard detail loads (TOTEM-BFF-17)** — `cartelera/detalle/{slug}`
  no longer blocks the page render on a cold-cache BFF round-trip (per-slug detail pages are
  deliberately excluded from the background warm-up). The shell renders immediately with a loading
  placeholder and a new `GET cartelera/detalle/{slug}/data` endpoint delivers the real content via
  `fetch()` right after paint. Cache hits (the common case) still render synchronously, unchanged.
- **Honest unavailable state for Colección screens (TOTEM-BFF-16)** — `collectionPuppetsExhibit`,
  `collectionMasksExhibit`, `collectionClownsExhibit` and `collectionTechniques` now distinguish
  a genuinely unreachable BFF from a confirmed-empty category, matching the pattern Cartelera and
  TeatroEscuela already used. A fully unreachable BFF now shows `content_unavailable.php`; a
  genuinely empty category shows a new honest empty-state message (`Collection.no_items_*`,
  added in all 4 locales) instead of a silently empty grid.
- **BFF disconnection resilience tests (TOTEM-BFF-13)** — new `tests/feature/*BffResilienceTest.php`
  suites cover the one path unit tests never exercised: a populated cache serving real stale
  content when the BFF goes down, and falling back to the honest "unavailable" state once the
  stale copy is also gone. Verified live against real `teatromuseo-bff`/`teatromuseo-totem-ci4`
  servers, not just test doubles.
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
- **Animated logo asset** — `MainLayout`'s social-sharing meta tags and the header/billboard/
  footer-ornament/section CSS now point at `LogoTeatroMuseoAnimadoLQ.gif` instead of the
  placeholder `logo.gif`.

### Fixed

- **Kiosk pages could be indexed by search engines** — `BaseTotemController::pageMeta()` now
  sets the `X-Robots-Tag: noindex, nofollow, noarchive` response header and a matching
  `metaRobots` value, rendered by `MainLayout` as `<meta name="robots">`; `public/robots.txt`
  disallows all crawling. The totem is a physical kiosk, never a search destination.

- **Collection and Totem PHPStan issues** — resolved pre-existing type errors in collection,
  story, and Totem API helpers.
- **Totem cache placeholder** — restored the missing writable cache index placeholder.
- **Content assertions** — synchronized stale tests with the current views and data.
