FROM php:8.4-alpine

ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN apk add --no-cache tini busybox busybox-suid \
	&& install-php-extensions redis pdo_mysql \
	&& echo "* * * * * php -f /app/artisan schedule:run" >> /etc/crontabs/root

ENTRYPOINT ["/sbin/tini", "--"]
