FROM alpine:latest

RUN apk add --no-cache tini curl \
	&& rm -rf /etc/periodic/* \
	&& echo '* * * * * curl --silent -H "Accept: application/json" "http://nginx:80/schedule-run?token=$SCHEDULER_TOKEN"' > /etc/crontabs/root

ENTRYPOINT ["/sbin/tini", "--", "crond", "-f", "-L", "/dev/stdout"]
