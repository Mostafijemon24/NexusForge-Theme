<?php
/**
 * Images page: how post images are made.
 *
 * @package FusionWrite_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$fswrt_settings = fswrt_get_settings();
$fswrt_images   = $fswrt_settings['images'];
$fswrt_engines  = fswrt_image_engines();

fswrt_page_head(
	__( 'Images', 'fusionwrite-ai' ),
	__( 'Choose the service that makes the featured image and the images inside a post.', 'fusionwrite-ai' )
);
?>
<div class="fw-col">
	<form id="fw-images-form" class="fw-form" novalidate>

		<fieldset class="fw-field" data-tour="image-engines">
			<legend><?php esc_html_e( 'Image service', 'fusionwrite-ai' ); ?></legend>
			<div class="fw-choices">
				<?php foreach ( $fswrt_engines as $fswrt_id => $fswrt_engine ) : ?>
					<label class="fw-choice">
						<input type="radio" name="engine" value="<?php echo esc_attr( $fswrt_id ); ?>" <?php checked( $fswrt_id, $fswrt_images['engine'] ); ?>>
						<span class="fw-choice-body">
							<strong>
								<?php echo esc_html( $fswrt_engine['label'] ); ?>
								<?php if ( $fswrt_engine['free'] ) : ?>
									<span class="fw-tag"><?php esc_html_e( 'Free', 'fusionwrite-ai' ); ?></span>
								<?php endif; ?>
							</strong>
							<span><?php echo esc_html( $fswrt_engine['summary'] ); ?></span>
						</span>
					</label>
				<?php endforeach; ?>
			</div>
		</fieldset>

		<div class="fw-engine-fields" data-fw-engine="openrouter" hidden>
			<div class="fw-field">
				<label for="fw-openrouter-image-model"><?php esc_html_e( 'OpenRouter image model', 'fusionwrite-ai' ); ?></label>
				<div class="fw-inline">
					<input class="fw-input" type="text" id="fw-openrouter-image-model" name="openrouter_model" value="<?php echo esc_attr( $fswrt_images['openrouter_model'] ); ?>" list="fw-openrouter-image-models" placeholder="<?php esc_attr_e( 'Load the list and pick one', 'fusionwrite-ai' ); ?>" autocomplete="off" spellcheck="false">
					<button type="button" class="fw-btn fw-btn--small" data-fw-load-image-models><?php esc_html_e( 'Load image models', 'fusionwrite-ai' ); ?></button>
				</div>
				<datalist id="fw-openrouter-image-models"></datalist>
				<p class="fw-hint" data-fw-model-hint><?php esc_html_e( 'Only models that can output images are listed.', 'fusionwrite-ai' ); ?></p>
			</div>
		</div>

		<div class="fw-engine-fields" data-fw-engine="openai" hidden>
			<div class="fw-fields fw-fields--two">
				<div class="fw-field">
					<label for="fw-openai-image-model"><?php esc_html_e( 'OpenAI image model', 'fusionwrite-ai' ); ?></label>
					<input class="fw-input" type="text" id="fw-openai-image-model" name="openai_model" value="<?php echo esc_attr( $fswrt_images['openai_model'] ); ?>" list="fw-openai-image-models" autocomplete="off" spellcheck="false">
					<datalist id="fw-openai-image-models"><option value="gpt-image-1"></option><option value="gpt-image-1-mini"></option></datalist>
				</div>
				<div class="fw-field">
					<label for="fw-openai-quality"><?php esc_html_e( 'Quality', 'fusionwrite-ai' ); ?></label>
					<select class="fw-input fw-select" id="fw-openai-quality" name="openai_quality">
						<option value="low" <?php selected( 'low', $fswrt_images['openai_quality'] ); ?>><?php esc_html_e( 'Low, cheapest', 'fusionwrite-ai' ); ?></option>
						<option value="medium" <?php selected( 'medium', $fswrt_images['openai_quality'] ); ?>><?php esc_html_e( 'Medium', 'fusionwrite-ai' ); ?></option>
						<option value="high" <?php selected( 'high', $fswrt_images['openai_quality'] ); ?>><?php esc_html_e( 'High', 'fusionwrite-ai' ); ?></option>
					</select>
				</div>
			</div>
			<p class="fw-hint"><?php esc_html_e( 'OpenAI removed DALL-E 3 from its API in May 2026. The GPT Image models replace it.', 'fusionwrite-ai' ); ?></p>
		</div>

		<div class="fw-engine-fields" data-fw-engine="stability" hidden>
			<div class="fw-field">
				<label for="fw-stability-key"><?php esc_html_e( 'Stability AI key', 'fusionwrite-ai' ); ?></label>
				<input class="fw-input" type="password" id="fw-stability-key" name="stability_key" value="" placeholder="<?php echo '' !== $fswrt_images['stability_key'] ? esc_attr( fswrt_mask_key( $fswrt_images['stability_key'] ) ) : esc_attr__( 'Paste your key', 'fusionwrite-ai' ); ?>" autocomplete="off" spellcheck="false">
				<p class="fw-hint">
					<?php if ( '' !== $fswrt_images['stability_key'] ) : ?>
						<?php esc_html_e( 'A key is saved. Leave this empty to keep it.', 'fusionwrite-ai' ); ?>
					<?php endif; ?>
					<a href="https://platform.stability.ai/account/keys" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Get a key from Stability AI', 'fusionwrite-ai' ); ?></a>
				</p>
			</div>
		</div>

		<div class="fw-engine-fields" data-fw-engine-not="none">
			<fieldset class="fw-field">
				<legend><?php esc_html_e( 'Images per post, unless you choose otherwise', 'fusionwrite-ai' ); ?></legend>
				<div class="fw-segments">
					<label><input type="radio" name="count" value="1" <?php checked( 3 !== (int) $fswrt_images['count'] ); ?>><span><?php esc_html_e( 'Featured image', 'fusionwrite-ai' ); ?></span></label>
					<label><input type="radio" name="count" value="3" <?php checked( 3, (int) $fswrt_images['count'] ); ?>><span><?php esc_html_e( 'Featured and 2 in the post', 'fusionwrite-ai' ); ?></span></label>
				</div>
			</fieldset>

			<div class="fw-field">
				<label for="fw-image-style"><?php esc_html_e( 'Look of the images', 'fusionwrite-ai' ); ?></label>
				<input class="fw-input" type="text" id="fw-image-style" name="style" maxlength="200" value="<?php echo esc_attr( $fswrt_images['style'] ); ?>" placeholder="<?php esc_attr_e( 'Soft watercolour, muted colours', 'fusionwrite-ai' ); ?>">
				<p class="fw-hint"><?php esc_html_e( 'A few words added to every image request. Each image is also told what its part of the post is about.', 'fusionwrite-ai' ); ?></p>
			</div>
		</div>

		<div class="fw-actions">
			<button type="submit" class="fw-btn fw-btn--primary"><?php esc_html_e( 'Save image settings', 'fusionwrite-ai' ); ?></button>
			<button type="button" class="fw-btn" id="fw-test-image"><?php esc_html_e( 'Save and create a sample', 'fusionwrite-ai' ); ?></button>
		</div>
		<p class="fw-result-line" data-fw-result role="status"></p>
		<figure class="fw-sample" id="fw-sample" hidden><img alt="<?php esc_attr_e( 'Sample image: a writing desk by a window', 'fusionwrite-ai' ); ?>"></figure>
	</form>
</div>
