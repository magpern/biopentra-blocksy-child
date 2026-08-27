# Changelog — Biopentra Blocksy Child

## [1.2.13] - 2026-08-27

### Added

- **M3 B4 PDP reviews section styles:** spacing for `.bp-pdp-reviews-section`; `:focus-visible` ring on `#reviews` for programmatic focus (A11). Sticky buy bar untouched.

### Fixed

- **Rating-summary link contrast:** hardcode `#174a87` for `.bp-pdp-rating-summary a` (+ focus-visible) to meet WCAG AA against the PDP summary surface (preserves prior uncommitted corrective).

## [1.2.10] - 2026-08-24

### Fixed

- **PDP-1 accessibility corrective:** `.bp-pdp-meta-label` (SKU/Category/Tags labels in the purchase-panel metadata) failed WCAG AA contrast (measured 2.97:1, required 4.5:1). Root cause: Blocksy's native `.product_meta > span > *` rule applies `opacity:.7` to every direct child, including this label span, dragging `var(--bp-color-text-muted, #666)` (~5.7:1 at full opacity) under the AA floor. Fixed with a scoped `opacity: 1` reset on `.bp-pdp-meta-label` only — value text keeps Blocksy's native muted treatment untouched, no other metadata/typography/layout changed.
- Found during PDP-1 final closure verification (real axe-core/Playwright run); `1.2.9` did not pass final accessibility verification — `1.2.10` is the corrected, final PDP-1 theme baseline. See `biopentra-custom-plugins/docs/storefront-redesign/changes/pdp-1-product-page-redesign.md`.

### Notes

- Rollback baseline unchanged: `1.1.0`. `1.2.9` remains an immutable historical tag (pre-corrective).

## [1.2.9] - 2026-08-24

### Added

- **PDP-1 — Expanded Product Page Redesign:** purchase-panel visual treatment (`assets/pdp/purchase-panel.css`) — border/radius/shadow surface, eyebrow, icon + two-line trust row, availability checkmark restyle, metadata label alignment, bold variation attribute label, variation "Clear" link suppression; gallery hover-zoom removed (scoped `.zoomImg` suppression); tabs (Description/Additional Information) styling (`assets/pdp/tabs.css`); thumbnail-strip clipping fixed at root cause, centered + tightened spacing, sticky gallery explicitly disabled (`assets/pdp/gallery.css`). Companion PHP module: `biopentra-custom-plugins` `plugins/biopentra-storefront/modules/pdp-purchase-panel/`.
- Intermediate point releases during this session (`1.1.4`, `1.1.5`, `1.1.8`, `1.1.9`, `1.2.3`) — cache-busting/incremental fixes, not separately tagged; `1.2.9` is this milestone's closure release. Plan: `biopentra-custom-plugins/docs/storefront-redesign/plans/PDP-1_PURCHASE_SUMMARY_REDESIGN.md`. Change record: `biopentra-custom-plugins/docs/storefront-redesign/changes/pdp-1-product-page-redesign.md`.

### Notes

- Rollback baseline: `1.1.0`.

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
