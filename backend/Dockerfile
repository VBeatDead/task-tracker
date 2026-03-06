FROM php:8.4-cli-alpine

RUN apk add --no-cache \
    libpq-dev \
    unzip \
    curl \
  && docker-php-ext-install pdo pdo_pgsql

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

RUN cp .env.example .env \
  && mkdir -p bootstrap/cache storage/logs storage/framework/cache storage/framework/sessions storage/framework/views \
  && chmod -R 775 storage bootstrap/cache

RUN composer install --no-dev --optimize-autoloader --no-interaction

EXPOSE 8000

CMD ["sh", "-c", "printf 'APP_NAME=TaskTracker\\nAPP_ENV=%s\\nAPP_KEY=%s\\nAPP_DEBUG=%s\\nAPP_URL=https://task-tracker-taskv-42c1987a.koyeb.app\\nDB_CONNECTION=pgsql\\nDB_HOST=%s\\nDB_PORT=%s\\nDB_DATABASE=%s\\nDB_USERNAME=%s\\nDB_PASSWORD=%s\\nDB_SSLMODE=%s\\nSESSION_DRIVER=file\\nCACHE_STORE=file\\nQUEUE_CONNECTION=sync\\nLOG_CHANNEL=stderr\\nLOG_LEVEL=%s\\nFRONTEND_URL=%s\\n' \"${APP_ENV:-production}\" \"$APP_KEY\" \"${APP_DEBUG:-false}\" \"${DB_HOST:-127.0.0.1}\" \"${DB_PORT:-5432}\" \"${DB_DATABASE:-tasktracker_db}\" \"${DB_USERNAME:-postgres}\" \"${DB_PASSWORD}\" \"${DB_SSLMODE:-disable}\" \"${LOG_LEVEL:-error}\" \"${FRONTEND_URL}\" > .env && php artisan config:clear && php artisan migrate --force && php artisan db:seed --force 2>/dev/null || true && php artisan serve --host=0.0.0.0 --port=8000"]
