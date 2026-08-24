<?php
/**
 * Blocksy Child — BioPentra
 *
 * @package Blocksy_Child
 */

defined( 'ABSPATH' ) || exit;

define( 'BLOCKSY_CHILD_VERSION', '1.1.8' );
define( 'BLOCKSY_CHILD_DIR', get_stylesheet_directory() );
define( 'BLOCKSY_CHILD_URI', get_stylesheet_directory_uri() );

require_once BLOCKSY_CHILD_DIR . '/inc/checkout-v2/class-checkout-v2.php';
require_once BLOCKSY_CHILD_DIR . '/inc/pdp-sticky-bar/class-pdp-sticky-bar.php';

Blocksy_Child_Checkout_V2::init();

/**
 * Milestone D1/D2A — PDP gallery + layout assets.
 */
function blocksy_child_enqueue_pdp_assets() {
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

	wp_enqueue_style(
		'blocksy-child-pdp-layout',
		BLOCKSY_CHILD_URI . '/assets/pdp/layout.css',
		array( 'blocksy-child-pdp-gallery' ),
		BLOCKSY_CHILD_VERSION
	);

	// PDP-1 — purchase panel (WP3/WP4).
	wp_enqueue_style(
		'blocksy-child-pdp-purchase-panel',
		BLOCKSY_CHILD_URI . '/assets/pdp/purchase-panel.css',
		array( 'blocksy-child-pdp-layout' ),
		BLOCKSY_CHILD_VERSION
	);
}
add_action( 'wp_enqueue_scripts', 'blocksy_child_enqueue_pdp_assets', 30 );
