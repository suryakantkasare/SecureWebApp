#!/bin/bash
# Ensure uploads directory exists and has correct permissions
mkdir -p /var/www/html/uploads
chown -R www-data:www-data /var/www/html/uploads
chmod -R 775 /var/www/html/uploads

# Start Apache
exec apache2-foreground
