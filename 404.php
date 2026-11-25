<?php
/**
 * Template for displaying 404 (not found) pages.
 *
 * @package NexusForge
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<main id="primary" class="nexusforge-main">
	<div class="nexusforge-container nexusforge-container--narrow nexusforge-404">
		<div class="nexusforge-404__code"><?php esc_html_e( '404', 'nexusforge' ); ?></div>
		<h1 class="nexusforge-404__title"><?php esc_html_e( 'We couldn\'t find that page.', 'nexusforge' ); ?></h1>
		<p class="nexusforge-404__text"><?php esc_html_e( 'It may have moved, or the link might be out of date. Try a search or head back home.', 'nexusforge' ); ?></p>

		<div class="nexusforge-404__actions">
			<a class="nexusforge-btn nexusforge-btn--primary" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to home', 'nexusforge' ); ?></a>
		</div>

		<div class="nexusforge-404__search">
			<?php get_search_form(); ?>
		</div>
	</div>
</main>
<?php
get_footer();
