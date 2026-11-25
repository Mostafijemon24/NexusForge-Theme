<?php
/**
 * NexusForge theme bootstrap.
 *
 * This file wires up the theme by loading focused modules from inc/.
 * It intentionally contains no business logic of its own.
 *
 * @package NexusForge
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Prevent direct access.
}

if ( ! defined( 'NEXUSFORGE_VERSION' ) ) {
	// Keep in sync with the Version header in style.css.
	define( 'NEXUSFORGE_VERSION', '1.0.3' );
}

define( 'NEXUSFORGE_DIR', get_template_directory() );
define( 'NEXUSFORGE_URI', get_template_directory_uri() );

/**
 * Require a theme module from the inc/ directory.
 *
 * @param string $file Relative path inside inc/ (without extension).
 */
function nexusforge_require( $file ) {
	$path = NEXUSFORGE_DIR . '/inc/' . $file . '.php';
	if ( is_readable( $path ) ) {
		require_once $path;
	}
}

nexusforge_require( 'setup' );
nexusforge_require( 'enqueue' );
nexusforge_require( 'template-tags' );
nexusforge_require( 'template-functions' );
nexusforge_require( 'customizer' );
nexusforge_require( 'customizer-css' );
nexusforge_require( 'blocks' );

// Load the accessible navigation walker.
require_once NEXUSFORGE_DIR . '/inc/class-nexusforge-walker-nav.php';

/*
 * Elementor compatibility is loaded only when Elementor is actually present,
 * so the theme never references Elementor classes when the plugin is absent.
 */
add_action(
	'after_setup_theme',
	static function () {
		if ( did_action( 'elementor/loaded' ) ) {
			nexusforge_require( 'elementor' );
		}
	},
	20
);
