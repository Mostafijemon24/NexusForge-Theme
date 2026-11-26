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
 * Print the FusionWrite mark: a pen writing a W on a dark tile, the same
 * drawing as the plugin's directory icon.
 */
function fswrt_mark() {
	$svg = '<svg class="fw-mark" width="32" height="32" viewBox="32 32 192 192" aria-hidden="true" focusable="false">'
		. '<defs>'
		. '<linearGradient id="fw-mark-tile" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#111a55"/><stop offset="1" stop-color="#2b1268"/></linearGradient>'
		. '<linearGradient id="fw-mark-w" x1="0" y1="0" x2="1" y2="0"><stop offset="0" stop-color="#49ebff"/><stop offset="0.5" stop-color="#5e93ff"/><stop offset="1" stop-color="#e3a2ff"/></linearGradient>'
		. '</defs>'
		. '<rect x="36" y="36" width="184" height="184" rx="44" fill="url(#fw-mark-tile)"/>'
		. '<path d="M96 116C99 144 104 180 113 180C123 180 125 138 133 138C141 138 143 180 153 180C164 180 171 142 178 108" fill="none" stroke="url(#fw-mark-w)" stroke-width="17" stroke-linecap="round" stroke-linejoin="round"/>'
		. '<g transform="translate(80.3 97.3) rotate(140) scale(0.72)">'
		. '<path d="M-12 -30Q-12 -35 -7 -35L7 -35Q12 -35 12 -30L14.5 -10C17 5 8 32 0 54C-8 32 -17 5 -14.5 -10Z" fill="#dff4ff"/>'
		. '<circle cx="0" cy="0" r="5" fill="#12205e"/>'
		. '</g></svg>';
	echo wp_kses( $svg, fswrt_svg_kses() );
}

/**
 * Elements and attributes allowed in the inline icons.
 *
 * @return array<string,array<string,bool>>
 */
function fswrt_svg_kses() {
	$shape = array(
		'd'               => true,
		'cx'              => true,
		'cy'              => true,
		'r'               => true,
		'x'               => true,
		'y'               => true,
		'rx'              => true,
		'width'           => true,
		'height'          => true,
		'class'           => true,
		'fill'            => true,
		'stroke'          => true,
		'stroke-width'    => true,
		'stroke-linecap'  => true,
		'stroke-linejoin' => true,
	);
	return array(
		'svg'            => array(
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
		'path'           => $shape,
		'circle'         => $shape,
		'rect'           => $shape,
		'g'              => array( 'transform' => true ),
		'defs'           => array(),
		'lineargradient' => array(
			'id' => true,
			'x1' => true,
			'y1' => true,
			'x2' => true,
			'y2' => true,
		),
		'stop'           => array(
			'offset'     => true,
			'stop-color' => true,
		),
	);
}
