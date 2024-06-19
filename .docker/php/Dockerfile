FROM php:7.2-apache

LABEL author="guenbakku"

# Install CakePHP required extensions
RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libicu-dev \
        libxrender1 \
        libfontconfig \
    && docker-php-ext-install -j$(nproc) intl \
    && docker-php-ext-install -j$(nproc) pdo_mysql \
    && pecl install xdebug-3.1.5 \
    && docker-php-ext-enable xdebug

# Install zip & git (Required for composer install)
RUN apt-get install -y git zip

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Enable apache rewrite module
RUN ln -s /etc/apache2/mods-available/rewrite.load /etc/apache2/mods-enabled/rewrite.load
