FROM composer AS composer

FROM php:8.0-cli
COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN apt-get update && DEBIAN_FRONTEND=noninteractive apt-get install -qq git zip unzip

COPY . /app
WORKDIR /app
RUN git clean -xf
RUN composer install

RUN docker-php-ext-install bcmath
