# Utilisation d'une image de base contenant PHP et Apache
FROM php:8-apache

# Installation des dépendances nécessaires à l'application
RUN apt-get update && apt-get install -y \
    libjpeg-dev \
    libpng-dev \
    libfreetype6-dev \
    libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd mysqli zip

# Copie des fichiers de l'application dans le conteneur
COPY index.php modifier_iptc.php style.css /var/www/html/
COPY img /var/www/html/img/

# Exposition du port 80 pour Apache
EXPOSE 80