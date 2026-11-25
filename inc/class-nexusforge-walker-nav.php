<?php
/**
 * Accessible navigation walker.
 *
 * Adds a real <button> toggle for each submenu so dropdowns are keyboard and
 * screen-reader operable, with aria-expanded state managed by navigation.js.
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
		$output .= "\n{$indent}<ul class=\"nexusforge-sub-menu\" role=\"menu\">\n";
	}

	/**
	 * Start a menu item, injecting a toggle button when it has children.
	 *
	 * @param string   $output Passed by reference.
	 * @param WP_Post  $item   Menu item data object.
	 * @param int      $depth  Depth of menu item.
	 * @param stdClass $args   Menu arguments.
	 * @param int      $id     Current item ID.
	 */
	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		$classes   = empty( $item->classes ) ? array() : (array) $item->classes;
		$has_child = in_array( 'menu-item-has-children', $classes, true );

		$class_names = implode( ' ', array_filter( array_map( 'sanitize_html_class', $classes ) ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		$output .= '<li' . $class_names . '>';

		$atts          = array();
		$atts['href']  = ! empty( $item->url ) ? $item->url : '';
		$atts['class'] = 'nexusforge-menu-link';

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( '' !== $value ) {
				$value       = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}

		$title        = apply_filters( 'the_title', $item->title, $item->ID );
		$item_output  = ( isset( $args->before ) ? $args->before : '' ) . '<a' . $attributes . '>';
		$item_output .= ( isset( $args->link_before ) ? $args->link_before : '' ) . $title . ( isset( $args->link_after ) ? $args->link_after : '' );
		$item_output .= '</a>';

		if ( $has_child && 0 === $depth ) {
			$label = sprintf(
				/* translators: %s: menu item name. */
				__( 'Open %s submenu', 'nexusforge' ),
				wp_strip_all_tags( $title )
			);
			$item_output .= '<button type="button" class="nexusforge-submenu-toggle" aria-expanded="false" aria-label="' . esc_attr( $label ) . '">';
			$item_output .= '<span class="nexusforge-submenu-toggle__icon" aria-hidden="true"></span>';
			$item_output .= '</button>';
		}

		$item_output .= isset( $args->after ) ? $args->after : '';

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}
}
