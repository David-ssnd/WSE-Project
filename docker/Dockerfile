ARG PHP_VERSION=8.3

FROM php:${PHP_VERSION}-apache-bookworm AS base

LABEL maintainer="Image maintainer <maintainer@contactemail.com>" \
      vendor="Institution or person name" \
      description="PHP image for backend development with Xdebug and Composer" \
      version="1.0"

# Update package information and install dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    zlib1g-dev \
    libpq-dev \
    libicu-dev \
    libzip-dev \
    libxml2-dev \
    libcurl4-openssl-dev \
    libonig-dev \
    libmagickwand-dev \
    g++ \
    libapache2-mod-xsendfile \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install zip fileinfo mbstring curl exif xml filter \
    && docker-php-ext-configure intl && docker-php-ext-install intl \
    && docker-php-ext-install pdo pdo_pgsql pgsql

# Install and enable Xdebug
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Configure Apache and PHP
RUN a2enmod rewrite \
    && sed -i 's!/var/www/html!/var/www/public!g' /etc/apache2/sites-available/000-default.conf

# Set the working directory
WORKDIR /var/www

# Copy PHP configuration files
COPY ./docker-php-ext-xdebug.ini /usr/local/etc/php/conf.d/
COPY ./docker-custom-php.ini "$PHP_INI_DIR/php.ini"

# Run as non-root user (optional, if needed)
# USER www-data