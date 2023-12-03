# Dockerfile for PHP and Apache2
FROM php:7.4-apache

# Copy the project files into the container, set permissions, and ownership
COPY --chown=www-data:www-data . /var/www/html/

# Set ownership and permissions for the vulns directory 
RUN chown -R www-data:www-data /var/www/html/ && chmod -R 7777 /var/www/html/ 

# Install ping command and the mysqli extension for connecting to MySQL
RUN apt update \
    && docker-php-ext-install mysqli  \
    && apt-get install -y libjpeg-dev libpng-dev \
    && docker-php-ext-configure gd --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

# Expose port 80
EXPOSE 80