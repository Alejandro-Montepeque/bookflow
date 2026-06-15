# syntax=docker/dockerfile:1.6

# ============================================================
# Stage 1 — Composer dependencies (cached layer)
# ============================================================
FROM composer:2 AS composer-deps

WORKDIR /app

# Copy only what Composer needs first to maximize layer reuse.
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --no-interaction \
    --prefer-dist


# ============================================================
# Stage 2 — Build frontend with Vite
# ============================================================
FROM node:20-alpine AS node-build

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

# Need vendor/ in place so that ziggy-js (vendor/tightenco/ziggy) is resolvable
# by Vite when compiling resources/js/app.ts.
COPY --from=composer-deps /app/vendor ./vendor

COPY . .

RUN npm run build && rm -rf node_modules


# ============================================================
# Stage 3 — Production runtime (PHP-FPM + Nginx + supervisord)
# ============================================================
FROM php:8.3-fpm-alpine AS runtime

# Install runtime system deps + PHP extensions Laravel needs.
RUN apk add --no-cache \
        nginx \
        supervisor \
        bash \
        curl \
        postgresql-libs \
        icu-libs \
        oniguruma \
        libzip \
    && apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        postgresql-dev \
        icu-dev \
        oniguruma-dev \
        libzip-dev \
    && docker-php-ext-install \
        pdo_pgsql \
        bcmath \
        intl \
        opcache \
        zip \
    && apk del .build-deps

# Composer is still needed in the final stage to dump optimized autoload
# AFTER the app source has been copied in.
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

WORKDIR /var/www/html

# Copy application source.
COPY --chown=www-data:www-data . .

# Copy already-built dependencies and assets from earlier stages.
COPY --from=composer-deps --chown=www-data:www-data /app/vendor ./vendor
COPY --from=node-build --chown=www-data:www-data /app/public/build ./public/build

# Final autoload optimization with the real app classes in place.
RUN composer dump-autoload --optimize --classmap-authoritative --no-dev

# Container configuration files.
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/php-fpm.pool.conf /usr/local/etc/php-fpm.d/zz-cloudrun.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/zz-laravel.ini
COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

# Storage permissions — Cloud Run runs as a non-root user.
RUN mkdir -p storage/framework/{sessions,views,cache} storage/logs \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Cloud Run injects $PORT (default 8080). Nginx config reads it dynamically.
ENV PORT=8080
EXPOSE 8080

CMD ["/start.sh"]
