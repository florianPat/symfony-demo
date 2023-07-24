FROM docker.io/library/composer:2.5.4 as composer

FROM docker.io/library/php:8.1-fpm-alpine as php

FROM php as base

COPY docker/php.ini /usr/local/etc/php/

COPY --from=composer /usr/bin/composer /usr/local/bin/composer

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

FROM public.ecr.aws/lambda/provided:al2 as aws-lambda
FROM base as prod-lambda

COPY composer.* ./

RUN composer install --no-scripts --no-dev

COPY . ./

RUN composer dump-autoload --classmap-authoritative --no-dev --no-ansi

ENV APP_ENV=prod
ENV APP_DEBUG=0
ENV PHP_MEMORY_LIMIT=128M
ENV PHP_MAX_EXECUTION_TIME=30
ENV OPCACHE_VALIDATE_TIMESTAMPS=1

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

FROM base as dev

ENV PHP_MEMORY_LIMIT=-1
ENV PHP_MAX_EXECUTION_TIME=0
ENV OPCACHE_VALIDATE_TIMESTAMPS=1

# Install xdebug
RUN \
    apk add --no-cache linux-headers $PHPIZE_DEPS \
    && \
    pecl install xdebug-3.2.0 \
    && \
    docker-php-ext-enable xdebug \
    && \
    apk del $PHPIZE_DEPS

COPY docker/zz-fpm-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf

ENTRYPOINT ["/helpers/lando-entrypoint.sh"]
CMD ["php-fpm"]
