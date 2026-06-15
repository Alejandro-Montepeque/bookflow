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
# --isolated uses a DB lock so concurrent Cloud Run instances don't race.
php artisan migrate --force --isolated

echo ">> Caching config, routes and views…"
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache || true

echo ">> Starting supervisord (nginx + php-fpm)…"
exec /usr/bin/supervisord -c /etc/supervisord.conf
