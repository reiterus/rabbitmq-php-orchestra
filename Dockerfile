FROM php:8.2.3-cli-alpine

RUN apk update && apk upgrade --no-cache \
    && apk add --no-cache linux-headers \
    && docker-php-ext-install sockets

WORKDIR /app
 
COPY . /app
COPY --from=composer:2.5.4 /usr/bin/composer /usr/bin/composer

COPY entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

ENTRYPOINT ["docker-entrypoint"]
