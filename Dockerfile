FROM php:8.3-apache

# La imagen oficial no trae driver de MySQL para PDO.
RUN docker-php-ext-install pdo_mysql

# Configuración de desarrollo: los errores se ven en pantalla.
RUN cp "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

RUN echo "date.timezone = America/Montevideo" > "$PHP_INI_DIR/conf.d/timezone.ini"
