# Tumia official PHP 8.2 with Apache
FROM php:8.2-apache

# Install mysqli extension
RUN docker-php-ext-install mysqli

# Copy source code kwenda directory ya Apache
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html/

# Set permissions kwa files na folders
RUN chown -R www-data:www-data /var/www/html/ && \
    find /var/www/html/ -type d -exec chmod 755 {} \; && \
    find /var/www/html/ -type f -exec chmod 644 {} \;

# Washa Apache mod_rewrite kwa .htaccess support
RUN a2enmod rewrite

# Expose port 80
EXPOSE 80

# Anza Apache server
CMD ["apache2-foreground"]
