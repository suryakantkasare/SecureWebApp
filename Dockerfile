# Use official PHP Apache image
FROM php:8.1-apache

# Enable mod_rewrite for clean URLs and SSL for HTTPS
RUN a2enmod rewrite ssl

# Set document root
WORKDIR /var/www/html

# Install necessary PHP extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copy application files to the container
COPY . /var/www/html/

# Copy SSL certificate and key files into the container
# Ensure these files exist in your project root
COPY selfsigned.crt /etc/ssl/certs/selfsigned.crt
COPY selfsigned.key /etc/ssl/private/selfsigned.key

# Copy custom Apache SSL configuration (defines VirtualHost for port 443)
COPY docker-config/default-ssl.conf /etc/apache2/sites-available/default-ssl.conf

# Enable the SSL site and disable the default HTTP site without reloading Apache at build time
RUN a2ensite default-ssl && a2dissite 000-default

# Expose HTTP and HTTPS ports
EXPOSE 80 443

# Copy the entrypoint script and give execute permissions
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Set the entrypoint script
ENTRYPOINT ["/entrypoint.sh"]