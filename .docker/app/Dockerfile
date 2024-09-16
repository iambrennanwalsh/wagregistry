# syntax=docker/dockerfile:1.4

# Base Image (base)
# ---------------------------- #

FROM php:8.3-fpm-alpine

ENV COMPOSER_ALLOW_SUPERUSER 1

RUN apk add --no-cache \
    acl \
    bash \
    make \
    nodejs \
    npm;

ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN set -eux; \
	install-php-extensions \
    @composer \
    apcu \
    intl \
    opcache \
    zip \
    pdo_pgsql \
    redis \
    xsl;

WORKDIR /app

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

COPY --link .docker/app/conf.d/app.ini $PHP_INI_DIR/conf.d/
COPY --link --chmod=755 .docker/app/docker-entrypoint.sh /usr/local/bin/docker-entrypoint

ENTRYPOINT ["docker-entrypoint"]

CMD ["php-fpm"]
