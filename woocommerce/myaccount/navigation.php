<?php
/**
 * My Account navigation — icon sidebar for My Account v2.
 *
 * Based on WooCommerce core 9.3.0. Only adds an icon per item when v2 is
 * active; the menu items, URLs, current-page detection and markup class
 * names are all WooCommerce's own (wc_get_account_menu_items() etc.),
 * untouched, so any endpoint a plugin adds still appears correctly.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.3.0
 */

defined( 'ABSPATH' ) || exit;

$use_v2_layout = class_exists( 'Blocksy_Child_My_Account_V2' ) && Blocksy_Child_My_Account_V2::is_layout_active();

do_action( 'woocommerce_before_account_navigation' );
?>

<nav class="woocommerce-MyAccount-navigation" aria-label="<?php esc_html_e( 'Account pages', 'woocommerce' ); ?>">
	<ul>
		<?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : ?>
			<li class="<?php echo wc_get_account_menu_item_classes( $endpoint ); ?>">
				<a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>" <?php echo wc_is_current_account_menu_item( $endpoint ) ? 'aria-current="page"' : ''; ?>>
					<?php if ( $use_v2_layout ) { echo Blocksy_Child_My_Account_V2::nav_icon( $endpoint ); } // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php echo esc_html( $label ); ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
</nav>

<?php do_action( 'woocommerce_after_account_navigation' ); ?>
