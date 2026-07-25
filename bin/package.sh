#!/usr/bin/env bash
# Script para empaquetar el Tótem para producción
# Uso: bash bin/package.sh

set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
OUT_ZIP="$ROOT/totem-prod.zip"

echo "📦 Creando paquete de producción en: $OUT_ZIP..."

# Eliminar zip anterior si existe
if [ -f "$OUT_ZIP" ]; then
    rm "$OUT_ZIP"
fi

# Cambiar al directorio raíz para evitar rutas absolutas en el zip
cd "$ROOT"

# Comprimir excluyendo archivos innecesarios para producción (pero conservando index.html)
zip -r "$OUT_ZIP" . \
    -x "*.git*" \
    -x "*.DS_Store*" \
    -x "*node_modules*" \
    -x "*tests*" \
    -x "*writable/debugbar/*.json" \
    -x "*writable/logs/log-*.php" \
    -x "*writable/session/ci_session*" \
    -x "*writable/cache/october*" \
    -x "*bin/package.sh*" \
    -x "*phpunit.xml*" \
    -x "*.phpunit.cache*" \
    -x "*builds*"

echo "--------------------------------------------------"
echo "✅ ¡Éxito! Archivo creado exitosamente en:"
echo "   $OUT_ZIP"
echo "--------------------------------------------------"
echo "👉 PRÓXIMOS PASOS:"
echo "1. Sube 'totem-prod.zip' a tu cPanel (ej. en la carpeta del subdominio del tótem)."
echo "2. Haz clic derecho en el archivo en cPanel y selecciona 'Extract'."
echo "3. Edita el archivo '.env' en cPanel y actualiza los dominios a producción."
