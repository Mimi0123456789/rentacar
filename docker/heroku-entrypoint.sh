#!/bin/bash
set -e

# Heroku fournit dynamiquement le port HTTP dans $PORT
PORT=${PORT:-8080}

sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf

sed -i "s/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/" \
    /etc/apache2/sites-available/000-default.conf

# initialisation des variables d'environnement
php artisan config:clear
php artisan route:clear
php artisan view:clear

exec "$@"