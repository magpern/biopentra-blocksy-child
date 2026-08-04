# Dev sync procedure

## Bind mount (current — Milestone D closure)

As of Milestone D stabilization, `biopentra-blocksy-child` is **bind-mounted** into the
dev WordPress container the same way first-party plugins are:

```yaml
# apps/wordpress/compose.yml (wordpress + wpcli services)
- /opt/biopentra/dev/biopentra-blocksy-child:/var/www/html/wp-content/themes/blocksy-child
```

Edits under `/opt/biopentra/dev/biopentra-blocksy-child` are live on `dev.biopentra.eu`
after a cache flush. No `docker cp` is required for day-to-day work.

After changing `compose.yml`, recreate the WordPress service once:

```bash
cd /opt/biopentra/apps/wordpress
docker compose config >/dev/null
docker compose up -d wordpress
docker compose run --rm -T wpcli wp elementor flush-css
docker compose run --rm -T wpcli wp cache flush
```

Verify:

```bash
diff <(docker exec wordpress cat /var/www/html/wp-content/themes/blocksy-child/functions.php) \
     /opt/biopentra/dev/biopentra-blocksy-child/functions.php
```

## Why this was deferred earlier

Phase 0 / early milestones treated theme bind-mount as an infrastructure change
and used `docker cp` instead. That caused repeated DEV-SYNC drift during Milestone D
PDP work. Closing D safely adopts the same mount pattern already used for plugins.

## Emergency fallback (if mount is removed)

```bash
#!/usr/bin/env bash
set -euo pipefail
REPO=/opt/biopentra/dev/biopentra-blocksy-child
CONTAINER=wordpress
DEST=/var/www/html/wp-content/themes/blocksy-child

docker cp "$REPO/style.css" "$CONTAINER:$DEST/style.css"
docker cp "$REPO/functions.php" "$CONTAINER:$DEST/functions.php"
docker cp "$REPO/assets/." "$CONTAINER:$DEST/assets"
docker cp "$REPO/inc/." "$CONTAINER:$DEST/inc"
docker cp "$REPO/woocommerce/." "$CONTAINER:$DEST/woocommerce"
docker exec -u root "$CONTAINER" chown -R www-data:www-data "$DEST"

cd /opt/biopentra/apps/wordpress
docker compose run --rm -T wpcli wp elementor flush-css
docker compose run --rm -T wpcli wp cache flush
```

## Production

Production never uses bind mounts or this sync script. Production deploys **only**
via the GitHub Release ZIP (`blocksy-child-{version}.zip`), extracted over
`wp-content/themes/blocksy-child/` — see the repo root `README.md` and
`/opt/biopentra/docs/storefront-redesign/deployment/`.
