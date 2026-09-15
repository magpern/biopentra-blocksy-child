# Biopentra Blocksy Child 1.3.0 — release notes

## Added

- **My Account v2.** Split-hero login/register layout (brand panel + Sign
  in/Create account tabs), a redesigned logged-in dashboard/addresses area
  (icon sidebar nav, dark hero banner, one "Delivery details" card instead
  of separate billing/shipping blocks, plus a read-only "Email address"
  card), and a review-before-save modal for address changes. Gated by
  option `biopentra_my_account_v2_enabled` (default **off**) or
  `?my_account_v2=1` for preview — same pattern as Checkout v2. Every new
  template override renders byte-identical to WooCommerce core when v2 is
  inactive, so installing this ZIP alone changes nothing until the option
  is turned on.
- Account-details email field is read-only when v2 is active, matching
  the addresses card's "contact support to change it" policy — enforced
  both in the UI and server-side, so it can't be bypassed with a
  hand-crafted POST.
- **Email branding.** WooCommerce's own generated email CSS gets one
  addition: real button styling for the `.link` call-to-action every
  `email_improvements`-era customer email uses (reset password, order
  details, fulfillment) — applies to all of them from one change. The
  reset-password email also gets a highlighted "what's in your account"
  box and corrected copy. This part is **not** gated by the v2 option —
  it's live as soon as this version is installed.

## Fixed

- A `wc_get_template()` self-recursion in the addresses-page template
  caused a memory-exhaustion fatal; the non-v2 fallback now inlines
  WooCommerce core's markup instead of delegating to itself.
- Blocksy's own icon-font glyph on each account-nav item was rendering
  alongside the new inline-SVG icons (two icons per row); suppressed for
  v2.
- The login-page logo was stretched ~2.4x wider than its true aspect
  ratio, from a flexbox `align-items: stretch` interaction; fixed with
  `align-self: flex-start`.
- The floating currency-switcher pill overlapped the panel/hero heading
  at mobile widths; cleared with scoped padding.

## Install

Deploy the `blocksy-child` theme **1.3.0** / tag **`v1.3.0`**.

The email-branding CSS/template changes are live immediately. My Account
v2 stays off until `wp option update biopentra_my_account_v2_enabled yes`
is run — WooCommerce's own email settings (base color, header image,
footer text, reset-password additional content) also need to be set to
match dev's, since those are database options, not code.

Rollback: **1.2.15** / `v1.2.15`.
