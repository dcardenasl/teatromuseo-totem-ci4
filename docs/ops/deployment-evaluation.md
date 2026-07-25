# Evaluación de Despliegue — Tótem Interactivo

> Análisis del proceso actual de despliegue y recomendaciones para mejoras.

---

## Estado actual

### Scripts de despliegue

| Script | Propósito | Estado |
|--------|-----------|--------|
| `.deploy/deploy.py` | Despliegue FTP incremental | ✅ Activo |
| `.deploy/sync-css.py` | Sincronización rápida de CSS | ⚠️ Legacy (opcional) |

### Características de seguridad actuales

✅ **Protección de credenciales:**
- Archivo `.deploy/.env.deploy` con permisos 600
- Validación de permisos en `deploy.py` (rechaza si no es 600)
- `.env.deploy` en `.gitignore` (no se commitea)

✅ **Exclusiones de despliegue:**
- `.env`, vendor/, tests/, writable/
- Archivos de desarrollo y configuración local

⚠️ **Limitaciones:**
- Usa FTP (puerto 21) sin cifrado
- No hay rollback automático
- Sin verificación post-despliegue

---

## Recomendaciones

### Opción 1: SFTP/FTPS (corto plazo)

**Ventajas:**
- Mínimos cambios en el proceso actual
- Tráfico cifrado
- Mayoría de hosting soporta SFTP

**Implementación:**
```python
# Reemplazar ftplib.FTP por paramiko.SFTPClient
import paramiko

transport = paramiko.Transport((host, 22))
transport.connect(username=user, password=passw)
sftp = paramiko.SFTPClient.from_transport(transport)
```

**Cambios necesarios:**
1. Instalar `paramiko`: `pip install paramiko`
2. Cambiar puerto default a 22
3. Actualizar script para usar SFTP

### Opción 2: CI/CD con GitHub Actions (recomendado)

**Ventajas:**
- Despliegue automatizado desde GitHub
- Secrets encriptados en GitHub
- Tests automáticos antes de desplegar
- Rollback fácil (revertir commit)

**Implementación:**

1. **Agregar secrets al repositorio:**
   - `DEPLOY_HOST`
   - `DEPLOY_USER`
   - `DEPLOY_KEY` (clave SSH privada)
   - `DEPLOY_PATH`

2. **Workflow de despliegue** (`.github/workflows/deploy.yml`):

```yaml
name: Deploy

on:
  push:
    branches: [main]

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
      
      - name: Install dependencies
        run: composer install --no-dev --optimize-autoloader
      
      - name: Build CSS
        run: npm ci && npm run build:css
      
      - name: Deploy via SSH
        uses: easingthemes/ssh-deploy@v2
        with:
          SSH_PRIVATE_KEY: ${{ secrets.DEPLOY_KEY }}
          REMOTE_HOST: ${{ secrets.DEPLOY_HOST }}
          REMOTE_USER: ${{ secrets.DEPLOY_USER }}
          TARGET: ${{ secrets.DEPLOY_PATH }}
          EXCLUDE: ".git/, .env, tests/, writable/"
```

### Opción 3: Docker + Orquestador (largo plazo)

**Ventajas:**
- Ambientes idénticos (dev/prod)
- Escalabilidad
- Rollback instantáneo

**Implementación:**
```dockerfile
FROM php:8.2-apache
COPY . /var/www/html
RUN composer install --no-dev
EXPOSE 80
```

---

## Plan de migración propuesto

### Fase 1: Inmediata (ahora)

1. ✅ Eliminar archivos legacy (`totem-prod.zip`)
2. ✅ Documentar proceso actual
3. ⚠️ Mantener `sync-css.py` como opción de desarrollo

### Fase 2: Corto plazo (1-2 semanas)

1. Evaluar soporte SFTP en hosting actual
2. Si es posible: migrar `deploy.py` a SFTP
3. Crear cuenta de deploy con permisos limitados

### Fase 3: Mediano plazo (1 mes)

1. Configurar GitHub Actions para CI/CD
2. Migrar secrets a GitHub
3. Probar despliegue automático en staging
4. Desactivar despliegue manual una vez validado

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

1. ✅ Documentado proceso actual
2. ✅ Eliminado `totem-prod.zip` (legacy)
3. ✅ Evaluadas opciones de mejora
4. ✅ Propuesto plan de migración a CI/CD

---

## Referencias

- Script actual: `.deploy/deploy.py`
- Configuración: `.deploy/.env.deploy`
- CI existente: `.github/workflows/ci.yml`
