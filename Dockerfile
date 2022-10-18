# Set the base image for subsequent instructions
FROM php:8.1-buster

# Update packages
RUN apt-get update

# Install PHP and composer dependencies
RUN apt-get install -qq git curl libmcrypt-dev libjpeg-dev libpng-dev libfreetype6-dev libbz2-dev libzip-dev

# Clear out the local repository of retrieved package files
RUN apt-get clean

# Install needed extensions
RUN docker-php-ext-configure gd --enable-gd --with-jpeg
# Here you can install any other extension that you need during the test and deployment process
RUN docker-php-ext-install pdo_mysql zip gd

# for test report
RUN pecl install xdebug
RUN docker-php-ext-enable xdebug
RUN echo xdebug.mode=coverage > /usr/local/etc/php/conf.d/xdebug.ini

# Install Composer
RUN curl --silent --show-error "https://getcomposer.org/installer" | php -- --install-dir=/usr/local/bin --filename=composer

# Install Laravel Envoy
RUN composer global require "laravel/envoy"