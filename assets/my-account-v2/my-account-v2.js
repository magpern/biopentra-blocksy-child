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
