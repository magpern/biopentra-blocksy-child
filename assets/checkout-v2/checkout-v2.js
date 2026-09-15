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
	 * directly under the coupon-code row in the order review table.
	 *
	 * Unlike the earlier #payment-sibling placement, this DOES need to
	 * re-run on `updated_checkout`: the coupon row lives inside
	 * `.woocommerce-checkout-review-order-table`, which WooCommerce
	 * replaces wholesale (replaceWith()) on every AJAX update — any
	 * descendant we'd inserted goes with it. We keep a persistent
	 * reference to the panel (captured once, since after the table's
	 * first replacement the panel node is detached and no longer
	 * reachable via a fresh `$('.mp-cp-gift-card-checkout')` document
	 * query) and re-append that same node — preserving any in-progress
	 * form input — into the freshly-rendered table each time.
	 */
	var $giftCardPanel = null;

	function relocateGiftCardPanel() {
		if (!$giftCardPanel || !$giftCardPanel.length) {
			$giftCardPanel = $('.mp-cp-gift-card-checkout').first();
		}

		if (!$giftCardPanel.length) {
			return;
		}

		var $couponRow = $('.bp-checkout-v2__coupon-row').first();

		if (!$couponRow.length) {
			return;
		}

		var $existingRow = $giftCardPanel.closest('tr.bp-checkout-v2__giftcard-row');

		if ($existingRow.length && $existingRow.prev()[0] === $couponRow[0]) {
			return;
		}

		var $row = $('<tr class="bp-checkout-v2__giftcard-row"><td colspan="2"></td></tr>');
		$row.find('td').append($giftCardPanel);
		$couponRow.after($row);
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
			relocateGiftCardPanel();
		});

		$(window).on('resize', setStickyOffset);
	}

	$(init);
})(jQuery);
