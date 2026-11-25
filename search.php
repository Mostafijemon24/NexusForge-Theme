<?php
/**
 * Template for displaying search results.
 *
 * @package NexusForge
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<main id="primary" class="nexusforge-main">
	<div class="nexusforge-container nexusforge-container--narrow">
		<header class="nexusforge-search-header">
			<h1 class="page-title">
				<?php
				printf(
					/* translators: %s: search query. */
					esc_html__( 'Search results for "%s"', 'nexusforge' ),
					'<span>' . esc_html( get_search_query() ) . '</span>'
				);
				?>
			</h1>
			<?php get_search_form(); ?>
		</header>

		<?php if ( have_posts() ) : ?>
			<div class="nexusforge-search-results">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/content/content', 'search' );
				endwhile;
				?>
			</div>
			<?php nexusforge_pagination(); ?>
		<?php else : ?>
			<?php get_template_part( 'template-parts/content/content', 'none' ); ?>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();
