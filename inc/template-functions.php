<?php
/**
 * Functions that hook into the output: body classes, pingback, etc.
 *
 * @package NexusForge
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add helpful classes to the body.
 *
 * @param array $classes Existing body classes.
 * @return array
 */
function nexusforge_body_classes( $classes ) {
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	if ( ! is_active_sidebar( 'footer-1' ) ) {
		$classes[] = 'no-footer-widgets';
	}

	$classes[] = 'nexusforge';

	return $classes;
}
add_filter( 'body_class', 'nexusforge_body_classes' );

/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 */
function nexusforge_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">' . "\n", esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}
add_action( 'wp_head', 'nexusforge_pingback_header' );

/**
 * Whether the current request uses Elementor's blank "Canvas" page template,
 * in which case the theme must not output its own header/footer chrome.
 *
 * Note: only `elementor_canvas` (Elementor Canvas) suppresses chrome. The
 * `elementor_header_footer` ("Elementor Full Width") template deliberately
 * calls get_header()/get_footer(), so the theme MUST render its chrome there —
 * it is intentionally excluded here.
 *
 * @return bool
 */
function nexusforge_is_elementor_canvas() {
	if ( ! did_action( 'elementor/loaded' ) ) {
		return false;
	}

	$template = get_page_template_slug();
	return in_array( $template, array( 'elementor_canvas' ), true );
}

/**
 * Fallback for a missing menu: a simple link to the admin menus page is NOT
 * shown on the front end; instead output a minimal page-list for usability.
 */
function nexusforge_primary_menu_fallback() {
	// Always emit a valid list: `before`/`after` carry the <ul> wrapper so the
	// <li> items are correctly contained (WCAG 1.3.1 / valid list markup).
	wp_page_menu(
		array(
			'container' => false,
			'before'    => '<ul class="nexusforge-menu">',
			'after'     => '</ul>',
			'show_home' => true,
			'depth'     => 1,
		)
	);
}
