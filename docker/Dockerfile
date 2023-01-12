FROM php:8.1-apache

# Copy virtual host into container
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

# Copy the project to the container
COPY ./../ /var/www/

# Enable rewrite mode
RUN a2enmod rewrite

# Install necessary packages
RUN apt-get update && apt-get install -y \
    libzip-dev \
    wget \
    git \
    unzip \
    -y --no-install-recommends

# Install PHP extensions
RUN docker-php-ext-install zip mysqli pdo pdo_mysql

# Install composer
COPY --from=composer /usr/bin/composer /usr/bin/composer

# Copy php.ini
COPY ./php.ini /usr/local/etc/php/

# Cleanup packages
RUN apt-get purge -y g++ \
    && apt-get autoremove -y \
    && rm -r /var/lib/apt/lists/* \
    && rm -rf /tmp/*

# Change the current working diretory
WORKDIR /var/www

# Change the owner of the container document root
RUN chown -R www-data:www-data /var/www

# Expose port 80
EXPOSE 80

# Start Apache in foreground
CMD ["apache2-foreground"]