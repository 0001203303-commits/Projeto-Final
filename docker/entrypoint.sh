#!/bin/bash
set -e
cd /var/www/html

# O disco do Render é descartável: recria as pastas que o Laravel precisa
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
chown -R www-data:www-data storage bootstrap/cache

# Usa as variáveis de ambiente configuradas no Render
php artisan config:cache
php artisan view:cache


# Opcional: rodam só quando as variáveis estiverem como "true" no Render
echo "=== DIAGNOSTICO ==="
echo "APP_ENV=$APP_ENV"
echo "MYSQL_ATTR_SSL_CA=$MYSQL_ATTR_SSL_CA"
ls -l /etc/ssl/certs/ca-certificates.crt || echo "CERTIFICADO AUSENTE"
php artisan tinker --execute="dump(config('database.connections.mysql.options'));" || true
echo "==================="

if [ "$RUN_MIGRATIONS" = "true" ]; then
    php artisan migrate --force
fi

if [ "$RUN_SEEDERS" = "true" ]; then
    php artisan db:seed --force
fi

if [ "$RUN_DEMO" = "true" ]; then
    php artisan db:seed --class=TriagensDemoSeeder --force
fi

exec apache2-foreground