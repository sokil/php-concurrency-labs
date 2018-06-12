FROM php:7.2.6-cli-alpine3.6

RUN apk update && \
    apk add --virtual .build-deps \
        autoconf \
        make \
        gcc \
        g++ \
        libevent-dev \
        openssl-dev && \
    docker-php-ext-install sockets pcntl && \
    pecl channel-update pecl.php.net && \
    yes '' | pecl install -o event-2.3.0 && \
    # ini name configured to load ext after sockets.so
    docker-php-ext-enable --ini-name=z-event.ini event.so && \
    apk del .build-deps && \
    apk add \
        bash \
        libevent \
        openssl