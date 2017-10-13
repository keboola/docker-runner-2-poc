FROM php:7.1-alpine
MAINTAINER Martin Halamicek <Martin@keboola.com>

RUN apk add --no-cache wget git unzip gzip docker libmcrypt-dev zlib-dev

RUN docker-php-ext-install mcrypt zip pcntl

COPY . /code/
WORKDIR /code/

RUN ./composer.sh \
  && rm composer.sh \
  && mv composer.phar /usr/local/bin/composer \
  && composer install --no-interaction \
  && apk del wget git unzip

CMD ["php","./run.php"]