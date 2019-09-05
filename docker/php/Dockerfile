FROM php:fpm-alpine
RUN apk update && apk add bash
RUN set -ex \
  && apk --no-cache add \
    postgresql-dev
RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql
RUN docker-php-ext-install sockets \
bcmath \
pdo_mysql \
pdo_pgsql \
pdo
WORKDIR /var/www/symfony
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
&& composer --version
CMD ["php-fpm"]
EXPOSE 9000