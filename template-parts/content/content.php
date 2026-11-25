<?php
/**
 * Default content part for the loop (index, home, archive).
 *
 * @package NexusForge
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry' ); ?>>
	<a class="entry-media-link" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
		<?php nexusforge_post_thumbnail( 'nexusforge-card' ); ?>
	</a>

	<?php nexusforge_entry_meta(); ?>

	<h2 class="entry-title">
		<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
	</h2>

	<div class="entry-summary">
		<?php the_excerpt(); ?>
	</div>

	<a class="nexusforge-btn nexusforge-btn--ghost" href="<?php the_permalink(); ?>">
		<?php
		printf(
			/* translators: %s: post title (screen-reader only). */
			esc_html__( 'Read more%s', 'nexusforge' ),
			'<span class="screen-reader-text">: ' . esc_html( get_the_title() ) . '</span>'
		);
		?>
	</a>
</article>
