# php7-fpm/Dockerfile
FROM php:7.1.1-fpm

MAINTAINER Quentin GRAS <quentin.gras@epita.fr>

RUN apt-get update && apt-get install -y \
    git \
    unzip

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN rm /etc/localtime
RUN ln -s /usr/share/zoneinfo/Europe/Paris /etc/localtime

RUN echo 'alias sf="php app/console"' >> ~/.bashrc

WORKDIR /var/www/symfony

RUN php bin/console doctrine:database:create
RUN php bin/console doctrine:schema:update --force