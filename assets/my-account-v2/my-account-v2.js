/**
 * BioPentra My Account v2 — sign-in / create-account tab switching.
 * Progressive enhancement only: both panes have no JS-dependent markup
 * beyond the is-active toggle, and CSS already marks the login pane active
 * by default, so a JS failure just leaves the register pane one click away
 * via a page in-place toggle that never fires.
 */
( function () {
	'use strict';

	var root = document.querySelector( '.bp-ma-v2' );
	if ( ! root ) {
		return;
	}

	var tabs = root.querySelectorAll( '.bp-ma-v2__tab' );
	var panes = root.querySelectorAll( '.bp-ma-v2__pane' );

	if ( ! tabs.length || ! panes.length ) {
		return;
	}

	tabs.forEach( function ( tab ) {
		tab.addEventListener( 'click', function () {
			var target = tab.getAttribute( 'data-bp-ma-tab' );

			tabs.forEach( function ( otherTab ) {
				var isActive = otherTab === tab;
				otherTab.classList.toggle( 'is-active', isActive );
				otherTab.setAttribute( 'aria-selected', isActive ? 'true' : 'false' );
			} );

			panes.forEach( function ( pane ) {
				pane.classList.toggle( 'is-active', pane.getAttribute( 'data-bp-ma-pane' ) === target );
			} );
		} );
	} );
} )();

/**
 * BioPentra My Account v2 — address-change confirm modal. Intercepts the
 * edit-address form's submit to show a review step; "Yes, save these"
 * submits the same real form (HTMLFormElement.submit() does not re-fire
 * the submit event, so no loop-prevention flag is needed there — only the
 * initial listener needs one, for the (unused) case something else also
 * calls form.submit() first).
 */
( function () {
	'use strict';

	var form = document.querySelector( '.bp-ma-v2__address-form' );
	var modal = document.getElementById( 'bp-ma-v2-address-confirm' );
	if ( ! form || ! modal ) {
		return;
	}

	var rowsTable = modal.querySelector( '.bp-ma-v2__modal-rows' );
	var backBtn = modal.querySelector( '.bp-ma-v2__modal-back' );
	var confirmBtn = modal.querySelector( '.bp-ma-v2__modal-confirm' );

	function fieldRows() {
		var rows = [];
		form.querySelectorAll( '.form-row' ).forEach( function ( row ) {
			var labelEl = row.querySelector( 'label' );
			var input = row.querySelector( 'input, select, textarea' );
			if ( ! labelEl || ! input || 'hidden' === input.type ) {
				return;
			}

			var label = labelEl.textContent.replace( /\(.*?\)/g, '' ).replace( /\*/g, '' ).trim();
			var value;
			if ( 'SELECT' === input.tagName && input.options[ input.selectedIndex ] ) {
				value = input.options[ input.selectedIndex ].text;
			} else {
				value = input.value;
			}
			value = ( value || '' ).trim();
			if ( ! value ) {
				return;
			}

			rows.push( { label: label, value: value } );
		} );
		return rows;
	}

	function openModal() {
		rowsTable.innerHTML = '';
		fieldRows().forEach( function ( row ) {
			var tr = document.createElement( 'tr' );
			var th = document.createElement( 'th' );
			th.textContent = row.label.toUpperCase();
			var td = document.createElement( 'td' );
			td.textContent = row.value;
			tr.appendChild( th );
			tr.appendChild( td );
			rowsTable.appendChild( tr );
		} );
		modal.hidden = false;
		document.body.classList.add( 'bp-ma-v2-modal-open' );
	}

	function closeModal() {
		modal.hidden = true;
		document.body.classList.remove( 'bp-ma-v2-modal-open' );
	}

	form.addEventListener( 'submit', function ( event ) {
		if ( '1' === form.dataset.bpConfirmed ) {
			return;
		}
		event.preventDefault();
		openModal();
	} );

	backBtn.addEventListener( 'click', closeModal );

	confirmBtn.addEventListener( 'click', function () {
		form.dataset.bpConfirmed = '1';
		closeModal();
		form.submit();
	} );

	modal.addEventListener( 'click', function ( event ) {
		if ( event.target === modal ) {
			closeModal();
		}
	} );

	document.addEventListener( 'keydown', function ( event ) {
		if ( 'Escape' === event.key && ! modal.hidden ) {
			closeModal();
		}
	} );
} )();
