<?php
/**
 * The comments template.
 *
 * @package NexusForge
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( post_password_required() ) {
	return;
}
?>
<div id="comments" class="comments-area">
	<?php if ( have_comments() ) : ?>
		<h2 class="comments-title">
			<?php
			$nexusforge_comment_count = get_comments_number();
			printf(
				/* translators: %s: comment count number. */
				esc_html( _n( '%s comment', '%s comments', $nexusforge_comment_count, 'nexusforge' ) ),
				esc_html( number_format_i18n( $nexusforge_comment_count ) )
			);
			?>
		</h2>

		<ol class="comment-list">
			<?php
			wp_list_comments(
				array(
					'style'       => 'ol',
					'short_ping'  => true,
					'avatar_size' => 46,
				)
			);
			?>
		</ol>

		<?php
		the_comments_navigation(
			array(
				'prev_text' => esc_html__( 'Older comments', 'nexusforge' ),
				'next_text' => esc_html__( 'Newer comments', 'nexusforge' ),
			)
		);

		if ( ! comments_open() ) :
			?>
			<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'nexusforge' ); ?></p>
			<?php
		endif;
	endif;

	comment_form();
	?>
</div>
