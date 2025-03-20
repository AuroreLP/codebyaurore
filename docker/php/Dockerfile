# Utilisation de l'image officielle PHP avec Apache
FROM php:8.2-apache

# Configuration Apache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Installation des dépendances système et extensions PHP nécessaires
RUN apt-get update \
    && apt-get install -qq -y --no-install-recommends \
    cron \
    locales coreutils apt-utils git libicu-dev g++ libpng-dev libxml2-dev libzip-dev libonig-dev libxslt-dev;

# Configuration de la langue et génération des locales
RUN echo "en_US.UTF-8 UTF-8" > /etc/locale.gen && \
    echo "fr_FR.UTF-8 UTF-8" >> /etc/locale.gen && \
    locale-gen

# Installation de Composer
RUN curl -sSk https://getcomposer.org/installer | php -- --disable-tls && \
   mv composer.phar /usr/local/bin/composer

# Installation des extensions PHP
RUN docker-php-ext-configure intl
RUN docker-php-ext-install pdo pdo_mysql mysqli gd opcache intl zip calendar dom mbstring zip gd xsl && a2enmod rewrite
RUN pecl install apcu && docker-php-ext-enable apcu

# Installation de php-extension-installer pour des extensions supplémentaires
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions && sync && \
    install-php-extensions amqp

# Définition du répertoire de travail
WORKDIR /var/www

# Copie des fichiers du projet dans le conteneur
COPY . /var/www/html

# Attribution des permissions correctes
RUN chown -R www-data:www-data /var/www/html

# Exposition du port Apache
EXPOSE 80