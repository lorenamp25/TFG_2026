FROM php:8.2-apache

RUN apt-get update && apt-get install -y libpq-dev \
 && docker-php-ext-install pdo_pgsql \
 && rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite

COPY backend/ /var/www/html/

WORKDIR /var/www/html/

RUN chown -R www-data:www-data /var/www/html

RUN printf '<Directory /var/www/html/>\nAllowOverride All\n</Directory>' > /etc/apache2/conf-available/rewrite.conf \
 && a2enconf rewrite

EXPOSE 80

CMD ["apache2-foreground"]