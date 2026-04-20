FROM php:8.2-cli

RUN apt-get update && apt-get install -y libpq-dev zip unzip git \
    && docker-php-ext-install pdo pdo_pgsql pgsql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

RUN if [ -f composer.json ]; then composer install --no-dev --optimize-autoloader; fi

RUN mkdir -p /var/www/html/storage/logs /var/www/html/storage/uploads \
    && chmod -R 777 /var/www/html/storage

EXPOSE 80

CMD ["php", "-S", "0.0.0.0:80", "-t", "/var/www/html/public"]