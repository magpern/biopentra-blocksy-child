<?php
/**
 * My Account v2 — split-hero login/register presentation layer.
 *
 * Does not alter WooCommerce auth logic, nonces, or field handling.
 *
 * @package Blocksy_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * My Account v2 controller.
 */
final class Blocksy_Child_My_Account_V2 {

	const VERSION = '1.0.0';

	const OPTION_ENABLED = 'biopentra_my_account_v2_enabled';

	/**
	 * Bootstrap hooks.
	 */
	public static function init(): void {
		add_filter( 'body_class', array( __CLASS__, 'body_class' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ), 120 );
	}

	/**
	 * Resolve v2 from option or preview query arg.
	 */
	public static function resolve_v2_from_options(): bool {
		$enabled = ( 'yes' === get_option( self::OPTION_ENABLED, 'no' ) );

		if ( ! $enabled && isset( $_GET['my_account_v2'] ) && '1' === (string) wp_unslash( $_GET['my_account_v2'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$enabled = true;
		}

		return (bool) apply_filters( 'biopentra_my_account_v2_request', $enabled );
	}

	/**
	 * Whether this HTTP request targets My Account v2.
	 */
	public static function is_v2_request(): bool {
		return self::resolve_v2_from_options();
	}

	/**
	 * Whether My Account v2 assets and templates apply — the My Account
	 * page's own login/register screen only, not the logged-in dashboard,
	 * orders, addresses, etc. views (those keep the default account layout).
	 */
	public static function is_active(): bool {
		if ( ! self::is_v2_request() ) {
			return false;
		}

		if ( is_admin() ) {
			return false;
		}

		if ( ! function_exists( 'is_account_page' ) || ! is_account_page() ) {
			return false;
		}

		if ( is_user_logged_in() ) {
			return false;
		}

		return true;
	}

	/**
	 * Layout wrapper in form-login.php (uses cached request flag only).
	 */
	public static function is_layout_active(): bool {
		return self::resolve_v2_from_options();
	}

	/**
	 * Add layout body class when active.
	 *
	 * @param string[] $classes Body classes.
	 * @return string[]
	 */
	public static function body_class( array $classes ): array {
		if ( self::is_active() ) {
			$classes[] = 'bp-my-account-v2';
		}
		return $classes;
	}

	/**
	 * Enqueue My Account v2 CSS/JS after Blocksy + storefront styles.
	 */
	public static function enqueue_assets(): void {
		if ( ! self::is_active() ) {
			return;
		}

		$base = BLOCKSY_CHILD_DIR . '/assets/my-account-v2/';
		$uri  = BLOCKSY_CHILD_URI . '/assets/my-account-v2/';

		$css_ver = file_exists( $base . 'my-account-v2.css' ) ? (string) filemtime( $base . 'my-account-v2.css' ) : self::VERSION;
		$js_ver  = file_exists( $base . 'my-account-v2.js' ) ? (string) filemtime( $base . 'my-account-v2.js' ) : self::VERSION;

		wp_enqueue_style(
			'bp-my-account-v2',
			$uri . 'my-account-v2.css',
			array( 'ct-woocommerce-styles' ),
			$css_ver
		);

		wp_enqueue_script(
			'bp-my-account-v2',
			$uri . 'my-account-v2.js',
			array(),
			$js_ver,
			true
		);
	}

	/**
	 * The brand benefit bullets shown on the marketing panel. A filter, not a
	 * hardcoded list, so copy can be tuned without another release.
	 *
	 * @return string[]
	 */
	public static function benefits(): array {
		return (array) apply_filters(
			'biopentra_my_account_v2_benefits',
			array(
				__( 'Track every order the moment it ships', 'blocksy-child' ),
				__( 'Reorder your usual research compounds in seconds', 'blocksy-child' ),
				__( 'COA batch reports linked to every order', 'blocksy-child' ),
			)
		);
	}

	/**
	 * Enable on production (WP-CLI): wp option update biopentra_my_account_v2_enabled yes
	 * Disable / rollback: wp option update biopentra_my_account_v2_enabled no
	 */
	public static function enable_production(): void {
		update_option( self::OPTION_ENABLED, 'yes', false );
	}

	/**
	 * Disable My Account v2 (instant rollback of presentation layer).
	 */
	public static function disable_production(): void {
		update_option( self::OPTION_ENABLED, 'no', false );
	}
}
