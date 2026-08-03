<?php
/**
 * Milestone D2B — sticky purchase bar on single product (mobile).
 *
 * @package Blocksy_Child
 */

defined( 'ABSPATH' ) || exit;

class Blocksy_Child_Pdp_Sticky_Bar {

	/**
	 * Bootstrap hooks.
	 */
	public static function init() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue' ), 35 );
		add_action( 'wp_footer', array( __CLASS__, 'render_markup' ), 20 );
	}

	/**
	 * Enqueue sticky bar assets on product pages only.
	 */
	public static function enqueue() {
		if ( ! function_exists( 'is_product' ) || ! is_product() ) {
			return;
		}

		$deps_css = array();
		if ( wp_style_is( 'biopentra-bp-tokens', 'registered' ) || wp_style_is( 'biopentra-bp-tokens', 'enqueued' ) ) {
			$deps_css[] = 'biopentra-bp-tokens';
		}
		if ( wp_style_is( 'blocksy-child-pdp-layout', 'registered' ) || wp_style_is( 'blocksy-child-pdp-layout', 'enqueued' ) ) {
			$deps_css[] = 'blocksy-child-pdp-layout';
		}

		wp_enqueue_style(
			'blocksy-child-pdp-sticky',
			BLOCKSY_CHILD_URI . '/assets/pdp/sticky-bar.css',
			$deps_css,
			BLOCKSY_CHILD_VERSION
		);

		wp_enqueue_script(
			'blocksy-child-pdp-sticky',
			BLOCKSY_CHILD_URI . '/assets/pdp/sticky-bar.js',
			array( 'jquery' ),
			BLOCKSY_CHILD_VERSION,
			true
		);

		wp_localize_script(
			'blocksy-child-pdp-sticky',
			'bpPdpSticky',
			array(
				'i18n' => array(
					'addToCart'   => __( 'Add to cart', 'blocksy-child' ),
					'selectOpts'  => __( 'Select options', 'blocksy-child' ),
					'outOfStock'  => __( 'Out of stock', 'blocksy-child' ),
					'regionLabel' => __( 'Quick purchase', 'blocksy-child' ),
				),
			)
		);
	}

	/**
	 * Footer region markup (populated/synced by JS).
	 */
	public static function render_markup() {
		if ( ! function_exists( 'is_product' ) || ! is_product() ) {
			return;
		}
		?>
		<div
			id="bp-pdp-sticky"
			class="bp-pdp-sticky"
			role="region"
			aria-label="<?php echo esc_attr__( 'Quick purchase', 'blocksy-child' ); ?>"
			hidden
			data-bp-sticky-bar
		>
			<div class="bp-pdp-sticky__inner">
				<div class="bp-pdp-sticky__info">
					<div class="bp-pdp-sticky__price" data-bp-sticky-price></div>
					<div class="bp-pdp-sticky__meta" data-bp-sticky-meta></div>
				</div>
				<button type="button" class="bp-pdp-sticky__atc" data-bp-sticky-atc>
					<?php esc_html_e( 'Add to cart', 'blocksy-child' ); ?>
				</button>
			</div>
		</div>
		<?php
	}
}

Blocksy_Child_Pdp_Sticky_Bar::init();
