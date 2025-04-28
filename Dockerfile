# Utilisation de l'image officielle PHP avec Apache
FROM php:8.4-apache

# Configuration Apache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Installation des dépendances système et extensions PHP nécessaires
RUN DEBIAN_FRONTEND=noninteractive apt-get update \
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
        libxslt-dev \
        ca-certificates \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Configuration de la langue et génération des locales
RUN echo "en_US.UTF-8 UTF-8" > /etc/locale.gen && \
    echo "fr_FR.UTF-8 UTF-8" >> /etc/locale.gen && \
    locale-gen

# Installation de Composer
RUN curl -sSk https://getcomposer.org/installer | php -- --disable-tls && \
   mv composer.phar /usr/local/bin/composer

# Installation des extensions PHP & Apache
RUN docker-php-ext-configure intl && \
    docker-php-ext-install pdo pdo_mysql mysqli gd opcache intl zip calendar dom mbstring xsl && \
    a2enmod rewrite

# Installation de APCu et autres extensions via pecl
RUN pecl install apcu mongodb && docker-php-ext-enable apcu mongodb

# Installation de php-extension-installer pour des extensions supplémentaires
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions \
 && install-php-extensions amqp


# Installer Node.js et npm (ajoute cette étape avant "RUN npm install")
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - && \
    apt-get install -y nodejs && \
    npm install -g npm && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

# Définition du répertoire de travail
WORKDIR /var/www/html

# Installation des dépendances front (NPM)
COPY package*.json ./
RUN npm install

# Copie du code applicatif
COPY . .

# Copie et activation du VirtualHost Apache
COPY docker/php/vhosts/vhost.conf /etc/apache2/sites-available/000-default.conf
RUN a2ensite 000-default.conf

# Heroku oblige Apache à écouter sur $PORT
ENV PORT=8080
RUN sed -i "s/80/\${PORT}/g" /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf

# Installer dépendances PHP pour production
RUN composer install --no-dev --optimize-autoloader

# Donner les bons droits à www-data
RUN chown -R www-data:www-data /var/www/html && chmod -R 775 /var/www/html/var

# Exécuter en tant que www-data
USER www-data

