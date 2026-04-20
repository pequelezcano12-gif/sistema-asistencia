FROM php:8.2-apache

RUN apt-get update && apt-get install -y libpq-dev zip unzip git && docker-php-ext-install pdo pdo_pgsql pgsql && a2enmod rewrite

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

RUN if [ -f composer.json ]; then composer install --no-dev --optimize-autoloader; fi

RUN printf "<VirtualHost *:80>\nDocumentRoot /var/www/html/public\n<Directory /var/www/html/public>\nAllowOverride All\nRequire all granted\n</Directory>\n</VirtualHost>" > /etc/apache2/sites-available/000-default.conf

RUN mkdir -p /var/www/html/storage/logs /var/www/html/storage/uploads && chmod -R 755 /var/www/html/storage

EXPOSE 80