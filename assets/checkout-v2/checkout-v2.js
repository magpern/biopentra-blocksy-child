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

	var STEP_ORDER = ['details', 'delivery', 'payment'];
	var currentStep = 'details';

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
			} else if (!$deliverySlot.find('.bp-checkout-v2__delivery-placeholder').length) {
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
	 * WooCommerce marks a required field's wrapping <p class="form-row"> with
	 * .validate-required — it never adds the native HTML5 `required`
	 * attribute to the input itself, so checkValidity() would silently pass
	 * everything. This checks the same convention WooCommerce's own
	 * checkout.js validates against, and reuses its own
	 * .woocommerce-invalid-required-field error styling. Hidden fields
	 * (e.g. the ship-to-different-address block, always CSS-hidden in v2)
	 * are excluded via :visible, so they can never block advancing.
	 */
	function isStepValid(stepName) {
		var valid = true;
		var $panel = $('.bp-checkout-v2__step-panel[data-step="' + stepName + '"]');
		var $firstInvalid = null;

		$panel.find('.form-row.validate-required:visible').each(function () {
			var $row = $(this);
			var $field = $row.find('input, select, textarea').first();
			var value = $field.length ? String($field.val() || '').trim() : '';

			$row.toggleClass('woocommerce-invalid woocommerce-invalid-required-field', '' === value);
			$row.toggleClass('woocommerce-validated', '' !== value);

			if ('' === value) {
				valid = false;
				if (!$firstInvalid) {
					$firstInvalid = $field;
				}
			}
		});

		if ($firstInvalid && $firstInvalid.length) {
			$firstInvalid.trigger('focus');
		}

		return valid;
	}

	function showStep(stepName) {
		currentStep = stepName;
		var idx = STEP_ORDER.indexOf(stepName);

		$('.bp-checkout-v2__step-panel').each(function () {
			var $panel = $(this);
			var isActive = $panel.data('step') === stepName;
			$panel.prop('hidden', !isActive).toggleClass('is-active', isActive);
		});

		$('.bp-checkout-v2__step-tab').each(function () {
			var $tab = $(this);
			var tabIdx = STEP_ORDER.indexOf($tab.data('step'));
			$tab.toggleClass('is-active', tabIdx === idx);
			$tab.toggleClass('is-done', tabIdx < idx);
		});

		var $wizard = $('.bp-checkout-v2__steps');
		if ($wizard.length) {
			$('html, body').animate({ scrollTop: Math.max(0, $wizard.offset().top - 90) }, 200);
		}
	}

	function initSteps() {
		var $wizard = $('.bp-checkout-v2__steps');
		if (!$wizard.length) {
			return;
		}

		relocateDeliveryAndPayment();
		$body.on('updated_checkout', relocateDeliveryAndPayment);

		$(document).on('click', '.bp-checkout-v2__step-next', function () {
			if (!isStepValid(currentStep)) {
				return;
			}
			var next = $(this).data('next');
			if ('delivery' === next) {
				$body.trigger('update_checkout');
			}
			showStep(next);
		});

		$(document).on('click', '.bp-checkout-v2__step-back', function () {
			showStep($(this).data('back'));
		});

		// Tabs only ever jump backward to an already-completed step —
		// forward navigation always goes through Continue, so validation
		// can't be skipped by clicking ahead.
		$(document).on('click', '.bp-checkout-v2__step-tab', function () {
			var targetIdx = STEP_ORDER.indexOf($(this).data('step'));
			if (targetIdx < STEP_ORDER.indexOf(currentStep)) {
				showStep(STEP_ORDER[targetIdx]);
			}
		});

		showStep('details');
	}

	function init() {
		if (!$body.hasClass('bp-checkout-v2')) {
			return;
		}

		setStickyOffset();
		markSelectedPayment();
		initSteps();

		$(document).on('change', '#payment input[type="radio"]', markSelectedPayment);

		// Re-apply after WooCommerce checkout AJAX updates.
		$body.on('updated_checkout payment_method_selected', function () {
			markSelectedPayment();
		});

		$(window).on('resize', setStickyOffset);
	}

	$(init);
})(jQuery);
