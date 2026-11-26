<?php
/**
 * Preferences page: the values new posts start with.
 *
 * @package FusionWrite_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$fswrt_settings  = fswrt_get_settings();
$fswrt_defaults  = $fswrt_settings['defaults'];
$fswrt_providers = fswrt_providers();
$fswrt_connected = fswrt_connected_providers();

fswrt_page_head(
	__( 'Preferences', 'fusionwrite-ai' ),
	__( 'What a new post starts with. Every one of these can still be changed for a single post.', 'fusionwrite-ai' )
);
?>
<div class="fw-col">
	<form id="fw-preferences-form" class="fw-form" novalidate data-tour="prefs">

		<div class="fw-fields">
			<div class="fw-field">
				<label for="fw-default-provider"><?php esc_html_e( 'Written by', 'fusionwrite-ai' ); ?></label>
				<select class="fw-input fw-select" id="fw-default-provider" name="default_provider" <?php disabled( ! $fswrt_connected ); ?>>
					<?php if ( ! $fswrt_connected ) : ?>
						<option value=""><?php esc_html_e( 'Not connected', 'fusionwrite-ai' ); ?></option>
					<?php endif; ?>
					<?php foreach ( $fswrt_connected as $fswrt_id ) : ?>
						<option value="<?php echo esc_attr( $fswrt_id ); ?>"><?php echo esc_html( $fswrt_providers[ $fswrt_id ]['label'] ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="fw-field">
				<label for="language"><?php esc_html_e( 'Language', 'fusionwrite-ai' ); ?></label>
				<?php fswrt_select( 'language', fswrt_languages(), $fswrt_defaults['language'] ); ?>
			</div>
			<div class="fw-field">
				<label for="tone"><?php esc_html_e( 'Tone', 'fusionwrite-ai' ); ?></label>
				<?php fswrt_select( 'tone', fswrt_tones(), $fswrt_defaults['tone'] ); ?>
			</div>
		</div>

		<div class="fw-field fw-field--range">
			<label for="fw-words"><?php esc_html_e( 'Length', 'fusionwrite-ai' ); ?></label>
			<input class="fw-range" type="range" id="fw-words" name="words" min="300" max="5000" step="100" value="<?php echo esc_attr( $fswrt_defaults['words'] ); ?>">
			<output class="fw-range-value" for="fw-words" data-fw-words-output></output>
		</div>

		<div class="fw-fields fw-fields--two">
			<div class="fw-field">
				<label for="fw-post-status"><?php esc_html_e( 'Save new posts as', 'fusionwrite-ai' ); ?></label>
				<select class="fw-input fw-select" id="fw-post-status" name="post_status">
					<option value="draft" <?php selected( 'draft', $fswrt_defaults['post_status'] ); ?>><?php esc_html_e( 'Draft', 'fusionwrite-ai' ); ?></option>
					<option value="pending" <?php selected( 'pending', $fswrt_defaults['post_status'] ); ?>><?php esc_html_e( 'Pending review', 'fusionwrite-ai' ); ?></option>
					<option value="publish" <?php selected( 'publish', $fswrt_defaults['post_status'] ); ?>><?php esc_html_e( 'Published', 'fusionwrite-ai' ); ?></option>
				</select>
			</div>
			<div class="fw-field">
				<label for="fw-category"><?php esc_html_e( 'Category', 'fusionwrite-ai' ); ?></label>
				<?php
				wp_dropdown_categories(
					array(
						'name'            => 'category',
						'id'              => 'fw-category',
						'class'           => 'fw-input fw-select',
						'selected'        => (int) $fswrt_defaults['category'],
						'hide_empty'      => false,
						'show_option_all' => __( 'Site default', 'fusionwrite-ai' ),
					)
				);
				?>
			</div>
		</div>
		<p class="fw-hint"><?php esc_html_e( 'Published posts go live without anyone reading them first. Draft is the safer choice.', 'fusionwrite-ai' ); ?></p>

		<label class="fw-check"><input type="checkbox" name="fullscreen" value="1" <?php checked( 1, (int) $fswrt_defaults['fullscreen'] ); ?>> <span><?php esc_html_e( 'Open FusionWrite as a full page, without the WordPress menu and toolbar', 'fusionwrite-ai' ); ?></span></label>

		<div class="fw-actions">
			<button type="submit" class="fw-btn fw-btn--primary"><?php esc_html_e( 'Save preferences', 'fusionwrite-ai' ); ?></button>
		</div>
	</form>
</div>
