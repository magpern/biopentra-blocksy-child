<?php
/**
 * Checkout form — layout wrapper for Checkout v2.
 *
 * Based on WooCommerce core 9.4.0. Only adds structural wrappers when body.bp-checkout-v2 is present.
 *
 * Step wizard (Details / Delivery / Payment): the aside still renders the
 * exact same do_action( 'woocommerce_checkout_order_review' ) as always —
 * items table (with its shipping-method row) + #payment, completely
 * untouched WooCommerce output. checkout-v2.js physically relocates the
 * shipping row into #bp-checkout-v2-delivery-slot and #payment into
 * #bp-checkout-v2-payment-slot on load and after every WooCommerce
 * `updated_checkout` AJAX refresh (which re-renders both back into the
 * aside each time) — never clones, never re-renders, never hides the
 * originals via CSS. If JS fails to run for any reason, both remain
 * exactly where WooCommerce always puts them, in the aside, fully visible
 * and usable — checkout can never get silently stuck on a step.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.4.0
 */

defined( 'ABSPATH' ) || exit;

$use_v2_layout = class_exists( 'Blocksy_Child_Checkout_V2' ) && Blocksy_Child_Checkout_V2::is_layout_active();

if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
	return;
}

do_action( 'woocommerce_before_checkout_form', $checkout );

?>

<form name="checkout" method="post" class="checkout woocommerce-checkout<?php echo $use_v2_layout ? ' bp-checkout-v2__form' : ''; ?>" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data" aria-label="<?php echo esc_attr__( 'Checkout', 'woocommerce' ); ?>">

	<?php if ( $use_v2_layout ) : ?>
	<!-- bp-checkout-v2-layout -->
	<div class="bp-checkout-v2__grid">
		<div class="bp-checkout-v2__main">

			<nav class="bp-checkout-v2__steps" aria-label="<?php echo esc_attr__( 'Checkout steps', 'blocksy-child' ); ?>">
				<button type="button" class="bp-checkout-v2__step-tab is-active" data-step="details">
					<span class="bp-checkout-v2__step-num">1</span><?php esc_html_e( 'Details', 'blocksy-child' ); ?>
				</button>
				<button type="button" class="bp-checkout-v2__step-tab" data-step="delivery">
					<span class="bp-checkout-v2__step-num">2</span><?php esc_html_e( 'Delivery', 'blocksy-child' ); ?>
				</button>
				<button type="button" class="bp-checkout-v2__step-tab" data-step="payment">
					<span class="bp-checkout-v2__step-num">3</span><?php esc_html_e( 'Payment', 'blocksy-child' ); ?>
				</button>
			</nav>

			<div class="bp-checkout-v2__step-panel is-active" data-step="details">
	<?php endif; ?>

	<?php if ( $checkout->get_checkout_fields() ) : ?>

		<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

		<div class="col2-set" id="customer_details">
			<div class="col-1">
				<?php do_action( 'woocommerce_checkout_billing' ); ?>
			</div>

			<div class="col-2 bp-checkout-v2__shipping-col">
				<?php do_action( 'woocommerce_checkout_shipping' ); ?>
			</div>
		</div>

		<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

	<?php endif; ?>

	<?php if ( $use_v2_layout ) : ?>
				<div class="bp-checkout-v2__step-actions">
					<button type="button" class="bp-checkout-v2__step-next" data-next="delivery"><?php esc_html_e( 'Continue to delivery', 'blocksy-child' ); ?></button>
				</div>
			</div><!-- step: details -->

			<div class="bp-checkout-v2__step-panel" data-step="delivery" hidden>
				<table class="bp-checkout-v2__delivery-slot" id="bp-checkout-v2-delivery-slot" aria-live="polite"><tbody>
					<tr class="bp-checkout-v2__delivery-placeholder">
						<td><?php esc_html_e( 'Enter your delivery details first — options appear here once your address is known.', 'blocksy-child' ); ?></td>
					</tr>
				</tbody></table>
				<div class="bp-checkout-v2__step-actions">
					<button type="button" class="bp-checkout-v2__step-back" data-back="details"><?php esc_html_e( 'Back', 'blocksy-child' ); ?></button>
					<button type="button" class="bp-checkout-v2__step-next" data-next="payment"><?php esc_html_e( 'Continue to payment', 'blocksy-child' ); ?></button>
				</div>
			</div><!-- step: delivery -->

			<div class="bp-checkout-v2__step-panel" data-step="payment" hidden>
				<div id="bp-checkout-v2-payment-slot" aria-live="polite"></div>
				<div class="bp-checkout-v2__step-actions bp-checkout-v2__step-actions--payment">
					<button type="button" class="bp-checkout-v2__step-back" data-back="delivery"><?php esc_html_e( 'Back', 'blocksy-child' ); ?></button>
				</div>
			</div><!-- step: payment -->

		</div><!-- .bp-checkout-v2__main -->

		<aside class="bp-checkout-v2__aside" aria-labelledby="order_review_heading">
			<div class="bp-checkout-v2__summary-card">
				<h3 id="order_review_heading" class="bp-checkout-v2__summary-title"><?php esc_html_e( 'Your order', 'blocksy-child' ); ?></h3>

				<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

				<div id="order_review" class="woocommerce-checkout-review-order bp-checkout-v2__order-review">
					<?php do_action( 'woocommerce_checkout_order_review' ); ?>
				</div>

				<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
			</div>
		</aside>

		</div><!-- .bp-checkout-v2__grid -->

	<?php else : ?>

		<?php do_action( 'woocommerce_checkout_before_order_review_heading' ); ?>

		<h3 id="order_review_heading"><?php esc_html_e( 'Your order', 'woocommerce' ); ?></h3>

		<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

		<div id="order_review" class="woocommerce-checkout-review-order">
			<?php do_action( 'woocommerce_checkout_order_review' ); ?>
		</div>

		<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>

	<?php endif; ?>

</form>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
