# Release procedure — teatromuseo-totem-ci4

This app does **not** ship via a GitHub Actions release pipeline like the other
apps in this workspace. It is a kiosk build deployed straight to a cPanel
shared-hosting subdomain over FTP, driven by the scripts in `.deploy/`
(hidden, gitignored, and excluded from what actually gets uploaded — see
`BLACKLIST_DIRS`/`BLACKLIST_FILES` in `.deploy/deploy.py`). Treat this
document as the authoritative "how do I ship a change" for this app; do not
add a `.github/workflows/release.yml` here unless the deploy mechanism itself
changes.

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
5. **`.deploy/.env.deploy` exists locally** with real cPanel FTP credentials (copied from `.env.deploy.example`, `chmod 600`, never committed).

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
4. **Deploy the CSS/asset build, then the full app:**
   ```bash
   python3 .deploy/sync-css.py   # fast path: only compiled stylesheets, ~1s
   python3 .deploy/deploy.py     # full sync: everything except the blacklist above
   ```
   Both scripts read credentials from `.deploy/.env.deploy` and write a timestamp to `.last_ftp_deploy` so the next `deploy.py` run only uploads what changed since.
5. **Smoke-test in production:** load the kiosk URL, confirm the splash screen and `curl https://<prod-host>/health` return healthy, and spot-check one screen per domain controller (Colección, Museo, Escuela, Cartelera, Amigos).

## Notes

- There is deliberately no Docker-based release path in production for this app — the `Dockerfile` in this repo exists for local parity/testing with the rest of the workspace, not for how this app is actually deployed today.
- `FTP_*` variables in `.env` (root) are never read by CodeIgniter; only `.deploy/.env.deploy` matters to the deploy scripts.
