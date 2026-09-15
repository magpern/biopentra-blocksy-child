<?php
/**
 * My Addresses — card layout for My Account v2.
 *
 * When v2 is inactive, this reproduces WooCommerce core's own my-address.php
 * markup verbatim (WooCommerce core 9.3.0) rather than delegating to
 * wc_get_template( 'myaccount/my-address.php' ) — a theme override of a
 * template always wins over any $default_path argument in
 * wc_locate_template(), so calling that from inside this very file would
 * just resolve back to this file again, recursing until PHP's memory
 * limit is hit.
 *
 * The v2 layout shows one "Delivery details" card (billing address — this
 * store already forces shipping = billing at checkout, per Checkout v2's
 * ship_to_billing_only filter, so a separate shipping card would just
 * repeat the same data) plus a read-only "Email address" card, since email
 * is account data, not address data, but customers look for it here too.
 *
 * @package Blocksy_Child
 */

defined( 'ABSPATH' ) || exit;

$use_v2_layout = class_exists( 'Blocksy_Child_My_Account_V2' ) && Blocksy_Child_My_Account_V2::is_layout_active();

if ( ! $use_v2_layout ) :
	$customer_id = get_current_user_id();

	if ( ! wc_ship_to_billing_address_only() && wc_shipping_enabled() ) {
		$get_addresses = apply_filters(
			'woocommerce_my_account_get_addresses',
			array(
				'billing'  => __( 'Billing address', 'woocommerce' ),
				'shipping' => __( 'Shipping address', 'woocommerce' ),
			),
			$customer_id
		);
	} else {
		$get_addresses = apply_filters(
			'woocommerce_my_account_get_addresses',
			array( 'billing' => __( 'Billing address', 'woocommerce' ) ),
			$customer_id
		);
	}

	$oldcol = 1;
	$col    = 1;
	?>

	<p>
		<?php echo apply_filters( 'woocommerce_my_account_my_address_description', esc_html__( 'The following addresses will be used on the checkout page by default.', 'woocommerce' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</p>

	<?php if ( ! wc_ship_to_billing_address_only() && wc_shipping_enabled() ) : ?>
		<div class="u-columns woocommerce-Addresses col2-set addresses">
	<?php endif; ?>

	<?php foreach ( $get_addresses as $name => $address_title ) : ?>
		<?php
			$address = wc_get_account_formatted_address( $name );
			$col     = $col * -1;
			$oldcol  = $oldcol * -1;
		?>

		<div class="u-column<?php echo $col < 0 ? 1 : 2; ?> col-<?php echo $oldcol < 0 ? 1 : 2; ?> woocommerce-Address">
			<header class="woocommerce-Address-title title">
				<h2><?php echo esc_html( $address_title ); ?></h2>
				<a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address', $name ) ); ?>" class="edit">
					<?php
						printf(
							/* translators: %s: Address title */
							$address ? esc_html__( 'Edit %s', 'woocommerce' ) : esc_html__( 'Add %s', 'woocommerce' ),
							esc_html( $address_title )
						);
					?>
				</a>
			</header>
			<address>
				<?php
					echo $address ? wp_kses_post( $address ) : esc_html_e( 'You have not set up this type of address yet.', 'woocommerce' );
					do_action( 'woocommerce_my_account_after_my_address', $name );
				?>
			</address>
		</div>

	<?php endforeach; ?>

	<?php if ( ! wc_ship_to_billing_address_only() && wc_shipping_enabled() ) : ?>
		</div>
	<?php endif; ?>

	<?php
	return;
endif;

$customer = new WC_Customer( get_current_user_id() );

$full_name = trim( $customer->get_billing_first_name() . ' ' . $customer->get_billing_last_name() );
$street    = trim( $customer->get_billing_address_1() . ( $customer->get_billing_address_2() ? ', ' . $customer->get_billing_address_2() : '' ) );

$countries       = WC()->countries->get_countries();
$country_code    = $customer->get_billing_country();
$country_name    = $country_code && isset( $countries[ $country_code ] ) ? $countries[ $country_code ] : $country_code;
$states          = $country_code ? WC()->countries->get_states( $country_code ) : array();
$state_code      = $customer->get_billing_state();
$state_name      = $state_code && isset( $states[ $state_code ] ) ? $states[ $state_code ] : $state_code;

$rows = array(
	__( 'Full name', 'blocksy-child' )    => $full_name,
	__( 'Phone number', 'blocksy-child' ) => $customer->get_billing_phone(),
	__( 'Street address', 'blocksy-child' ) => $street,
	__( 'City', 'blocksy-child' )         => $customer->get_billing_city(),
	__( 'County', 'blocksy-child' )       => $state_name,
	__( 'Postcode', 'blocksy-child' )     => $customer->get_billing_postcode(),
	__( 'Country', 'blocksy-child' )      => $country_name,
);

$has_address = '' !== trim( implode( '', $rows ) );

?>

<div class="bp-ma-v2__notice">
	<?php echo Blocksy_Child_My_Account_V2::warning_icon(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	<p><strong><?php esc_html_e( 'Please check these are right.', 'blocksy-child' ); ?></strong> <?php esc_html_e( 'They fill your checkout and go on the parcel label, so a wrong postcode or a missing house number is the commonest reason an order is delayed or comes back to us.', 'blocksy-child' ); ?></p>
</div>

<div class="bp-ma-v2__panel-card">
	<div class="bp-ma-v2__panel-card-header">
		<h2>
			<?php echo Blocksy_Child_My_Account_V2::nav_icon( 'edit-address' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php esc_html_e( 'Delivery details', 'blocksy-child' ); ?>
		</h2>
		<a class="bp-ma-v2__edit-link" href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address', 'billing' ) ); ?>">
			<?php echo $has_address ? esc_html__( 'Edit', 'blocksy-child' ) : esc_html__( 'Add', 'blocksy-child' ); ?>
		</a>
	</div>
	<?php if ( $has_address ) : ?>
		<table class="bp-ma-v2__rows">
			<?php foreach ( $rows as $label => $value ) : ?>
				<?php if ( '' === trim( (string) $value ) ) { continue; } ?>
				<tr>
					<th><?php echo esc_html( strtoupper( $label ) ); ?></th>
					<td><?php echo esc_html( $value ); ?></td>
				</tr>
			<?php endforeach; ?>
		</table>
	<?php else : ?>
		<p class="bp-ma-v2__empty"><?php esc_html_e( 'You have not added a delivery address yet.', 'blocksy-child' ); ?></p>
	<?php endif; ?>
</div>

<div class="bp-ma-v2__panel-card">
	<div class="bp-ma-v2__panel-card-header">
		<h2><?php esc_html_e( 'Email address', 'blocksy-child' ); ?></h2>
	</div>
	<p class="bp-ma-v2__email"><?php echo esc_html( wp_get_current_user()->user_email ); ?></p>
	<p class="bp-ma-v2__hint">
		<?php
		printf(
			/* translators: %s: contact page URL */
			wp_kses(
				__( 'Your order history is matched to this address, so keep ordering with it. To change it, <a href="%s">contact support</a> and your past orders will move with you.', 'blocksy-child' ),
				array( 'a' => array( 'href' => array() ) )
			),
			esc_url( home_url( '/contact/' ) )
		);
		?>
	</p>
</div>
