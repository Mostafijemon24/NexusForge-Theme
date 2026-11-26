/**
 * FusionWrite AI guided tour.
 *
 * The tour walks across several admin pages. The step number is kept in
 * sessionStorage so the tour carries on after each page load.
 */
( function () {
	'use strict';

	var D = window.fswrtData || {};
	var steps = D.tour || [];
	var labels = D.strings || {};
	var KEY = 'fswrtTourStep';
	var current = -1;
	var hole, shield, card, target;

	if ( ! steps.length ) {
		return;
	}

	function remember( index ) {
		try {
			window.sessionStorage.setItem( KEY, String( index ) );
		} catch ( e ) {}
	}

	function recall() {
		try {
			var saved = window.sessionStorage.getItem( KEY );
			return null === saved ? -1 : parseInt( saved, 10 );
		} catch ( e ) {
			return -1;
		}
	}

	function forget() {
		try {
			window.sessionStorage.removeItem( KEY );
		} catch ( e ) {}
	}

	function build() {
		if ( card ) {
			return;
		}

		shield = document.createElement( 'div' );
		shield.className = 'fw-tour-shield';

		hole = document.createElement( 'div' );
		hole.className = 'fw-tour-hole';

		card = document.createElement( 'div' );
		card.className = 'fw-tour-card';
		card.setAttribute( 'role', 'dialog' );
		card.setAttribute( 'aria-modal', 'true' );
		card.setAttribute( 'aria-labelledby', 'fw-tour-title' );
		card.setAttribute( 'aria-describedby', 'fw-tour-text' );
		card.tabIndex = -1;
		card.innerHTML =
			'<p class="fw-tour-count" aria-hidden="true"></p>' +
			'<h2 id="fw-tour-title"></h2>' +
			'<p class="fw-tour-text" id="fw-tour-text"></p>' +
			'<div class="fw-tour-buttons">' +
			'<button type="button" class="fw-tour-skip"></button>' +
			'<button type="button" class="fw-tour-back"></button>' +
			'<button type="button" class="fw-tour-next"></button>' +
			'</div>';

		card.querySelector( '.fw-tour-skip' ).textContent = labels.tourSkip;
		card.querySelector( '.fw-tour-back' ).textContent = labels.tourBack;

		card.querySelector( '.fw-tour-skip' ).addEventListener( 'click', finish );
		card.querySelector( '.fw-tour-back' ).addEventListener( 'click', function () {
			go( current - 1 );
		} );
		card.querySelector( '.fw-tour-next' ).addEventListener( 'click', function () {
			go( current + 1 );
		} );

		document.body.appendChild( shield );
		document.body.appendChild( hole );
		document.body.appendChild( card );

		document.addEventListener( 'keydown', onKey, true );
		window.addEventListener( 'resize', place );
		window.addEventListener( 'scroll', place, true );
	}

	function onKey( event ) {
		if ( ! card ) {
			return;
		}
		if ( 'Escape' === event.key ) {
			event.preventDefault();
			finish();
		} else if ( 'ArrowRight' === event.key ) {
			go( current + 1 );
		} else if ( 'ArrowLeft' === event.key && current > 0 ) {
			go( current - 1 );
		} else if ( 'Tab' === event.key ) {
			// Keep the keyboard inside the tour card.
			var buttons = Array.prototype.filter.call( card.querySelectorAll( 'button' ), function ( button ) {
				return ! button.hidden;
			} );
			var first = buttons[ 0 ];
			var last = buttons[ buttons.length - 1 ];
			if ( event.shiftKey && ( document.activeElement === first || document.activeElement === card ) ) {
				event.preventDefault();
				last.focus();
			} else if ( ! event.shiftKey && document.activeElement === last ) {
				event.preventDefault();
				first.focus();
			} else if ( ! card.contains( document.activeElement ) ) {
				event.preventDefault();
				first.focus();
			}
		}
	}

	function place() {
		if ( ! card ) {
			return;
		}

		var vw = window.innerWidth;
		var vh = window.innerHeight;
		var cw = card.offsetWidth;
		var ch = card.offsetHeight;
		var gap = 16;
		var pad = 8;
		var left, top;

		if ( ! target ) {
			hole.classList.add( 'is-empty' );
			hole.style.cssText = 'top:' + ( vh / 2 ) + 'px;left:' + ( vw / 2 ) + 'px;width:0;height:0;';
			left = ( vw - cw ) / 2;
			top = ( vh - ch ) / 2;
		} else {
			var rect = target.getBoundingClientRect();
			var box = {
				top: Math.max( 6, rect.top - pad ),
				left: Math.max( 6, rect.left - pad ),
				right: Math.min( vw - 6, rect.right + pad ),
				bottom: Math.min( vh - 6, rect.bottom + pad )
			};

			hole.classList.remove( 'is-empty' );
			hole.style.cssText = 'top:' + box.top + 'px;left:' + box.left + 'px;width:' + ( box.right - box.left ) + 'px;height:' + ( box.bottom - box.top ) + 'px;';

			var boxWidth = box.right - box.left;
			var boxHeight = box.bottom - box.top;
			var roomRight = vw - 12 - ( box.right + gap );

			if ( roomRight >= cw && rect.width < vw * 0.4 ) {
				// Room beside a narrow target, such as the menu.
				left = box.right + gap;
				top = box.top + ( boxHeight - ch ) / 2;
			} else if ( box.bottom + gap + ch <= vh - 12 ) {
				left = box.left + ( boxWidth - cw ) / 2;
				top = box.bottom + gap;
			} else if ( box.top - gap - ch >= 12 ) {
				left = box.left + ( boxWidth - cw ) / 2;
				top = box.top - gap - ch;
			} else if ( roomRight >= cw * 0.6 ) {
				// A tall target: sit against the right edge, mostly beside it.
				left = vw - cw - 12;
				top = box.top + ( boxHeight - ch ) / 2;
			} else {
				// The target fills the screen: sit over its lower edge.
				left = box.left + ( boxWidth - cw ) / 2;
				top = vh - ch - 24;
			}
		}

		card.style.left = Math.round( Math.min( Math.max( 12, left ), vw - cw - 12 ) ) + 'px';
		card.style.top = Math.round( Math.min( Math.max( 12, top ), vh - ch - 12 ) ) + 'px';
	}

	function show( index ) {
		var step = steps[ index ];
		var isLast = index === steps.length - 1;

		build();
		current = index;
		remember( index );

		target = step.target ? document.querySelector( step.target ) : null;
		if ( target && ! target.getClientRects().length ) {
			target = null;
		}

		card.querySelector( '.fw-tour-count' ).textContent = labels.tourCount.replace( '%1$s', index + 1 ).replace( '%2$s', steps.length );
		card.querySelector( '#fw-tour-title' ).textContent = step.title;
		card.querySelector( '#fw-tour-text' ).textContent = step.text;
		card.querySelector( '.fw-tour-back' ).hidden = 0 === index;
		card.querySelector( '.fw-tour-skip' ).hidden = isLast;
		card.querySelector( '.fw-tour-next' ).textContent = isLast ? labels.tourFinish : labels.tourNext;

		if ( target ) {
			target.scrollIntoView( { block: 'center', inline: 'nearest' } );
		} else {
			window.scrollTo( 0, 0 );
		}

		place();
		window.requestAnimationFrame( place );
		card.querySelector( '.fw-tour-next' ).focus();
	}

	function go( index ) {
		if ( index < 0 ) {
			return;
		}
		if ( index >= steps.length ) {
			finish();
			return;
		}

		var step = steps[ index ];
		if ( step.page && step.page !== D.page ) {
			remember( index );
			window.location.href = D.pages[ step.page ];
			return;
		}
		show( index );
	}

	function finish() {
		forget();
		current = -1;
		target = null;
		document.removeEventListener( 'keydown', onKey, true );
		window.removeEventListener( 'resize', place );
		window.removeEventListener( 'scroll', place, true );
		[ shield, hole, card ].forEach( function ( element ) {
			if ( element ) {
				element.remove();
			}
		} );
		shield = hole = card = null;
	}

	function markSeen() {
		if ( window.fswrtPost ) {
			window.fswrtPost( 'tour_done' ).catch( function () {} );
		}
	}

	Array.prototype.forEach.call( document.querySelectorAll( '[data-fw-tour-start]' ), function ( button ) {
		button.addEventListener( 'click', function () {
			markSeen();
			go( 0 );
		} );
	} );

	// Carry on after a page change, or start on the first visit.
	var saved = recall();
	if ( saved >= 0 && steps[ saved ] && ( ! steps[ saved ].page || steps[ saved ].page === D.page ) ) {
		show( saved );
	} else if ( saved >= 0 ) {
		forget();
	} else if ( D.tourAuto ) {
		markSeen();
		go( 0 );
	}
}() );
