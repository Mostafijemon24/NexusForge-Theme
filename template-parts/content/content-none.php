<?php
/**
 * The template part for displaying a message when posts are not found.
 *
 * @package NexusForge
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section class="nexusforge-empty no-results not-found">
	<h2 class="entry-title"><?php esc_html_e( 'Nothing found', 'nexusforge' ); ?></h2>

	<div class="entry-content">
		<?php if ( is_home() && current_user_can( 'publish_posts' ) ) : ?>
			<p>
				<?php
				printf(
					wp_kses(
						/* translators: %s: URL to the WordPress "write post" page. */
						__( 'Ready to publish your first post? <a href="%s">Get started here</a>.', 'nexusforge' ),
						array( 'a' => array( 'href' => array() ) )
					),
					esc_url( admin_url( 'post-new.php' ) )
				);
				?>
			</p>
		<?php elseif ( is_search() ) : ?>
			<p><?php esc_html_e( 'Sorry, nothing matched your search. Try again with different keywords.', 'nexusforge' ); ?></p>
			<?php get_search_form(); ?>
		<?php else : ?>
			<p><?php esc_html_e( 'It seems we cannot find what you are looking for. Perhaps a search can help.', 'nexusforge' ); ?></p>
			<?php get_search_form(); ?>
		<?php endif; ?>
	</div>
</section>
