<?php
/**
 * Template for displaying all single posts.
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
		<?php
		while ( have_posts() ) :
			the_post();
			get_template_part( 'template-parts/content/content', 'single' );

			the_post_navigation(
				array(
					'prev_text' => '<span class="nav-subtitle">' . esc_html__( 'Previous', 'nexusforge' ) . '</span> <span class="nav-title">%title</span>',
					'next_text' => '<span class="nav-subtitle">' . esc_html__( 'Next', 'nexusforge' ) . '</span> <span class="nav-title">%title</span>',
				)
			);

			if ( comments_open() || get_comments_number() ) {
				comments_template();
			}
		endwhile;
		?>
	</div>
</main>
<?php
get_footer();
