<?php
/**
 * Post options shared by the Write and Bulk pages.
 *
 * @package FusionWrite_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$fswrt_settings  = fswrt_get_settings();
$fswrt_providers = fswrt_providers();
$fswrt_connected = fswrt_connected_providers();
$fswrt_engines   = fswrt_image_engines();
$fswrt_engine    = $fswrt_settings['images']['engine'];
$fswrt_problem   = fswrt_image_engine_problem( $fswrt_engine );
$fswrt_images_on = '' === $fswrt_problem;
$fswrt_count     = $fswrt_images_on ? (int) $fswrt_settings['images']['count'] : 0;
?>
<div class="fw-options" data-tour="write-options">
	<div class="fw-fields">
		<div class="fw-field">
			<label for="fw-provider"><?php esc_html_e( 'Written by', 'fusionwrite-ai' ); ?></label>
			<select class="fw-input fw-select" id="fw-provider" name="fw-provider" <?php disabled( ! $fswrt_connected ); ?>>
				<?php if ( ! $fswrt_connected ) : ?>
					<option value=""><?php esc_html_e( 'Not connected', 'fusionwrite-ai' ); ?></option>
				<?php endif; ?>
				<?php foreach ( $fswrt_connected as $fswrt_id ) : ?>
					<option value="<?php echo esc_attr( $fswrt_id ); ?>"><?php echo esc_html( $fswrt_providers[ $fswrt_id ]['label'] ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="fw-field">
			<label for="fw-language"><?php esc_html_e( 'Language', 'fusionwrite-ai' ); ?></label>
			<?php fswrt_select( 'fw-language', fswrt_languages(), $fswrt_settings['defaults']['language'] ); ?>
		</div>
		<div class="fw-field">
			<label for="fw-tone"><?php esc_html_e( 'Tone', 'fusionwrite-ai' ); ?></label>
			<?php fswrt_select( 'fw-tone', fswrt_tones(), $fswrt_settings['defaults']['tone'] ); ?>
		</div>
	</div>

	<div class="fw-field fw-field--range">
		<label for="fw-words"><?php esc_html_e( 'Length', 'fusionwrite-ai' ); ?></label>
		<input class="fw-range" type="range" id="fw-words" name="fw-words" min="300" max="5000" step="100" value="<?php echo esc_attr( $fswrt_settings['defaults']['words'] ); ?>">
		<output class="fw-range-value" for="fw-words" data-fw-words-output></output>
	</div>

	<fieldset class="fw-field">
		<legend><?php esc_html_e( 'Images', 'fusionwrite-ai' ); ?></legend>
		<div class="fw-segments">
			<label><input type="radio" name="fw-images" value="0" <?php checked( 0, $fswrt_count ); ?>><span><?php esc_html_e( 'None', 'fusionwrite-ai' ); ?></span></label>
			<label><input type="radio" name="fw-images" value="1" <?php checked( 1, $fswrt_count ); ?> <?php disabled( ! $fswrt_images_on ); ?>><span><?php esc_html_e( 'Featured image', 'fusionwrite-ai' ); ?></span></label>
			<label><input type="radio" name="fw-images" value="3" <?php checked( 3, $fswrt_count ); ?> <?php disabled( ! $fswrt_images_on ); ?>><span><?php esc_html_e( 'Featured and 2 in the post', 'fusionwrite-ai' ); ?></span></label>
		</div>
		<p class="fw-hint">
			<?php
			if ( $fswrt_images_on ) {
				/* translators: %s: image engine name. */
				echo esc_html( sprintf( __( 'Made with %s.', 'fusionwrite-ai' ), $fswrt_engines[ $fswrt_engine ]['label'] ) );
			} else {
				echo esc_html( $fswrt_problem );
			}
			?>
			<a href="<?php echo esc_url( fswrt_page_url( 'images' ) ); ?>"><?php esc_html_e( 'Image settings', 'fusionwrite-ai' ); ?></a>
		</p>
	</fieldset>

	<details class="fw-more">
		<summary><?php esc_html_e( 'Custom instructions', 'fusionwrite-ai' ); ?></summary>
		<label class="screen-reader-text" for="fw-custom"><?php esc_html_e( 'Custom instructions', 'fusionwrite-ai' ); ?></label>
		<textarea class="fw-input fw-textarea" id="fw-custom" name="fw-custom" rows="4" placeholder="<?php esc_attr_e( 'Write a buying guide about {keyword} with a comparison table and a short FAQ.', 'fusionwrite-ai' ); ?>"></textarea>
		<p class="fw-hint"><?php esc_html_e( 'Replaces the standard brief, so tone and length above are ignored. {keyword} stands for the topic. The post language still applies.', 'fusionwrite-ai' ); ?></p>
	</details>
</div>
