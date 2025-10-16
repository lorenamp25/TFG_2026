# Imagen base oficial con PHP y Apache
FROM php:8.2-apache

# Instala extensiones necesarias (por ejemplo, para PDO y JSON)
RUN docker-php-ext-install pdo pdo_mysql

# Habilita mod_rewrite (necesario para frameworks REST)
RUN a2enmod rewrite

# Copia el contenido del proyecto al contenedor
COPY . /var/www/html/

# Establece el directorio de trabajo
WORKDIR /var/www/html/

# Instala Composer (para manejar dependencias)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Instala dependencias PHP (si tienes un composer.json)
RUN composer install --no-dev --optimize-autoloader

# Ajusta permisos (opcional pero recomendable)
RUN chown -R www-data:www-data /var/www/html

# Configura Apache para permitir URL amigables
RUN echo '<Directory /var/www/html/>\n\
    AllowOverride All\n\
</Directory>' > /etc/apache2/conf-available/rewrite.conf \
 && a2enconf rewrite

# Expone el puerto 80 (por defecto de Apache)
EXPOSE 80

# Comando de inicio (Apache en primer plano)
CMD ["apache2-foreground"]
