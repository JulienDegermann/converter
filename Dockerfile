FROM php:8.3-apache-bookworm

RUN apt-get update && apt-get install -y \
    # openssl \
    zip \
    unzip \
    libpng-dev libjpeg-dev libfreetype6-dev libwebp-dev libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install gd zip \
    && rm -rf /var/lib/apt/lists/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN a2enmod rewrite

COPY . /var/www/html

COPY ./docker.sh /var/opt/docker.sh
COPY php.ini /usr/local/etc/php/conf.d/custom.ini

COPY ./apache.conf /etc/apache2/sites-available/000-default.conf


RUN chmod +x /var/opt/docker.sh

RUN chown -R www-data:www-data /var/www/html/
RUN chmod -R 775 /var/www/html/public/uploads/

ENTRYPOINT ["/var/opt/docker.sh"]

WORKDIR /var/www/html


EXPOSE 80
