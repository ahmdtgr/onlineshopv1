FROM php:8.3-cli

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libzip-dev \
    libpng-dev \
    libicu-dev \
    libxml2-dev \
    zip \
    nodejs \
    npm \
    && docker-php-ext-configure gd \
    && docker-php-ext-install pdo pdo_mysql zip intl gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

CMD php artisan serve --host=0.0.0.0 --port=8000