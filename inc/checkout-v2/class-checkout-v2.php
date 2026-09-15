<?php
/**
 * Checkout v2 — Scandinavian biotech/pharma presentation layer.
 *
 * Does not alter WooCommerce checkout logic, AJAX, gateways, or fragments.
 *
 * @package Blocksy_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * Checkout v2 controller.
 */
final class Blocksy_Child_Checkout_V2 {

	const VERSION = '1.0.0';

	const OPTION_ENABLED = 'biopentra_checkout_v2_enabled';

	/**
	 * Bootstrap hooks.
	 */
	public static function init(): void {
		add_filter( 'body_class', array( __CLASS__, 'body_class' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ), 120 );
		add_filter( 'woocommerce_ship_to_different_address_checked', array( __CLASS__, 'ship_to_billing_only' ), 1000 );
		add_filter( 'biopentra_checkout_v2_enabled', array( __CLASS__, 'filter_enabled' ) );
		/**
		 * Not woocommerce_before_checkout_form: the "New to crypto payments?"
		 * banner (biopentra-storefront's Crypto_Payment_Guide_Module) is
		 * prepended via a the_content filter at priority 8, which runs
		 * BEFORE the [woocommerce_checkout] shortcode itself executes — so
		 * no hook inside the checkout form, at any priority, can render
		 * above it. Hooking the_content ourselves at a later priority lets
		 * us prepend our banner in front of whatever the_content already
		 * built (including that plugin's banner), landing it above.
		 */
		add_filter( 'the_content', array( __CLASS__, 'prepend_banner_to_content' ), 9 );
		add_filter( 'woocommerce_checkout_fields', array( __CLASS__, 'tune_field_classes' ), 20 );
		add_action( 'woocommerce_review_order_before_order_total', array( __CLASS__, 'render_coupon_row' ) );
	}

	/**
	 * Resolve v2 from option, QA page, or preview query arg.
	 */
	public static function resolve_v2_from_options(): bool {
		$enabled = ( 'yes' === get_option( self::OPTION_ENABLED, 'no' ) );

		if ( ! $enabled && function_exists( 'is_page' ) && is_page( 'checkout-v2' ) ) {
			$enabled = true;
		}

		if ( ! $enabled && isset( $_GET['checkout_v2'] ) && '1' === (string) wp_unslash( $_GET['checkout_v2'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$enabled = true;
		}

		return (bool) apply_filters( 'biopentra_checkout_v2_request', $enabled );
	}

	/**
	 * Whether this HTTP request targets checkout v2 (independent of Blocksy skip flags).
	 */
	public static function is_v2_request(): bool {
		return self::resolve_v2_from_options();
	}

	/**
	 * Whether checkout v2 assets and templates apply.
	 */
	public static function is_active(): bool {
		if ( ! self::is_v2_request() ) {
			return false;
		}

		if ( ! empty( $GLOBALS['ct_skip_checkout'] ) ) {
			return false;
		}

		if ( ! function_exists( 'is_checkout' ) || ! is_checkout() ) {
			return false;
		}

		if ( is_admin() ) {
			return false;
		}

		if ( self::is_elementor_checkout_canvas() ) {
			return false;
		}

		return true;
	}

	/**
	 * Layout wrapper in form-checkout.php (uses cached request flag only).
	 */
	public static function is_layout_active(): bool {
		return self::resolve_v2_from_options();
	}

	/**
	 * Back-compat filter for biopentra_checkout_v2_enabled.
	 *
	 * @param bool $enabled Current filtered value.
	 * @return bool
	 */
	public static function filter_enabled( bool $enabled ): bool {
		return $enabled || self::is_v2_request();
	}

	/**
	 * Detect Elementor-built checkout (not classic shortcode page).
	 */
	private static function is_elementor_checkout_canvas(): bool {
		if ( ! class_exists( '\Elementor\Plugin' ) ) {
			return false;
		}

		$post_id = get_queried_object_id();
		if ( ! $post_id ) {
			return false;
		}

		$document = \Elementor\Plugin::$instance->documents->get( $post_id );
		if ( ! $document || ! $document->is_built_with_elementor() ) {
			return false;
		}

		$data = get_post_meta( $post_id, '_elementor_data', true );
		if ( ! is_string( $data ) || '' === $data ) {
			return false;
		}

		return ( false !== strpos( $data, 'woocommerce-checkout-page' ) );
	}

	/**
	 * Always bill-to = ship-to (hide separate shipping address flow).
	 *
	 * @param mixed $checked Current value.
	 * @return bool
	 */
	public static function ship_to_billing_only( $checked ): bool {
		if ( ! self::is_active() ) {
			return (bool) $checked;
		}
		return false;
	}

	/**
	 * Add layout body class when active.
	 *
	 * @param string[] $classes Body classes.
	 * @return string[]
	 */
	public static function body_class( array $classes ): array {
		if ( self::is_active() ) {
			$classes[] = 'bp-checkout-v2';
		}
		return $classes;
	}

	/**
	 * Enqueue checkout v2 CSS/JS after Blocksy + storefront styles.
	 */
	public static function enqueue_assets(): void {
		if ( ! self::is_active() ) {
			return;
		}

		$base = BLOCKSY_CHILD_DIR . '/assets/checkout-v2/';
		$uri  = BLOCKSY_CHILD_URI . '/assets/checkout-v2/';

		$css_ver = file_exists( $base . 'checkout-v2.css' ) ? (string) filemtime( $base . 'checkout-v2.css' ) : self::VERSION;
		$js_ver  = file_exists( $base . 'checkout-v2.js' ) ? (string) filemtime( $base . 'checkout-v2.js' ) : self::VERSION;

		wp_enqueue_style(
			'bp-checkout-v2',
			$uri . 'checkout-v2.css',
			array( 'ct-woocommerce-styles' ),
			$css_ver
		);

		wp_enqueue_script(
			'bp-checkout-v2',
			$uri . 'checkout-v2.js',
			array( 'jquery', 'wc-checkout' ),
			$js_ver,
			true
		);

		wp_localize_script(
			'bp-checkout-v2',
			'bpCheckoutV2',
			array(
				'stickyOffset' => is_admin_bar_showing() ? 46 : 16,
				'i18n'         => array(
					'orderSummary' => __( 'Order summary', 'blocksy-child' ),
				),
			)
		);
	}

	/**
	 * Banner above the checkout form, replacing both the plain "Checkout"
	 * page title (hidden accessibly, not removed, via .bp-checkout-v2__title-sr
	 * in CSS — kept in the DOM for SEO/screen readers) and the old plain-text
	 * trust strip it used to show here, which duplicated the same three
	 * points the banner already covers visually.
	 *
	 * @param string $content Existing page content (may already have other
	 *                        plugins' banners/notices prepended by earlier
	 *                        the_content priorities).
	 * @return string
	 */
	public static function prepend_banner_to_content( string $content ): string {
		if ( ! self::is_active() || is_admin() || ! in_the_loop() || ! is_main_query() ) {
			return $content;
		}

		$banner_path = BLOCKSY_CHILD_DIR . '/assets/checkout-v2/images/checkout-banner.webp';
		$banner_ver  = file_exists( $banner_path ) ? (string) filemtime( $banner_path ) : self::VERSION;

		ob_start();
		?>
		<div class="bp-checkout-v2__banner">
			<img
				src="<?php echo esc_url( BLOCKSY_CHILD_URI . '/assets/checkout-v2/images/checkout-banner.webp?ver=' . $banner_ver ); ?>"
				alt="<?php esc_attr_e( 'Secure checkout — encrypted payment, quality fulfillment, and fast order processing.', 'blocksy-child' ); ?>"
				width="2120"
				height="442"
				loading="eager"
			/>
		</div>
		<?php
		$banner_html = ob_get_clean();

		return $banner_html . $content;
	}

	/**
	 * Inline coupon-code row inside the order review table, between the
	 * Shipping row and the Total row (Blocksy's own coupon-form removal
	 * elsewhere on the page doesn't affect this — it's a separate hook).
	 *
	 * Deliberately NOT a <form>: this row sits inside
	 * .woocommerce-checkout-review-order-table, which itself lives inside
	 * WooCommerce's own outer <form class="checkout">. A <form> nested
	 * inside another <form> is invalid HTML, and — even though the DOM
	 * happily contains it once inserted via replaceWith() — its native
	 * `submit` event does NOT reliably bubble up to a document.body
	 * delegated handler (confirmed: jQuery('.checkout_coupon').trigger
	 * ('submit') silently fails to reach a body-level delegated listener
	 * in Chromium). WooCommerce's own coupon-apply binding
	 * ($('form.checkout_coupon').on('submit', ...) in checkout.js) is
	 * ALSO non-delegated and only ever attaches to the form present at
	 * page-ready — which this row usually isn't, since WooCommerce
	 * itself replaces the whole table via an automatic update_checkout
	 * shortly after page load. Combined, "Apply" would silently fall
	 * back to a genuine full-page form POST to the checkout URL, which
	 * WooCommerce's non-JS fallback processes server-side and redirects
	 * back from — looking like the coupon reappearing a couple of
	 * seconds later. Plain markup + our own click/keydown handlers in
	 * checkout-v2.js (delegated on body, unaffected by nesting) sidesteps
	 * all of this.
	 */
	public static function render_coupon_row(): void {
		if ( ! self::is_active() ) {
			return;
		}

		if ( ! function_exists( 'wc_coupons_enabled' ) || ! wc_coupons_enabled() ) {
			return;
		}
		?>
		<tr class="bp-checkout-v2__coupon-row">
			<td colspan="2">
				<div class="bp-checkout-v2__coupon-form">
					<label for="coupon_code" class="screen-reader-text"><?php esc_html_e( 'Coupon:', 'woocommerce' ); ?></label>
					<input type="text" name="coupon_code" class="input-text" placeholder="<?php esc_attr_e( 'Coupon code', 'woocommerce' ); ?>" id="coupon_code" value="" />
					<button type="button" class="button bp-checkout-v2__coupon-apply" name="apply_coupon"><?php esc_html_e( 'Apply', 'woocommerce' ); ?></button>
				</div>
			</td>
		</tr>
		<?php
	}

	/**
	 * Add BEM-friendly classes to checkout fields (presentation only).
	 *
	 * @param array<string, array<string, mixed>> $fields Checkout fields.
	 * @return array<string, array<string, mixed>>
	 */
	public static function tune_field_classes( array $fields ): array {
		if ( ! self::is_active() ) {
			return $fields;
		}

		foreach ( $fields as $group => $group_fields ) {
			foreach ( $group_fields as $key => $field ) {
				$existing = isset( $field['class'] ) && is_array( $field['class'] ) ? $field['class'] : array();
				$existing[] = 'bp-checkout-v2__field';
				$fields[ $group ][ $key ]['class'] = $existing;
			}
		}

		return $fields;
	}

	/**
	 * Enable on production (WP-CLI): wp option update biopentra_checkout_v2_enabled yes
	 * Disable / rollback: wp option update biopentra_checkout_v2_enabled no
	 */
	public static function enable_production(): void {
		update_option( self::OPTION_ENABLED, 'yes', false );
	}

	/**
	 * Disable checkout v2 (instant rollback of presentation layer).
	 */
	public static function disable_production(): void {
		update_option( self::OPTION_ENABLED, 'no', false );
	}
}
