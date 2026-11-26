<?php
/**
 * Guide page.
 *
 * @package FusionWrite_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

fswrt_page_head(
	__( 'Guide', 'fusionwrite-ai' ),
	__( 'How to get a free key, what happens when a post is written, and what to do when something fails.', 'fusionwrite-ai' )
);
?>
<div class="fw-col fw-guide">

	<div class="fw-actions">
		<button type="button" class="fw-btn fw-btn--primary" data-fw-tour-start><?php esc_html_e( 'Take the tour', 'fusionwrite-ai' ); ?></button>
	</div>

	<section class="fw-section">
		<h2><?php esc_html_e( 'Get a free key', 'fusionwrite-ai' ); ?></h2>
		<p><?php esc_html_e( 'One provider is enough. All three below let you write without paying, within daily limits they set.', 'fusionwrite-ai' ); ?></p>

		<h3>OpenRouter</h3>
		<ol class="fw-numbered">
			<li><?php esc_html_e( 'Create an account at openrouter.ai and open the Keys page.', 'fusionwrite-ai' ); ?></li>
			<li><?php esc_html_e( 'Create a key and copy it.', 'fusionwrite-ai' ); ?></li>
			<li><?php esc_html_e( 'Paste it under Providers, leave the model empty and choose Save and test.', 'fusionwrite-ai' ); ?></li>
		</ol>
		<p class="fw-hint"><?php esc_html_e( 'With the model left empty, OpenRouter picks any free model that is available. To choose one yourself, load the list and keep “free models only” ticked.', 'fusionwrite-ai' ); ?></p>

		<h3>Google Gemini</h3>
		<ol class="fw-numbered">
			<li><?php esc_html_e( 'Sign in to Google AI Studio and open “Get API key”.', 'fusionwrite-ai' ); ?></li>
			<li><?php esc_html_e( 'Create a key in a project without billing to stay on the free tier.', 'fusionwrite-ai' ); ?></li>
			<li><?php esc_html_e( 'Paste it under Providers and choose Save and test.', 'fusionwrite-ai' ); ?></li>
		</ol>

		<h3>Groq</h3>
		<ol class="fw-numbered">
			<li><?php esc_html_e( 'Create an account at console.groq.com and open API Keys.', 'fusionwrite-ai' ); ?></li>
			<li><?php esc_html_e( 'Create a key and copy it. It is shown only once.', 'fusionwrite-ai' ); ?></li>
			<li><?php esc_html_e( 'Paste it under Providers and choose Save and test.', 'fusionwrite-ai' ); ?></li>
		</ol>

		<p><a class="fw-btn fw-btn--small" href="<?php echo esc_url( fswrt_page_url( 'providers' ) ); ?>"><?php esc_html_e( 'Open Providers', 'fusionwrite-ai' ); ?></a></p>
	</section>

	<section class="fw-section">
		<h2><?php esc_html_e( 'What happens when you write a post', 'fusionwrite-ai' ); ?></h2>
		<ol class="fw-numbered">
			<li><?php esc_html_e( 'Your topic and options are sent to the provider you chose, which writes the post.', 'fusionwrite-ai' ); ?></li>
			<li><?php esc_html_e( 'The post is saved to WordPress straight away, as a draft unless you changed that in Preferences.', 'fusionwrite-ai' ); ?></li>
			<li><?php esc_html_e( 'Images are made one at a time and added to the saved post: first the featured image, then the ones inside the text.', 'fusionwrite-ai' ); ?></li>
			<li><?php esc_html_e( 'The post opens in an editor on the same page, where you can change it and save again.', 'fusionwrite-ai' ); ?></li>
		</ol>
	</section>

	<section class="fw-section">
		<h2><?php esc_html_e( 'When something fails', 'fusionwrite-ai' ); ?></h2>
		<dl class="fw-faq">
			<dt><?php esc_html_e( 'The provider is limiting requests', 'fusionwrite-ai' ); ?></dt>
			<dd><?php esc_html_e( 'Free plans allow a small number of requests per minute and per day. Wait a minute and try again, or switch to another connected provider for this post.', 'fusionwrite-ai' ); ?></dd>
			<dt><?php esc_html_e( 'The provider does not know the model', 'fusionwrite-ai' ); ?></dt>
			<dd><?php esc_html_e( 'Providers retire models regularly. Under Providers, clear the model field to let FusionWrite choose, or load the list and pick a current one.', 'fusionwrite-ai' ); ?></dd>
			<dt><?php esc_html_e( 'The post was written but an image is missing', 'fusionwrite-ai' ); ?></dt>
			<dd><?php esc_html_e( 'The post is saved either way. The free image service allows one image every 45 seconds and sometimes refuses; add the missing image from the WordPress editor, or write with “Featured image” only.', 'fusionwrite-ai' ); ?></dd>
			<dt><?php esc_html_e( 'The request times out', 'fusionwrite-ai' ); ?></dt>
			<dd><?php esc_html_e( 'Long posts can take a couple of minutes, and some hosts stop requests sooner. Choose a shorter length or a faster provider such as Groq.', 'fusionwrite-ai' ); ?></dd>
		</dl>
	</section>

</div>
