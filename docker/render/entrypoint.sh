#!/bin/sh
set -eu

export PORT="${PORT:-10000}"

if [ -z "${APP_KEY:-}" ] && [ -n "${APP_KEY_RAW:-}" ]; then
    export APP_KEY="base64:${APP_KEY_RAW}"
fi

if [ -n "${RENDER_EXTERNAL_URL:-}" ]; then
    export APP_URL="$RENDER_EXTERNAL_URL"
fi

envsubst '${PORT}' \
    < /etc/nginx/templates/render.conf.template \
    > /etc/nginx/sites-enabled/render.conf

exec taller-entrypoint "$@"
