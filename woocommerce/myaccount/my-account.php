<?php
/**
 * My Account page — adds the full-width hero above Blocksy's own
 * `.ct-acount-nav` + content layout, for My Account v2.
 *
 * Blocksy's parent-theme override of this same template file wraps
 * do_action('woocommerce_account_navigation') in `.ct-acount-nav`; a hook
 * fired from inside navigation.php (woocommerce_before_account_navigation)
 * would land nested inside that wrapper, not spanning full width above it,
 * so the hero is rendered directly here instead, as a sibling before it.
 * Everything else replicates Blocksy's own template exactly.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.0
 */

defined( 'ABSPATH' ) || exit;

$use_v2_layout = class_exists( 'Blocksy_Child_My_Account_V2' ) && Blocksy_Child_My_Account_V2::is_layout_active();

if ( $use_v2_layout ) {
	Blocksy_Child_My_Account_V2::render_hero();
}

if ( function_exists( 'blocksy_woocommerce_has_account_customizations' ) && blocksy_woocommerce_has_account_customizations() ) {
	echo '<div class="ct-acount-nav">';
}

do_action( 'woocommerce_account_navigation' );

if ( function_exists( 'blocksy_woocommerce_has_account_customizations' ) && blocksy_woocommerce_has_account_customizations() ) {
	echo '</div>';
}

?>

<div class="woocommerce-MyAccount-content">
	<?php do_action( 'woocommerce_account_content' ); ?>
</div>
