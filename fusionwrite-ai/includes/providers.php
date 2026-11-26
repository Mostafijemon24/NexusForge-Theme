<?php
/**
 * Talking to the text providers.
 *
 * @package FusionWrite_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Base URL of a provider's API, without a trailing slash.
 *
 * @param string $id Provider id.
 * @return string
 */
function fswrt_provider_base( $id ) {
	$providers = fswrt_providers();
	if ( 'custom' === $id ) {
		$settings = fswrt_get_settings();
		return untrailingslashit( $settings['providers']['custom']['base_url'] );
	}
	return isset( $providers[ $id ] ) ? $providers[ $id ]['base'] : '';
}

/**
 * Request headers for a provider.
 *
 * @param string $id Provider id.
 * @return array<string,string>
 */
function fswrt_provider_headers( $id ) {
	$settings  = fswrt_get_settings();
	$providers = fswrt_providers();
	$key       = isset( $settings['providers'][ $id ] ) ? $settings['providers'][ $id ]['key'] : '';
	$headers   = array( 'Content-Type' => 'application/json' );

	if ( isset( $providers[ $id ] ) && 'gemini' === $providers[ $id ]['kind'] ) {
		$headers['x-goog-api-key'] = $key;
	} elseif ( '' !== $key ) {
		$headers['Authorization'] = 'Bearer ' . $key;
	}

	if ( 'openrouter' === $id ) {
		$headers['X-Title'] = 'FusionWrite AI';
	}

	return $headers;
}

/**
 * Turn a failed HTTP response into a message a site owner can act on.
 *
 * @param array|WP_Error $response Response from the HTTP API.
 * @param string         $label    Name of the service.
 * @return WP_Error|null Null when the response is a success.
 */
function fswrt_response_error( $response, $label ) {
	if ( is_wp_error( $response ) ) {
		return new WP_Error(
			'fswrt_connection',
			/* translators: 1: service name, 2: technical error message. */
			sprintf( __( 'Could not reach %1$s: %2$s', 'fusionwrite-ai' ), $label, $response->get_error_message() )
		);
	}

	$code = (int) wp_remote_retrieve_response_code( $response );
	$data = json_decode( wp_remote_retrieve_body( $response ), true );

	$detail = '';
	if ( is_array( $data ) ) {
		if ( isset( $data['error']['metadata']['raw'] ) && is_string( $data['error']['metadata']['raw'] ) ) {
			$detail = $data['error']['metadata']['raw'];
		} elseif ( isset( $data['error']['message'] ) ) {
			$detail = (string) $data['error']['message'];
		} elseif ( isset( $data['error'] ) && is_string( $data['error'] ) ) {
			$detail = $data['error'];
		} elseif ( isset( $data['message'] ) && is_string( $data['message'] ) ) {
			$detail = $data['message'];
		} elseif ( isset( $data['errors'] ) && is_array( $data['errors'] ) ) {
			$detail = implode( ' ', array_filter( $data['errors'], 'is_string' ) );
		}
	}

	if ( $code >= 200 && $code < 300 && ( ! is_array( $data ) || ! isset( $data['error'] ) ) ) {
		return null;
	}

	if ( 401 === $code || 403 === $code ) {
		/* translators: %s: service name. */
		$message = sprintf( __( '%s rejected the API key. Check that it was pasted in full and is still active.', 'fusionwrite-ai' ), $label );
	} elseif ( 402 === $code ) {
		/* translators: %s: service name. */
		$message = sprintf( __( '%s needs credits for this model. Choose a free model or add credits to the account.', 'fusionwrite-ai' ), $label );
	} elseif ( 404 === $code ) {
		/* translators: %s: service name. */
		$message = sprintf( __( '%s does not know this model. Pick another one on the Providers page.', 'fusionwrite-ai' ), $label );
	} elseif ( 429 === $code ) {
		/* translators: %s: service name. */
		$message = sprintf( __( '%s is limiting requests right now. Free plans allow only a few per minute, so wait a moment and try again.', 'fusionwrite-ai' ), $label );
	} else {
		/* translators: 1: service name, 2: HTTP status code. */
		$message = sprintf( __( '%1$s returned an error (HTTP %2$d).', 'fusionwrite-ai' ), $label, $code );
	}

	if ( '' !== $detail ) {
		$message .= ' ' . wp_strip_all_tags( mb_substr( $detail, 0, 300 ) );
	}

	$error = new WP_Error( 'fswrt_api', $message, array( 'status' => $code ) );
	$retry = (int) wp_remote_retrieve_header( $response, 'retry-after' );
	if ( $retry > 0 ) {
		$error->add_data(
			array(
				'status'      => $code,
				'retry_after' => $retry,
			)
		);
	}
	return $error;
}

/**
 * List the models a provider offers.
 *
 * @param string $id    Provider id.
 * @param string $want  "text" or "image" (image applies to OpenRouter only).
 * @param bool   $force Skip the cached list.
 * @return array<int,array{id:string,label:string,free:bool}>|WP_Error
 */
function fswrt_list_models( $id, $want = 'text', $force = false ) {
	$providers = fswrt_providers();
	if ( ! isset( $providers[ $id ] ) ) {
		return new WP_Error( 'fswrt_provider', __( 'Unknown provider.', 'fusionwrite-ai' ) );
	}

	$base = fswrt_provider_base( $id );
	if ( '' === $base ) {
		return new WP_Error( 'fswrt_provider', __( 'Enter the base URL of the API first.', 'fusionwrite-ai' ) );
	}

	$settings  = fswrt_get_settings();
	$cache_key = 'fswrt_models_' . md5( $id . '|' . $want . '|' . $base . '|' . $settings['providers'][ $id ]['key'] );
	if ( ! $force ) {
		$cached = get_transient( $cache_key );
		if ( is_array( $cached ) ) {
			return $cached;
		}
	}

	$url      = 'gemini' === $providers[ $id ]['kind'] ? $base . '/models?pageSize=1000' : $base . '/models';
	$response = wp_remote_get(
		$url,
		array(
			'headers' => fswrt_provider_headers( $id ),
			'timeout' => 30,
		)
	);

	$error = fswrt_response_error( $response, $providers[ $id ]['label'] );
	if ( $error ) {
		return $error;
	}

	$data   = json_decode( wp_remote_retrieve_body( $response ), true );
	$models = array();
	$skip   = '/whisper|tts|embed|guard|moderation|dall-e|audio|realtime|transcri|sora|babbage|davinci|speech|rerank|orpheus|aqa|imagen|veo|robotics|computer-use|-live/i';

	if ( 'gemini' === $providers[ $id ]['kind'] ) {
		$rows = isset( $data['models'] ) && is_array( $data['models'] ) ? $data['models'] : array();
		foreach ( $rows as $row ) {
			$methods = isset( $row['supportedGenerationMethods'] ) ? (array) $row['supportedGenerationMethods'] : array();
			$name    = isset( $row['name'] ) ? str_replace( 'models/', '', (string) $row['name'] ) : '';
			if ( '' === $name || ! in_array( 'generateContent', $methods, true ) || preg_match( $skip, $name ) || false !== strpos( $name, 'image' ) ) {
				continue;
			}
			$models[] = array(
				'id'    => $name,
				'label' => isset( $row['displayName'] ) ? (string) $row['displayName'] : $name,
				'free'  => false,
			);
		}
	} else {
		$rows = array();
		if ( isset( $data['data'] ) && is_array( $data['data'] ) ) {
			$rows = $data['data'];
		} elseif ( isset( $data['models'] ) && is_array( $data['models'] ) ) {
			$rows = $data['models'];
		}
		foreach ( $rows as $row ) {
			$name = isset( $row['id'] ) ? (string) $row['id'] : ( isset( $row['name'] ) ? (string) $row['name'] : '' );
			if ( '' === $name ) {
				continue;
			}

			$outputs  = isset( $row['architecture']['output_modalities'] ) ? (array) $row['architecture']['output_modalities'] : array( 'text' );
			$is_image = in_array( 'image', $outputs, true );
			if ( 'image' === $want ) {
				if ( ! $is_image ) {
					continue;
				}
			} elseif ( ! in_array( 'text', $outputs, true ) || preg_match( $skip, $name ) ) {
				continue;
			} elseif ( 'openai' === $id && ! preg_match( '/^(gpt-|o\d|chatgpt-)/', $name ) ) {
				continue;
			} elseif ( 'openai' === $id && false !== strpos( $name, 'image' ) ) {
				continue;
			}

			$free = false;
			if ( isset( $row['pricing']['prompt'], $row['pricing']['completion'] ) ) {
				$free = 0.0 === (float) $row['pricing']['prompt'] && 0.0 === (float) $row['pricing']['completion'];
			}

			$models[] = array(
				'id'    => $name,
				'label' => isset( $row['name'] ) && $row['name'] !== $name ? (string) $row['name'] : $name,
				'free'  => $free,
			);
		}
	}

	usort(
		$models,
		static function ( $a, $b ) {
			if ( $a['free'] !== $b['free'] ) {
				return $a['free'] ? -1 : 1;
			}
			return strnatcasecmp( $a['id'], $b['id'] );
		}
	);

	if ( $models ) {
		set_transient( $cache_key, $models, 12 * HOUR_IN_SECONDS );
	}

	return $models;
}

/**
 * Pick a sensible model from a provider's list when the site owner left the
 * choice on automatic. Model names change often, so this reads the live list
 * instead of trusting a hard-coded name.
 *
 * @param string   $id  Provider id.
 * @param string[] $ids Model ids the provider lists.
 * @return string Empty when nothing fits.
 */
function fswrt_pick_model( $id, $ids ) {
	$patterns = array(
		'gemini' => array( '/^gemini-flash-latest$/', '/^gemini-[\d.]+-flash$/', '/^gemini-.*flash(?!-lite)/', '/flash/' ),
		'groq'   => array( '/llama-[\d.]+-70b-versatile/', '/versatile/', '/llama/' ),
		'openai' => array( '/^gpt-[\d.]+-mini$/', '/^gpt-4o-mini$/', '/^gpt-[\d.]+$/', '/^gpt-/' ),
	);

	if ( isset( $patterns[ $id ] ) ) {
		foreach ( $patterns[ $id ] as $pattern ) {
			$matches = preg_grep( $pattern, $ids );
			if ( $matches ) {
				// Newest version number first.
				usort( $matches, 'strnatcasecmp' );
				return (string) end( $matches );
			}
		}
	}

	return $ids ? (string) reset( $ids ) : '';
}

/**
 * The model to use for a provider.
 *
 * @param string $id Provider id.
 * @return string|WP_Error
 */
function fswrt_resolve_model( $id ) {
	$settings  = fswrt_get_settings();
	$providers = fswrt_providers();

	if ( ! isset( $providers[ $id ] ) ) {
		return new WP_Error( 'fswrt_provider', __( 'Unknown provider.', 'fusionwrite-ai' ) );
	}

	$model = $settings['providers'][ $id ]['model'];
	if ( '' !== $model ) {
		return $model;
	}

	if ( 'openrouter' === $id ) {
		return $providers[ $id ]['fallback'];
	}

	$list = fswrt_list_models( $id );
	if ( ! is_wp_error( $list ) && $list ) {
		$picked = fswrt_pick_model( $id, wp_list_pluck( $list, 'id' ) );
		if ( '' !== $picked ) {
			return $picked;
		}
	}

	if ( '' !== $providers[ $id ]['fallback'] ) {
		return $providers[ $id ]['fallback'];
	}

	if ( is_wp_error( $list ) ) {
		return $list;
	}

	return new WP_Error( 'fswrt_model', __( 'Enter a model name for this provider on the Providers page.', 'fusionwrite-ai' ) );
}

/**
 * Ask a provider to write something.
 *
 * @param string $id     Provider id.
 * @param string $prompt Full prompt.
 * @return array{text:string,model:string}|WP_Error
 */
function fswrt_generate_text( $id, $prompt ) {
	$providers = fswrt_providers();

	if ( ! isset( $providers[ $id ] ) || ! fswrt_provider_is_connected( $id ) ) {
		return new WP_Error( 'fswrt_provider', __( 'This provider is not connected yet. Add its key on the Providers page.', 'fusionwrite-ai' ) );
	}

	$model = fswrt_resolve_model( $id );
	if ( is_wp_error( $model ) ) {
		return $model;
	}

	$label = $providers[ $id ]['label'];
	$base  = fswrt_provider_base( $id );

	if ( function_exists( 'set_time_limit' ) ) {
		set_time_limit( 240 );
	}

	if ( 'gemini' === $providers[ $id ]['kind'] ) {
		$model    = str_replace( 'models/', '', $model );
		$response = wp_remote_post(
			$base . '/models/' . rawurlencode( $model ) . ':generateContent',
			array(
				'headers' => fswrt_provider_headers( $id ),
				'timeout' => 180,
				'body'    => wp_json_encode(
					array(
						'contents'         => array(
							array(
								'role'  => 'user',
								'parts' => array( array( 'text' => $prompt ) ),
							),
						),
						'generationConfig' => array( 'temperature' => 0.7 ),
					)
				),
			)
		);

		$error = fswrt_response_error( $response, $label );
		if ( $error ) {
			return $error;
		}

		$data = json_decode( wp_remote_retrieve_body( $response ), true );
		$text = '';
		if ( isset( $data['candidates'][0]['content']['parts'] ) && is_array( $data['candidates'][0]['content']['parts'] ) ) {
			foreach ( $data['candidates'][0]['content']['parts'] as $part ) {
				if ( isset( $part['text'] ) && empty( $part['thought'] ) ) {
					$text .= $part['text'];
				}
			}
		}

		if ( '' === trim( $text ) ) {
			$reason = '';
			if ( isset( $data['promptFeedback']['blockReason'] ) ) {
				$reason = (string) $data['promptFeedback']['blockReason'];
			} elseif ( isset( $data['candidates'][0]['finishReason'] ) ) {
				$reason = (string) $data['candidates'][0]['finishReason'];
			}
			return new WP_Error(
				'fswrt_empty',
				/* translators: %s: reason code from the API, may be empty. */
				trim( sprintf( __( 'Gemini returned no text. %s', 'fusionwrite-ai' ), $reason ) )
			);
		}

		return array(
			'text'  => $text,
			'model' => $model,
		);
	}

	$response = wp_remote_post(
		$base . '/chat/completions',
		array(
			'headers' => fswrt_provider_headers( $id ),
			'timeout' => 180,
			'body'    => wp_json_encode(
				array(
					'model'    => $model,
					'messages' => array(
						array(
							'role'    => 'user',
							'content' => $prompt,
						),
					),
				)
			),
		)
	);

	$error = fswrt_response_error( $response, $label );
	if ( $error ) {
		return $error;
	}

	$data    = json_decode( wp_remote_retrieve_body( $response ), true );
	$content = isset( $data['choices'][0]['message']['content'] ) ? $data['choices'][0]['message']['content'] : '';
	if ( is_array( $content ) ) {
		$joined = '';
		foreach ( $content as $part ) {
			if ( isset( $part['text'] ) ) {
				$joined .= $part['text'];
			}
		}
		$content = $joined;
	}

	if ( '' === trim( (string) $content ) ) {
		return new WP_Error(
			'fswrt_empty',
			/* translators: %s: service name. */
			sprintf( __( '%s returned an empty answer. Free models do this when they are busy, so try again or pick another model.', 'fusionwrite-ai' ), $label )
		);
	}

	return array(
		'text'  => (string) $content,
		// A router such as openrouter/free reports the model it really used.
		'model' => isset( $data['model'] ) && is_string( $data['model'] ) && '' !== $data['model'] ? $data['model'] : $model,
	);
}

/**
 * Send a tiny request to confirm a provider works.
 *
 * @param string $id Provider id.
 * @return array{text:string,model:string}|WP_Error
 */
function fswrt_test_provider( $id ) {
	return fswrt_generate_text( $id, 'Reply with the single word: ready' );
}
