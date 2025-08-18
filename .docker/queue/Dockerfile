FROM php:8.4-alpine

ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN apk add --no-cache tini \
	&& install-php-extensions pdo_mysql

ENTRYPOINT ["/sbin/tini", "--"]
