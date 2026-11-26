<?php
/**
 * AJAX endpoints used by the workspace.
 *
 * @package FusionWrite_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

foreach ( array( 'write_post', 'add_image', 'update_post', 'save_provider', 'disconnect_provider', 'list_models', 'save_images', 'test_image', 'save_preferences', 'tour_done' ) as $fswrt_action ) {
	add_action( 'wp_ajax_fswrt_' . $fswrt_action, 'fswrt_ajax_' . $fswrt_action );
}
unset( $fswrt_action );

/**
 * Check the nonce and the capability, or stop.
 */
function fswrt_ajax_guard() {
	check_ajax_referer( 'fswrt_nonce', 'nonce' );
	if ( ! current_user_can( fswrt_capability() ) ) {
		wp_send_json_error( array( 'message' => __( 'You are not allowed to do that.', 'fusionwrite-ai' ) ), 403 );
	}
}

/**
 * Read a text field from the request.
 *
 * @param string $name    Field name.
 * @param string $default Value when the field is missing.
 * @return string
 */
function fswrt_post_text( $name, $default = '' ) {
	// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Every caller runs fswrt_ajax_guard() first.
	return isset( $_POST[ $name ] ) ? sanitize_text_field( wp_unslash( $_POST[ $name ] ) ) : $default;
}

/**
 * Send a WP_Error as a JSON error.
 *
 * @param WP_Error $error Error.
 */
function fswrt_send_error( $error ) {
	$data    = (array) $error->get_error_data();
	$payload = array( 'message' => $error->get_error_message() );
	if ( isset( $data['retry_after'] ) ) {
		$payload['retry_after'] = (int) $data['retry_after'];
	}
	wp_send_json_error( $payload );
}

/**
 * Write one post.
 */
function fswrt_ajax_write_post() {
	fswrt_ajax_guard();

	$settings  = fswrt_get_settings();
	$connected = fswrt_connected_providers();
	$provider  = fswrt_post_text( 'provider' );
	if ( ! in_array( $provider, $connected, true ) ) {
		$provider = $connected ? $connected[0] : '';
	}
	if ( '' === $provider ) {
		wp_send_json_error( array( 'message' => __( 'Connect an AI provider first, on the Providers page.', 'fusionwrite-ai' ) ) );
	}

	$keyword = fswrt_post_text( 'keyword' );
	if ( '' === $keyword ) {
		wp_send_json_error( array( 'message' => __( 'Enter a topic for the post.', 'fusionwrite-ai' ) ) );
	}

	$language = fswrt_post_text( 'language', $settings['defaults']['language'] );
	$tone     = fswrt_post_text( 'tone', $settings['defaults']['tone'] );
	// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Checked in fswrt_ajax_guard().
	$custom = isset( $_POST['custom'] ) ? sanitize_textarea_field( wp_unslash( $_POST['custom'] ) ) : '';

	$result = fswrt_write_post(
		array(
			'keyword'  => $keyword,
			'provider' => $provider,
			'words'    => max( 300, min( 5000, (int) fswrt_post_text( 'words', (string) $settings['defaults']['words'] ) ) ),
			'language' => in_array( $language, fswrt_languages(), true ) ? $language : $settings['defaults']['language'],
			'tone'     => in_array( $tone, fswrt_tones(), true ) ? $tone : $settings['defaults']['tone'],
			'custom'   => $custom,
		)
	);

	if ( is_wp_error( $result ) ) {
		fswrt_send_error( $result );
	}
	wp_send_json_success( $result );
}

/**
 * Create one image for a post.
 */
function fswrt_ajax_add_image() {
	fswrt_ajax_guard();

	$post_id = (int) fswrt_post_text( 'post_id' );
	$slot    = max( 0, min( 2, (int) fswrt_post_text( 'slot' ) ) );
	if ( ! $post_id || ! current_user_can( 'edit_post', $post_id ) ) {
		wp_send_json_error( array( 'message' => __( 'You cannot edit that post.', 'fusionwrite-ai' ) ) );
	}

	$result = fswrt_add_post_image( $post_id, $slot );
	if ( is_wp_error( $result ) ) {
		fswrt_send_error( $result );
	}
	wp_send_json_success( $result );
}

/**
 * Save edits made in the workspace editor.
 */
function fswrt_ajax_update_post() {
	fswrt_ajax_guard();

	$post_id = (int) fswrt_post_text( 'post_id' );
	if ( ! $post_id || ! current_user_can( 'edit_post', $post_id ) ) {
		wp_send_json_error( array( 'message' => __( 'You cannot edit that post.', 'fusionwrite-ai' ) ) );
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Checked in fswrt_ajax_guard().
	$content = isset( $_POST['content'] ) ? wp_kses_post( wp_unslash( $_POST['content'] ) ) : '';
	$title   = fswrt_post_text( 'title' );

	$saved = wp_update_post(
		wp_slash(
			array(
				'ID'           => $post_id,
				'post_title'   => $title,
				'post_content' => $content,
			)
		),
		true
	);
	if ( is_wp_error( $saved ) ) {
		fswrt_send_error( $saved );
	}
	wp_send_json_success( array( 'message' => __( 'Changes saved', 'fusionwrite-ai' ) ) );
}

/**
 * Save one provider's key and model, then test the connection.
 */
function fswrt_ajax_save_provider() {
	fswrt_ajax_guard();

	$id        = sanitize_key( fswrt_post_text( 'provider' ) );
	$providers = fswrt_providers();
	if ( ! isset( $providers[ $id ] ) ) {
		wp_send_json_error( array( 'message' => __( 'Unknown provider.', 'fusionwrite-ai' ) ) );
	}

	$settings = fswrt_get_settings();
	$key      = preg_replace( '/\s+/', '', fswrt_post_text( 'key' ) );

	// An empty key field means "keep the saved key".
	if ( '' !== $key ) {
		$settings['providers'][ $id ]['key'] = $key;
	}
	$settings['providers'][ $id ]['model'] = fswrt_post_text( 'model' );

	if ( 'custom' === $id ) {
		$base = esc_url_raw( fswrt_post_text( 'base_url' ), array( 'http', 'https' ) );
		$base = untrailingslashit( preg_replace( '#/chat/completions/?$#', '', $base ) );
		if ( '' === $base ) {
			wp_send_json_error( array( 'message' => __( 'Enter the base URL of the API, for example https://api.mistral.ai/v1', 'fusionwrite-ai' ) ) );
		}
		$settings['providers']['custom']['base_url'] = $base;
	} elseif ( '' === $settings['providers'][ $id ]['key'] ) {
		wp_send_json_error( array( 'message' => __( 'Paste an API key first.', 'fusionwrite-ai' ) ) );
	}

	if ( '1' === fswrt_post_text( 'make_default' ) || '' === $settings['default_provider'] ) {
		$settings['default_provider'] = $id;
	}

	fswrt_update_settings( $settings );

	$test    = fswrt_test_provider( $id );
	$payload = array(
		'provider' => $id,
		'masked'   => fswrt_mask_key( $settings['providers'][ $id ]['key'] ),
		'default'  => $settings['default_provider'],
	);

	if ( is_wp_error( $test ) ) {
		$payload['working'] = false;
		$payload['message'] = $test->get_error_message();
	} else {
		$payload['working'] = true;
		/* translators: %s: model name. */
		$payload['message'] = sprintf( __( 'Connected. The test was answered by %s.', 'fusionwrite-ai' ), $test['model'] );
	}

	wp_send_json_success( $payload );
}

/**
 * Remove a provider's key.
 */
function fswrt_ajax_disconnect_provider() {
	fswrt_ajax_guard();

	$id       = sanitize_key( fswrt_post_text( 'provider' ) );
	$settings = fswrt_get_settings();
	$defaults = fswrt_default_settings();
	if ( ! isset( $settings['providers'][ $id ] ) ) {
		wp_send_json_error( array( 'message' => __( 'Unknown provider.', 'fusionwrite-ai' ) ) );
	}

	$settings['providers'][ $id ] = $defaults['providers'][ $id ];
	if ( $settings['default_provider'] === $id ) {
		$settings['default_provider'] = '';
	}
	fswrt_update_settings( $settings );

	$connected = fswrt_connected_providers();
	if ( '' === $settings['default_provider'] && $connected ) {
		$settings['default_provider'] = $connected[0];
		fswrt_update_settings( $settings );
	}

	wp_send_json_success( array( 'message' => __( 'Disconnected', 'fusionwrite-ai' ) ) );
}

/**
 * List a provider's models.
 */
function fswrt_ajax_list_models() {
	fswrt_ajax_guard();

	$id   = sanitize_key( fswrt_post_text( 'provider' ) );
	$want = 'image' === fswrt_post_text( 'want' ) ? 'image' : 'text';

	// Let the list be loaded with values that are typed in but not saved yet.
	$settings = fswrt_get_settings();
	$key      = preg_replace( '/\s+/', '', fswrt_post_text( 'key' ) );
	$base     = esc_url_raw( fswrt_post_text( 'base_url' ), array( 'http', 'https' ) );
	if ( isset( $settings['providers'][ $id ] ) && ( '' !== $key || ( 'custom' === $id && '' !== $base ) ) ) {
		if ( '' !== $key ) {
			$settings['providers'][ $id ]['key'] = $key;
		}
		if ( 'custom' === $id && '' !== $base ) {
			$settings['providers']['custom']['base_url'] = untrailingslashit( $base );
		}
		fswrt_use_settings_for_request( $settings );
	}

	$models = fswrt_list_models( $id, $want, true );
	if ( is_wp_error( $models ) ) {
		fswrt_send_error( $models );
	}
	if ( ! $models ) {
		wp_send_json_error( array( 'message' => __( 'The API listed no matching models.', 'fusionwrite-ai' ) ) );
	}
	wp_send_json_success( array( 'models' => $models ) );
}

/**
 * Save the image settings.
 */
function fswrt_ajax_save_images() {
	fswrt_ajax_guard();

	$settings = fswrt_get_settings();
	$engine   = sanitize_key( fswrt_post_text( 'engine' ) );
	if ( ! array_key_exists( $engine, fswrt_image_engines() ) ) {
		$engine = 'none';
	}
	$quality = fswrt_post_text( 'openai_quality', 'medium' );

	$settings['images']['engine']           = $engine;
	$settings['images']['count']            = 3 === (int) fswrt_post_text( 'count', '1' ) ? 3 : 1;
	$settings['images']['style']            = mb_substr( fswrt_post_text( 'style' ), 0, 200 );
	$settings['images']['openai_model']     = fswrt_post_text( 'openai_model', 'gpt-image-1' );
	$settings['images']['openai_quality']   = in_array( $quality, array( 'low', 'medium', 'high' ), true ) ? $quality : 'medium';
	$settings['images']['openrouter_model'] = fswrt_post_text( 'openrouter_model' );

	$stability = preg_replace( '/\s+/', '', fswrt_post_text( 'stability_key' ) );
	if ( '' !== $stability ) {
		$settings['images']['stability_key'] = $stability;
	} elseif ( '1' === fswrt_post_text( 'stability_clear' ) ) {
		$settings['images']['stability_key'] = '';
	}

	fswrt_update_settings( $settings );

	wp_send_json_success(
		array(
			'message' => __( 'Image settings saved', 'fusionwrite-ai' ),
			'problem' => 'none' === $engine ? '' : fswrt_image_engine_problem( $engine ),
		)
	);
}

/**
 * Create a sample image with the saved settings, without keeping it.
 */
function fswrt_ajax_test_image() {
	fswrt_ajax_guard();

	$settings = fswrt_get_settings();
	$style    = trim( $settings['images']['style'] );
	$image    = fswrt_generate_image( 'A writing desk by a window with a notebook, a pen and a cup of tea. ' . ( $style ? $style . '. ' : '' ) . 'No text or watermarks.' );
	if ( is_wp_error( $image ) ) {
		fswrt_send_error( $image );
	}

	$info = getimagesizefromstring( $image['bytes'] );
	if ( ! $info ) {
		wp_send_json_error( array( 'message' => __( 'The image service returned something that is not an image.', 'fusionwrite-ai' ) ) );
	}

	wp_send_json_success(
		array(
			// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode -- Inline preview of the sample image.
			'src' => 'data:' . $info['mime'] . ';base64,' . base64_encode( $image['bytes'] ),
		)
	);
}

/**
 * Save the writing preferences.
 */
function fswrt_ajax_save_preferences() {
	fswrt_ajax_guard();

	$settings = fswrt_get_settings();
	$language = fswrt_post_text( 'language' );
	$tone     = fswrt_post_text( 'tone' );
	$status   = fswrt_post_text( 'post_status' );
	$default  = sanitize_key( fswrt_post_text( 'default_provider' ) );

	if ( in_array( $language, fswrt_languages(), true ) ) {
		$settings['defaults']['language'] = $language;
	}
	if ( in_array( $tone, fswrt_tones(), true ) ) {
		$settings['defaults']['tone'] = $tone;
	}
	if ( in_array( $status, array( 'draft', 'pending', 'publish' ), true ) ) {
		$settings['defaults']['post_status'] = $status;
	}
	if ( fswrt_provider_is_connected( $default ) ) {
		$settings['default_provider'] = $default;
	}
	$settings['defaults']['words']      = max( 300, min( 5000, (int) fswrt_post_text( 'words', '1500' ) ) );
	$settings['defaults']['category']   = max( 0, (int) fswrt_post_text( 'category', '0' ) );
	$settings['defaults']['fullscreen'] = '1' === fswrt_post_text( 'fullscreen' ) ? 1 : 0;

	fswrt_update_settings( $settings );

	wp_send_json_success(
		array(
			'message'    => __( 'Preferences saved', 'fusionwrite-ai' ),
			'fullscreen' => $settings['defaults']['fullscreen'],
		)
	);
}

/**
 * Remember that this user has seen the tour.
 */
function fswrt_ajax_tour_done() {
	fswrt_ajax_guard();
	update_user_meta( get_current_user_id(), 'fswrt_tour_done', FSWRT_VERSION );
	wp_send_json_success();
}
