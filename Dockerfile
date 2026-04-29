FROM php:8.4.6-zts-alpine3.20

RUN apk add --repository dl-cdn.alpinelinux.org

RUN apk update && apk upgrade

RUN apk --no-cache add \
	icu-dev \
	libpng-dev \
    libzip-dev \
	libmcrypt-dev \
	libmcrypt \
	curl-dev \
	bash \
	postgresql-dev

# Installing this packages will improve speed when installing php extensions
RUN apk --no-cache add \
	gcc g++ autoconf m4 libbz2 perl dpkg-dev dpkg libmagic file \
	libgcc libstdc++ binutils gmp libgomp libatomic mpc1 gcc musl-dev \
	libc-dev make re2c

RUN docker-php-ext-install intl 
RUN docker-php-ext-install gd 
RUN docker-php-ext-install exif 
RUN docker-php-ext-install zip 
RUN docker-php-ext-install bcmath 
RUN docker-php-ext-install curl 
RUN docker-php-ext-install pdo 
# RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pgsql pdo_pgsql

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

RUN  rm -rf /var/cache/apk/*

WORKDIR /var/www/myapp

COPY . /var/www/myapp

RUN composer install --no-dev --optimize-autoloader

RUN chown -R www-data:www-data /var/www/myapp \
    && chmod -R 775 /var/www/myapp/storage \
    && chmod -R 775 /var/www/myapp/bootstrap/cache

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0"]