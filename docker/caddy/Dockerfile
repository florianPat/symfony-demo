# Build Caddy with the Mercure and Vulcain modules
# Temporary fix for https://github.com/dunglas/mercure/issues/770
FROM caddy:2.8.4-builder-alpine@sha256:c7b4d7372becf00ffbb2862572f203e7e7e202f456545563ce3f41e5d561ef36 AS app_caddy_builder

RUN xcaddy build v2.8.4 \
	--with github.com/dunglas/mercure/caddy \
	--with github.com/dunglas/vulcain/caddy

# Caddy image
FROM caddy:2.8.4-alpine@sha256:33937b9d51461ea87794350c1c09ce53f327802508929d78f3b5642533f2f7db AS app_caddy

WORKDIR /app

COPY --from=app_caddy_builder /usr/bin/caddy /usr/bin/caddy
COPY docker/caddy/Caddyfile /etc/caddy/Caddyfile

ENTRYPOINT ["/helpers/lando-entrypoint.sh"]
CMD ["caddy", "run", "--config", "/etc/caddy/Caddyfile", "--adapter", "caddyfile"]
