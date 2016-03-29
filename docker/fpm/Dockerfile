FROM php:fpm

RUN mkdir /app && \
    apt-get update && apt-get install -y libmcrypt-dev && \
    docker-php-ext-install -j$(nproc) mcrypt pdo_mysql

WORKDIR /app
