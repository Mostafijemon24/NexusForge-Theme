<?php
/**
 * Overview page.
 *
 * @package FusionWrite_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$fswrt_settings  = fswrt_get_settings();
$fswrt_stats     = fswrt_get_stats();
$fswrt_providers = fswrt_providers();
$fswrt_connected = fswrt_connected_providers();
$fswrt_engines   = fswrt_image_engines();
$fswrt_engine    = $fswrt_settings['images']['engine'];
$fswrt_user      = wp_get_current_user();
$fswrt_name      = $fswrt_user->first_name ? $fswrt_user->first_name : $fswrt_user->display_name;

if ( $fswrt_stats['posts'] > 0 ) {
	$fswrt_lede = sprintf(
		/* translators: 1: number of posts, 2: number of words, 3: number of images. */
		__( 'FusionWrite has written %1$s posts for this site so far: about %2$s words and %3$s images.', 'fusionwrite-ai' ),
		number_format_i18n( $fswrt_stats['posts'] ),
		number_format_i18n( $fswrt_stats['words'] ),
		number_format_i18n( $fswrt_stats['images'] )
	);
} else {
	$fswrt_lede = __( 'Nothing written yet. Three short steps and the first post is on its way.', 'fusionwrite-ai' );
}

/* translators: %s: the user's name. */
fswrt_page_head( sprintf( __( 'Welcome, %s', 'fusionwrite-ai' ), $fswrt_name ), $fswrt_lede );

$fswrt_recent = get_posts(
	array(
		'numberposts' => 5,
		'post_status' => array( 'draft', 'pending', 'publish', 'future', 'private' ),
		'meta_key'    => '_fswrt_generated', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- Small list of this plugin's own posts.
	)
);
?>
<div class="fw-col">

	<?php if ( 0 === $fswrt_stats['posts'] || ! $fswrt_connected ) : ?>
		<ol class="fw-steps" data-tour="steps">
			<li class="<?php echo $fswrt_connected ? 'is-done' : ''; ?>">
				<strong><?php esc_html_e( 'Connect an AI provider', 'fusionwrite-ai' ); ?></strong>
				<?php if ( $fswrt_connected ) : ?>
					<?php /* translators: %s: provider name. */ ?>
					<span><?php echo esc_html( sprintf( __( '%s is connected.', 'fusionwrite-ai' ), $fswrt_providers[ $fswrt_connected[0] ]['label'] ) ); ?></span>
				<?php else : ?>
					<span><?php esc_html_e( 'OpenRouter, Gemini and Groq all have free plans.', 'fusionwrite-ai' ); ?></span>
					<a class="fw-btn fw-btn--primary fw-btn--small" href="<?php echo esc_url( fswrt_page_url( 'providers' ) ); ?>"><?php esc_html_e( 'Connect a provider', 'fusionwrite-ai' ); ?></a>
				<?php endif; ?>
			</li>
			<li class="is-done">
				<strong><?php esc_html_e( 'Choose how images are made', 'fusionwrite-ai' ); ?></strong>
				<?php /* translators: %s: image engine name. */ ?>
				<span><?php echo esc_html( sprintf( __( 'Currently: %s.', 'fusionwrite-ai' ), $fswrt_engines[ $fswrt_engine ]['label'] ) ); ?> <a href="<?php echo esc_url( fswrt_page_url( 'images' ) ); ?>"><?php esc_html_e( 'Change', 'fusionwrite-ai' ); ?></a></span>
			</li>
			<li class="<?php echo $fswrt_stats['posts'] > 0 ? 'is-done' : ''; ?>">
				<strong><?php esc_html_e( 'Write your first post', 'fusionwrite-ai' ); ?></strong>
				<span><?php esc_html_e( 'Give a topic and FusionWrite saves a finished draft.', 'fusionwrite-ai' ); ?></span>
				<?php if ( $fswrt_connected ) : ?>
					<a class="fw-btn fw-btn--primary fw-btn--small" href="<?php echo esc_url( fswrt_page_url( 'write' ) ); ?>"><?php esc_html_e( 'Write a post', 'fusionwrite-ai' ); ?></a>
				<?php endif; ?>
			</li>
		</ol>
	<?php else : ?>
		<div class="fw-actions" data-tour="steps">
			<a class="fw-btn fw-btn--primary" href="<?php echo esc_url( fswrt_page_url( 'write' ) ); ?>"><?php esc_html_e( 'Write a post', 'fusionwrite-ai' ); ?></a>
			<a class="fw-btn" href="<?php echo esc_url( fswrt_page_url( 'bulk' ) ); ?>"><?php esc_html_e( 'Write in bulk', 'fusionwrite-ai' ); ?></a>
		</div>
	<?php endif; ?>

	<section class="fw-section">
		<h2><?php esc_html_e( 'Providers', 'fusionwrite-ai' ); ?></h2>
		<ul class="fw-pills">
			<?php foreach ( $fswrt_providers as $fswrt_id => $fswrt_provider ) : ?>
				<?php $fswrt_on = fswrt_provider_is_connected( $fswrt_id ); ?>
				<li>
					<a class="fw-pill" href="<?php echo esc_url( fswrt_page_url( 'providers' ) . '#provider-' . $fswrt_id ); ?>">
						<span class="fw-dot" data-state="<?php echo $fswrt_on ? 'on' : 'off'; ?>"></span>
						<?php echo esc_html( $fswrt_provider['label'] ); ?>
						<span class="screen-reader-text"><?php echo $fswrt_on ? esc_html__( 'connected', 'fusionwrite-ai' ) : esc_html__( 'not connected', 'fusionwrite-ai' ); ?></span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</section>

	<?php if ( $fswrt_recent ) : ?>
		<section class="fw-section">
			<h2><?php esc_html_e( 'Recent posts', 'fusionwrite-ai' ); ?></h2>
			<ul class="fw-list">
				<?php foreach ( $fswrt_recent as $fswrt_post ) : ?>
					<?php $fswrt_status = get_post_status_object( $fswrt_post->post_status ); ?>
					<li>
						<a href="<?php echo esc_url( get_edit_post_link( $fswrt_post->ID ) ); ?>"><?php echo esc_html( get_the_title( $fswrt_post ) ); ?></a>
						<span><?php echo esc_html( ( $fswrt_status ? $fswrt_status->label : $fswrt_post->post_status ) . ', ' . get_the_date( '', $fswrt_post ) ); ?></span>
					</li>
				<?php endforeach; ?>
			</ul>
			<p><a href="<?php echo esc_url( fswrt_page_url( 'history' ) ); ?>"><?php esc_html_e( 'See all history', 'fusionwrite-ai' ); ?></a></p>
		</section>
	<?php endif; ?>

</div>
