<?php
/**
 * Branded CSS for WooCommerce's own email header/footer/content templates.
 *
 * Extends the CSS WooCommerce already generates from its Settings > Emails
 * options (colors, header image, footer text — all set via those options,
 * not here) rather than replacing the table-based HTML shell those
 * templates render, so every client-compatibility fix WooCommerce ships
 * stays intact. Applies to every customer email built on the
 * `email_improvements` template (order emails, fulfillment, reset
 * password, etc. all share the same header/footer + this filter).
 *
 * @package Blocksy_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * Email branding controller.
 */
final class Blocksy_Child_Email_Branding {

	/**
	 * Bootstrap hooks.
	 */
	public static function init(): void {
		add_filter( 'woocommerce_email_styles', array( __CLASS__, 'extend_styles' ), 20, 1 );
	}

	/**
	 * Appends brand CSS after WooCommerce's own generated styles, so the
	 * equal-specificity `.link` selector resolves to this rule on source
	 * order without needing `!important`. Everything else worth branding
	 * (logo, heading/link color, footer text) is already driven by
	 * WooCommerce's own Settings > Emails options — set there, not here —
	 * so this stays a single, low-risk addition: turning the plain-text
	 * `.link` call-to-action every `email_improvements` template uses
	 * (reset password, order details, fulfillment) into a real button.
	 *
	 * @param string $css WooCommerce's own generated CSS.
	 * @return string
	 */
	public static function extend_styles( string $css ): string {
		return $css . '
			.link {
				display: inline-block;
				background-color: #1c2329;
				color: #ffffff !important;
				padding: 12px 28px;
				border-radius: 8px;
				font-weight: 600;
				text-decoration: none;
				margin: 4px 0 8px;
			}
		';
	}
}
