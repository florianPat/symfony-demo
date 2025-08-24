FROM composer:2.8.11@sha256:d013f2c06509ff0a06342f67a7fc062deebda2c36e17b415b035f1299dcd181e AS composer

FROM php:8.2.29-fpm-alpine3.22@sha256:b7355fb38ef93fd0ef6ccbecee006a25f9bb7fbb65a9f6b7a4721582b75c073a AS php

FROM php AS base

# install php extensions
#RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-install bcmath
RUN apk --no-cache add postgresql-libs postgresql-dev \
	&& docker-php-ext-install pgsql pdo_pgsql \
	&& apk del postgresql-dev

# install ca-certificates
RUN \
   apk --update --no-cache add ca-certificates \
   && \
   update-ca-certificates

# Install tools
RUN apk add --no-cache \
	git \
	unzip

ARG APP_USER=app
ARG APP_GROUP=app
ARG WORKDIR=/app

WORKDIR $WORKDIR

# Add app user
RUN \
    addgroup -S "$APP_GROUP" \
    && \
    adduser \
        -S "$APP_USER" \
        -G "$APP_GROUP" \
    && \
    chown $APP_USER:$APP_GROUP .

COPY --from=composer /usr/bin/composer /usr/local/bin/composer

FROM public.ecr.aws/lambda/provided:al2 AS aws-lambda
FROM base AS prod-lambda

COPY docker/php.ini /usr/local/etc/php/

COPY composer.* ./

RUN composer install --no-scripts --no-dev

COPY . ./

RUN composer dump-autoload --classmap-authoritative --no-dev --no-ansi

ENV APP_ENV=prod
ENV APP_DEBUG=0
ENV PHP_MEMORY_LIMIT=128M
ENV PHP_MAX_EXECUTION_TIME=30
ENV OPCACHE_VALIDATE_TIMESTAMPS=0
ENV ENABLE_OPCACHE=1

ENV LAMBDA_TASK_ROOT=/var/task
WORKDIR ${LAMBDA_TASK_ROOT}

COPY --from=aws-lambda /lambda-entrypoint.sh /lambda-entrypoint.sh
COPY --from=aws-lambda /usr/local/bin/aws-lambda-rie /usr/local/bin/aws-lambda-rie
COPY docker/lambda-bootstrap /var/runtime/bootstrap
RUN chmod +rx /var/runtime/bootstrap

RUN DATABASE_URL= php bin/console cache:warmup --quiet

ENV APP_RUNTIME='Runtime\Bref\Runtime'
ENV BREF_LOOP_MAX=100
ENV BREF_RUNNER_TYPE=aws

ENTRYPOINT ["/lambda-entrypoint.sh"]
CMD [ "public/index.php" ]

FROM base AS dev

# Install xdebug
RUN \
    apk add --no-cache linux-headers $PHPIZE_DEPS \
    && \
    pecl install xdebug-3.4.5 \
    && \
    docker-php-ext-enable xdebug \
    && \
    apk del $PHPIZE_DEPS

COPY docker/php.ini /usr/local/etc/php/
COPY docker/zz-fpm-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf

ENV PHP_MEMORY_LIMIT=-1
ENV PHP_MAX_EXECUTION_TIME=0
ENV OPCACHE_VALIDATE_TIMESTAMPS=1
ENV ENABLE_OPCACHE=1

ENTRYPOINT ["/helpers/lando-entrypoint.sh"]
CMD ["php-fpm"]
