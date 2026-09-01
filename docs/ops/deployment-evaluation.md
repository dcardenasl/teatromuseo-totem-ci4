# Evaluación de Despliegue — Tótem Interactivo

> Estado actualizado después de implementar la normalización del helper.

---

## Estado actual

### Scripts de despliegue

| Script | Propósito | Estado |
|--------|-----------|--------|
| `scripts/deploy_ftp.py` | Helper común FTPS/FTP, health check, rollback y prune | ✅ Activo |
| `.deploy/deploy.py` | Wrapper versionado compatible | ✅ Activo |

### Características de seguridad actuales

✅ **Protección de credenciales:**
- Archivo `.deploy/.env.deploy` con permisos 600
- Validación de permisos en `deploy.py` (rechaza si no es 600)
- Solo credenciales, estado y backups quedan en `.gitignore`

✅ **Exclusiones de despliegue:**
- `.env*`, `vendor/`, `tests/`, `writable/`, `docs/`, `scripts/`
- Documentación, caches, tooling y configuración local

✅ **Controles implementados:**
- FTPS verificado por defecto; FTP plano solo con opt-in explícito
- Backup local por release y rollback manual/automático
- Health check configurable antes de confirmar el estado incremental
- Reconciliación remota explícita mediante `--prune`

---

## Operación recomendada

```bash
cp .deploy/.env.deploy.example .deploy/.env.deploy
chmod 600 .deploy/.env.deploy
python3 .deploy/deploy.py --dry-run
python3 .deploy/deploy.py --yes
python3 .deploy/deploy.py --rollback <release-id>
```

Configure `DEPLOY_HEALTHCHECK_URL=https://<prod-host>/health`. Use
`--prune` only when the remote listing is understood and the deletion list has
been reviewed.

---

## Pending infrastructure work

- Confirm that the cPanel account supports verified FTPS and has a dedicated
  least-privilege deploy user.
- Add a GitHub Actions deploy workflow only after the manual FTPS flow is
  validated against staging and its secrets can be managed safely.

---

## Checklist de seguridad para despliegue

Antes de cada despliegue, verificar:

- [ ] `composer test` pasa localmente
- [ ] `composer lint` no reporta errores
- [ ] `composer analyse` está limpio
- [ ] `composer build:css` se ejecutó
- [ ] `.env` no está en los archivos a subir
- [ ] Credenciales tienen permisos 600
- [ ] Cambios commiteados y pusheados

---

## Acciones realizadas en F4-T8

1. ✅ Unified the deploy helper with the other applications.
2. ✅ Removed the duplicate CSS sync and cleanup scripts.
3. ✅ Added FTPS, health-check, rollback and explicit prune support.
4. ✅ Versioned the tooling while keeping credentials/state ignored.

---

## Referencias

- Script actual: `.deploy/deploy.py`
- Configuración: `.deploy/.env.deploy`
- CI existente: `.github/workflows/ci.yml`
