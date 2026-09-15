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
	 * The hero banner above the logged-in account sidebar/content. Called
	 * directly from the my-account.php override (only reached once a
	 * customer is logged in — the logged-out view calls form-login.php
	 * directly and never renders my-account.php at all).
	 */
	public static function render_hero(): void {
		?>
		<div class="bp-ma-v2__hero">
			<div class="bp-ma-v2__hero-text">
				<p class="bp-ma-v2__eyebrow bp-ma-v2__eyebrow--on-dark"><?php esc_html_e( 'Your account', 'blocksy-child' ); ?></p>
				<h1><?php esc_html_e( 'Account settings', 'blocksy-child' ); ?></h1>
				<p><?php esc_html_e( 'The details we deliver to, and how you sign in.', 'blocksy-child' ); ?></p>
			</div>
		</div>
		<?php
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
	 * Whether My Account v2 assets and templates apply — the whole My
	 * Account area, logged out (login/register) and logged in (dashboard,
	 * addresses, etc.) alike.
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
	 * A 20x20 stroke icon for one account-navigation endpoint, or a plain
	 * dot for anything unmapped (e.g. an endpoint a third-party plugin
	 * adds — gift cards, subscriptions — never left iconless or broken).
	 *
	 * @param string $endpoint The account menu endpoint key.
	 * @return string Inline SVG markup.
	 */
	public static function nav_icon( string $endpoint ): string {
		$icons = array(
			'dashboard'        => '<rect x="2.5" y="2.5" width="6" height="6" rx="1.5"/><rect x="11.5" y="2.5" width="6" height="6" rx="1.5"/><rect x="2.5" y="11.5" width="6" height="6" rx="1.5"/><rect x="11.5" y="11.5" width="6" height="6" rx="1.5"/>',
			'orders'           => '<path d="M5 6.5V5a5 5 0 0 1 10 0v1.5"/><rect x="2.5" y="6.5" width="15" height="11" rx="2"/>',
			'downloads'        => '<path d="M10 3v10m0 0-4-4m4 4 4-4"/><path d="M3.5 15v1.5A1.5 1.5 0 0 0 5 18h10a1.5 1.5 0 0 0 1.5-1.5V15"/>',
			'edit-address'     => '<path d="M10 2.5c-3 0-5.5 2.4-5.5 5.6C4.5 12 10 17.5 10 17.5s5.5-5.5 5.5-9.4c0-3.2-2.5-5.6-5.5-5.6Z"/><circle cx="10" cy="8.2" r="2.2"/>',
			'payment-methods'  => '<rect x="2.5" y="5" width="15" height="10.5" rx="2"/><path d="M2.5 8.5h15"/>',
			'edit-account'     => '<circle cx="10" cy="6.5" r="3.2"/><path d="M3.5 17c.9-3.4 3.4-5.2 6.5-5.2s5.6 1.8 6.5 5.2"/>',
			'customer-logout'  => '<path d="M8 17.5H4.5A1.5 1.5 0 0 1 3 16V4a1.5 1.5 0 0 1 1.5-1.5H8"/><path d="M13 6.5 17 10l-4 3.5"/><path d="M17 10H7.5"/>',
		);

		$path = $icons[ $endpoint ] ?? '<circle cx="10" cy="10" r="2.5"/>';

		return '<svg class="bp-ma-v2__nav-icon" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $path . '</svg>';
	}

	/**
	 * The warning-triangle icon used on the "please check these are right"
	 * address notice and the address-change confirmation modal.
	 */
	public static function warning_icon(): string {
		return '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M8.5 3.3 1.8 15a1.5 1.5 0 0 0 1.3 2.2h13.8a1.5 1.5 0 0 0 1.3-2.2L11.5 3.3a1.5 1.5 0 0 0-2.6 0Z"/><path d="M10 8v3.5"/><circle cx="10" cy="14" r="0.15" fill="currentColor"/></svg>';
	}

	/**
	 * The info-circle icon used on the address-change confirmation modal's
	 * footnote.
	 */
	public static function info_icon(): string {
		return '<svg width="16" height="16" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="10" cy="10" r="7.5"/><path d="M10 9v5"/><circle cx="10" cy="6.3" r="0.15" fill="currentColor"/></svg>';
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
