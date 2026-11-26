<?php
/**
 * Bulk page: a queue of topics.
 *
 * @package FusionWrite_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

fswrt_page_head(
	__( 'Write in bulk', 'fusionwrite-ai' ),
	__( 'List your topics, one per line. Posts are written one after another and each is saved as soon as it is done.', 'fusionwrite-ai' )
);
?>
<div class="fw-col" id="fw-bulk">

	<?php if ( ! fswrt_connected_providers() ) : ?>
		<p class="fw-notice">
			<?php esc_html_e( 'No AI provider is connected yet, so nothing can be written.', 'fusionwrite-ai' ); ?>
			<a class="fw-btn fw-btn--primary fw-btn--small" href="<?php echo esc_url( fswrt_page_url( 'providers' ) ); ?>"><?php esc_html_e( 'Connect a provider', 'fusionwrite-ai' ); ?></a>
		</p>
	<?php endif; ?>

	<form id="fw-bulk-form" class="fw-form" novalidate>
		<div class="fw-field">
			<label for="fw-bulk-topics"><?php esc_html_e( 'Topics', 'fusionwrite-ai' ); ?></label>
			<textarea class="fw-input fw-textarea fw-textarea--topics" id="fw-bulk-topics" rows="7" placeholder="<?php echo esc_attr( __( 'How to brew pour-over coffee at home', 'fusionwrite-ai' ) . "\n" . __( 'Best houseplants for low light', 'fusionwrite-ai' ) . "\n" . __( 'A beginner’s guide to composting', 'fusionwrite-ai' ) ); ?>" data-tour="bulk-topics"></textarea>
			<p class="fw-hint" id="fw-bulk-count" aria-live="polite"></p>
		</div>

		<?php require FSWRT_DIR . 'views/partials/options.php'; ?>

		<button type="submit" class="fw-btn fw-btn--primary fw-btn--large" id="fw-bulk-submit"><?php esc_html_e( 'Write posts', 'fusionwrite-ai' ); ?></button>
	</form>

	<section id="fw-queue-wrap" class="fw-progress-wrap" hidden>
		<h2 id="fw-queue-title" tabindex="-1"></h2>
		<ul class="fw-queue" id="fw-queue"></ul>
		<div class="fw-actions">
			<button type="button" class="fw-btn" id="fw-queue-stop"><?php esc_html_e( 'Stop after this post', 'fusionwrite-ai' ); ?></button>
			<a class="fw-btn fw-btn--quiet" id="fw-queue-again" href="<?php echo esc_url( fswrt_page_url( 'bulk' ) ); ?>" hidden><?php esc_html_e( 'Start a new list', 'fusionwrite-ai' ); ?></a>
		</div>
		<p class="fw-hint" id="fw-queue-hint"><?php esc_html_e( 'Keep this tab open until the queue finishes.', 'fusionwrite-ai' ); ?></p>
	</section>
</div>
