FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip git libpng-dev libonig-dev libxml2-dev libicu-dev libjpeg-dev libfreetype6-dev zlib1g-dev && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl gd intl && \
    a2enmod rewrite && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock* /var/www/html/
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist || true

COPY . /var/www/html

ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN sed -ri -e 's!DocumentRoot /var/www/html!DocumentRoot ${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf \
 && sed -ri -e "s!<Directory /var/www/>.*</Directory>!<Directory ${APACHE_DOCUMENT_ROOT}>\n\tOptions Indexes FollowSymLinks\n\tAllowOverride All\n\tRequire all granted\n</Directory>!g" /etc/apache2/apache2.conf

RUN chown -R www-data:www-data /var/www/html && \
    find /var/www/html/storage -type d -exec chmod 775 {} \; || true && \
    find /var/www/html/storage -type f -exec chmod 664 {} \; || true && \
    chmod -R 775 /var/www/html/bootstrap/cache || true

COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["apache2-foreground"]
