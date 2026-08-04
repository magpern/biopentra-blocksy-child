# Release notes — biopentra-blocksy-child v1.1.0

**Milestone D2 — PDP layout polish + sticky purchase bar**

## Summary

Product detail page work for Milestone D: gallery height caps, related/upsell visibility on small viewports, SDS typography touch targets, and a mobile sticky add-to-cart bar with focus-safe sync.

## Changes

- **D2A:** `assets/pdp/layout.css`, `assets/pdp/gallery.css` — gallery caps; unhide related/upsells previously `ct-hidden-sm/md`; ATC touch target polish.
- **D2B:** `inc/pdp-sticky-bar/` + sticky bar CSS/JS — mobile sticky purchase bar; IntersectionObserver show/hide with debounce; ATC `disabled`/label sync only when values change (avoids focus loss).

## Dev sync

Theme is bind-mounted on `dev.biopentra.eu` (`apps/wordpress/compose.yml`). See `docs/DEV-SYNC.md`.

## Upgrade notes

- Pair with `biopentra-storefront` **0.8.0** and `biopentra-loop-card` **1.6.1**.
- After deploy: `wp elementor flush-css && wp cache flush`.

## Rollback

Install v1.0.0 release ZIP (pre-PDP Milestone D theme).
