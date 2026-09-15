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
		var top = bpCheckoutV2.stickyOffset || 16;
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

	/**
	 * Moves the gift-card/store-credit accordion (rendered by
	 * mp-commerce-promotions on woocommerce_before_checkout_form, so it
	 * starts out above Billing details, before <form> even opens) to sit
	 * right after the Place order button instead. A one-time move, not
	 * re-run on `updated_checkout`: the accordion is a plain PHP-rendered,
	 * full-page-POST element with no AJAX refresh of its own, and — unlike
	 * the earlier (reverted) step-wizard work — it's inserted as a SIBLING
	 * after #payment, never as #payment's child, specifically so
	 * WooCommerce's own `.woocommerce-checkout-payment` fragment replace
	 * (on every update_checkout) can't carry it away: replaceWith() only
	 * swaps the matched element itself, never its siblings.
	 */
	function relocateGiftCardPanel() {
		var $panel = $('.mp-cp-gift-card-checkout').first();
		var $payment = $('#payment').first();

		if ($panel.length && $payment.length) {
			$payment.after($panel);
		}
	}

	function init() {
		if (!$body.hasClass('bp-checkout-v2')) {
			return;
		}

		setStickyOffset();
		markSelectedPayment();
		relocateGiftCardPanel();

		$(document).on('change', '#payment input[type="radio"]', markSelectedPayment);

		// Re-apply after WooCommerce checkout AJAX updates.
		$body.on('updated_checkout payment_method_selected', function () {
			markSelectedPayment();
		});

		$(window).on('resize', setStickyOffset);
	}

	$(init);
})(jQuery);
