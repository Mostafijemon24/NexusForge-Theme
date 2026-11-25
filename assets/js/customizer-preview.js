/**
 * NexusForge — Customizer live preview (postMessage).
 * Mirrors each control to its CSS custom property for instant feedback.
 */
( function ( api ) {
	'use strict';

	var root = document.documentElement;

	api( 'blogname', function ( value ) {
		value.bind( function ( to ) {
			var el = document.querySelector( '.nexusforge-branding .site-name' );
			if ( el ) {
				el.textContent = to;
			}
		} );
	} );

	api( 'nexusforge_color_brand', function ( value ) {
		value.bind( function ( to ) {
			root.style.setProperty( '--nexusforge-teal', to );
			root.style.setProperty( '--nexusforge-color-brand', to );
		} );
	} );

	api( 'nexusforge_color_canvas', function ( value ) {
		value.bind( function ( to ) {
			root.style.setProperty( '--nexusforge-ivory', to );
			root.style.setProperty( '--nexusforge-color-canvas', to );
		} );
	} );

	api( 'nexusforge_color_text', function ( value ) {
		value.bind( function ( to ) {
			root.style.setProperty( '--nexusforge-ink', to );
			root.style.setProperty( '--nexusforge-color-text', to );
		} );
	} );

	api( 'nexusforge_base_font_px', function ( value ) {
		value.bind( function ( to ) {
			var rem = ( parseInt( to, 10 ) || 17 ) / 16;
			root.style.setProperty( '--nexusforge-fs-body', 'clamp(1rem, 0.95rem + 0.2vw, ' + rem + 'rem)' );
		} );
	} );

	api( 'nexusforge_content_width', function ( value ) {
		value.bind( function ( to ) {
			root.style.setProperty( '--nexusforge-wide', ( parseInt( to, 10 ) || 1200 ) + 'px' );
		} );
	} );

	api( 'nexusforge_button_radius', function ( value ) {
		value.bind( function ( to ) {
			root.style.setProperty( '--nexusforge-radius-md', ( parseInt( to, 10 ) || 10 ) + 'px' );
		} );
	} );
}( wp.customize ) );
