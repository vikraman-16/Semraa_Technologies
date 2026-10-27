FROM php:8.3-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends libpng-dev libjpeg62-turbo-dev libwebp-dev \
    && docker-php-ext-configure gd --with-jpeg --with-webp \
    && docker-php-ext-install -j"$(nproc)" pdo_mysql gd opcache \
    && a2enmod rewrite headers expires deflate \
    && sed -i 's/Listen 80/Listen 10000/' /etc/apache2/ports.conf \
    && sed -i 's/:80>/:10000>/' /etc/apache2/sites-available/000-default.conf \
    && rm -rf /var/lib/apt/lists/*

COPY tools/render-preview.conf /etc/apache2/conf-available/semraa-preview.conf
COPY tools/php-production.ini /usr/local/etc/php/conf.d/semraa.ini
RUN a2enconf semraa-preview
COPY . /var/www/html/
RUN mkdir -p /var/www/html/logs /var/www/html/assets/img/case-studies \
    && chown -R www-data:www-data /var/www/html/logs /var/www/html/assets/img/case-studies

ENV APP_PREVIEW=1 APP_ENV=local
EXPOSE 10000

