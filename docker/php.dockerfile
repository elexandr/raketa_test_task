FROM php:8.2-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    build-base \
    autoconf \
    automake \
    libtool \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    oniguruma-dev \
    zlib-dev \
    libxml2-dev \
    curl \
    git \
    unzip \
     acl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_mysql zip mbstring xml

# Install Redis PHP extension
RUN pecl install redis && docker-php-ext-enable redis

# Install bash
RUN apk add --no-cache bash

# Set working directory
WORKDIR /var/www/html

# Copy composer files
COPY backend-test-task-main/composer.* ./

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# Copy application code from the main project directory
COPY backend-test-task-main/ .

# Создаем var директории с правильными правами
RUN mkdir -p var/cache var/log

EXPOSE 9000

CMD ["php-fpm"]