<?php
/**
 * Content part for single posts.
 *
 * @package NexusForge
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry entry--single' ); ?>>
	<header class="entry-header">
		<?php nexusforge_entry_meta(); ?>
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

	<footer class="entry-footer">
		<?php
		$nexusforge_tags = get_the_tag_list( '', '' );
		if ( $nexusforge_tags ) {
			echo '<div class="entry-tags">' . wp_kses_post( $nexusforge_tags ) . '</div>';
		}
		?>
	</footer>
</article>
