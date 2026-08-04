# Changelog — Biopentra Blocksy Child

## [1.1.0] - 2026-08-04

### Added

- **Milestone D2A:** PDP layout/gallery CSS — gallery height caps; related/upsell unhide on small viewports; ATC touch targets.
- **Milestone D2B:** mobile sticky purchase bar (`inc/pdp-sticky-bar/`) with focus-safe ATC sync and debounced IO hide.
- Dev bind-mount documented in `docs/DEV-SYNC.md` (same pattern as first-party plugins).

## [1.0.0] - 2026-08-01

### Added

- Initial import into version control. Byte-identical to the theme as it existed at `/opt/biopentra/data/wordpress/html/wp-content/themes/blocksy-child/` on dev.biopentra.eu (checksum-verified against `style.css` and `functions.php`; all other files copied unmodified).
- `.github/workflows/ci.yml` and `.github/workflows/release.yml`, `scripts/build-zip.sh`, `scripts/release-audit.sh`, `scripts/lib/verify-release-zip.py` — same conventions as [biopentra-loop-card](https://github.com/magpern/biopentra-loop-card).
- `docs/DEV-SYNC.md` — documents how to sync repo changes to the live (non-bind-mounted) dev theme directory.

### Notes

- No functional or behavioral change to the theme. This release exists solely to bring the theme under version control ahead of the mobile-first storefront redesign (see `/opt/biopentra/docs/storefront-redesign/`).
