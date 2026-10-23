FROM php:8.2-apache

RUN docker-php-ext-install pdo_mysql

# PHP settings: errors to the log only, no version header
COPY docker/php.ini /usr/local/etc/php/conf.d/site.ini

# Listen on $PORT: 80 locally, 10000 on Render
ENV PORT=80
RUN sed -i 's/^Listen 80$/Listen ${PORT}/' /etc/apache2/ports.conf \
    && sed -i 's/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/' /etc/apache2/sites-available/000-default.conf

# Server name, so Apache does not warn at every start
RUN echo 'ServerName localhost' > /etc/apache2/conf-available/servername.conf && a2enconf servername

# Server header says only "Apache": no version or OS, and no server line on error pages
RUN sed -i -e 's/^ServerTokens .*/ServerTokens Prod/' -e 's/^ServerSignature .*/ServerSignature Off/' \
    /etc/apache2/conf-available/security.conf

# Render's secret files (the database CA certificate) are readable by group 1000: add Apache's user to it
RUN groupadd --gid 1000 render-secrets && usermod --append --groups render-secrets www-data

# Only public/ is served; the rest of the project stays out of reach
RUN sed -i 's#/var/www/html#/var/www/html/public#' /etc/apache2/sites-available/000-default.conf

# The code itself (locally, docker-compose mounts the working copy over it)
COPY . /var/www/html
