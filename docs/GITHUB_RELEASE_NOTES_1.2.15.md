# Biopentra Blocksy Child 1.2.15 — release notes

## Fixed

- **PDP dedicated reviews section alignment.** The storefront reviews module
  renders a bare `<section class="bp-pdp-reviews-section">` into Blocksy's
  full-width `.ct-container-full`, so it spanned the whole container and
  Blocksy's `[data-content="normal"] .woocommerce-Reviews` 2-column grid split
  the host "reviews unavailable" notice (left) from the `#comments` box (right).
  The section is now constrained to the content column
  (`--theme-container-width` / `--theme-block-max-width`, centered) and
  `.woocommerce-Reviews` is forced to `display: block` inside it, so the notice
  and the Reviews box stack under the description. CSS only.
- **Unavailable-notice styling.** The host adapter renders
  `.upr-host-adapter-review-unavailable`; the stylesheet only targeted the
  legacy `.biopentra-upr-host-review-unavailable` class, so the notice had lost
  its bordered callout box. Added the current class to the affected rules.

## Install

Deploy the `blocksy-child` theme **1.2.15** / tag **`v1.2.15`**.

Rollback: **1.2.13** / `v1.2.13` (1.2.14 was tagged but not deployed).
