<?php
/**
 * The front page.
 *
 * Behaviour:
 *  - If a static front page is set and it has real content (classic, blocks, or
 *    an Elementor-built layout), render that content so page builders own it.
 *  - Otherwise render the theme's designed starter sections as a fallback so a
 *    fresh install still shows a coherent homepage (no demo content imported).
 *  - If "Your latest posts" is the front page, fall back to the blog loop.
 *
 * @package NexusForge
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

if ( ! function_exists( 'nexusforge_is_built_with_elementor' ) ) {
	/**
	 * Whether the given page is built with Elementor.
	 *
	 * @param int $page_id Page ID.
	 * @return bool
	 */
	function nexusforge_is_built_with_elementor( $page_id ) {
		if ( ! did_action( 'elementor/loaded' ) || ! class_exists( '\Elementor\Plugin' ) ) {
			return false;
		}
		$document = \Elementor\Plugin::$instance->documents->get( $page_id );
		return $document && $document->is_built_with_elementor();
	}
}

if ( 'page' === get_option( 'show_on_front' ) && have_posts() ) :
	the_post();
	$nexusforge_id          = get_the_ID();
	$nexusforge_has_content = ( '' !== trim( get_the_content() ) ) || nexusforge_is_built_with_elementor( $nexusforge_id );

	if ( $nexusforge_has_content ) :
		?>
		<main id="primary" class="nexusforge-main nexusforge-front">
			<div class="entry-content"><?php the_content(); ?></div>
		</main>
		<?php
	else :
		?>
		<main id="primary" class="nexusforge-front nexusforge-front--starter">
			<?php
			get_template_part( 'template-parts/front/hero' );
			get_template_part( 'template-parts/front/trust' );
			get_template_part( 'template-parts/front/services' );
			get_template_part( 'template-parts/front/process' );
			get_template_part( 'template-parts/front/testimonial' );
			get_template_part( 'template-parts/front/insights' );
			get_template_part( 'template-parts/front/cta' );
			?>
		</main>
		<?php
	endif;
else :
	// Front page shows latest posts.
	?>
	<main id="primary" class="nexusforge-main">
		<div class="nexusforge-container">
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
endif;

get_footer();
