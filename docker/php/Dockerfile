# Utilisation de l'image officielle PHP avec Apache
FROM php:8.4-apache

# Configuration Apache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Installation des dépendances système et extensions PHP nécessaires
RUN apt-get update \
    && apt-get install -qq -y --no-install-recommends \
    cron \
    locales \
    coreutils \
    apt-utils \
    git \
    libicu-dev \
    g++ \
    libpng-dev \
    libxml2-dev \
    libzip-dev \
    libonig-dev \
    libxslt-dev && \

# Configuration de la langue et génération des locales
echo "en_US.UTF-8 UTF-8" > /etc/locale.gen && \
echo "fr_FR.UTF-8 UTF-8" >> /etc/locale.gen && \
    locale-gen

# Installation de Composer
RUN curl -sSk https://getcomposer.org/installer | php -- --disable-tls && \
   mv composer.phar /usr/local/bin/composer

# Installation des extensions PHP
RUN docker-php-ext-configure intl && \
    docker-php-ext-install pdo pdo_mysql mysqli gd opcache intl zip calendar dom mbstring xsl && \
    a2enmod rewrite

# Installation de APCu et autres extensions via pecl
RUN pecl install apcu && docker-php-ext-enable apcu

# Installation de php-extension-installer pour des extensions supplémentaires
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions && sync && \
    install-php-extensions amqp

# Ajout du fichier vhost.conf dans le conteneur et activation du VirtualHost
COPY vhosts/vhost.conf /etc/apache2/sites-available/000-default.conf

# Activation des sites et modules Apache nécessaires
RUN a2ensite 000-default.conf && \
    a2enmod rewrite

RUN pecl install mongodb
RUN docker-php-ext-enable mongodb


# Définition du répertoire de travail
WORKDIR /var/www/html


