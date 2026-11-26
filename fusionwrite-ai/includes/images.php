<?php
/**
 * Image engines and saving images to the media library.
 *
 * @package FusionWrite_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Image engines the plugin can use.
 *
 * `wait` is the pause, in seconds, the service asks for between two images.
 *
 * @return array<string,array<string,mixed>>
 */
function fswrt_image_engines() {
	return array(
		'none'         => array(
			'label'   => __( 'No images', 'fusionwrite-ai' ),
			'summary' => __( 'Posts are written without images.', 'fusionwrite-ai' ),
			'free'    => false,
			'wait'    => 0,
		),
		'pollinations' => array(
			'label'   => 'Pollinations',
			'summary' => __( 'Free and needs no key. One image every 45 seconds, with a small Pollinations logo in the corner.', 'fusionwrite-ai' ),
			'free'    => true,
			'wait'    => 46,
		),
		'openrouter'   => array(
			'label'   => 'OpenRouter',
			'summary' => __( 'Uses the OpenRouter key from the Providers page with an image model of your choice.', 'fusionwrite-ai' ),
			'free'    => false,
			'wait'    => 0,
		),
		'openai'       => array(
			'label'   => 'OpenAI',
			'summary' => __( 'Uses the OpenAI key from the Providers page with a GPT Image model.', 'fusionwrite-ai' ),
			'free'    => false,
			'wait'    => 0,
		),
		'stability'    => array(
			'label'   => 'Stability AI',
			'summary' => __( 'Stable Image Core, with its own key.', 'fusionwrite-ai' ),
			'free'    => false,
			'wait'    => 0,
		),
	);
}

/**
 * Why the chosen image engine cannot be used yet.
 *
 * @param string $engine Engine id.
 * @return string Empty when the engine is ready.
 */
function fswrt_image_engine_problem( $engine ) {
	$settings = fswrt_get_settings();

	switch ( $engine ) {
		case 'openrouter':
			if ( '' === $settings['providers']['openrouter']['key'] ) {
				return __( 'Connect OpenRouter on the Providers page first.', 'fusionwrite-ai' );
			}
			if ( '' === $settings['images']['openrouter_model'] ) {
				return __( 'Choose an OpenRouter image model.', 'fusionwrite-ai' );
			}
			return '';
		case 'openai':
			return '' === $settings['providers']['openai']['key'] ? __( 'Connect OpenAI on the Providers page first.', 'fusionwrite-ai' ) : '';
		case 'stability':
			return '' === $settings['images']['stability_key'] ? __( 'Add a Stability AI key.', 'fusionwrite-ai' ) : '';
		case 'none':
			return __( 'Images are turned off.', 'fusionwrite-ai' );
	}

	return '';
}

/**
 * Create one image.
 *
 * @param string $prompt What the image should show.
 * @return array{bytes:string}|WP_Error
 */
function fswrt_generate_image( $prompt ) {
	$settings = fswrt_get_settings();
	$engine   = $settings['images']['engine'];
	$engines  = fswrt_image_engines();

	$problem = fswrt_image_engine_problem( $engine );
	if ( '' !== $problem || ! isset( $engines[ $engine ] ) ) {
		return new WP_Error( 'fswrt_image_setup', $problem ? $problem : __( 'Unknown image engine.', 'fusionwrite-ai' ) );
	}

	if ( function_exists( 'set_time_limit' ) ) {
		set_time_limit( 240 );
	}

	$prompt = trim( preg_replace( '/\s+/', ' ', $prompt ) );

	if ( 'pollinations' === $engine ) {
		// The free service allows at most 768 x 768 pixels in area; this is the 16:9 size that fits.
		$url      = 'https://image.pollinations.ai/prompt/' . rawurlencode( mb_substr( $prompt, 0, 600 ) ) . '?width=1024&height=576&seed=' . wp_rand( 1, 999999 );
		$response = wp_remote_get( $url, array( 'timeout' => 120 ) );
		$error    = fswrt_response_error( $response, 'Pollinations' );
		if ( $error ) {
			$data = (array) $error->get_error_data();
			if ( isset( $data['status'] ) && in_array( (int) $data['status'], array( 402, 429 ), true ) ) {
				$wait = isset( $data['retry_after'] ) ? (int) $data['retry_after'] : 45;
				return new WP_Error(
					'fswrt_image_wait',
					__( 'Pollinations allows one free image every 45 seconds.', 'fusionwrite-ai' ),
					array( 'retry_after' => max( 5, min( 120, $wait ) ) )
				);
			}
			return $error;
		}
		return array( 'bytes' => wp_remote_retrieve_body( $response ) );
	}

	if ( 'openrouter' === $engine ) {
		$response = wp_remote_post(
			'https://openrouter.ai/api/v1/chat/completions',
			array(
				'headers' => fswrt_provider_headers( 'openrouter' ),
				'timeout' => 180,
				'body'    => wp_json_encode(
					array(
						'model'      => $settings['images']['openrouter_model'],
						'modalities' => array( 'image', 'text' ),
						'messages'   => array(
							array(
								'role'    => 'user',
								'content' => 'Generate a wide 16:9 image. ' . $prompt,
							),
						),
					)
				),
			)
		);
		$error    = fswrt_response_error( $response, 'OpenRouter' );
		if ( $error ) {
			return $error;
		}

		$data    = json_decode( wp_remote_retrieve_body( $response ), true );
		$message = isset( $data['choices'][0]['message'] ) ? (array) $data['choices'][0]['message'] : array();
		$source  = '';
		if ( isset( $message['images'][0]['image_url']['url'] ) ) {
			$source = (string) $message['images'][0]['image_url']['url'];
		} elseif ( isset( $message['content'] ) && is_array( $message['content'] ) ) {
			foreach ( $message['content'] as $part ) {
				if ( isset( $part['image_url']['url'] ) ) {
					$source = (string) $part['image_url']['url'];
					break;
				}
			}
		}
		return fswrt_image_from_source( $source, 'OpenRouter' );
	}

	if ( 'openai' === $engine ) {
		$model = $settings['images']['openai_model'] ? $settings['images']['openai_model'] : 'gpt-image-1';
		$body  = array(
			'model'  => $model,
			'prompt' => $prompt,
			'n'      => 1,
			'size'   => '1024x1024',
		);
		if ( 0 === strpos( $model, 'gpt-image' ) ) {
			$body['size']    = '1536x1024';
			$body['quality'] = $settings['images']['openai_quality'];
		}

		$response = wp_remote_post(
			'https://api.openai.com/v1/images/generations',
			array(
				'headers' => fswrt_provider_headers( 'openai' ),
				'timeout' => 180,
				'body'    => wp_json_encode( $body ),
			)
		);
		$error    = fswrt_response_error( $response, 'OpenAI' );
		if ( $error ) {
			return $error;
		}

		$data = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( isset( $data['data'][0]['b64_json'] ) ) {
			return fswrt_image_from_source( 'data:image/png;base64,' . $data['data'][0]['b64_json'], 'OpenAI' );
		}
		return fswrt_image_from_source( isset( $data['data'][0]['url'] ) ? (string) $data['data'][0]['url'] : '', 'OpenAI' );
	}

	// Stability AI.
	$boundary = 'fswrt' . wp_generate_password( 24, false );
	$fields   = array(
		'prompt'        => $prompt,
		'aspect_ratio'  => '16:9',
		'output_format' => 'jpeg',
	);
	$payload  = '';
	foreach ( $fields as $name => $value ) {
		$payload .= '--' . $boundary . "\r\n" . 'Content-Disposition: form-data; name="' . $name . '"' . "\r\n\r\n" . $value . "\r\n";
	}
	$payload .= '--' . $boundary . '--' . "\r\n";

	$response = wp_remote_post(
		'https://api.stability.ai/v2beta/stable-image/generate/core',
		array(
			'headers' => array(
				'Authorization' => 'Bearer ' . $settings['images']['stability_key'],
				'Accept'        => 'application/json',
				'Content-Type'  => 'multipart/form-data; boundary=' . $boundary,
			),
			'timeout' => 180,
			'body'    => $payload,
		)
	);
	$error    = fswrt_response_error( $response, 'Stability AI' );
	if ( $error ) {
		return $error;
	}

	$data = json_decode( wp_remote_retrieve_body( $response ), true );
	return fswrt_image_from_source( isset( $data['image'] ) ? 'data:image/jpeg;base64,' . $data['image'] : '', 'Stability AI' );
}

/**
 * Read image bytes from a data URL or a normal URL.
 *
 * @param string $source Data URL or https URL.
 * @param string $label  Name of the service, for error messages.
 * @return array{bytes:string}|WP_Error
 */
function fswrt_image_from_source( $source, $label ) {
	if ( 0 === strpos( $source, 'data:' ) ) {
		$comma = strpos( $source, ',' );
		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode -- Image data returned by the image API.
		$bytes = false !== $comma ? base64_decode( substr( $source, $comma + 1 ), true ) : false;
		if ( $bytes ) {
			return array( 'bytes' => $bytes );
		}
	} elseif ( preg_match( '#^https?://#i', $source ) ) {
		$download = wp_safe_remote_get( $source, array( 'timeout' => 60 ) );
		if ( ! is_wp_error( $download ) && 200 === (int) wp_remote_retrieve_response_code( $download ) ) {
			return array( 'bytes' => wp_remote_retrieve_body( $download ) );
		}
	}

	return new WP_Error(
		'fswrt_image_empty',
		/* translators: %s: service name. */
		sprintf( __( '%s did not return an image. Try again, or choose another image model.', 'fusionwrite-ai' ), $label )
	);
}

/**
 * Save image bytes to the media library.
 *
 * @param string $bytes   Raw image data.
 * @param int    $post_id Post the image belongs to.
 * @param string $title   Used for the file name, title and alt text.
 * @return int|WP_Error Attachment id.
 */
function fswrt_save_image( $bytes, $post_id, $title ) {
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$info = strlen( $bytes ) > 100 ? getimagesizefromstring( $bytes ) : false;
	$exts = array(
		'image/jpeg' => 'jpg',
		'image/png'  => 'png',
		'image/webp' => 'webp',
	);
	if ( ! $info || ! isset( $exts[ $info['mime'] ] ) ) {
		return new WP_Error( 'fswrt_image_invalid', __( 'The image service returned something that is not an image.', 'fusionwrite-ai' ) );
	}

	$slug = sanitize_title( $title );
	$name = ( $slug ? mb_substr( $slug, 0, 60 ) : 'fusionwrite' ) . '-' . wp_rand( 1000, 9999 ) . '.' . $exts[ $info['mime'] ];
	$file = wp_upload_bits( $name, null, $bytes );
	if ( ! empty( $file['error'] ) ) {
		return new WP_Error( 'fswrt_image_upload', $file['error'] );
	}

	$attachment_id = wp_insert_attachment(
		array(
			'post_mime_type' => $info['mime'],
			'post_title'     => $title,
			'post_status'    => 'inherit',
		),
		$file['file'],
		$post_id,
		true
	);
	if ( is_wp_error( $attachment_id ) ) {
		return $attachment_id;
	}

	wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $file['file'] ) );
	update_post_meta( $attachment_id, '_wp_attachment_image_alt', $title );

	return (int) $attachment_id;
}

/**
 * Work out what one of a post's images should show and where it goes.
 *
 * Slot 0 is the featured image. Slots 1 and 2 sit in the body, each just
 * before a section heading, and illustrate that section.
 *
 * @param string $content Post content.
 * @param string $title   Post title.
 * @param int    $slot    0, 1 or 2.
 * @return array{prompt:string,subject:string,offset:int|null}
 */
function fswrt_image_plan( $content, $title, $slot ) {
	$settings = fswrt_get_settings();
	$style    = trim( $settings['images']['style'] );
	$rules    = ( $style ? $style . '. ' : '' ) . 'Wide 16:9 composition. No text, letters, captions, logos or watermarks.';

	if ( 0 === $slot ) {
		return array(
			'prompt'  => 'Header image for a blog article titled "' . $title . '". ' . $rules,
			'subject' => $title,
			'offset'  => null,
		);
	}

	preg_match_all( '/<h2\b[^>]*>(.*?)<\/h2>/is', $content, $headings, PREG_OFFSET_CAPTURE );
	$count = count( $headings[0] );
	$index = 1 === $slot ? 1 : max( 2, (int) floor( $count * 0.66 ) );

	if ( $index < $count ) {
		$subject = trim( wp_strip_all_tags( $headings[1][ $index ][0] ) );
		return array(
			'prompt'  => 'Image illustrating "' . $subject . '", for a blog article titled "' . $title . '". ' . $rules,
			'subject' => $subject ? $subject : $title,
			'offset'  => (int) $headings[0][ $index ][1],
		);
	}

	// Too few headings: place the image after a paragraph instead.
	$after  = 1 === $slot ? 2 : 6;
	$offset = 0;
	$found  = 0;
	while ( $found < $after ) {
		$next = stripos( $content, '</p>', $offset );
		if ( false === $next ) {
			break;
		}
		$offset = $next + 4;
		++$found;
	}

	return array(
		'prompt'  => 'Image for a blog article titled "' . $title . '", showing a supporting detail of the topic. ' . $rules,
		'subject' => $title,
		'offset'  => $found === $after ? $offset : strlen( $content ),
	);
}
