FROM composer:1 AS composer

FROM php:7.0-fpm

COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN apt-get update && apt-get install -qq \
    git \
    unzip

COPY php.ini /usr/local/etc/php/php.ini