# Release procedure — teatromuseo-totem-ci4

This app is a kiosk build deployed to a cPanel shared-hosting subdomain through
the same versioned helper used by the other applications. Production transport
is FTPS by default; plain FTP requires an explicit temporary opt-in. The
helper, wrapper, template and operator notes live under `.deploy/`; only
credentials, state and rollback backups are ignored.

## Pre-flight checklist

Before deploying, every item below must be true. Treat any "no" as a blocker.

1. **CI is green.** `.github/workflows/ci.yml` passes on the commit you're about to deploy (lint, PHPStan, PHPUnit).
2. **Working tree is clean.** `git status --porcelain` returns nothing.
3. **Local quality gate passes.**
   ```bash
   composer quality   # format:check + analyse + test
   npm run build:css  # CSS compiles cleanly from public/assets/css/src/
   ```
4. **`CHANGELOG.md` has a dated `## [X.Y.Z]` section** at the top (under `## [Unreleased]`, which should be empty afterward), matching the version you're about to tag.
5. **`.deploy/.env.deploy` exists locally** with real cPanel credentials
   (copied from `.deploy/.env.deploy.example`, `chmod 600`, never committed).
6. **`DEPLOY_HEALTHCHECK_URL` is configured** to the kiosk HTTPS `/health`
   endpoint so a failed release can be rolled back automatically.

## Release steps

1. **Land the release-marker commit** on `dev` — only `CHANGELOG.md` changes (rename `[Unreleased]` → `[X.Y.Z] — YYYY-MM-DD`, add a fresh empty `[Unreleased]` on top). No code changes in this commit.
   ```bash
   git checkout dev
   git pull --ff-only
   # Edit CHANGELOG.md
   git add CHANGELOG.md
   git commit -m "chore: release vX.Y.Z"
   git push origin dev
   ```
2. **Merge `dev` into `main`** (PR or fast-forward merge — do not squash, the release marker commit should survive).
3. **Tag from `main`.**
   ```bash
   git checkout main && git pull --ff-only
   git tag vX.Y.Z
   git push origin vX.Y.Z
   ```
4. **Build and deploy the app:**
   ```bash
   composer build:css
   python3 .deploy/deploy.py --dry-run
   python3 .deploy/deploy.py --yes
   ```
   The helper uploads only changed runtime files, saves a rollback release ID,
   and runs the configured health check before committing the local state.
5. **Rollback if needed:** use the release ID printed by the deploy:
   ```bash
   python3 .deploy/deploy.py --rollback <release-id>
   ```
6. **Smoke-test in production:** load the kiosk URL, confirm the splash screen
   and `curl https://<prod-host>/health` return healthy, and spot-check one
   screen per domain controller (Colección, Museo, Escuela, Cartelera, Amigos).

## Notes

- There is deliberately no Docker-based release path in production for this app — the `Dockerfile` in this repo exists for local parity/testing with the rest of the workspace, not for how this app is actually deployed today.
- `FTP_*` variables in `.env` (root) are never read by CodeIgniter; only `.deploy/.env.deploy` matters to the deploy helper.
- `--prune` is an explicit reconciliation operation and should only be used
  after reviewing its dry-run output. It requires FTP `MLSD` support.
- **BFF cache warm-up cron (`TOTEM-BFF-10`):** register on the production
  host so the offline "stale" cache is refreshed proactively instead of
  relying purely on visitor traffic:
  ```cron
  */5 * * * * cd /path/to/totem && php spark totem:warm-cache >> writable/logs/warm-cache.log 2>&1
  ```
  This CI4 install has no native task scheduler (`vendor/codeigniter4/framework`
  4.7.3 ships no `Tasks`/`Scheduler` namespace) — a plain crontab entry is
  the correct mechanism here, not a workaround.
