<?php
/**
 * Block editor integration: block styles, starter patterns, and the
 * stylesheet that renders the custom block styles on the front end and in
 * the editor.
 *
 * @package NexusForge
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register genuine, theme-styled block styles.
 *
 * Each style has matching CSS in assets/css/blocks.css (loaded on both the
 * front end and in the editor), so these are real visual variations, not
 * empty registrations.
 */
function nexusforge_register_block_styles() {
	if ( ! function_exists( 'register_block_style' ) ) {
		return;
	}

	register_block_style(
		'core/button',
		array(
			'name'  => 'nexusforge-outline',
			'label' => __( 'Outline', 'nexusforge' ),
		)
	);

	register_block_style(
		'core/button',
		array(
			'name'  => 'nexusforge-pill',
			'label' => __( 'Pill', 'nexusforge' ),
		)
	);

	register_block_style(
		'core/group',
		array(
			'name'  => 'nexusforge-card',
			'label' => __( 'Card', 'nexusforge' ),
		)
	);

	register_block_style(
		'core/image',
		array(
			'name'  => 'nexusforge-frame',
			'label' => __( 'Framed', 'nexusforge' ),
		)
	);

	register_block_style(
		'core/quote',
		array(
			'name'  => 'nexusforge-panel',
			'label' => __( 'Panel', 'nexusforge' ),
		)
	);
}
add_action( 'init', 'nexusforge_register_block_styles' );

/**
 * Enqueue the block-styles stylesheet for the editor so the custom styles
 * preview correctly while editing. (The front end loads it via the main
 * enqueue in inc/enqueue.php.)
 */
function nexusforge_block_styles_editor_css() {
	wp_enqueue_style(
		'nexusforge-blocks',
		NEXUSFORGE_URI . '/assets/css/blocks.css',
		array(),
		nexusforge_asset_version( 'assets/css/blocks.css' )
	);
}
add_action( 'enqueue_block_editor_assets', 'nexusforge_block_styles_editor_css' );

/**
 * Register a pattern category and a small set of original starter patterns
 * that match the theme's design language (theme palette, no external images,
 * no premium dependencies).
 */
function nexusforge_register_block_patterns() {
	if ( ! function_exists( 'register_block_pattern' ) || ! function_exists( 'register_block_pattern_category' ) ) {
		return;
	}

	register_block_pattern_category(
		'nexusforge',
		array( 'label' => __( 'NexusForge', 'nexusforge' ) )
	);

	// 1) Hero with call to action.
	$hero_heading = esc_html__( 'Build something people remember', 'nexusforge' );
	$hero_text    = esc_html__( 'A clean, fast foundation for your next site — flexible layouts, accessible components, and a design system you can make your own.', 'nexusforge' );
	$hero_primary = esc_html__( 'Get started', 'nexusforge' );
	$hero_second  = esc_html__( 'Learn more', 'nexusforge' );

	register_block_pattern(
		'nexusforge/hero-cta',
		array(
			'title'       => __( 'Hero with call to action', 'nexusforge' ),
			'description' => __( 'A centered hero with a headline, supporting text, and two buttons.', 'nexusforge' ),
			'categories'  => array( 'nexusforge', 'featured', 'call-to-action' ),
			'content'     => '<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}}},"backgroundColor":"mint-pale","layout":{"type":"constrained"}} -->'
				. '<div class="wp-block-group alignwide has-mint-pale-background-color has-background" style="padding-top:var(--wp--preset--spacing--70);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--70);padding-left:var(--wp--preset--spacing--40)">'
				. '<!-- wp:heading {"textAlign":"center","level":1,"fontSize":"xx-large"} -->'
				. '<h1 class="wp-block-heading has-text-align-center has-xx-large-font-size">' . $hero_heading . '</h1>'
				. '<!-- /wp:heading -->'
				. '<!-- wp:paragraph {"align":"center","fontSize":"large"} -->'
				. '<p class="has-text-align-center has-large-font-size">' . $hero_text . '</p>'
				. '<!-- /wp:paragraph -->'
				. '<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->'
				. '<div class="wp-block-buttons">'
				. '<!-- wp:button -->'
				. '<div class="wp-block-button"><a class="wp-block-button__link wp-element-button">' . $hero_primary . '</a></div>'
				. '<!-- /wp:button -->'
				. '<!-- wp:button {"className":"is-style-nexusforge-outline"} -->'
				. '<div class="wp-block-button is-style-nexusforge-outline"><a class="wp-block-button__link wp-element-button">' . $hero_second . '</a></div>'
				. '<!-- /wp:button -->'
				. '</div>'
				. '<!-- /wp:buttons -->'
				. '</div>'
				. '<!-- /wp:group -->',
		)
	);

	// 2) Three feature columns.
	$f1_h = esc_html__( 'Fast by default', 'nexusforge' );
	$f1_t = esc_html__( 'Lightweight markup, local fonts, and deferred scripts keep pages quick to load.', 'nexusforge' );
	$f2_h = esc_html__( 'Accessible components', 'nexusforge' );
	$f2_t = esc_html__( 'Keyboard-friendly navigation, clear focus states, and semantic landmarks.', 'nexusforge' );
	$f3_h = esc_html__( 'Yours to shape', 'nexusforge' );
	$f3_t = esc_html__( 'A tidy design system with colours, type, and spacing you can adjust.', 'nexusforge' );

	$column = function ( $heading, $text ) {
		return '<!-- wp:column -->'
			. '<div class="wp-block-column">'
			. '<!-- wp:heading {"level":3,"fontSize":"large"} -->'
			. '<h3 class="wp-block-heading has-large-font-size">' . $heading . '</h3>'
			. '<!-- /wp:heading -->'
			. '<!-- wp:paragraph -->'
			. '<p>' . $text . '</p>'
			. '<!-- /wp:paragraph -->'
			. '</div>'
			. '<!-- /wp:column -->';
	};

	register_block_pattern(
		'nexusforge/feature-columns',
		array(
			'title'       => __( 'Three feature columns', 'nexusforge' ),
			'description' => __( 'A three-column row for highlighting features or services.', 'nexusforge' ),
			'categories'  => array( 'nexusforge', 'columns', 'features' ),
			'content'     => '<!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|50"}}}} -->'
				. '<div class="wp-block-columns alignwide">'
				. $column( $f1_h, $f1_t )
				. $column( $f2_h, $f2_t )
				. $column( $f3_h, $f3_t )
				. '</div>'
				. '<!-- /wp:columns -->',
		)
	);

	// 3) Call-to-action band.
	$cta_h = esc_html__( 'Ready to get building?', 'nexusforge' );
	$cta_t = esc_html__( 'Start with a blank canvas or one of these patterns and make it your own.', 'nexusforge' );
	$cta_b = esc_html__( 'Start now', 'nexusforge' );

	register_block_pattern(
		'nexusforge/cta-band',
		array(
			'title'       => __( 'Call to action band', 'nexusforge' ),
			'description' => __( 'A full-width brand-coloured band with a heading and a button.', 'nexusforge' ),
			'categories'  => array( 'nexusforge', 'call-to-action', 'banner' ),
			'content'     => '<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}}},"backgroundColor":"brand","textColor":"surface","layout":{"type":"constrained"}} -->'
				. '<div class="wp-block-group alignfull has-surface-color has-brand-background-color has-text-color has-background" style="padding-top:var(--wp--preset--spacing--60);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--60);padding-left:var(--wp--preset--spacing--40)">'
				. '<!-- wp:heading {"textAlign":"center","level":2,"textColor":"surface","fontSize":"x-large"} -->'
				. '<h2 class="wp-block-heading has-text-align-center has-surface-color has-text-color has-x-large-font-size">' . $cta_h . '</h2>'
				. '<!-- /wp:heading -->'
				. '<!-- wp:paragraph {"align":"center","textColor":"surface"} -->'
				. '<p class="has-text-align-center has-surface-color has-text-color">' . $cta_t . '</p>'
				. '<!-- /wp:paragraph -->'
				. '<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->'
				. '<div class="wp-block-buttons">'
				. '<!-- wp:button {"className":"is-style-nexusforge-pill"} -->'
				. '<div class="wp-block-button is-style-nexusforge-pill"><a class="wp-block-button__link wp-element-button">' . $cta_b . '</a></div>'
				. '<!-- /wp:button -->'
				. '</div>'
				. '<!-- /wp:buttons -->'
				. '</div>'
				. '<!-- /wp:group -->',
		)
	);
}
add_action( 'init', 'nexusforge_register_block_patterns' );
