FROM php:8.1-fpm-alpine3.16

# Install linux packages
RUN apk --no-cache add \
    sudo \
    make \
    bash-completion \
    nano

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && composer --version

RUN apk add --no-cache --virtual BuildDeps autoconf g++ linux-headers \
    && pecl install ast \
    && docker-php-ext-enable ast \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && rm -rf /tmp/pear; apk del BuildDeps;

# Copy php configs
COPY ./conf/dev/php /usr/local/etc/php

CMD php-fpm