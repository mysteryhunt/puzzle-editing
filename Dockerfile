FROM composer:1.4.1
ADD composer.json .
ADD composer.lock .
RUN composer install


FROM php:5.6.31-apache

# Add PHP mysql
RUN docker-php-ext-install mysql

RUN apt-get update && apt-get install -qy zip

# Add files
ADD . /var/www/html
RUN rm -rf /var/log/www/html/docker
ADD ./docker/prod/errors /var/www/errors
COPY --from=0 /app/vendor /var/www/html/vendor
WORKDIR /var/www/html

# Configure apache
RUN touch /etc/apache2/sites-available/puzzletron.htpasswd
ADD ./docker/prod/puzzletron.conf /etc/apache2/sites-available

RUN a2dissite default-ssl.conf || true && \
    a2dissite 000-default.conf && \
    a2ensite puzzletron.conf && \
    a2enmod rewrite && \
    a2enmod headers && \
    a2enmod remoteip && \
    a2enmod session_cookie && \
    a2enmod request && \
    a2enmod auth_form

RUN touch /var/www/html/.env
RUN mkdir -p /tmp/purifier-cache /var/www/html/uploads/pictures/thumbs /var/www/html/uploads/puzzle_files && \
    chown -R www-data: /tmp/purifier-cache /var/www/html/uploads

# Configure PHP
ADD ./docker/prod/php-config.ini /usr/local/etc/php/conf.d/

# Set up cron
ADD ./docker/prod/ptron-cron /ptron-cron

RUN apt-get update && \
    apt-get -y install cron && \
    crontab /ptron-cron

# Set up entrypoint
ADD ./docker/prod/cron-entrypoint.sh /cron-entrypoint.sh
ADD ./docker/prod/entrypoint.sh /entrypoint.sh
CMD /entrypoint.sh
