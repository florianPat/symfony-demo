# Build Caddy with the Mercure and Vulcain modules
# Temporary fix for https://github.com/dunglas/mercure/issues/770
FROM caddy:2.7-builder-alpine AS app_caddy_builder

RUN xcaddy build v2.6.4 \
	--with github.com/dunglas/mercure/caddy \
	--with github.com/dunglas/vulcain/caddy

# Caddy image
FROM caddy:2-alpine AS app_caddy

WORKDIR /app

COPY --from=app_caddy_builder /usr/bin/caddy /usr/bin/caddy
COPY docker/caddy/Caddyfile /etc/caddy/Caddyfile

ENTRYPOINT ["/helpers/lando-entrypoint.sh"]
CMD ["caddy", "run", "--config", "/etc/caddy/Caddyfile", "--adapter", "caddyfile"]
