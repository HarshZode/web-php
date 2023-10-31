FROM php:8.0-apache

RUN docker-php-ext-install mysqli &&  docker-php-ext-enable mysqli

COPY index.php /var/www/html
COPY phpinfo.php /var/www/html
# RUN apt-get update && apt-get upgrade -y
