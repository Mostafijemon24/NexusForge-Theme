/**
 * NexusForge — Customizer live preview (postMessage).
 * Mirrors each control to its CSS custom property for instant feedback.
 *
 * Values are validated here the same way the PHP side validates them
 * (hex colours only; integers clamped to a fixed range) before they are
 * written to the document through the CSSOM.
 */
( function ( api ) {
	'use strict';

	var root = document.documentElement;
	var HEX = /^#([0-9a-f]{3}|[0-9a-f]{6})$/i;

	/**
	 * Set (or, for an invalid colour, reset) a list of colour custom properties.
	 *
	 * @param {string[]} properties Custom property names.
	 * @param {string}   value      Raw colour value from the control.
	 */
	function setColor( properties, value ) {
		var valid = 'string' === typeof value && HEX.test( value );
		properties.forEach( function ( property ) {
			if ( valid ) {
				root.style.setProperty( property, value );
			} else {
				root.style.removeProperty( property );
			}
		} );
	}

	/**
	 * Parse an integer and clamp it to a range.
	 *
	 * @param {*}      value    Raw value from the control.
	 * @param {number} min      Lowest allowed value.
	 * @param {number} max      Highest allowed value.
	 * @param {number} fallback Value used when the input is not a number.
	 * @return {number} Clamped integer.
	 */
	function clampInt( value, min, max, fallback ) {
		var number = parseInt( value, 10 );
		if ( isNaN( number ) ) {
			number = fallback;
		}
		return Math.max( min, Math.min( max, number ) );
	}

	api( 'blogname', function ( value ) {
		value.bind( function ( to ) {
			var names = document.querySelectorAll( '.nexusforge-branding .site-name' );
			Array.prototype.forEach.call( names, function ( el ) {
				el.textContent = to;
			} );
		} );
	} );

	api( 'nexusforge_color_brand', function ( value ) {
		value.bind( function ( to ) {
			setColor( [ '--nexusforge-teal', '--nexusforge-color-brand' ], to );
		} );
	} );

	api( 'nexusforge_color_canvas', function ( value ) {
		value.bind( function ( to ) {
			setColor( [ '--nexusforge-ivory', '--nexusforge-color-canvas' ], to );
		} );
	} );

	api( 'nexusforge_color_text', function ( value ) {
		value.bind( function ( to ) {
			setColor( [ '--nexusforge-ink', '--nexusforge-color-text' ], to );
		} );
	} );

	api( 'nexusforge_base_font_px', function ( value ) {
		value.bind( function ( to ) {
			var rem = clampInt( to, 14, 22, 17 ) / 16;
			root.style.setProperty( '--nexusforge-fs-body', 'clamp(1rem, 0.95rem + 0.2vw, ' + rem + 'rem)' );
		} );
	} );

	api( 'nexusforge_content_width', function ( value ) {
		value.bind( function ( to ) {
			root.style.setProperty( '--nexusforge-wide', clampInt( to, 960, 1440, 1200 ) + 'px' );
		} );
	} );

	api( 'nexusforge_button_radius', function ( value ) {
		value.bind( function ( to ) {
			root.style.setProperty( '--nexusforge-radius-md', clampInt( to, 0, 40, 10 ) + 'px' );
		} );
	} );
}( wp.customize ) );
