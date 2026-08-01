<?php
/**
 * Checkout form — layout wrapper for Checkout v2.
 *
 * Based on WooCommerce core 9.4.0. Only adds structural wrappers when body.bp-checkout-v2 is present.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.4.0
 */

defined( 'ABSPATH' ) || exit;

$use_v2_layout = function_exists( 'Blocksy_Child_Checkout_V2' ) && Blocksy_Child_Checkout_V2::is_layout_active();

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

	<?php if ( ! $use_v2_layout ) : ?>

		<?php do_action( 'woocommerce_checkout_before_order_review_heading' ); ?>

		<h3 id="order_review_heading"><?php esc_html_e( 'Your order', 'woocommerce' ); ?></h3>

		<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

		<div id="order_review" class="woocommerce-checkout-review-order">
			<?php do_action( 'woocommerce_checkout_order_review' ); ?>
		</div>

		<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>

	<?php else : ?>

		<?php do_action( 'woocommerce_checkout_before_order_review_heading' ); ?>

		</div><!-- .bp-checkout-v2__main -->

		<aside class="bp-checkout-v2__aside" aria-labelledby="order_review_heading">
			<div class="bp-checkout-v2__summary-card">
				<h3 id="order_review_heading" class="bp-checkout-v2__summary-title"><?php esc_html_e( 'Order summary', 'blocksy-child' ); ?></h3>

				<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

				<div id="order_review" class="woocommerce-checkout-review-order bp-checkout-v2__order-review">
					<?php do_action( 'woocommerce_checkout_order_review' ); ?>
				</div>

				<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
			</div>
		</aside>

		</div><!-- .bp-checkout-v2__grid -->

	<?php endif; ?>

</form>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
