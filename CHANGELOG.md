# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added

- **Interactive museum collection content** — connected collection pages to the Totem API and
  added the clowns exhibit content.
- **Billboard interactions** — added day filter chips and a multi-image slider to billboard views.
- **Deployment and environment tooling** — added non-interactive environment initialization,
  production Docker parity, and deployment-oriented project setup.

### Changed

- **Fleet configuration** — aligned base URL and Totem API URL with the fleet port conventions.
- **Quality workflow** — aligned Composer scripts and Git hooks with workspace conventions.
- **Collection and school view contracts** — updated presenters and views to expose
  `featuredCourse` and prevent request-to-request view context leakage.

### Fixed

- **Collection and Totem PHPStan issues** — resolved pre-existing type errors in collection,
  story, and Totem API helpers.
- **Totem cache placeholder** — restored the missing writable cache index placeholder.
- **Content assertions** — synchronized stale tests with the current views and data.
