FROM php:8.2-apache

# Extensiones necesarias
RUN apt-get update && apt-get install -y libpq-dev zip unzip git \
    && docker-php-ext-install pdo pdo_pgsql pgsql \
    && a2enmod rewrite

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiar proyecto — el código está en la subcarpeta sistema-asistencia/
WORKDIR /var/www/html
COPY sistema-asistencia/ .

# Instalar dependencias PHP
RUN composer install --no-dev --optimize-autoloader 2>/dev/null || true

# Configurar Apache para apuntar a /public
RUN echo '<VirtualHost *:80>\n\
    DocumentRoot /var/www/html/public\n\
    <Directory /var/www/html/public>\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# Permisos
RUN mkdir -p /var/www/html/storage/logs /var/www/html/storage/uploads \
    && chmod -R 755 /var/www/html/storage

EXPOSE 80
