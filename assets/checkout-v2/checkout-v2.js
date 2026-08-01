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

	function init() {
		if (!$body.hasClass('bp-checkout-v2')) {
			return;
		}

		setStickyOffset();
		markSelectedPayment();

		$(document).on('change', '#payment input[type="radio"]', markSelectedPayment);

		// Re-apply after WooCommerce checkout AJAX updates.
		$body.on('updated_checkout payment_method_selected', function () {
			markSelectedPayment();
		});

		$(window).on('resize', setStickyOffset);
	}

	$(init);
})(jQuery);
