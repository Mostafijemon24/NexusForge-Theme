<?php
/**
 * History page: every post the plugin wrote.
 *
 * @package FusionWrite_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reading the page number of a list.
$fswrt_paged = isset( $_GET['paged'] ) ? max( 1, absint( wp_unslash( $_GET['paged'] ) ) ) : 1;
$fswrt_query = new WP_Query(
	array(
		'post_type'      => 'post',
		'post_status'    => array( 'draft', 'pending', 'publish', 'future', 'private' ),
		'meta_key'       => '_fswrt_generated', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- This plugin's own posts, 15 per page.
		'posts_per_page' => 15,
		'paged'          => $fswrt_paged,
	)
);

fswrt_page_head(
	__( 'History', 'fusionwrite-ai' ),
	__( 'Every post FusionWrite has written for this site, newest first.', 'fusionwrite-ai' )
);
?>
<div class="fw-col fw-col--wide" data-tour="history">

	<?php if ( ! $fswrt_query->have_posts() ) : ?>
		<p class="fw-empty">
			<?php esc_html_e( 'No posts yet. The ones you write will be listed here with the model that wrote them.', 'fusionwrite-ai' ); ?>
			<a class="fw-btn fw-btn--primary fw-btn--small" href="<?php echo esc_url( fswrt_page_url( 'write' ) ); ?>"><?php esc_html_e( 'Write a post', 'fusionwrite-ai' ); ?></a>
		</p>
	<?php else : ?>
		<div class="fw-table-wrap">
			<table class="fw-table">
				<thead>
					<tr>
						<th scope="col"><?php esc_html_e( 'Post', 'fusionwrite-ai' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Written by', 'fusionwrite-ai' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Words', 'fusionwrite-ai' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Status', 'fusionwrite-ai' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Date', 'fusionwrite-ai' ); ?></th>
						<th scope="col"><span class="screen-reader-text"><?php esc_html_e( 'Actions', 'fusionwrite-ai' ); ?></span></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $fswrt_query->posts as $fswrt_post ) : ?>
						<?php
						$fswrt_status = get_post_status_object( $fswrt_post->post_status );
						$fswrt_model  = (string) get_post_meta( $fswrt_post->ID, '_fswrt_model', true );
						$fswrt_view   = 'publish' === $fswrt_post->post_status ? get_permalink( $fswrt_post ) : get_preview_post_link( $fswrt_post );
						?>
						<tr>
							<th scope="row"><a href="<?php echo esc_url( get_edit_post_link( $fswrt_post->ID ) ); ?>"><?php echo esc_html( get_the_title( $fswrt_post ) ); ?></a></th>
							<td>
								<?php echo esc_html( (string) get_post_meta( $fswrt_post->ID, '_fswrt_provider', true ) ); ?>
								<?php if ( '' !== $fswrt_model ) : ?>
									<small><?php echo esc_html( $fswrt_model ); ?></small>
								<?php endif; ?>
							</td>
							<td><?php echo esc_html( number_format_i18n( (int) get_post_meta( $fswrt_post->ID, '_fswrt_words', true ) ) ); ?></td>
							<td><span class="fw-status" data-state="<?php echo esc_attr( $fswrt_post->post_status ); ?>"><?php echo esc_html( $fswrt_status ? $fswrt_status->label : $fswrt_post->post_status ); ?></span></td>
							<td><?php echo esc_html( get_the_date( '', $fswrt_post ) ); ?></td>
							<td>
								<a href="<?php echo esc_url( get_edit_post_link( $fswrt_post->ID ) ); ?>"><?php esc_html_e( 'Edit', 'fusionwrite-ai' ); ?></a>
								<a href="<?php echo esc_url( $fswrt_view ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'View', 'fusionwrite-ai' ); ?></a>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>

		<?php if ( $fswrt_query->max_num_pages > 1 ) : ?>
			<nav class="fw-pager" aria-label="<?php esc_attr_e( 'History pages', 'fusionwrite-ai' ); ?>">
				<?php if ( $fswrt_paged > 1 ) : ?>
					<a class="fw-btn fw-btn--small" href="<?php echo esc_url( add_query_arg( 'paged', $fswrt_paged - 1, fswrt_page_url( 'history' ) ) ); ?>"><?php esc_html_e( 'Newer', 'fusionwrite-ai' ); ?></a>
				<?php endif; ?>
				<?php /* translators: 1: current page, 2: number of pages. */ ?>
				<span><?php echo esc_html( sprintf( __( 'Page %1$d of %2$d', 'fusionwrite-ai' ), $fswrt_paged, $fswrt_query->max_num_pages ) ); ?></span>
				<?php if ( $fswrt_paged < $fswrt_query->max_num_pages ) : ?>
					<a class="fw-btn fw-btn--small" href="<?php echo esc_url( add_query_arg( 'paged', $fswrt_paged + 1, fswrt_page_url( 'history' ) ) ); ?>"><?php esc_html_e( 'Older', 'fusionwrite-ai' ); ?></a>
				<?php endif; ?>
			</nav>
		<?php endif; ?>
	<?php endif; ?>

</div>
