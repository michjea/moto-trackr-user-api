FROM php:8.1-apache

# Installez les dépendances nécessaires pour Laravel
RUN apt-get update && apt-get install -y libpng-dev zip unzip git libxml2-dev && \
    docker-php-ext-configure gd && \
    docker-php-ext-install pdo pdo_mysql gd xml

# Activez le module Apache mod_rewrite
RUN a2enmod rewrite

# Définissez le répertoire de travail dans le conteneur
WORKDIR /var/www/html

# Copiez tous les fichiers Laravel dans le conteneur
COPY . .
COPY .env.docker .env

# Installez les dépendances de Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install

RUN php artisan key:generate
#RUN php artisan migrate

# Exposez le port 80 pour le serveur Apache
EXPOSE 80

# Exécutez Apache
#CMD ["apache2-foreground"]

#COPY ./run.sh /tmp
#ENTRYPOINT ["/tmp/run.sh"]

# make ./run.sh executable
RUN chmod +x run.sh
ENTRYPOINT [ "./run.sh" ]
