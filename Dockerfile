# Utilisation d'une image de base contenant PHP et Apache
FROM php:8-apache

# Installation des dépendances nécessaires à l'application
WORKDIR /var/www/html/

# Copie des fichiers de l'application dans le conteneur
COPY index.php .
COPY modifier_iptc .
COPY README.md .
COPY style.css .
COPY img img/ 

# Exposition du port 80 pour Apache
EXPOSE 80


CMD [ "apache2-foreground" ]