#!/usr/bin/env bash
# Teatro Museo — interactive kiosk (totem) environment initializer.
# Run from the project root after cloning.
#
# Usage:
#   ./init.sh                      interactive prompts for every required value
#   ./init.sh --yes                non-interactive: reads required values from
#                                   env vars (see below), fails if any are missing
#   ./init.sh --yes --skip-server  same, and never offers to start `php spark serve`
#
# Non-interactive (--yes) required env vars:
#   TOTEM_APP_BASE_URL   e.g. http://localhost:8192
#   TOTEM_API_URL        e.g. http://localhost:8180/api/v1/totem (the hub's totem endpoints)
#   TOTEM_API_KEY        shared secret configured as X-Totem-Key on the hub
# Optional: TOTEM_APP_LOCALE (default: es)
#
# This app has no database — there is deliberately no --skip-db here.
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$SCRIPT_DIR"

RED='\033[0;31m'
YELLOW='\033[1;33m'
GREEN='\033[0;32m'
CYAN='\033[0;36m'
BOLD='\033[1m'
RESET='\033[0m'

print_header() { echo -e "\n${BOLD}${CYAN}── $* ──${RESET}"; }
print_ok()     { echo -e "  ${GREEN}${BOLD}✓${RESET}  $*"; }
print_warn()   { echo -e "  ${YELLOW}${BOLD}!${RESET}  $*"; }
print_error()  { echo -e "  ${RED}${BOLD}✗${RESET}  $*" >&2; }
die()          { print_error "$*"; exit 1; }

NON_INTERACTIVE=false
SKIP_SERVER=false

while [ $# -gt 0 ]; do
    case "$1" in
        --yes)         NON_INTERACTIVE=true; shift ;;
        --skip-server) SKIP_SERVER=true; shift ;;
        --help)
            printf "Usage: ./init.sh [--yes] [--skip-server]\n\n"
            printf "  --yes           Non-interactive: read required values from env vars\n"
            printf "  --skip-server   Do not offer to start the development server\n"
            printf "  --help          Show this help message\n"
            exit 0
            ;;
        *)
            die "Unknown option: $1"
            ;;
    esac
done

LOG_FILE="${SCRIPT_DIR}/init.log"
if [ "${CI4_FORCE_LOG_TO_FILE:-false}" = "true" ]; then
    exec >"$LOG_FILE" 2>&1
else
    exec > >(tee -a "$LOG_FILE") 2>&1
fi
printf "Init log: %s\n" "$LOG_FILE"

print_header "Teatro Museo — Totem Setup"

# ---------------------------------------------------------------------------
# Gather required values (env vars in --yes mode, prompts otherwise)
# ---------------------------------------------------------------------------
prompt_required() {
    local var_name="$1" prompt_text="$2" default_value="${3:-}"
    local current="${!var_name:-}"

    if [ -n "$current" ]; then
        return 0
    fi

    if [ "$NON_INTERACTIVE" = "true" ]; then
        [ -n "$default_value" ] && { printf -v "$var_name" '%s' "$default_value"; return 0; }
        die "${var_name} is required in --yes mode and was not set."
    fi

    local input
    read -r -p "${prompt_text}$([ -n "$default_value" ] && echo " [${default_value}]"): " input
    input="${input:-$default_value}"
    [ -n "$input" ] || die "${var_name} cannot be empty."
    printf -v "$var_name" '%s' "$input"
}

prompt_required TOTEM_APP_BASE_URL "Public base URL (e.g. http://localhost:8192)" "http://localhost:8192"
prompt_required TOTEM_API_URL "Hub totem API URL (e.g. http://localhost:8180/api/v1/totem)" "http://localhost:8180/api/v1/totem"
prompt_required TOTEM_API_KEY "TOTEM_API_KEY (shared secret, must match the hub's X-Totem-Key config)"
prompt_required TOTEM_APP_LOCALE "Default locale" "es"

# ---------------------------------------------------------------------------
# .env: back up existing, copy from the `env` template, patch required values
# ---------------------------------------------------------------------------
print_header "Configuring .env"

if [ -f .env ]; then
    _backup=".env.bak.$(date +%Y%m%d%H%M%S)"
    cp .env "$_backup"
    print_warn "Existing .env backed up to ${_backup}"
fi

[ -f env ] || die "env template not found — cannot bootstrap .env."
cp env .env

_sed_inplace() {
    if [[ "$(uname -s)" == "Darwin" ]]; then
        sed -i '' "$1" .env
    else
        sed -i "$1" .env
    fi
}

_set_env_value() {
    local key="$1" value="$2"
    local escaped_value
    escaped_value="$(printf '%s' "$value" | sed 's/[&/\]/\\&/g')"
    if grep -qE "^#?[[:space:]]*${key}[[:space:]]*=" .env; then
        _sed_inplace "s/^#*[[:space:]]*${key}[[:space:]]*=.*/${key} = ${escaped_value}/"
    else
        printf '%s = %s\n' "$key" "$value" >> .env
    fi
}

_set_env_value "app.baseURL" "'${TOTEM_APP_BASE_URL}/'"
_set_env_value "app.defaultLocale" "'${TOTEM_APP_LOCALE}'"
_set_env_value "TOTEM_API_URL" "'${TOTEM_API_URL}'"
_set_env_value "TOTEM_API_KEY" "'${TOTEM_API_KEY}'"

print_ok ".env configured (app.baseURL, app.defaultLocale, TOTEM_API_URL, TOTEM_API_KEY)"

# CI_ENVIRONMENT, TOTEM_ENABLE_*, FTP_* and any other optional settings are
# intentionally left untouched — the `env` template ships them commented out,
# and this script never copies real secrets from anywhere; the operator
# configures them by hand if/when needed (FTP_* in particular is only read by
# .deploy/deploy.py, never by CodeIgniter itself).

# ---------------------------------------------------------------------------
print_header "Dependencies"

if command -v composer >/dev/null 2>&1; then
    composer install --no-interaction
    print_ok "Composer dependencies installed"
else
    die "composer not found — install Composer 2.x and re-run."
fi

if command -v npm >/dev/null 2>&1; then
    npm install
    npm run build:css
    print_ok "npm dependencies installed and CSS built"
else
    print_warn "npm not found — skipping asset install/build. Run 'npm install && npm run build:css' manually."
fi

print_header "Done"
print_ok "teatromuseo-totem-ci4 is configured."

if [ "$SKIP_SERVER" = "true" ]; then
    exit 0
fi

if [ "$NON_INTERACTIVE" = "true" ]; then
    exit 0
fi

read -r -p "Start the development server now? [y/N]: " _start
if [[ "$_start" =~ ^[Yy]$ ]]; then
    php spark serve --port 8192
fi
