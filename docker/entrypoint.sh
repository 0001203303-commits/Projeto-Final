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

# Opcional: só roda quando RUN_MIGRATIONS=true no Render
if [ "$RUN_MIGRATIONS" = "true" ]; then
    php artisan migrate --force
fi

exec apache2-foreground