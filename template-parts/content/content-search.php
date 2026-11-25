<?php
/**
 * Content part for search results.
 *
 * @package NexusForge
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry entry--search' ); ?>>
	<h2 class="entry-title">
		<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
	</h2>
	<?php nexusforge_entry_meta(); ?>
	<div class="entry-summary">
		<?php the_excerpt(); ?>
	</div>
</article>
