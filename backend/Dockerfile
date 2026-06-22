FROM php:8.3-cli

WORKDIR /app

ENV APP_ENV=prod
ENV APP_DEBUG=0
ENV COMPOSER_ALLOW_SUPERUSER=1

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY . .

RUN touch .env

RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

RUN composer dump-autoload --optimize --no-dev

RUN php bin/console cache:clear --env=prod || true

EXPOSE 10000

CMD ["sh", "-c", "php -S 0.0.0.0:${PORT:-10000} -t public"]