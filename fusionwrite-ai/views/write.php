<?php
/**
 * Write page: one post at a time.
 *
 * @package FusionWrite_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

fswrt_page_head(
	__( 'Write a post', 'fusionwrite-ai' ),
	__( 'Give a topic. FusionWrite writes the post, adds the images and saves it for you to review.', 'fusionwrite-ai' )
);
?>
<div class="fw-col" id="fw-write">

	<?php if ( ! fswrt_connected_providers() ) : ?>
		<p class="fw-notice">
			<?php esc_html_e( 'No AI provider is connected yet, so nothing can be written.', 'fusionwrite-ai' ); ?>
			<a class="fw-btn fw-btn--primary fw-btn--small" href="<?php echo esc_url( fswrt_page_url( 'providers' ) ); ?>"><?php esc_html_e( 'Connect a provider', 'fusionwrite-ai' ); ?></a>
		</p>
	<?php endif; ?>

	<form id="fw-write-form" class="fw-form" novalidate>
		<label class="screen-reader-text" for="fw-topic"><?php esc_html_e( 'Topic', 'fusionwrite-ai' ); ?></label>
		<textarea class="fw-topic" id="fw-topic" name="fw-topic" rows="1" maxlength="300" placeholder="<?php esc_attr_e( 'What should the post be about?', 'fusionwrite-ai' ); ?>" data-tour="topic"></textarea>

		<?php require FSWRT_DIR . 'views/partials/options.php'; ?>

		<button type="submit" class="fw-btn fw-btn--primary fw-btn--large" data-tour="write-submit"><?php esc_html_e( 'Write post', 'fusionwrite-ai' ); ?></button>
	</form>

	<section id="fw-progress" class="fw-progress-wrap" hidden>
		<h2 id="fw-progress-title" tabindex="-1"></h2>
		<ol class="fw-progress" id="fw-progress-list"></ol>
		<p class="fw-hint"><?php esc_html_e( 'Keep this tab open until the post is saved.', 'fusionwrite-ai' ); ?></p>
	</section>
</div>

<section id="fw-result" class="fw-col fw-col--wide fw-offstage">
	<p class="fw-result-note" id="fw-result-note"></p>
	<label class="screen-reader-text" for="fw-post-title"><?php esc_html_e( 'Post title', 'fusionwrite-ai' ); ?></label>
	<input type="text" class="fw-title-input" id="fw-post-title" autocomplete="off">
	<div class="fw-editor">
		<?php
		wp_editor(
			'',
			'fswrt_editor',
			array(
				'textarea_name' => 'fswrt_editor',
				'media_buttons' => true,
				'textarea_rows' => 18,
			)
		);
		?>
	</div>
	<div class="fw-actions">
		<button type="button" class="fw-btn fw-btn--primary" id="fw-save-post"><?php esc_html_e( 'Save changes', 'fusionwrite-ai' ); ?></button>
		<a class="fw-btn" id="fw-open-editor" href="#" target="_blank" rel="noopener"><?php esc_html_e( 'Open in WordPress editor', 'fusionwrite-ai' ); ?></a>
		<a class="fw-btn" id="fw-preview-post" href="#" target="_blank" rel="noopener"><?php esc_html_e( 'Preview', 'fusionwrite-ai' ); ?></a>
		<a class="fw-btn fw-btn--quiet" href="<?php echo esc_url( fswrt_page_url( 'write' ) ); ?>"><?php esc_html_e( 'Write another post', 'fusionwrite-ai' ); ?></a>
	</div>
</section>
