ARG PHP_VERSION=8.4
ARG NODE_VERSION=20

# -----------------------------------------------------------
# Stage 1: Composer dependencies
# -----------------------------------------------------------
FROM composer:2 as composer_builder
WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts

COPY . .
RUN composer dump-autoload --optimize --no-dev

# -----------------------------------------------------------
# Stage 2: Node frontend assets build
# -----------------------------------------------------------
FROM node:${NODE_VERSION}-alpine as node_builder
WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

# Copy application files and vendor directory (needed for Flux UI CSS stubs)
COPY . .
COPY --from=composer_builder /app/vendor /app/vendor

RUN npm run build

# -----------------------------------------------------------
# Stage 3: Production Runtime
# -----------------------------------------------------------
FROM ubuntu:22.04 as base

ARG PHP_VERSION=8.4
ENV DEBIAN_FRONTEND=noninteractive \
    PHP_PM_MAX_CHILDREN=10 \
    PHP_PM_START_SERVERS=3 \
    PHP_MIN_SPARE_SERVERS=2 \
    PHP_MAX_SPARE_SERVERS=4 \
    PHP_DATE_TIMEZONE=UTC \
    PHP_DISPLAY_ERRORS=Off \
    PHP_ERROR_REPORTING=22527 \
    PHP_MEMORY_LIMIT=256M \
    PHP_MAX_EXECUTION_TIME=90 \
    PHP_POST_MAX_SIZE=100M \
    PHP_UPLOAD_MAX_FILE_SIZE=100M \
    PHP_ALLOW_URL_FOPEN=On

# Install PHP, Nginx, Supervisor, SQLite & system utilities
COPY .fly/php/ondrej_ubuntu_php.gpg /etc/apt/trusted.gpg.d/ondrej_ubuntu_php.gpg
ADD .fly/php/packages/${PHP_VERSION}.txt /tmp/php-packages.txt

RUN apt-get update \
    && apt-get install -y --no-install-recommends gnupg2 ca-certificates git curl zip unzip \
                                                  sqlite3 nginx supervisor cron \
    && echo "deb http://ppa.launchpad.net/ondrej/php/ubuntu jammy main" > /etc/apt/sources.list.d/ondrej-ubuntu-php.list \
    && apt-get update \
    && apt-get -y --no-install-recommends install $(cat /tmp/php-packages.txt) \
    && ln -sf /usr/sbin/php-fpm${PHP_VERSION} /usr/sbin/php-fpm \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Copy configuration files
COPY .fly/nginx/ /etc/nginx/
COPY .fly/fpm/ /etc/php/${PHP_VERSION}/fpm/
COPY .fly/supervisor/ /etc/supervisor/
COPY .fly/entrypoint.sh /entrypoint
COPY .fly/start-nginx.sh /usr/local/bin/start-nginx
RUN chmod 754 /usr/local/bin/start-nginx \
    && chmod +x /entrypoint

# Copy application source and vendor from composer_builder
WORKDIR /var/www/html
COPY . /var/www/html
COPY --from=composer_builder /app/vendor /var/www/html/vendor

# Copy compiled frontend assets from node_builder
COPY --from=node_builder /app/public/build /var/www/html/public/build

# Setup storage permissions and cron scheduler
RUN mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache storage/logs bootstrap/cache database /var/run/php \
    && rm -f database/database.sqlite* \
    && touch database/database.sqlite \
    && chown -R www-data:www-data /var/www/html /var/run/php \
    && chmod -R 775 storage bootstrap/cache database /var/run/php \
    && echo "MAILTO=\"\"\n* * * * * www-data /usr/bin/php /var/www/html/artisan schedule:run >> /dev/null 2>&1" > /etc/cron.d/laravel \
    && chmod 0644 /etc/cron.d/laravel

EXPOSE 8080

ENTRYPOINT ["/entrypoint"]
