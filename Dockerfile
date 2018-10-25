FROM php:fpm-alpine
COPY ./ /app/
WORKDIR /app
RUN apk add --no-cache nginx \
    && mkdir /run/nginx \
    && chown -R www-data:www-data /app \
    && chmod -R 777 /app \
    && mv default.conf /etc/nginx/conf.d \
    && mv php.ini /usr/local/etc/php

EXPOSE 80
# Persistent config file and cache
VOLUME [ "/app"]

CMD php-fpm & \
    nginx -g "daemon off;"