<?php
/**
 * Compile Customizer mods into :root CSS custom property overrides.
 *
 * Only tokens that differ from their defaults are emitted, keeping the inline
 * payload tiny. These overrides re-declare the same variables defined in
 * assets/css/tokens.css, so the Customizer is the single editing surface.
 *
 * Security: values are sanitized when saved (see the sanitize callbacks in
 * inc/customizer.php) AND validated again here, at output time. Nothing read
 * from the database is ever concatenated into the stylesheet as-is:
 *  - colours must pass sanitize_hex_color();
 *  - numbers are cast to integers and clamped to a fixed range;
 *  - property names are a hard-coded allow-list;
 *  - the finished string is passed through wp_strip_all_tags().
 *
 * @package NexusForge
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Read a colour theme mod and return it only if it is a valid hex colour.
 *
 * @param string $setting Theme mod name.
 * @return string Hex colour (e.g. "#287c70"), or an empty string when the
 *                stored value is missing, invalid, or equal to the default.
 */
function nexusforge_get_color_mod( $setting ) {
	$defaults = nexusforge_customizer_defaults();
	if ( ! isset( $defaults[ $setting ] ) ) {
		return '';
	}

	$value = get_theme_mod( $setting, $defaults[ $setting ] );
	$color = is_string( $value ) ? sanitize_hex_color( $value ) : '';

	if ( ! $color || strtolower( $color ) === strtolower( $defaults[ $setting ] ) ) {
		return '';
	}

	return strtolower( $color );
}

/**
 * Read a numeric theme mod as an integer clamped to its allowed range.
 *
 * @param string $setting Theme mod name.
 * @return int|null The validated integer, or null when the stored value is
 *                  missing, not numeric, or equal to the default.
 */
function nexusforge_get_int_mod( $setting ) {
	$defaults = nexusforge_customizer_defaults();
	$ranges   = nexusforge_customizer_ranges();
	if ( ! isset( $defaults[ $setting ], $ranges[ $setting ] ) ) {
		return null;
	}

	$value = get_theme_mod( $setting, $defaults[ $setting ] );
	if ( ! is_numeric( $value ) ) {
		return null;
	}

	$value = (int) $value;
	$value = max( $ranges[ $setting ]['min'], min( $ranges[ $setting ]['max'], $value ) );

	if ( (int) $defaults[ $setting ] === $value ) {
		return null;
	}

	return $value;
}

/**
 * Build the inline CSS string for the current Customizer settings.
 *
 * @return string Safe CSS (a single :root rule), or an empty string.
 */
function nexusforge_customizer_css() {
	$vars = array();

	// Colour tokens: setting => custom properties it controls.
	$color_map = array(
		'nexusforge_color_brand'  => array( '--nexusforge-teal', '--nexusforge-color-brand' ),
		'nexusforge_color_canvas' => array( '--nexusforge-ivory', '--nexusforge-color-canvas' ),
		'nexusforge_color_text'   => array( '--nexusforge-ink', '--nexusforge-color-text' ),
	);

	foreach ( $color_map as $setting => $properties ) {
		$color = nexusforge_get_color_mod( $setting );
		if ( '' === $color ) {
			continue;
		}
		foreach ( $properties as $property ) {
			$vars[ $property ] = $color;
		}
	}

	$font_px = nexusforge_get_int_mod( 'nexusforge_base_font_px' );
	if ( null !== $font_px ) {
		$rem                          = number_format( $font_px / 16, 4, '.', '' );
		$vars['--nexusforge-fs-body'] = 'clamp(1rem, 0.95rem + 0.2vw, ' . $rem . 'rem)';
	}

	$content_width = nexusforge_get_int_mod( 'nexusforge_content_width' );
	if ( null !== $content_width ) {
		$vars['--nexusforge-wide'] = $content_width . 'px';
	}

	$radius = nexusforge_get_int_mod( 'nexusforge_button_radius' );
	if ( null !== $radius ) {
		$vars['--nexusforge-radius-md'] = $radius . 'px';
	}

	if ( empty( $vars ) ) {
		return '';
	}

	$declarations = '';
	foreach ( $vars as $property => $value ) {
		$declarations .= $property . ':' . $value . ';';
	}

	// Final late escape: a stylesheet must never contain markup.
	return wp_strip_all_tags( ':root{' . $declarations . '}' );
}
