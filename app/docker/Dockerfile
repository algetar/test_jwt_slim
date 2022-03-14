FROM php:7.4-fpm

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod uga+x /usr/local/bin/install-php-extensions && sync && \
    /usr/local/bin/install-php-extensions amqp \
    apcu \
    bcmath \
    exif \
    gd \
    grpc \
    imap \
    intl \
    ldap \
    mcrypt \
    opcache \
    redis \
    sockets \
    uuid \
    yaml \
    zip  \
    soap

RUN apt-get update

# Install Postgre PDO
RUN apt-get install -y libpq-dev \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql

RUN pecl install xdebug && docker-php-ext-enable xdebug
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www
