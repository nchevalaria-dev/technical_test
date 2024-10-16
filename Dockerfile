FROM php:7.4-alpine

ARG UID=1000
ARG GID=1000

ENV COMPOSER_MEMORY_LIMIT=-1

RUN apk update \
    && apk upgrade \
    && apk add --no-cache bash shadow

RUN usermod -u ${UID} www-data && groupmod -g ${GID} www-data

WORKDIR /var/www

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY --chown=www-data:www-data composer.lock composer.json ./

RUN mkdir -p /var/www/public/tmp  \
    && /bin/bash -c 'mkdir -p /var/www/storage/{app,logs,framework/{cache,sessions,testing,views}}'

RUN chown -R www-data:www-data /var/www && chmod 775 -R /var/www

USER www-data

RUN composer install --no-scripts && rm -rf .composer

COPY --chown=www-data:www-data ./artisan ./artisan
COPY --chown=www-data:www-data ./server.php ./server.php
COPY --chown=www-data:www-data ./bootstrap ./bootstrap
COPY --chown=www-data:www-data ./config ./config
COPY --chown=www-data:www-data ./database ./database
COPY --chown=www-data:www-data ./routes ./routes
COPY --chown=www-data:www-data ./resources ./resources
COPY --chown=www-data:www-data ./public/index.php ./public/index.php
COPY --chown=www-data:www-data ./.env.example ./.env
COPY --chown=www-data:www-data ./app ./app

CMD ["php", "artisan", "serve", "--host", "0.0.0.0", "--port", "8000"]
