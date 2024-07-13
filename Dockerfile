# Use the official PHP image as a base
FROM php:8.0-fpm

# Set the working directory
WORKDIR /var/www/html

# Install dependencies
RUN apt-get update && apt-get install -y \
    libsqlite3-dev \
    && docker-php-ext-install pdo_sqlite

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy the application code to the container
COPY . .

# Install Composer dependencies
RUN composer install --working-dir=/var/www/html/src

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
