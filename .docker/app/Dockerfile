FROM php:8.2-fpm-alpine

LABEL maintainer="Shohanur Rahman <shohan.nsu.cse@gmail.com>"

# Define the build argument for PHP extension dependencies
ARG PHPIZE_DEPS="autoconf curl g++ gcc libtool make"

# Set environment variables
ENV PECL_EXTENSIONS="pcov redis"
ENV PHP_EXTENSIONS="bz2 exif gd gettext intl pcntl pdo_mysql zip bcmath"

# Install system dependencies and linux-headers
RUN apk add --no-cache --virtual .build-deps \
    $PHPIZE_DEPS libtool linux-headers \
    && apk add --no-cache bzip2-dev gettext-dev git icu icu-dev libintl libpng-dev libzip-dev mysql-client \
    # Install and enable PECL extensions
    && docker-php-source extract \
    && pecl channel-update pecl.php.net \
    && pecl install $PECL_EXTENSIONS \
    && cd /usr/src/php/ext/ \
    && docker-php-ext-enable $PECL_EXTENSIONS \
    && docker-php-ext-configure opcache --enable-opcache \
    # Install and enable PHP extensions
    && docker-php-ext-install -j "$(nproc)" $PHP_EXTENSIONS \
    # Clean up
    && apk del -f .build-deps \
    && cd /usr/local/etc/php/conf.d/ \
    && pecl clear-cache \
    && docker-php-source delete \
    && rm -rf /var/cache/apk/* /tmp/* /var/tmp/* /usr/share/doc/* /usr/share/man/*

# Install latest composer with prestissimo
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Node.js and npm
RUN apk add --no-cache nodejs npm

# Install required packages for building extensions
RUN apk add --no-cache rabbitmq-c-dev autoconf g++ make

# Install AMQP extension
RUN pecl install amqp
RUN docker-php-ext-enable amqp

# Install Supervisor and copy the required file
RUN apk add --no-cache supervisor
COPY supervisord.conf /etc/supervisord.conf
RUN chmod 644 /etc/supervisord.conf

# Set working directory and non-root user
WORKDIR /var/www/html

# Start Supervisor and php-fpm
CMD /bin/sh -c "php-fpm -F & /usr/bin/supervisord -c /etc/supervisord.conf"

USER www-data