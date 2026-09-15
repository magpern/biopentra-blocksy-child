<?php
/**
 * Edit address form — card layout + confirm-before-save modal for My
 * Account v2.
 *
 * Field rendering, nonce, and the underlying POST are WooCommerce's own
 * (woocommerce_form_field(), wp_nonce_field()), untouched — the modal only
 * intercepts the submit client-side to show a review step; the "Yes, save
 * these" button submits the same real form (bp-my-account-v2.js).
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.3.0
 */

defined( 'ABSPATH' ) || exit;

$use_v2_layout = class_exists( 'Blocksy_Child_My_Account_V2' ) && Blocksy_Child_My_Account_V2::is_layout_active();
$page_title     = ( 'billing' === $load_address ) ? esc_html__( 'Billing address', 'woocommerce' ) : esc_html__( 'Shipping address', 'woocommerce' );

do_action( 'woocommerce_before_edit_account_address_form' ); ?>

<?php if ( ! $load_address ) : ?>
	<?php wc_get_template( 'myaccount/my-address.php' ); ?>
<?php else : ?>

	<?php if ( $use_v2_layout ) : ?>
	<div class="bp-ma-v2__panel-card">
	<?php endif; ?>

	<form method="post" novalidate<?php echo $use_v2_layout ? ' class="bp-ma-v2__address-form"' : ''; ?>>

		<h2><?php echo apply_filters( 'woocommerce_my_account_edit_address_title', $use_v2_layout ? esc_html__( 'Delivery details', 'blocksy-child' ) : $page_title, $load_address ); ?></h2><?php // @codingStandardsIgnoreLine ?>

		<div class="woocommerce-address-fields">
			<?php do_action( "woocommerce_before_edit_address_form_{$load_address}" ); ?>

			<div class="woocommerce-address-fields__field-wrapper">
				<?php
				foreach ( $address as $key => $field ) {
					woocommerce_form_field( $key, $field, wc_get_post_data_by_key( $key, $field['value'] ) );
				}
				?>
			</div>

			<?php do_action( "woocommerce_after_edit_address_form_{$load_address}" ); ?>

			<p>
				<button type="submit" class="button<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>" name="save_address" value="<?php esc_attr_e( 'Save address', 'woocommerce' ); ?>"><?php esc_html_e( 'Save address', 'woocommerce' ); ?></button>
				<?php wp_nonce_field( 'woocommerce-edit_address', 'woocommerce-edit-address-nonce' ); ?>
				<input type="hidden" name="action" value="edit_address" />
			</p>
		</div>

	</form>

	<?php if ( $use_v2_layout ) : ?>
	</div>

	<div class="bp-ma-v2__modal" id="bp-ma-v2-address-confirm" hidden>
		<div class="bp-ma-v2__modal-card" role="dialog" aria-modal="true" aria-labelledby="bp-ma-v2-modal-title">
			<div class="bp-ma-v2__modal-header">
				<span class="bp-ma-v2__modal-icon"><?php echo Blocksy_Child_My_Account_V2::warning_icon(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<h2 id="bp-ma-v2-modal-title"><?php esc_html_e( 'Is this right?', 'blocksy-child' ); ?></h2>
			</div>
			<p><?php esc_html_e( 'This is what will go on the parcel label and fill your next checkout.', 'blocksy-child' ); ?></p>
			<table class="bp-ma-v2__rows bp-ma-v2__modal-rows"></table>
			<p class="bp-ma-v2__modal-footnote">
				<?php echo Blocksy_Child_My_Account_V2::info_icon(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php esc_html_e( 'Orders already placed keep the address they were placed with. This only changes what happens next time.', 'blocksy-child' ); ?>
			</p>
			<div class="bp-ma-v2__modal-actions">
				<button type="button" class="bp-ma-v2__modal-back"><?php esc_html_e( 'Go back and change', 'blocksy-child' ); ?></button>
				<button type="button" class="bp-ma-v2__modal-confirm"><?php esc_html_e( 'Yes, save these', 'blocksy-child' ); ?></button>
			</div>
		</div>
	</div>
	<?php endif; ?>

<?php endif; ?>

<?php do_action( 'woocommerce_after_edit_account_address_form' ); ?>
