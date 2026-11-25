/**
 * NexusForge — accessible navigation.
 *
 * Mobile menu (modal drawer):
 * - Opening moves focus to the first focusable element inside the drawer.
 * - Tab / Shift+Tab are trapped inside the drawer (focus wraps around).
 * - Escape, the Close button and a backdrop click all close the drawer.
 * - Closing returns focus to the button that opened the drawer.
 * - While open, the rest of the page is made inert (not focusable/clickable).
 *
 * Submenus:
 * - Each parent item has a real <button> that toggles aria-expanded.
 * - On desktop, Escape closes an open dropdown and returns focus to its parent.
 *
 * No framework; vanilla JS; reduced motion is handled in CSS.
 */
( function () {
	'use strict';

	var FOCUSABLE = 'a[href], area[href], button:not([disabled]), input:not([disabled]):not([type="hidden"]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])';

	/**
	 * Whether an element is actually rendered and visible.
	 *
	 * @param {Element} el Element to test.
	 * @return {boolean} True when visible.
	 */
	function isVisible( el ) {
		return el.getClientRects().length > 0 && 'hidden' !== window.getComputedStyle( el ).visibility;
	}

	/**
	 * Visible, focusable descendants of a container, in DOM order.
	 *
	 * @param {Element} container Container element.
	 * @return {Element[]} Focusable elements.
	 */
	function getFocusable( container ) {
		return Array.prototype.filter.call( container.querySelectorAll( FOCUSABLE ), isVisible );
	}

	/**
	 * Whether an element currently shows keyboard focus (:focus-visible).
	 *
	 * @param {Element} el Element to test.
	 * @return {boolean} True for keyboard focus (or when the selector is unsupported).
	 */
	function hasKeyboardFocus( el ) {
		try {
			return el.matches( ':focus-visible' );
		} catch ( err ) {
			return true;
		}
	}

	function init() {
		var header = document.querySelector( '.nexusforge-header' );
		if ( ! header ) {
			return;
		}

		var toggle = header.querySelector( '.nexusforge-menu-toggle' );
		var drawer = header.querySelector( '.nexusforge-primary-nav' );
		var closeButton = drawer ? drawer.querySelector( '.nexusforge-menu-close' ) : null;
		var mobileQuery = window.matchMedia( '(max-width: 900px)' );
		var isMenuOpen = false;
		var closeMenu = function () {};

		/* ------------------------------------------------ Mobile drawer */
		if ( toggle && drawer ) {
			var inertElements = [];

			// The backdrop lives inside the header so it shares the drawer's
			// stacking context and always sits *behind* the drawer.
			var backdrop = document.createElement( 'div' );
			backdrop.className = 'nexusforge-nav-backdrop';
			header.appendChild( backdrop );

			/**
			 * Make everything outside the drawer inert (or restore it).
			 *
			 * @param {boolean} enable True to set inert, false to restore.
			 */
			var setBackgroundInert = function ( enable ) {
				if ( ! enable ) {
					inertElements.forEach( function ( el ) {
						el.inert = false;
						el.removeAttribute( 'inert' );
					} );
					inertElements = [];
					return;
				}

				var node = drawer;
				while ( node && node !== document.body ) {
					var parent = node.parentElement;
					if ( ! parent ) {
						break;
					}
					Array.prototype.forEach.call( parent.children, function ( sibling ) {
						if (
							sibling === node ||
							sibling === backdrop ||
							'wpadminbar' === sibling.id ||
							sibling.inert ||
							/^(SCRIPT|STYLE|LINK|TEMPLATE)$/.test( sibling.tagName )
						) {
							return;
						}
						sibling.inert = true;
						sibling.setAttribute( 'inert', '' );
						inertElements.push( sibling );
					} );
					node = parent;
				}
			};

			var openMenu = function () {
				if ( isMenuOpen ) {
					return;
				}
				isMenuOpen = true;

				drawer.classList.add( 'is-open' );
				drawer.setAttribute( 'role', 'dialog' );
				drawer.setAttribute( 'aria-modal', 'true' );
				drawer.setAttribute( 'aria-label', drawer.getAttribute( 'data-nexusforge-dialog-label' ) || '' );
				backdrop.classList.add( 'is-visible' );
				toggle.setAttribute( 'aria-expanded', 'true' );
				document.body.classList.add( 'nexusforge-nav-open' );
				setBackgroundInert( true );

				// Move focus to the first focusable element inside the drawer.
				var focusable = getFocusable( drawer );
				if ( focusable.length ) {
					focusable[ 0 ].focus();
				}
			};

			/**
			 * Close the drawer.
			 *
			 * @param {boolean} returnFocus Whether to send focus back to the toggle.
			 */
			closeMenu = function ( returnFocus ) {
				if ( ! isMenuOpen ) {
					return;
				}
				isMenuOpen = false;

				drawer.classList.remove( 'is-open' );
				drawer.removeAttribute( 'role' );
				drawer.removeAttribute( 'aria-modal' );
				drawer.removeAttribute( 'aria-label' );
				backdrop.classList.remove( 'is-visible' );
				toggle.setAttribute( 'aria-expanded', 'false' );
				document.body.classList.remove( 'nexusforge-nav-open' );
				setBackgroundInert( false );

				if ( false !== returnFocus ) {
					toggle.focus();
				}
			};

			toggle.addEventListener( 'click', function () {
				if ( isMenuOpen ) {
					closeMenu( true );
				} else {
					openMenu();
				}
			} );

			if ( closeButton ) {
				closeButton.addEventListener( 'click', function () {
					closeMenu( true );
				} );
			}

			backdrop.addEventListener( 'click', function () {
				closeMenu( true );
			} );

			// Following a link inside the drawer (e.g. an in-page anchor) closes it.
			drawer.addEventListener( 'click', function ( e ) {
				if ( isMenuOpen && e.target.closest && e.target.closest( 'a[href]' ) ) {
					closeMenu( false );
				}
			} );

			// Escape closes; Tab / Shift+Tab stay trapped inside the drawer.
			document.addEventListener( 'keydown', function ( e ) {
				if ( ! isMenuOpen ) {
					return;
				}

				if ( 'Escape' === e.key || 'Esc' === e.key ) {
					e.preventDefault();
					closeMenu( true );
					return;
				}

				if ( 'Tab' !== e.key ) {
					return;
				}

				var focusable = getFocusable( drawer );
				if ( ! focusable.length ) {
					e.preventDefault();
					return;
				}

				var first = focusable[ 0 ];
				var last = focusable[ focusable.length - 1 ];
				var active = document.activeElement;

				if ( ! drawer.contains( active ) ) {
					e.preventDefault();
					( e.shiftKey ? last : first ).focus();
				} else if ( e.shiftKey && active === first ) {
					e.preventDefault();
					last.focus();
				} else if ( ! e.shiftKey && active === last ) {
					e.preventDefault();
					first.focus();
				}
			} );

			// Safety net: if focus ever lands outside the open drawer, pull it back.
			document.addEventListener( 'focusin', function ( e ) {
				if ( ! isMenuOpen || drawer.contains( e.target ) ) {
					return;
				}
				var adminBar = document.getElementById( 'wpadminbar' );
				if ( adminBar && adminBar.contains( e.target ) ) {
					return;
				}
				var focusable = getFocusable( drawer );
				if ( focusable.length ) {
					focusable[ 0 ].focus();
				}
			} );

			// Leaving the mobile layout (resize/rotate) closes the drawer.
			var onQueryChange = function () {
				if ( ! mobileQuery.matches ) {
					closeMenu( false );
				}
			};
			if ( mobileQuery.addEventListener ) {
				mobileQuery.addEventListener( 'change', onQueryChange );
			} else if ( mobileQuery.addListener ) {
				mobileQuery.addListener( onQueryChange );
			}
		}

		/* ---------------------------------------------- Submenu toggles */
		var setSubmenu = function ( item, open ) {
			item.classList.toggle( 'is-open', open );
			var button = null;
			Array.prototype.forEach.call( item.children, function ( child ) {
				if ( child.classList.contains( 'nexusforge-submenu-toggle' ) ) {
					button = child;
				}
			} );
			if ( button ) {
				button.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
			}
			if ( ! open ) {
				// Collapse nested submenus too.
				Array.prototype.forEach.call( item.querySelectorAll( 'li.is-open' ), function ( nested ) {
					setSubmenu( nested, false );
				} );
			}
		};

		var parentItems = header.querySelectorAll( '.nexusforge-menu li.menu-item-has-children' );

		Array.prototype.forEach.call( header.querySelectorAll( '.nexusforge-submenu-toggle' ), function ( button ) {
			button.addEventListener( 'click', function ( e ) {
				e.preventDefault();
				var item = button.closest( 'li' );
				if ( ! item ) {
					return;
				}
				var willOpen = ! item.classList.contains( 'is-open' );
				setSubmenu( item, willOpen );
				// On desktop a closed dropdown must stay closed even though the
				// pointer/focus is still inside its parent item.
				item.classList.toggle( 'is-dismissed', ! willOpen && ! mobileQuery.matches );
			} );
		} );

		Array.prototype.forEach.call( parentItems, function ( item ) {
			// Desktop: keyboard focus anywhere inside a parent item opens its
			// dropdown, and aria-expanded on the toggle reflects that.
			item.addEventListener( 'focusin', function ( e ) {
				if ( mobileQuery.matches || item.classList.contains( 'is-dismissed' ) ) {
					return;
				}
				if ( ! item.classList.contains( 'is-open' ) && hasKeyboardFocus( e.target ) ) {
					setSubmenu( item, true );
				}
			} );

			// Desktop: close a dropdown when focus leaves its parent item.
			item.addEventListener( 'focusout', function ( e ) {
				if ( mobileQuery.matches ) {
					return;
				}
				if ( ! e.relatedTarget || ! item.contains( e.relatedTarget ) ) {
					item.classList.remove( 'is-dismissed' );
					if ( e.relatedTarget ) {
						setSubmenu( item, false );
					}
				}
			} );

			item.addEventListener( 'mouseleave', function () {
				item.classList.remove( 'is-dismissed' );
			} );
		} );

		// Desktop: Escape dismisses the open dropdown and returns focus to its parent.
		header.addEventListener( 'keydown', function ( e ) {
			if ( isMenuOpen || ( 'Escape' !== e.key && 'Esc' !== e.key ) ) {
				return;
			}
			var submenu = e.target.closest ? e.target.closest( '.nexusforge-sub-menu' ) : null;
			var item = submenu ? submenu.parentElement : ( e.target.closest ? e.target.closest( 'li.menu-item-has-children' ) : null );
			if ( ! item ) {
				return;
			}

			var wasInSubmenu = !! submenu;
			setSubmenu( item, false );
			item.classList.add( 'is-dismissed' );

			if ( wasInSubmenu ) {
				var target = null;
				Array.prototype.forEach.call( item.children, function ( child ) {
					if ( ! target && child.classList.contains( 'nexusforge-submenu-toggle' ) ) {
						target = child;
					}
				} );
				if ( target ) {
					target.focus();
				}
			}
		} );

		// Desktop: a click outside closes any dropdown opened with its toggle.
		document.addEventListener( 'click', function ( e ) {
			if ( mobileQuery.matches ) {
				return;
			}
			Array.prototype.forEach.call( parentItems, function ( item ) {
				if ( item.classList.contains( 'is-open' ) && ! item.contains( e.target ) ) {
					setSubmenu( item, false );
				}
			} );
		} );
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
}() );
