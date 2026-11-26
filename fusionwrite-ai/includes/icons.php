<?php
/**
 * Inline icons used by the workspace.
 *
 * @package FusionWrite_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Print an icon.
 *
 * @param string $name Icon name.
 */
function fswrt_icon( $name ) {
	$paths = array(
		'overview'    => '<path d="M3 10.5 12 3l9 7.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1z"/>',
		'write'       => '<path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4z"/>',
		'bulk'        => '<path d="m12 2 10 5-10 5L2 7z"/><path d="m2 12 10 5 10-5"/><path d="m2 17 10 5 10-5"/>',
		'history'     => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
		'providers'   => '<path d="M9 2v6"/><path d="M15 2v6"/><path d="M6 8h12v3a6 6 0 0 1-12 0z"/><path d="M12 17v5"/>',
		'images'      => '<rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-5-5L5 21"/>',
		'preferences' => '<path d="M4 6h10"/><path d="M18 6h2"/><circle cx="16" cy="6" r="2"/><path d="M4 12h2"/><path d="M10 12h10"/><circle cx="8" cy="12" r="2"/><path d="M4 18h10"/><path d="M18 18h2"/><circle cx="16" cy="18" r="2"/>',
		'guide'       => '<path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20V3H6.5A2.5 2.5 0 0 0 4 5.5z"/><path d="M4 19.5A2.5 2.5 0 0 0 6.5 22H20v-5"/>',
		'back'        => '<path d="M19 12H5"/><path d="m12 19-7-7 7-7"/>',
		'tour'        => '<circle cx="12" cy="12" r="9"/><path d="m15.5 8.5-2 5-5 2 2-5z"/>',
		'check'       => '<path d="M20 6 9 17l-5-5"/>',
		'close'       => '<path d="M18 6 6 18"/><path d="m6 6 12 12"/>',
	);

	if ( ! isset( $paths[ $name ] ) ) {
		return;
	}

	$svg = '<svg class="fw-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $paths[ $name ] . '</svg>';

	echo wp_kses( $svg, fswrt_svg_kses() );
}

/**
 * Print the FusionWrite mark: two overlapping circles.
 */
function fswrt_mark() {
	$svg = '<svg class="fw-mark" width="26" height="26" viewBox="0 0 26 26" aria-hidden="true" focusable="false"><circle class="fw-mark-a" cx="9.5" cy="13" r="7.5"/><circle class="fw-mark-b" cx="16.5" cy="13" r="7.5"/></svg>';
	echo wp_kses( $svg, fswrt_svg_kses() );
}

/**
 * Elements and attributes allowed in the inline icons.
 *
 * @return array<string,array<string,bool>>
 */
function fswrt_svg_kses() {
	$shape = array(
		'd'      => true,
		'cx'     => true,
		'cy'     => true,
		'r'      => true,
		'x'      => true,
		'y'      => true,
		'rx'     => true,
		'width'  => true,
		'height' => true,
		'class'  => true,
	);
	return array(
		'svg'    => array(
			'class'           => true,
			'width'           => true,
			'height'          => true,
			'viewbox'         => true,
			'fill'            => true,
			'stroke'          => true,
			'stroke-width'    => true,
			'stroke-linecap'  => true,
			'stroke-linejoin' => true,
			'aria-hidden'     => true,
			'focusable'       => true,
		),
		'path'   => $shape,
		'circle' => $shape,
		'rect'   => $shape,
	);
}
