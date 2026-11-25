<?php
/**
 * Custom, labelled search form.
 *
 * @package NexusForge
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$nexusforge_unique_id = wp_unique_id( 'search-field-' );
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label for="<?php echo esc_attr( $nexusforge_unique_id ); ?>" class="screen-reader-text">
		<?php esc_html_e( 'Search for:', 'nexusforge' ); ?>
	</label>
	<input
		type="search"
		id="<?php echo esc_attr( $nexusforge_unique_id ); ?>"
		class="search-field"
		placeholder="<?php echo esc_attr_x( 'Search&hellip;', 'placeholder', 'nexusforge' ); ?>"
		value="<?php echo esc_attr( get_search_query( false ) ); ?>"
		name="s"
	/>
	<button type="submit" class="nexusforge-btn nexusforge-btn--primary search-submit">
		<?php echo esc_html_x( 'Search', 'submit button', 'nexusforge' ); ?>
	</button>
</form>
