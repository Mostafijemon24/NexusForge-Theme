<?php
/**
 * Elementor compatibility.
 *
 * Loaded only when `elementor/loaded` has fired (see functions.php), so this file
 * may safely reference Elementor APIs. It keeps the theme independent: Elementor
 * owns page content and (with Pro) builder templates, while the theme owns global
 * tokens and fallback chrome.
 *
 * @package NexusForge
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Declare theme support for Elementor features where opt-in is appropriate.
 */
function nexusforge_elementor_support() {
	// Let Elementor know the theme provides locations for the Pro Theme Builder.
	add_theme_support( 'elementor' );
}
add_action( 'after_setup_theme', 'nexusforge_elementor_support', 30 );

/**
 * Register Theme Builder locations so Elementor Pro can own header/footer/single/
 * archive when the user creates templates for them.
 *
 * @param \ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager $manager Locations manager.
 */
function nexusforge_register_elementor_locations( $manager ) {
	if ( is_object( $manager ) && method_exists( $manager, 'register_all_core_location' ) ) {
		$manager->register_all_core_location();
	}
}
add_action( 'elementor/theme/register_locations', 'nexusforge_register_elementor_locations' );

/**
 * Whether Elementor Pro Theme Builder owns a given location (header/footer/etc.).
 *
 * The theme calls this before printing its own header/footer so there is never a
 * duplicate. See header.php / footer.php.
 *
 * @param string $location Location slug (e.g. 'header', 'footer').
 * @return bool True when Elementor rendered the location.
 */
function nexusforge_elementor_did_location( $location ) {
	if ( function_exists( 'elementor_theme_do_location' ) ) {
		return (bool) elementor_theme_do_location( $location );
	}
	return false;
}

/**
 * Whether Elementor Pro is active (not just Free).
 *
 * @return bool
 */
function nexusforge_has_elementor_pro() {
	return (bool) function_exists( 'elementor_pro_load_plugin' );
}
