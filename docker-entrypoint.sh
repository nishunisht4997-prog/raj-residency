#!/bin/sh
set -e

# Render passes the port in $PORT environment variable (defaults to 10000)
TARGET_PORT="${PORT:-10000}"

# Configure Apache to listen on Render's assigned $PORT
sed -i "s/Listen [0-9]*/Listen ${TARGET_PORT}/g" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:[0-9]*>/<VirtualHost \*:${TARGET_PORT}>/g" /etc/apache2/sites-available/000-default.conf

# Start Apache in foreground
exec apache2-foreground
