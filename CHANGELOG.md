# Changelog — Biopentra Blocksy Child

## [1.4.0] - 2026-09-16

### Added

- **Checkout v2**: inline coupon-code field in the order-review table (Blocksy's theme setting had removed the default coupon form site-wide, so there was previously no way to enter one at checkout).
- **Checkout v2**: gift card / store credit panel relocated from a full-width box above Billing Details to a small link under the coupon field.
- **Checkout v2**: centered "Secure Checkout" banner replaces the plain page title and old plain-text trust strip; real `<h1>` kept off-screen for accessibility; cropped (not scaled) on mobile to stay legible.

### Fixed

- Checkout v2 coupon removal silently reapplying itself a few seconds later — the coupon-row `<form>` was invalidly nested inside WooCommerce's own outer checkout form, breaking native `submit` event bubbling and causing "Apply" to fall back to a full-page form POST. Fixed by driving it off delegated click/keydown handlers on plain markup instead of a nested `<form>`.
- Checkout banner now renders via a `the_content` filter so it reliably outranks other plugins' own `the_content`-injected banners, which no in-form hook priority could ever beat.
- Collapsed an empty ~280px gap Blocksy's page-title section left above the banner.

### Notes

- An in-progress 3-step checkout wizard was built and then fully reverted mid-session per product decision; net change to the checkout layout is zero beyond the items above.

## [1.3.0] - 2026-09-15

### Added

- **My Account v2**: split-hero login/register layout (brand panel + Sign in/Create account tabs), a redesigned logged-in dashboard/addresses area (icon sidebar nav, dark hero banner, a single "Delivery details" card instead of separate billing/shipping blocks, plus a read-only "Email address" card), and a review-before-save modal for address changes. Gated by option `biopentra_my_account_v2_enabled` (default off) or `?my_account_v2=1` for preview, same pattern as Checkout v2. Every new template override renders byte-identical to WooCommerce core when v2 is inactive.
- Account-details email field is now read-only when v2 is active, matching the addresses card's "contact support to change it" policy — enforced both in the UI and server-side (`Blocksy_Child_My_Account_V2::guard_account_email()`, so a hand-crafted POST bypassing the `readonly` attribute is still a no-op).
- **Email branding** (`Blocksy_Child_Email_Branding`): extends WooCommerce's own `woocommerce_email_styles`-generated CSS with real button styling for the `.link` call-to-action every `email_improvements`-era customer email uses (reset password, order details, fulfillment) — one change, applies to all of them. `emails/customer-reset-password.php` override adds a highlighted "what's in your account" box and corrected copy.

### Fixed

- A `wc_get_template()` self-recursion in the addresses-page template (calling `wc_get_template( 'myaccount/my-address.php' )` from inside that same override) caused a memory-exhaustion fatal — the non-v2 fallback now inlines WooCommerce core's markup instead of delegating.
- Blocksy's own icon-font glyph on each account-nav item was rendering alongside the new inline-SVG icons, showing two icons per row — suppressed via CSS for v2.
- The login-page logo was stretched ~2.4x wider than its true aspect ratio: `.bp-ma-v2__panel`'s flex `align-items: stretch` (the default) resolves an item's own `width: auto` to 100% of the cross axis regardless of that declaration — fixed with `align-self: flex-start`.
- The floating currency-switcher pill (fixed to the viewport's left edge) overlapped the panel/hero heading at mobile widths — cleared with scoped padding.

## [1.2.15] - 2026-09-10

### Fixed

- **PDP dedicated reviews section alignment:** the storefront module renders a bare `<section class="bp-pdp-reviews-section">` directly into `.ct-container-full`, so it spanned the full container instead of lining up with the description tabs / "You may also like" row. Constrain it to the content column (`--theme-container-width` / `--theme-block-max-width`, centered) to match Blocksy's `.is-width-constrained` blocks.
- **Split reviews notice:** Blocksy's `[data-content="normal"] .woocommerce-Reviews` 2-column grid pushed the host "reviews unavailable" notice into the left column and the `#comments` box into the right. Force `display: block` on `.woocommerce-Reviews` inside the section so the notice and the Reviews box stack under the description.
- **Unavailable-notice styling:** the host adapter renders `.upr-host-adapter-review-unavailable`, but the stylesheet only targeted the old `.biopentra-upr-host-review-unavailable` class, so the notice lost its bordered callout box. Add the current class to the affected rules.

## [1.2.14] - 2026-09-01

### Added

- Self-updates from a private update server via the bundled Plugin Update Checker v5 library (`lib/plugin-update-checker/`), registered only when `PRIVATE_UPDATE_SERVER` is defined in `wp-config.php`.
- CI workflow that uploads the release ZIP to the update server on each `v*` tag.

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
