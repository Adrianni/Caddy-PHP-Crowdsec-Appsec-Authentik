# Custom PHP-FPM build pinned to a specific PHP release.
ARG PHP_VERSION=8.5

FROM php:${PHP_VERSION}-fpm-alpine

WORKDIR /var/www/html
