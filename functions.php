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
