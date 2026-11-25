<?php
/**
 * Content part for static pages.
 *
 * @package NexusForge
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry entry--page' ); ?>>
	<header class="entry-header">
		<h1 class="entry-title"><?php the_title(); ?></h1>
	</header>

	<?php nexusforge_post_thumbnail( 'nexusforge-wide' ); ?>

	<div class="entry-content">
		<?php
		the_content();

		wp_link_pages(
			array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'nexusforge' ),
				'after'  => '</div>',
			)
		);
		?>
	</div>
</article>
