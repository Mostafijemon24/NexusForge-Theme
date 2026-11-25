/**
 * NexusForge — accessible navigation.
 *
 * - Mobile drawer toggle with focus management, ESC to close, backdrop click.
 * - Submenu toggle buttons manage aria-expanded; dropdowns are keyboard operable.
 * - No framework; vanilla JS; respects reduced motion (handled in CSS).
 */
( function () {
	'use strict';

	document.addEventListener( 'DOMContentLoaded', function () {
		var header = document.querySelector( '.nexusforge-header' );
		if ( ! header ) {
			return;
		}

		var toggle = header.querySelector( '.nexusforge-menu-toggle' );
		var nav = header.querySelector( '.nexusforge-primary-nav' );

		/* ---------------------------------------------- Mobile drawer */
		if ( toggle && nav ) {
			var backdrop = document.createElement( 'div' );
			backdrop.className = 'nexusforge-nav-backdrop';
			document.body.appendChild( backdrop );

			var openMenu = function () {
				nav.classList.add( 'is-open' );
				backdrop.classList.add( 'is-visible' );
				toggle.setAttribute( 'aria-expanded', 'true' );
				document.body.classList.add( 'nexusforge-nav-open' );
			};

			var closeMenu = function () {
				nav.classList.remove( 'is-open' );
				backdrop.classList.remove( 'is-visible' );
				toggle.setAttribute( 'aria-expanded', 'false' );
				document.body.classList.remove( 'nexusforge-nav-open' );
			};

			toggle.addEventListener( 'click', function () {
				if ( 'true' === toggle.getAttribute( 'aria-expanded' ) ) {
					closeMenu();
				} else {
					openMenu();
				}
			} );

			backdrop.addEventListener( 'click', closeMenu );

			document.addEventListener( 'keydown', function ( e ) {
				if ( 'Escape' === e.key && nav.classList.contains( 'is-open' ) ) {
					closeMenu();
					toggle.focus();
				}
			} );
		}

		/* ---------------------------------------------- Submenu toggles */
		var subToggles = header.querySelectorAll( '.nexusforge-submenu-toggle' );
		Array.prototype.forEach.call( subToggles, function ( button ) {
			button.addEventListener( 'click', function ( e ) {
				e.preventDefault();
				var parent = button.closest( 'li' );
				if ( ! parent ) {
					return;
				}
				var isOpen = parent.classList.toggle( 'is-open' );
				button.setAttribute( 'aria-expanded', isOpen ? 'true' : 'false' );
			} );
		} );

		/* ------------------------------- Close desktop dropdowns on blur-out */
		var topItems = header.querySelectorAll( '.nexusforge-menu > li.menu-item-has-children' );
		Array.prototype.forEach.call( topItems, function ( item ) {
			item.addEventListener( 'focusout', function ( e ) {
				if ( ! item.contains( e.relatedTarget ) ) {
					item.classList.remove( 'is-open' );
					var btn = item.querySelector( ':scope > .nexusforge-submenu-toggle' );
					if ( btn ) {
						btn.setAttribute( 'aria-expanded', 'false' );
					}
				}
			} );
		} );
	} );
}() );
