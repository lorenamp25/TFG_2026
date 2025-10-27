# Imagen base oficial con PHP y Apache
FROM php:8.2-apache

# Instala dependencias de PostgreSQL y extensiones PDO para pgsql (y mantiene mysql si la necesitas)
RUN apt-get update && apt-get install -y libpq-dev \
 && docker-php-ext-install pdo_pgsql \
 && rm -rf /var/lib/apt/lists/*

# Habilita mod_rewrite (necesario para frameworks REST)
RUN a2enmod rewrite

# Copia el contenido del proyecto al contenedor
COPY . /var/www/html/

# Establece el directorio de trabajo
WORKDIR /var/www/html/

# Ajusta permisos (opcional pero recomendable)
RUN chown -R www-data:www-data /var/www/html

# Configura Apache para permitir URL amigables
RUN printf '<Directory /var/www/html/>\n    AllowOverride All\n</Directory>' > /etc/apache2/conf-available/rewrite.conf \
 && a2enconf rewrite

# Expone el puerto 80 (por defecto de Apache)
EXPOSE 80

# Comando de inicio (Apache en primer plano)
CMD ["apache2-foreground"]
