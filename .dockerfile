FROM php:8.3-apache

# Dependências do sistema e extensões PHP
RUN apt-get update \
    && apt-get install -y --no-install-recommends git unzip libzip-dev \
    && docker-php-ext-install pdo_mysql zip bcmath \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

# Apache: escuta na porta que o Render informa (PORT) e serve a pasta public
RUN rm /etc/apache2/ports.conf && touch /etc/apache2/ports.conf \
    && a2dissite 000-default
COPY docker/apache.conf /etc/apache2/sites-available/app.conf
RUN a2ensite app

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction \
    && chown -R www-data:www-data storage bootstrap/cache

ENV PORT=10000
EXPOSE 10000

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh
ENTRYPOINT ["entrypoint.sh"]