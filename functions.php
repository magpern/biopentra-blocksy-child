<?php
/**
 * Blocksy Child — BioPentra
 *
 * @package Blocksy_Child
 */

defined( 'ABSPATH' ) || exit;

define( 'BLOCKSY_CHILD_VERSION', '1.0.0' );
define( 'BLOCKSY_CHILD_DIR', get_stylesheet_directory() );
define( 'BLOCKSY_CHILD_URI', get_stylesheet_directory_uri() );

require_once BLOCKSY_CHILD_DIR . '/inc/checkout-v2/class-checkout-v2.php';

Blocksy_Child_Checkout_V2::init();

/**
 * Milestone D1 — PDP gallery image caps + mobile unsticky.
 */
function blocksy_child_enqueue_pdp_gallery_assets() {
	if ( ! function_exists( 'is_product' ) || ! is_product() ) {
		return;
	}

	$deps = array( 'ct-main-styles' );
	if ( wp_style_is( 'biopentra-bp-tokens', 'registered' ) || wp_style_is( 'biopentra-bp-tokens', 'enqueued' ) ) {
		$deps[] = 'biopentra-bp-tokens';
	}

	wp_enqueue_style(
		'blocksy-child-pdp-gallery',
		BLOCKSY_CHILD_URI . '/assets/pdp/gallery.css',
		$deps,
		BLOCKSY_CHILD_VERSION
	);
}
add_action( 'wp_enqueue_scripts', 'blocksy_child_enqueue_pdp_gallery_assets', 30 );
