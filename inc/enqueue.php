<?php
/**
 * Asset registration and enqueue.
 *
 * @package NexusForge
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Version a theme asset by file modification time (cache-busting in dev,
 * stable in production).
 *
 * @param string $relative_path Path relative to the theme root.
 * @return string
 */
function nexusforge_asset_version( $relative_path ) {
	$file = NEXUSFORGE_DIR . '/' . ltrim( $relative_path, '/' );
	return file_exists( $file ) ? (string) filemtime( $file ) : NEXUSFORGE_VERSION;
}

/**
 * Enqueue front-end styles and scripts.
 */
function nexusforge_enqueue_assets() {
	// Fonts — Manrope (variable, wght 200–800), bundled locally under
	// assets/fonts/ and declared via @font-face in assets/css/fonts.css.
	// Served from the theme: no requests to fonts.googleapis.com / fonts.gstatic.com.
	wp_enqueue_style(
		'nexusforge-fonts',
		NEXUSFORGE_URI . '/assets/css/fonts.css',
		array(),
		nexusforge_asset_version( 'assets/css/fonts.css' )
	);

	// Design tokens — the single palette/scale.
	wp_enqueue_style(
		'nexusforge-tokens',
		NEXUSFORGE_URI . '/assets/css/tokens.css',
		array( 'nexusforge-fonts' ),
		nexusforge_asset_version( 'assets/css/tokens.css' )
	);

	// Main stylesheet (base, layout, components, templates).
	wp_enqueue_style(
		'nexusforge-main',
		NEXUSFORGE_URI . '/assets/css/main.css',
		array( 'nexusforge-tokens' ),
		nexusforge_asset_version( 'assets/css/main.css' )
	);

	// Custom block styles (Outline/Pill buttons, Card group, Framed image, Panel quote).
	wp_enqueue_style(
		'nexusforge-blocks',
		NEXUSFORGE_URI . '/assets/css/blocks.css',
		array( 'nexusforge-tokens' ),
		nexusforge_asset_version( 'assets/css/blocks.css' )
	);

	// Customizer-driven token overrides, inlined onto the tokens handle.
	$inline = nexusforge_customizer_css();
	if ( '' !== $inline ) {
		wp_add_inline_style( 'nexusforge-tokens', $inline );
	}

	// Accessible navigation JS — deferred, only when a header/menu is present.
	wp_enqueue_script(
		'nexusforge-navigation',
		NEXUSFORGE_URI . '/assets/js/navigation.js',
		array(),
		nexusforge_asset_version( 'assets/js/navigation.js' ),
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);

	// Threaded comments on singular views only.
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'nexusforge_enqueue_assets' );

/**
 * Enqueue block editor assets so editor matches the front end.
 */
function nexusforge_editor_assets() {
	// Locally bundled Manrope, so the editor matches the front end with no
	// external font requests.
	wp_enqueue_style(
		'nexusforge-fonts',
		NEXUSFORGE_URI . '/assets/css/fonts.css',
		array(),
		nexusforge_asset_version( 'assets/css/fonts.css' )
	);
	wp_enqueue_style(
		'nexusforge-editor-tokens',
		NEXUSFORGE_URI . '/assets/css/tokens.css',
		array( 'nexusforge-fonts' ),
		nexusforge_asset_version( 'assets/css/tokens.css' )
	);
}
add_action( 'enqueue_block_editor_assets', 'nexusforge_editor_assets' );
