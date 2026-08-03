/**
 * Milestone D2B — sticky purchase bar sync (mobile).
 */
(function () {
	'use strict';

	var MQ = window.matchMedia('(max-width: 767px)');
	var i18n = (window.bpPdpSticky && window.bpPdpSticky.i18n) || {};

	function bar() {
		return document.querySelector('[data-bp-sticky-bar]');
	}

	function form() {
		return document.querySelector('form.variations_form.cart, form.cart');
	}

	function nativeAtc() {
		return document.querySelector(
			'.ct-product-add-to-cart .single_add_to_cart_button, form.cart .single_add_to_cart_button'
		);
	}

	function isDesktop() {
		return !MQ.matches;
	}

	function setVisible(show) {
		var el = bar();
		if (!el) {
			return;
		}
		if (isDesktop() || !show) {
			el.hidden = true;
			document.body.classList.remove('bp-pdp-sticky-active');
			return;
		}
		el.hidden = false;
		document.body.classList.add('bp-pdp-sticky-active');
	}

	function syncPrice() {
		var el = bar();
		if (!el) {
			return;
		}
		var target = el.querySelector('[data-bp-sticky-price]');
		if (!target) {
			return;
		}

		var variationPrice = document.querySelector(
			'.summary .woocommerce-variation-price .price, .summary .single_variation .price'
		);
		var basePrice = document.querySelector('.summary > p.price, .summary p.price');
		var src = variationPrice && variationPrice.getClientRects().length ? variationPrice : basePrice;
		target.innerHTML = src ? src.innerHTML : '';
	}

	function syncMeta() {
		var el = bar();
		if (!el) {
			return;
		}
		var target = el.querySelector('[data-bp-sticky-meta]');
		if (!target) {
			return;
		}

		var parts = [];
		document.querySelectorAll('form.variations_form select[name^="attribute_"]').forEach(function (sel) {
			if (sel.value) {
				var opt = sel.options[sel.selectedIndex];
				parts.push(opt ? opt.text : sel.value);
			}
		});

		var stock = document.querySelector(
			'.summary .woocommerce-variation-availability .bp-stock-status, .summary .stock .bp-stock-status, .summary .stock'
		);
		if (stock && stock.textContent.trim()) {
			parts.push(stock.textContent.trim());
		}

		target.textContent = parts.join(' · ');
	}

	function syncAtc() {
		var el = bar();
		if (!el) {
			return;
		}
		var btn = el.querySelector('[data-bp-sticky-atc]');
		var native = nativeAtc();
		if (!btn || !native) {
			return;
		}

		var disabled = native.disabled || native.classList.contains('disabled');
		btn.disabled = disabled;

		var label = (native.textContent || '').trim() || i18n.addToCart || 'Add to cart';
		var vidInput = document.querySelector('form.variations_form input.variation_id');
		if (disabled && vidInput && (!vidInput.value || vidInput.value === '0')) {
			label = i18n.selectOpts || 'Select options';
		}
		btn.textContent = label;
	}

	function syncAll() {
		syncPrice();
		syncMeta();
		syncAtc();
	}

	function onStickyAtc(e) {
		e.preventDefault();
		var native = nativeAtc();
		if (!native || native.disabled || native.classList.contains('disabled')) {
			return;
		}
		native.click();
	}

	function bindForm($form) {
		$form.on(
			'found_variation reset_data woocommerce_variation_has_changed show_variation hide_variation check_variations',
			syncAll
		);
		$form.find('input.qty').on('change input', syncAll);
	}

	function observeNativeAtc() {
		var target = document.querySelector('.ct-product-add-to-cart') || form();
		if (!target || typeof IntersectionObserver === 'undefined') {
			setVisible(!isDesktop());
			return;
		}

		var io = new IntersectionObserver(
			function (entries) {
				entries.forEach(function (entry) {
					setVisible(!entry.isIntersecting);
				});
			},
			{ root: null, threshold: 0.2, rootMargin: '0px' }
		);
		io.observe(target);
	}

	function init() {
		if (!bar() || !document.body.classList.contains('single-product')) {
			return;
		}

		var btn = bar().querySelector('[data-bp-sticky-atc]');
		if (btn) {
			btn.addEventListener('click', onStickyAtc);
		}

		if (window.jQuery) {
			var $form = window.jQuery('form.variations_form');
			if (!$form.length) {
				$form = window.jQuery('form.cart');
			}
			if ($form.length) {
				bindForm($form);
			}
		}

		syncAll();
		if (isDesktop()) {
			setVisible(false);
		} else {
			observeNativeAtc();
		}

		if (MQ.addEventListener) {
			MQ.addEventListener('change', function () {
				syncAll();
				if (isDesktop()) {
					setVisible(false);
				} else {
					observeNativeAtc();
				}
			});
		}
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
