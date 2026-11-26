<?php
/**
 * Providers page: API keys and models for writing.
 *
 * @package FusionWrite_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$fswrt_settings  = fswrt_get_settings();
$fswrt_providers = fswrt_providers();
$fswrt_connected = fswrt_connected_providers();
$fswrt_default   = $fswrt_connected ? $fswrt_connected[0] : '';

fswrt_page_head(
	__( 'AI providers', 'fusionwrite-ai' ),
	__( 'Connect one or more. A key is stored on this site and sent only to the provider it belongs to.', 'fusionwrite-ai' )
);
?>
<div class="fw-col" data-tour="providers-list">
	<?php foreach ( $fswrt_providers as $fswrt_id => $fswrt_provider ) : ?>
		<?php
		$fswrt_saved = $fswrt_settings['providers'][ $fswrt_id ];
		$fswrt_on    = fswrt_provider_is_connected( $fswrt_id );
		$fswrt_open  = ! $fswrt_connected && 'openrouter' === $fswrt_id;
		?>
		<details class="fw-provider" id="provider-<?php echo esc_attr( $fswrt_id ); ?>" data-provider="<?php echo esc_attr( $fswrt_id ); ?>" <?php echo $fswrt_open ? 'open' : ''; ?>>
			<summary>
				<span class="fw-provider-name">
					<?php echo esc_html( $fswrt_provider['label'] ); ?>
					<?php if ( $fswrt_provider['free'] ) : ?>
						<span class="fw-tag"><?php esc_html_e( 'Free plan', 'fusionwrite-ai' ); ?></span>
					<?php endif; ?>
				</span>
				<span class="fw-provider-summary"><?php echo esc_html( $fswrt_provider['summary'] ); ?></span>
				<span class="fw-status" data-state="<?php echo $fswrt_on ? 'on' : 'off'; ?>" data-fw-status>
					<?php
					if ( $fswrt_on && $fswrt_id === $fswrt_default ) {
						esc_html_e( 'Connected, used by default', 'fusionwrite-ai' );
					} elseif ( $fswrt_on ) {
						esc_html_e( 'Connected', 'fusionwrite-ai' );
					} else {
						esc_html_e( 'Not connected', 'fusionwrite-ai' );
					}
					?>
				</span>
			</summary>

			<form class="fw-form fw-provider-form" novalidate>
				<?php if ( 'custom' === $fswrt_id ) : ?>
					<div class="fw-field">
						<label for="fw-base-custom"><?php esc_html_e( 'Base URL', 'fusionwrite-ai' ); ?></label>
						<input class="fw-input" type="url" id="fw-base-custom" name="base_url" value="<?php echo esc_attr( $fswrt_saved['base_url'] ); ?>" placeholder="https://api.example.com/v1" autocomplete="off" spellcheck="false">
						<p class="fw-hint">
							<?php esc_html_e( 'Fill in:', 'fusionwrite-ai' ); ?>
							<?php foreach ( fswrt_custom_presets() as $fswrt_name => $fswrt_url ) : ?>
								<button type="button" class="fw-link" data-fw-preset="<?php echo esc_attr( $fswrt_url ); ?>"><?php echo esc_html( $fswrt_name ); ?></button>
							<?php endforeach; ?>
						</p>
					</div>
				<?php endif; ?>

				<div class="fw-field">
					<label for="fw-key-<?php echo esc_attr( $fswrt_id ); ?>"><?php esc_html_e( 'API key', 'fusionwrite-ai' ); ?></label>
					<input class="fw-input" type="password" id="fw-key-<?php echo esc_attr( $fswrt_id ); ?>" name="key" value="" placeholder="<?php echo '' !== $fswrt_saved['key'] ? esc_attr( fswrt_mask_key( $fswrt_saved['key'] ) ) : esc_attr__( 'Paste your key', 'fusionwrite-ai' ); ?>" autocomplete="off" spellcheck="false">
					<p class="fw-hint">
						<?php if ( '' !== $fswrt_saved['key'] ) : ?>
							<?php esc_html_e( 'A key is saved. Leave this empty to keep it.', 'fusionwrite-ai' ); ?>
						<?php elseif ( 'custom' === $fswrt_id ) : ?>
							<?php esc_html_e( 'Leave empty for a local server that needs no key.', 'fusionwrite-ai' ); ?>
						<?php endif; ?>
						<?php if ( '' !== $fswrt_provider['key_url'] ) : ?>
							<?php /* translators: %s: provider name. */ ?>
							<a href="<?php echo esc_url( $fswrt_provider['key_url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( sprintf( __( 'Get a key from %s', 'fusionwrite-ai' ), $fswrt_provider['label'] ) ); ?></a>
						<?php endif; ?>
					</p>
				</div>

				<div class="fw-field">
					<label for="fw-model-<?php echo esc_attr( $fswrt_id ); ?>"><?php esc_html_e( 'Model', 'fusionwrite-ai' ); ?></label>
					<div class="fw-inline">
						<input class="fw-input" type="text" id="fw-model-<?php echo esc_attr( $fswrt_id ); ?>" name="model" value="<?php echo esc_attr( $fswrt_saved['model'] ); ?>" placeholder="<?php echo esc_attr( $fswrt_provider['auto'] ); ?>" list="fw-models-<?php echo esc_attr( $fswrt_id ); ?>" autocomplete="off" spellcheck="false">
						<button type="button" class="fw-btn fw-btn--small" data-fw-load-models><?php esc_html_e( 'Load models', 'fusionwrite-ai' ); ?></button>
					</div>
					<datalist id="fw-models-<?php echo esc_attr( $fswrt_id ); ?>"></datalist>
					<?php if ( 'openrouter' === $fswrt_id ) : ?>
						<label class="fw-check"><input type="checkbox" data-fw-free-only checked> <span><?php esc_html_e( 'List free models only', 'fusionwrite-ai' ); ?></span></label>
					<?php endif; ?>
					<p class="fw-hint" data-fw-model-hint><?php esc_html_e( 'Leave empty to let FusionWrite choose. Model names change often, so loading the list is the safest way to pick one.', 'fusionwrite-ai' ); ?></p>
				</div>

				<label class="fw-check"><input type="checkbox" name="make_default" value="1" <?php checked( $fswrt_id, $fswrt_default ); ?>> <span><?php esc_html_e( 'Use this provider by default', 'fusionwrite-ai' ); ?></span></label>

				<div class="fw-actions">
					<button type="submit" class="fw-btn fw-btn--primary"><?php esc_html_e( 'Save and test', 'fusionwrite-ai' ); ?></button>
					<button type="button" class="fw-btn fw-btn--quiet" data-fw-disconnect <?php echo $fswrt_on ? '' : 'hidden'; ?>><?php esc_html_e( 'Disconnect', 'fusionwrite-ai' ); ?></button>
				</div>
				<p class="fw-result-line" data-fw-result role="status"></p>
			</form>
		</details>
	<?php endforeach; ?>
</div>
