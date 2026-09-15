/**
 * Checkout v2 — lightweight enhancements only.
 * Relies on WooCommerce checkout.js for AJAX, validation, and fragments.
 */
(function ($) {
	'use strict';

	if (typeof bpCheckoutV2 === 'undefined') {
		return;
	}

	var $body = $(document.body);

	function setStickyOffset() {
		// wp_localize_script stringifies every value, so bpCheckoutV2.stickyOffset
		// arrives as e.g. "46", not 46 — without parseInt, `top += 32` below does
		// string concatenation ("46" + 32 -> "4632") instead of addition, only
		// when the admin bar is present (i.e. only for logged-in users), which is
		// exactly why this only broke logged-in checkout: the resulting
		// `--bp-co-sticky-top: 4632px` made the sticky order-summary card's
		// `max-height: calc(100vh - var(--bp-co-sticky-top) - 1.5rem)` resolve
		// to 0 (clamped), collapsing it to just its heading, cart contents and
		// all.
		var top = parseInt(bpCheckoutV2.stickyOffset, 10) || 16;
		if ($body.hasClass('admin-bar')) {
			top += 32;
		}
		document.documentElement.style.setProperty('--bp-co-sticky-top', top + 'px');
	}

	function markSelectedPayment() {
		$('#payment .wc_payment_method').each(function () {
			var $li = $(this);
			var checked = $li.find('input[type="radio"]').is(':checked');
			$li.toggleClass('payment_method_selected', checked);
		});
	}

	// Set once a real update_checkout response has been processed at least
	// once, so relocateDeliveryAndPayment can tell "WC hasn't calculated
	// shipping yet" (keep the initial placeholder) apart from "WC has
	// calculated, and this order genuinely needs no shipping" (show the
	// no-delivery-needed message instead) — both cases have no tr.shipping
	// to relocate, so that alone can't distinguish them.
	var hasCalculatedOnce = false;

	/**
	 * Moves the shipping-method row and #payment out of the aside's order-
	 * review (where WooCommerce always renders them, unchanged) into the
	 * Delivery/Payment step slots. Physically relocates the live nodes —
	 * never clones, never re-renders — so every listener WooCommerce (or a
	 * payment-gateway plugin) already attached keeps working. Re-run after
	 * every `updated_checkout`, since that AJAX refresh replaces the whole
	 * review-order table (and #payment) back into the aside each time.
	 */
	function relocateDeliveryAndPayment() {
		var $deliverySlot = $('#bp-checkout-v2-delivery-slot').find('tbody');
		var $shippingRow = $('.bp-checkout-v2__order-review tr.shipping');

		if ($deliverySlot.length) {
			if ($shippingRow.length) {
				$deliverySlot.empty().append($shippingRow);
			} else if (hasCalculatedOnce) {
				var noDeliveryText = (bpCheckoutV2.i18n && bpCheckoutV2.i18n.noDeliveryNeeded) || '';
				$deliverySlot.empty().append(
					$('<tr/>', { 'class': 'bp-checkout-v2__delivery-empty' }).append(
						$('<td/>').text(noDeliveryText)
					)
				);
			}
		}

		var $paymentSlot = $('#bp-checkout-v2-payment-slot');
		var $payment = $('.bp-checkout-v2__order-review #payment');

		if ($paymentSlot.length && $payment.length) {
			$paymentSlot.empty().append($payment);
		}
	}

	/**
	 * The three sections (Details/Delivery/Payment) are always visible, in
	 * document order — no click-to-advance gate, so nothing here can ever
	 * block a customer from reaching a later section. The pills are plain
	 * anchor links (native browser scroll); this only tracks which
	 * section is currently in view to highlight the matching pill.
	 */
	function initSectionSpy() {
		var $tabs = $('.bp-checkout-v2__steps');
		var sections = document.querySelectorAll('.bp-checkout-v2__section');
		if (!$tabs.length || !sections.length || typeof IntersectionObserver === 'undefined') {
			return;
		}

		var observer = new IntersectionObserver(
			function (entries) {
				entries.forEach(function (entry) {
					if (!entry.isIntersecting) {
						return;
					}
					var step = entry.target.getAttribute('data-step');
					$tabs.find('.bp-checkout-v2__step-tab').each(function () {
						$(this).toggleClass('is-active', $(this).data('step') === step);
					});
				});
			},
			{ rootMargin: '-40% 0px -50% 0px' }
		);

		sections.forEach(function (section) {
			observer.observe(section);
		});
	}

	function init() {
		if (!$body.hasClass('bp-checkout-v2')) {
			return;
		}

		setStickyOffset();
		markSelectedPayment();
		relocateDeliveryAndPayment();
		initSectionSpy();
		document.documentElement.style.scrollBehavior = 'smooth';

		$body.on('updated_checkout', function () {
			hasCalculatedOnce = true;
			relocateDeliveryAndPayment();
		});
		$(document).on('change', '#payment input[type="radio"]', markSelectedPayment);

		// Re-apply after WooCommerce checkout AJAX updates.
		$body.on('updated_checkout payment_method_selected', function () {
			markSelectedPayment();
		});

		$(window).on('resize', setStickyOffset);
	}

	$(init);
})(jQuery);
