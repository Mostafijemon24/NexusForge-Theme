<?php
/**
 * Theme setup: supports, menus, image sizes, content width.
 *
 * @package NexusForge
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register theme support and navigation menus.
 */
function nexusforge_setup() {
	load_theme_textdomain( 'nexusforge', NEXUSFORGE_DIR . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'appearance-tools' );
	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/css/editor.css' );

	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
			'navigation-widgets',
		)
	);

	add_theme_support(
		'custom-logo',
		array(
			'height'               => 48,
			'width'                => 180,
			'flex-height'          => true,
			'flex-width'           => true,
			'unlink-homepage-logo' => true,
		)
	);

	/*
	 * Custom Background support. No default colour/image is declared so the
	 * theme's own canvas colour (set via the Customizer "Page canvas colour"
	 * control) stays authoritative until a visitor actively opts into a custom
	 * background. When set, WordPress outputs a `body.custom-background` rule
	 * that cleanly overrides the theme's low-specificity `body { background }`.
	 */
	add_theme_support(
		'custom-background',
		array(
			'default-color' => '',
			'default-image' => '',
		)
	);

	/*
	 * Custom Header support. Used as an optional full-width banner image shown
	 * beneath the site header (see header.php). Header text is disabled because
	 * the theme renders the site title/logo through its own branding part.
	 */
	add_theme_support(
		'custom-header',
		array(
			'default-image' => '',
			'width'         => 1600,
			'height'        => 400,
			'flex-width'    => true,
			'flex-height'   => true,
			'header-text'   => false,
			'video'         => false,
		)
	);

	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu', 'nexusforge' ),
			'footer'  => __( 'Footer Menu', 'nexusforge' ),
		)
	);

	// Named image size matching the design's card media (16:9-ish).
	add_image_size( 'nexusforge-card', 720, 405, true );
	add_image_size( 'nexusforge-wide', 1280, 640, true );
}
add_action( 'after_setup_theme', 'nexusforge_setup' );

/**
 * Set the content width in pixels, based on the theme's design.
 */
function nexusforge_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'nexusforge_content_width', 1200 );
}
add_action( 'after_setup_theme', 'nexusforge_content_width', 0 );

/**
 * Register widget areas.
 */
function nexusforge_widgets_init() {
	register_sidebar(
		array(
			'name'          => __( 'Footer', 'nexusforge' ),
			'id'            => 'footer-1',
			'description'   => __( 'Appears in the site footer.', 'nexusforge' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'nexusforge_widgets_init' );
