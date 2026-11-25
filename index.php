<?php
/**
 * The main template file — required fallback for the template hierarchy.
 *
 * @package NexusForge
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<main id="primary" class="nexusforge-main">
	<div class="nexusforge-container">
		<?php if ( have_posts() ) : ?>

			<?php if ( is_home() && ! is_front_page() ) : ?>
				<header class="nexusforge-archive-header">
					<p class="nexusforge-eyebrow"><?php esc_html_e( 'Insights', 'nexusforge' ); ?></p>
					<h1 class="page-title"><?php single_post_title(); ?></h1>
				</header>
			<?php endif; ?>

			<div class="nexusforge-grid">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/content/content', get_post_type() );
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
