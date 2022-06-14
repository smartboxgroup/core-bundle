FROM composer:2 AS composer

FROM php:8.0.19-fpm-alpine3.15

COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN apk update && apk add git unzip ${PHPIZE_DEPS}\
  && pecl install xdebug \
  && docker-php-ext-enable xdebug

COPY php.ini /usr/local/etc/php/php.ini
COPY xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini