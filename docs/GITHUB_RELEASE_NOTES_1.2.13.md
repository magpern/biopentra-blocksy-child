# Release notes — biopentra-blocksy-child v1.2.13

**M3 B4 — PDP reviews section CSS + rating-summary contrast**

## Summary

CSS-only companion to `biopentra-storefront` M3 UPR host integration: styles the dedicated PDP reviews section and hardens rating-summary link contrast for WCAG AA.

## Changes

- **B4 reviews section:** spacing for `.bp-pdp-reviews-section`; `:focus-visible` ring on `#reviews` for programmatic focus (A11). Sticky buy bar untouched.
- **Rating summary contrast:** hardcode `#174a87` for `.bp-pdp-rating-summary a` (+ focus-visible) against the PDP summary surface.

## Upgrade notes

- Pair with `biopentra-storefront` **0.9.37+** (M3 host integration) and UPR host modules as deployed on DEV.
- Theme is bind-mounted on `dev.biopentra.eu` — see `docs/DEV-SYNC.md`.
- After deploy: `wp elementor flush-css && wp cache flush`.

## Rollback

Install v1.2.10 release ZIP (PDP-1 baseline without M3 reviews-section CSS).
