<?php
/**
 * Compile Customizer mods into :root CSS custom property overrides.
 *
 * Only tokens that differ from their defaults are emitted, keeping the inline
 * payload tiny. These overrides re-declare the same variables defined in
 * assets/css/tokens.css, so the Customizer is the single editing surface.
 *
 * @package NexusForge
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Build the inline CSS string for the current Customizer settings.
 *
 * @return string
 */
function nexusforge_customizer_css() {
	$defaults = nexusforge_customizer_defaults();
	$vars     = array();

	$brand = get_theme_mod( 'nexusforge_color_brand', $defaults['nexusforge_color_brand'] );
	if ( $brand && strtolower( $brand ) !== strtolower( $defaults['nexusforge_color_brand'] ) ) {
		$vars['--nexusforge-teal']        = $brand;
		$vars['--nexusforge-color-brand'] = $brand;
	}

	$canvas = get_theme_mod( 'nexusforge_color_canvas', $defaults['nexusforge_color_canvas'] );
	if ( $canvas && strtolower( $canvas ) !== strtolower( $defaults['nexusforge_color_canvas'] ) ) {
		$vars['--nexusforge-ivory']        = $canvas;
		$vars['--nexusforge-color-canvas'] = $canvas;
	}

	$text = get_theme_mod( 'nexusforge_color_text', $defaults['nexusforge_color_text'] );
	if ( $text && strtolower( $text ) !== strtolower( $defaults['nexusforge_color_text'] ) ) {
		$vars['--nexusforge-ink']        = $text;
		$vars['--nexusforge-color-text'] = $text;
	}

	$font_px = (int) get_theme_mod( 'nexusforge_base_font_px', $defaults['nexusforge_base_font_px'] );
	if ( $font_px && $font_px !== (int) $defaults['nexusforge_base_font_px'] ) {
		$rem                          = round( $font_px / 16, 4 );
		$vars['--nexusforge-fs-body'] = sprintf( 'clamp(1rem, 0.95rem + 0.2vw, %srem)', $rem );
	}

	$content_width = (int) get_theme_mod( 'nexusforge_content_width', $defaults['nexusforge_content_width'] );
	if ( $content_width && $content_width !== (int) $defaults['nexusforge_content_width'] ) {
		$vars['--nexusforge-wide'] = $content_width . 'px';
	}

	$radius = get_theme_mod( 'nexusforge_button_radius', $defaults['nexusforge_button_radius'] );
	if ( '' !== $radius && (int) $radius !== (int) $defaults['nexusforge_button_radius'] ) {
		$vars['--nexusforge-radius-md'] = (int) $radius . 'px';
	}

	if ( empty( $vars ) ) {
		return '';
	}

	$declarations = '';
	foreach ( $vars as $prop => $value ) {
		$declarations .= $prop . ':' . $value . ';';
	}

	return ':root{' . $declarations . '}';
}
