<?php
/**
 * Login Form — layout wrapper for My Account v2.
 *
 * Based on WooCommerce core 9.9.0. When body.bp-my-account-v2 is NOT active,
 * this renders byte-identical output to WooCommerce's own template — only
 * additional wrapper markup is inserted for the v2 layout, nothing from the
 * original template is removed. The login/register forms themselves are
 * WooCommerce's own markup, hooks, and nonces, untouched either way.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.9.0
 */

defined( 'ABSPATH' ) || exit;

$use_v2_layout      = class_exists( 'Blocksy_Child_My_Account_V2' ) && Blocksy_Child_My_Account_V2::is_layout_active();
$registration_is_on = ( 'yes' === get_option( 'woocommerce_enable_myaccount_registration' ) );

do_action( 'woocommerce_before_customer_login_form' );

?>

<?php if ( $use_v2_layout ) : ?>

	<!-- bp-my-account-v2-layout -->
	<div class="bp-ma-v2">

		<div class="bp-ma-v2__panel">
			<?php
			$bp_ma_logo_id = get_theme_mod( 'custom_logo' );
			if ( $bp_ma_logo_id ) {
				echo wp_get_attachment_image( $bp_ma_logo_id, 'medium', false, array( 'class' => 'bp-ma-v2__logo' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			} else {
				echo '<span class="bp-ma-v2__logo bp-ma-v2__logo--text">' . esc_html( get_bloginfo( 'name' ) ) . '</span>';
			}
			?>
			<p class="bp-ma-v2__eyebrow"><?php esc_html_e( 'Your account', 'blocksy-child' ); ?></p>
			<h1 class="bp-ma-v2__heading"><?php esc_html_e( 'All your orders, one place', 'blocksy-child' ); ?></h1>
			<ul class="bp-ma-v2__benefits">
				<?php foreach ( Blocksy_Child_My_Account_V2::benefits() as $bp_ma_benefit ) : ?>
					<li><?php echo esc_html( $bp_ma_benefit ); ?></li>
				<?php endforeach; ?>
			</ul>
		</div>

		<div class="bp-ma-v2__card">

			<?php if ( $registration_is_on ) : ?>
				<div class="bp-ma-v2__tabs" role="tablist">
					<button type="button" class="bp-ma-v2__tab is-active" data-bp-ma-tab="login" role="tab" aria-selected="true"><?php esc_html_e( 'Sign in', 'blocksy-child' ); ?></button>
					<button type="button" class="bp-ma-v2__tab" data-bp-ma-tab="register" role="tab" aria-selected="false"><?php esc_html_e( 'Create account', 'blocksy-child' ); ?></button>
				</div>
			<?php endif; ?>

			<div class="bp-ma-v2__pane is-active" data-bp-ma-pane="login">
				<h2 class="bp-ma-v2__pane-title"><?php esc_html_e( 'Welcome back', 'blocksy-child' ); ?></h2>

<?php else : ?>

	<?php if ( $registration_is_on ) : ?>

	<div class="u-columns col2-set" id="customer_login">

		<div class="u-column1 col-1">

	<?php endif; ?>

			<h2><?php esc_html_e( 'Login', 'woocommerce' ); ?></h2>

<?php endif; ?>

			<form class="woocommerce-form woocommerce-form-login login" method="post" novalidate>

				<?php do_action( 'woocommerce_login_form_start' ); ?>

				<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
					<label for="username"><?php esc_html_e( 'Username or email address', 'woocommerce' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span><span class="screen-reader-text"><?php esc_html_e( 'Required', 'woocommerce' ); ?></span></label>
					<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) && is_string( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" required aria-required="true" /><?php // @codingStandardsIgnoreLine ?>
				</p>
				<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
					<label for="password"><?php esc_html_e( 'Password', 'woocommerce' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span><span class="screen-reader-text"><?php esc_html_e( 'Required', 'woocommerce' ); ?></span></label>
					<input class="woocommerce-Input woocommerce-Input--text input-text" type="password" name="password" id="password" autocomplete="current-password" required aria-required="true" />
				</p>

				<?php do_action( 'woocommerce_login_form' ); ?>

				<p class="form-row">
					<label class="woocommerce-form__label woocommerce-form__label-for-checkbox woocommerce-form-login__rememberme">
						<input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" /> <span><?php esc_html_e( 'Remember me', 'woocommerce' ); ?></span>
					</label>
					<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
					<button type="submit" class="woocommerce-button button woocommerce-form-login__submit<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>" name="login" value="<?php esc_attr_e( 'Log in', 'woocommerce' ); ?>"><?php esc_html_e( 'Log in', 'woocommerce' ); ?></button>
				</p>
				<p class="woocommerce-LostPassword lost_password">
					<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Lost your password?', 'woocommerce' ); ?></a>
				</p>

				<?php do_action( 'woocommerce_login_form_end' ); ?>

			</form>

<?php if ( $use_v2_layout ) : ?>

			</div><!-- .bp-ma-v2__pane[login] -->

			<?php if ( $registration_is_on ) : ?>
			<div class="bp-ma-v2__pane" data-bp-ma-pane="register">
				<h2 class="bp-ma-v2__pane-title"><?php esc_html_e( 'Create your account', 'blocksy-child' ); ?></h2>
			<?php endif; ?>

<?php else : ?>

	<?php if ( $registration_is_on ) : ?>

		</div>

		<div class="u-column2 col-2">

			<h2><?php esc_html_e( 'Register', 'woocommerce' ); ?></h2>

	<?php endif; ?>

<?php endif; ?>

<?php if ( $registration_is_on ) : ?>

			<form method="post" class="woocommerce-form woocommerce-form-register register" <?php do_action( 'woocommerce_register_form_tag' ); ?> >

				<?php do_action( 'woocommerce_register_form_start' ); ?>

				<?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>

					<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
						<label for="reg_username"><?php esc_html_e( 'Username', 'woocommerce' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span><span class="screen-reader-text"><?php esc_html_e( 'Required', 'woocommerce' ); ?></span></label>
						<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="reg_username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" required aria-required="true" /><?php // @codingStandardsIgnoreLine ?>
					</p>

				<?php endif; ?>

				<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
					<label for="reg_email"><?php esc_html_e( 'Email address', 'woocommerce' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span><span class="screen-reader-text"><?php esc_html_e( 'Required', 'woocommerce' ); ?></span></label>
					<input type="email" class="woocommerce-Input woocommerce-Input--text input-text" name="email" id="reg_email" autocomplete="email" value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>" required aria-required="true" /><?php // @codingStandardsIgnoreLine ?>
				</p>

				<?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>

					<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
						<label for="reg_password"><?php esc_html_e( 'Password', 'woocommerce' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span><span class="screen-reader-text"><?php esc_html_e( 'Required', 'woocommerce' ); ?></span></label>
						<input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password" id="reg_password" autocomplete="new-password" required aria-required="true" />
					</p>

				<?php else : ?>

					<p><?php esc_html_e( 'A link to set a new password will be sent to your email address.', 'woocommerce' ); ?></p>

				<?php endif; ?>

				<?php do_action( 'woocommerce_register_form' ); ?>

				<p class="woocommerce-form-row form-row">
					<?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
					<button type="submit" class="woocommerce-Button woocommerce-button button<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?> woocommerce-form-register__submit" name="register" value="<?php esc_attr_e( 'Register', 'woocommerce' ); ?>"><?php esc_html_e( 'Register', 'woocommerce' ); ?></button>
				</p>

				<?php do_action( 'woocommerce_register_form_end' ); ?>

			</form>

<?php if ( $use_v2_layout ) : ?>

			</div><!-- .bp-ma-v2__pane[register] -->

<?php else : ?>

		</div>

	</div>

<?php endif; ?>

<?php endif; ?>

<?php if ( $use_v2_layout ) : ?>

		</div><!-- .bp-ma-v2__card -->

	</div><!-- .bp-ma-v2 -->

<?php endif; ?>

<?php do_action( 'woocommerce_after_customer_login_form' ); ?>
