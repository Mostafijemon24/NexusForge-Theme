<?php
/**
 * Accessible navigation walker.
 *
 * Extends the core walker (so every core attribute, filter and aria-current is
 * preserved) and adds a real <button> toggle after each item that has a
 * submenu. The toggle makes dropdowns keyboard and screen-reader operable;
 * its aria-expanded state is managed by assets/js/navigation.js.
 *
 * @package NexusForge
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Nexusforge_Walker_Nav
 */
class Nexusforge_Walker_Nav extends Walker_Nav_Menu {

	/**
	 * Start the submenu wrapper.
	 *
	 * @param string   $output Passed by reference.
	 * @param int      $depth  Depth of menu item.
	 * @param stdClass $args   Menu arguments.
	 */
	public function start_lvl( &$output, $depth = 0, $args = null ) {
		$indent  = str_repeat( "\t", $depth );
		$output .= "\n{$indent}<ul class=\"sub-menu nexusforge-sub-menu\">\n";
	}

	/**
	 * Start a menu item, appending a submenu toggle button when it has children.
	 *
	 * @param string   $output            Passed by reference.
	 * @param WP_Post  $data_object       Menu item data object.
	 * @param int      $depth             Depth of menu item.
	 * @param stdClass $args              Menu arguments.
	 * @param int      $current_object_id Current item ID.
	 */
	public function start_el( &$output, $data_object, $depth = 0, $args = null, $current_object_id = 0 ) {
		parent::start_el( $output, $data_object, $depth, $args, $current_object_id );

		$classes = empty( $data_object->classes ) ? array() : (array) $data_object->classes;
		if ( ! in_array( 'menu-item-has-children', $classes, true ) ) {
			return;
		}

		/** This filter is documented in wp-includes/post-template.php */
		$title = apply_filters( 'the_title', $data_object->title, $data_object->ID ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Core filter, as used by Walker_Nav_Menu.
		$label = sprintf(
			/* translators: %s: menu item name. */
			__( '%s submenu', 'nexusforge' ),
			wp_strip_all_tags( $title )
		);

		$output .= '<button type="button" class="nexusforge-submenu-toggle" aria-expanded="false" aria-label="' . esc_attr( $label ) . '">';
		$output .= '<span class="nexusforge-submenu-toggle__icon" aria-hidden="true"></span>';
		$output .= '</button>';
	}
}
