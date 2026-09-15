<?php
/**
 * Customer Reset Password email — branded content, WooCommerce core 11.1.0
 * behaviour and variables unchanged.
 *
 * The `email_improvements` branch below adds a highlighted "what's in your
 * account" box and turns the CTA into a real button (styled by
 * Blocksy_Child_Email_Branding's `.link` CSS). The non-improvements branch
 * is WooCommerce core's own markup, untouched, so this still renders
 * correctly if that feature is ever turned off site-wide.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates\Emails
 * @version 10.9.0
 */

use Automattic\WooCommerce\Utilities\FeaturesUtil;

defined( 'ABSPATH' ) || exit;

$email_improvements_enabled = FeaturesUtil::feature_is_enabled( 'email_improvements' );
$reset_url                  = add_query_arg(
	array(
		'key'   => $reset_key,
		'id'    => $user_id,
		'login' => rawurlencode( $user_login ),
	),
	wc_get_endpoint_url( 'lost-password', '', wc_get_page_permalink( 'myaccount' ) )
);

do_action( 'woocommerce_email_header', $email_heading, $email );

?>

<?php if ( $email_improvements_enabled ) : ?>

<div class="email-introduction">

	<?php /* translators: %s: Customer first name, or username if name is not available */ ?>
	<p><?php printf( esc_html__( 'Hi %s,', 'woocommerce' ), esc_html( $user_display_name ) ); ?></p>
	<?php /* translators: %s: Store name */ ?>
	<p><?php printf( esc_html__( 'Press the button below to choose a new password for your %s account. The link works once and expires within 24 hours.', 'woocommerce' ), esc_html( $blogname ) ); ?></p>

	<p><a class="link" href="<?php echo esc_url( $reset_url ); ?>"><?php esc_html_e( 'Set your password', 'woocommerce' ); ?></a></p>

	<table border="0" cellpadding="0" cellspacing="0" width="100%" role="presentation">
		<tr>
			<td style="background-color:#f2f6f5;border-radius:12px;padding:20px 24px;">
				<p style="margin:0 0 4px;font-weight:700;"><?php esc_html_e( 'What is in your account', 'blocksy-child' ); ?></p>
				<p style="margin:0;"><?php esc_html_e( 'Every order you have placed with us, with its status and what was in it.', 'blocksy-child' ); ?></p>
			</td>
		</tr>
	</table>

	<div class="hr hr-top"></div>
	<?php /* translators: %s: Username */ ?>
	<p><?php echo wp_kses( sprintf( __( 'Username: <b>%s</b>', 'woocommerce' ), esc_html( $user_login ) ), array( 'b' => array() ) ); ?></p>
	<div class="hr hr-bottom"></div>

	<p><?php esc_html_e( 'If you did not request this, you can ignore it. Nothing changes until the link above is used.', 'blocksy-child' ); ?></p>

</div>

<?php else : ?>

	<p><?php printf( esc_html__( 'Hi %s,', 'woocommerce' ), esc_html( $user_display_name ) ); ?></p>
	<p><?php printf( esc_html__( 'Someone has requested a new password for the following account on %s:', 'woocommerce' ), esc_html( $blogname ) ); ?></p>
	<p><?php printf( esc_html__( 'Username: %s', 'woocommerce' ), esc_html( $user_login ) ); ?></p>
	<p><?php esc_html_e( 'If you didn\'t make this request, just ignore this email. If you\'d like to proceed:', 'woocommerce' ); ?></p>
	<p><a class="link" href="<?php echo esc_url( $reset_url ); ?>"><?php esc_html_e( 'Click here to reset your password', 'woocommerce' ); ?></a></p>

<?php endif; ?>

<?php
if ( $additional_content ) {
	echo $email_improvements_enabled ? '<table border="0" cellpadding="0" cellspacing="0" width="100%" role="presentation"><tr><td class="email-additional-content email-additional-content-aligned">' : '';
	echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
	echo $email_improvements_enabled ? '</td></tr></table>' : '';
}

do_action( 'woocommerce_email_footer', $email );
