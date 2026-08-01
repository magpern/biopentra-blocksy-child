# Biopentra Blocksy Child

Child theme of [Blocksy](https://wordpress.org/themes/blocksy/) for the Biopentra WooCommerce storefront. Adds the "checkout v2" presentation layer and two safe WooCommerce template overrides. Does not change WooCommerce checkout logic, AJAX, gateways, or validation — see [THEME-README.md](THEME-README.md) for the feature-level rollback/preview notes carried over from the original in-place theme.

**Canonical source and releases:** [magpern/biopentra-blocksy-child](https://github.com/magpern/biopentra-blocksy-child)

**Deployed folder name:** `blocksy-child` (must stay `blocksy-child` under `wp-content/themes/` — Blocksy's `Template:` field in `style.css` targets the parent by slug, and WordPress' active-theme record keys off the stylesheet directory name). The GitHub **repository** is named `biopentra-blocksy-child` for clarity in the `magpern` org; the **release ZIP** unpacks to `blocksy-child/` to match.

## History

Originally created directly on dev.biopentra.eu (`/opt/biopentra/data/wordpress/html/wp-content/themes/blocksy-child/`) with no version control and no bind mount — a rebuild of the WordPress container/volume would have silently lost it. This repository is the byte-identical import of that theme (verified via checksum against the live files) as of 2026-08-01, formalized with the same CI/build/release conventions as [biopentra-loop-card](https://github.com/magpern/biopentra-loop-card). No functional change was made in the import.

## Features

- `inc/checkout-v2/class-checkout-v2.php` — "Checkout v2" presentation layer: trust strip, ship-to-billing default, scoped `body.bp-checkout-v2` styling. Gated by option `biopentra_checkout_v2_enabled`, page slug `checkout-v2`, or `?checkout_v2=1`. Skips itself when the checkout page uses the Elementor Pro `woocommerce-checkout-page` widget.
- `woocommerce/checkout/form-checkout.php`, `woocommerce/checkout/form-shipping.php` — WooCommerce template overrides used only when checkout v2 is active.
- `assets/checkout-v2/checkout-v2.css`, `assets/checkout-v2/checkout-v2.js` — presentation assets, cache-busted via `filemtime()`.

## Requirements

- WordPress 6.5+
- [Blocksy](https://wordpress.org/themes/blocksy/) parent theme active
- WooCommerce (checkout v2 is a no-op without it)

## Development

This theme is **not** bind-mounted into the dev WordPress container (unlike the `biopentra-*` plugins under `/opt/biopentra/dev/`, see `apps/wordpress/compose.yml`). Edits here do not take effect on dev.biopentra.eu until synced. See [`docs/DEV-SYNC.md`](docs/DEV-SYNC.md) for the sync procedure and rationale.

```bash
bash scripts/build-zip.sh
bash scripts/release-audit.sh
```

Tag `v{version}` (e.g. `v1.0.0`) to publish a GitHub Release with `blocksy-child-{version}.zip`. **Do not tag without explicit product-owner approval.**

## Production deploy

Production deploys ONLY via GitHub Release ZIP — extract `blocksy-child-{version}.zip` over `wp-content/themes/blocksy-child/` (git is the source of truth, never hand-edit the server). See `/opt/biopentra/docs/storefront-redesign/deployment/` for phase-by-phase replay records once changes beyond this import are made.
