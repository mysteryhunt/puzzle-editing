FROM php:5.6.31-apache

RUN docker-php-ext-install mysql
RUN mkdir /tmp/purifier-cache && chown www-data: /tmp/purifier-cache
