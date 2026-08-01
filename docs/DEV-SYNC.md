# Dev sync procedure

This theme is **not** bind-mounted into the dev WordPress container (unlike the `biopentra-*` plugins, which are bind-mounted from `/opt/biopentra/dev/<plugin>` — see `apps/wordpress/compose.yml`). Editing files in this repo has **no effect** on dev.biopentra.eu until you sync them across.

## Why not just add a bind mount?

Adding a theme bind mount is an infrastructure change to `apps/wordpress/compose.yml` (requires `docker compose up -d` to recreate the container) and is out of scope for the work that created this repo (Phase 0 of the storefront redesign — see `/opt/biopentra/docs/storefront-redesign/`). It may be proposed separately later. Until then, use the sync script below.

## Why not edit the files directly on the server?

Per `/opt/biopentra/CLAUDE.md`: `data/` is persistent service data owned by each image's runtime UID (html = uid 33 `www-data` inside the container). It must never be hand-edited or chowned to `magpern` — the live theme directory is not even writable by `magpern` on the host (verified: `www-data:www-data`, mode `rwxrwxr-x`, and `magpern` is not in that group).

## Sync script

```bash
#!/usr/bin/env bash
set -euo pipefail
REPO=/opt/biopentra/dev/biopentra-blocksy-child
CONTAINER=wordpress
DEST=/var/www/html/wp-content/themes/blocksy-child

# Copy repo content (excluding VCS/CI/build-only files) into the live theme dir.
docker cp "$REPO/style.css" "$CONTAINER:$DEST/style.css"
docker cp "$REPO/functions.php" "$CONTAINER:$DEST/functions.php"
docker cp "$REPO/assets/." "$CONTAINER:$DEST/assets"
docker cp "$REPO/inc/." "$CONTAINER:$DEST/inc"
docker cp "$REPO/woocommerce/." "$CONTAINER:$DEST/woocommerce"

# docker cp writes as root; restore the runtime UID/GID the image expects.
docker exec -u root "$CONTAINER" chown -R www-data:www-data "$DEST"

# Elementor/object caches may hold stale CSS references after a theme change.
cd /opt/biopentra/apps/wordpress
docker compose run --rm -T wpcli wp elementor flush-css
docker compose run --rm -T wpcli wp cache flush
```

This is intentionally the same shape as `docker cp` + `chown` — it never runs as `magpern`, never uses `docker.sock` from inside a container, and never modifies `compose.yml`. It only touches files already owned by `www-data`, restoring that ownership afterward (consistent with "do NOT chown to magpern" — we chown *to* `www-data`, matching the existing owner).

## Verifying a sync

```bash
diff <(docker exec wordpress cat /var/www/html/wp-content/themes/blocksy-child/style.css) style.css
diff <(docker exec wordpress cat /var/www/html/wp-content/themes/blocksy-child/functions.php) functions.php
```

## Production

Production never uses this sync script. Production deploys **only** via the GitHub Release ZIP (`blocksy-child-{version}.zip`), extracted over `wp-content/themes/blocksy-child/` — see the repo root `README.md` and `/opt/biopentra/docs/storefront-redesign/deployment/`.
