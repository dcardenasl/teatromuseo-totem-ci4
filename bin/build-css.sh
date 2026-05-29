#!/usr/bin/env bash
# Concatena los parciales en public/assets/css/src/ → public/assets/css/style.css
#
# El orden importa: respeta el cascade original. Si agregas un parcial nuevo,
# añadelo en el lugar correcto de la lista FILES.
#
# Uso:
#   bash bin/build-css.sh

set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
SRC="$ROOT/public/assets/css/src"
OUT="$ROOT/public/assets/css/style.css"

FILES=(
    "$SRC/00-tokens.css"
    "$SRC/01-base.css"
    "$SRC/shared/header.css"
    "$SRC/shared/screen.css"
    "$SRC/shared/hero.css"
    "$SRC/screens/splash.css"
    "$SRC/screens/language.css"
    "$SRC/screens/idle.css"
    "$SRC/screens/menu.css"
    "$SRC/screens/section.css"
    "$SRC/screens/section-extras.css"
    "$SRC/screens/billboard.css"
    "$SRC/screens/detail.css"
    "$SRC/shared/utils.css"
    "$SRC/shared/orientation-warning.css"
    "$SRC/02-shell.css"
    "$SRC/shared/footer-ornament.css"
    "$SRC/99-responsive.css"
)

for f in "${FILES[@]}"; do
    if [[ ! -f "$f" ]]; then
        echo "ERROR: parcial no encontrado: $f" >&2
        exit 1
    fi
done

cat "${FILES[@]}" > "$OUT"
echo "Generado: $OUT ($(wc -l < "$OUT") líneas)"
