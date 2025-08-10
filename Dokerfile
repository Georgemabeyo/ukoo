# Tumia official PHP 8.2 na Apache
FROM php:8.2-apache

# Install mysqli extension (kwa MySQL connection)
RUN docker-php-ext-install mysqli

# Copy project files kwenda folder la Apache
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html/

# Set permissions (optional, kwa usalama)
RUN chown -R www-data:www-data /var/www/html/

# Enable Apache mod_rewrite ikiwa unahitaji (mfano kwa URL rewriting)
RUN a2enmod rewrite

# Expose port 80
EXPOSE 80

# Start Apache in foreground
CMD ["apache2-foreground"]
