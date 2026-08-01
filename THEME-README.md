# Blocksy Child — Checkout v2

Premium Scandinavian biotech/pharma checkout presentation for BioPentra. **Does not change** WooCommerce checkout logic, AJAX, gateways, or validation.

## Rollback (instant)

```bash
# Disable presentation layer (keeps child theme active)
./wp option update biopentra_checkout_v2_enabled no

# Or switch back to parent theme only
./wp theme activate blocksy
```

## Preview (safe)

| URL | Effect |
|-----|--------|
| `/checkout/?checkout_v2=1` | Preview v2 on live checkout page |
| `/checkout-v2/` | Draft QA page (same cart session rules as checkout) |

## Enable production

```bash
./wp option update biopentra_checkout_v2_enabled yes
./wp elementor flush-css
./wp cache flush
```

## Files

- `inc/checkout-v2/class-checkout-v2.php` — toggles, assets, ship-to-billing
- `assets/checkout-v2/checkout-v2.css` — scoped `body.bp-checkout-v2` styles
- `assets/checkout-v2/checkout-v2.js` — sticky offset + payment card class
- `woocommerce/checkout/form-checkout.php` — grid wrapper (v2 only)
- `woocommerce/checkout/form-shipping.php` — hides ship-to-different (v2 only)

## Backups

WooCommerce template snapshots before v2: `../../backups/pre-checkout-v2-*` in project `checkout-v2-build/backups/`.

## Elementor

Skips v2 when the checkout page is built with Elementor Pro `woocommerce-checkout-page` widget (uses `$ct_skip_checkout` pattern).
