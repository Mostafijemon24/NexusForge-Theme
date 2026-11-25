<?php
/**
 * The blog index (posts page).
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
		<header class="nexusforge-archive-header">
			<p class="nexusforge-eyebrow"><?php esc_html_e( 'Insights', 'nexusforge' ); ?></p>
			<h1 class="page-title">
				<?php
				$nexusforge_posts_page = (int) get_option( 'page_for_posts' );
				echo esc_html( $nexusforge_posts_page ? get_the_title( $nexusforge_posts_page ) : __( 'From the field', 'nexusforge' ) );
				?>
			</h1>
		</header>

		<?php if ( have_posts() ) : ?>
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
