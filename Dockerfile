FROM php:8.4-fpm AS base 

ARG user=bloodbank
ARG uid=1000

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    bash \
    libpq-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd sockets

RUN pecl install -o -f redis \
    && docker-php-ext-enable redis

RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - && \
    apt-get install -y nodejs

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN useradd -G www-data,root -u $uid -d /home/$user $user \
    && mkdir -p /home/$user/.composer \
    && chown -R $user:$user /home/$user
    
COPY docker/php/custom.ini /usr/local/etc/php/conf.d/custom.ini

RUN chmod -R 777 /var/www

WORKDIR /var/www

USER $user

FROM base AS prod
RUN docker-php-ext-install opcache

FROM base AS development
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug