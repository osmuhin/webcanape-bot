FROM alpine:latest

ARG SCHEDULER_TOKEN=''

RUN apk add --no-cache tini busybox busybox-suid curl \
	&& rm -rf /etc/periodic/*  \
	&& printf '* * * * * curl --silent -H "Accept: application/json" "http://nginx:80/schedule-run?token=%s"' "$SCHEDULER_TOKEN" >> /etc/crontabs/root

ENTRYPOINT ["/sbin/tini", "--"]
