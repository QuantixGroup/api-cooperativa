#!/bin/sh
set -e
APP_ROOT=/var/www/html
PHP_USER=www-data
PHP_GROUP=www-data

echo "[entrypoint] Starting container entrypoint tasks"
cd "$APP_ROOT" || exit 0

if [ ! -f "$APP_ROOT/.env" ] && [ -f "$APP_ROOT/.env.example" ]; then
  echo "[entrypoint] .env not found, copying .env.example"
  cp "$APP_ROOT/.env.example" "$APP_ROOT/.env"
fi

echo "[entrypoint] Fixing permissions for storage and bootstrap/cache"
chown -R ${PHP_USER}:${PHP_GROUP} "$APP_ROOT/storage" "$APP_ROOT/bootstrap/cache" || true
find "$APP_ROOT/storage" -type d -exec chmod 775 {} \; || true
find "$APP_ROOT/storage" -type f -exec chmod 664 {} \; || true
chmod -R 775 "$APP_ROOT/bootstrap/cache" || true

if [ -f composer.json ] && [ ! -d vendor ]; then
  echo "[entrypoint] vendor not found, running composer install"
  composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader || true
fi

if php -r "echo (bool)strlen(trim(getenv('APP_KEY')?:'')).PHP_EOL;" | grep -q "1"; then
  echo "[entrypoint] APP_KEY present via env"
else
  if [ -f artisan ]; then
    if php -r "require 'vendor/autoload.php'; echo (bool)config('app.key');" 2>/dev/null | grep -q "1"; then
      echo "[entrypoint] APP_KEY already set in config"
    else
      echo "[entrypoint] Generating APP_KEY"
      php artisan key:generate --no-interaction || true
    fi
  fi
fi

if [ -f artisan ]; then
  if [ ! -L "$APP_ROOT/public/storage" ]; then
    echo "[entrypoint] Creating storage symlink"
    php artisan storage:link || true
  fi
fi

if [ -f artisan ]; then
  if [ ! -f "$APP_ROOT/oauth-private.key" ] || [ ! -f "$APP_ROOT/oauth-public.key" ]; then
    echo "[entrypoint] Passport keys not found, generating"
    php artisan passport:keys || php artisan passport:install --force || true
    chown ${PHP_USER}:${PHP_GROUP} "$APP_ROOT/oauth-private.key" "$APP_ROOT/oauth-public.key" 2>/dev/null || true
  fi
fi

if [ "${APP_ENV:-production}" != "production" ]; then
  echo "[entrypoint] Clearing caches (dev)"
  php artisan config:clear || true
  php artisan route:clear || true
  php artisan view:clear || true
fi

chown -R ${PHP_USER}:${PHP_GROUP} "$APP_ROOT" || true

echo "[entrypoint] Done. Executing CMD"
exec "$@"
