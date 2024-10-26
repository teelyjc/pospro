FROM php:8.3-apache

RUN apt update
RUN apt upgrade

RUN docker-php-ext-install pdo pdo_mysql

COPY ./ /var/www/html

RUN mkdir -p /var/www/html/uploads && \
  chmod -R 775 /var/www/html/uploads && \
  chown -R www-data:www-data /var/www

EXPOSE 80
