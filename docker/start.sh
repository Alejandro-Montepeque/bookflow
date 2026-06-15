#!/usr/bin/env bash
set -euo pipefail

cd /var/www/html

# Cloud Run sets $PORT (default 8080). Render it into the nginx template
# because nginx itself doesn't evaluate environment variables inside listen directives.
: "${PORT:=8080}"
sed -i "s/__PORT__/${PORT}/g" /etc/nginx/nginx.conf

# Make sure runtime directories exist before nginx/php-fpm start.
mkdir -p /run/nginx storage/framework/{sessions,views,cache} storage/logs
chown -R www-data:www-data storage bootstrap/cache /run/nginx

echo ">> Running migrations…"
# Note: we don't use --isolated here. It would require the `cache_locks` table
# to take a lock, but that table is itself created by one of the migrations
# we're about to run. Concurrent migrations are very unlikely with Cloud Run
# min-instances=0 and Laravel's migration tracker is idempotent enough.
php artisan migrate --force

echo ">> Seeding demo data…"
php artisan db:seed --force || echo "Warning: seed failed (continuing)"

echo ">> Caching config, routes and views…"
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache || true

echo ">> Starting supervisord (nginx + php-fpm)…"
exec /usr/bin/supervisord -c /etc/supervisord.conf
