<?php
/**
 * Settings, defaults and the lists the workspace offers.
 *
 * @package FusionWrite_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Capability needed to use the workspace.
 *
 * @return string
 */
function fswrt_capability() {
	return apply_filters( 'fswrt_capability', 'manage_options' );
}

/**
 * Text providers the plugin can talk to.
 *
 * `kind` is the wire format: "openai" for any OpenAI-compatible chat
 * completions API, "gemini" for Google's native API.
 *
 * @return array<string,array<string,mixed>>
 */
function fswrt_providers() {
	return array(
		'openrouter' => array(
			'label'    => 'OpenRouter',
			'kind'     => 'openai',
			'base'     => 'https://openrouter.ai/api/v1',
			'key_url'  => 'https://openrouter.ai/keys',
			'free'     => true,
			'summary'  => __( 'One key for hundreds of models, with a rotating set of free ones.', 'fusionwrite-ai' ),
			'fallback' => 'openrouter/free',
			'auto'     => __( 'Automatic: any free model', 'fusionwrite-ai' ),
		),
		'gemini'     => array(
			'label'    => 'Google Gemini',
			'kind'     => 'gemini',
			'base'     => 'https://generativelanguage.googleapis.com/v1beta',
			'key_url'  => 'https://aistudio.google.com/apikey',
			'free'     => true,
			'summary'  => __( 'Google AI Studio keys include a free tier for the Flash models.', 'fusionwrite-ai' ),
			'fallback' => 'gemini-flash-latest',
			'auto'     => __( 'Automatic: newest Flash model', 'fusionwrite-ai' ),
		),
		'groq'       => array(
			'label'    => 'Groq',
			'kind'     => 'openai',
			'base'     => 'https://api.groq.com/openai/v1',
			'key_url'  => 'https://console.groq.com/keys',
			'free'     => true,
			'summary'  => __( 'Very fast open models with a free plan.', 'fusionwrite-ai' ),
			'fallback' => 'llama-3.3-70b-versatile',
			'auto'     => __( 'Automatic: best general model', 'fusionwrite-ai' ),
		),
		'openai'     => array(
			'label'    => 'OpenAI',
			'kind'     => 'openai',
			'base'     => 'https://api.openai.com/v1',
			'key_url'  => 'https://platform.openai.com/api-keys',
			'free'     => false,
			'summary'  => __( 'GPT models, billed by OpenAI per use.', 'fusionwrite-ai' ),
			'fallback' => 'gpt-4o-mini',
			'auto'     => __( 'Automatic: newest mini model', 'fusionwrite-ai' ),
		),
		'custom'     => array(
			'label'    => __( 'Custom API', 'fusionwrite-ai' ),
			'kind'     => 'openai',
			'base'     => '',
			'key_url'  => '',
			'free'     => false,
			'summary'  => __( 'Any OpenAI-compatible endpoint: Mistral, Cerebras, a local Ollama server and others.', 'fusionwrite-ai' ),
			'fallback' => '',
			'auto'     => __( 'Automatic: first model the API lists', 'fusionwrite-ai' ),
		),
	);
}

/**
 * Ready-made base URLs for the custom provider.
 *
 * @return array<string,string>
 */
function fswrt_custom_presets() {
	return array(
		'Mistral'        => 'https://api.mistral.ai/v1',
		'Cerebras'       => 'https://api.cerebras.ai/v1',
		'Ollama (local)' => 'http://localhost:11434/v1',
	);
}

/**
 * Languages offered for a post.
 *
 * @return string[]
 */
function fswrt_languages() {
	return apply_filters(
		'fswrt_languages',
		array( 'English', 'Bengali (বাংলা)', 'Spanish', 'German', 'French', 'Italian', 'Portuguese', 'Dutch', 'Russian', 'Chinese', 'Japanese', 'Arabic', 'Hindi', 'Urdu', 'Turkish', 'Korean', 'Indonesian' )
	);
}

/**
 * Tones offered for a post.
 *
 * @return string[]
 */
function fswrt_tones() {
	return apply_filters(
		'fswrt_tones',
		array( 'Professional', 'Casual', 'Friendly', 'Informative', 'Persuasive', 'Authoritative', 'Empathetic', 'Journalistic', 'Excited', 'Funny', 'Dramatic' )
	);
}

/**
 * Default settings.
 *
 * @return array<string,mixed>
 */
function fswrt_default_settings() {
	$providers = array();
	foreach ( array_keys( fswrt_providers() ) as $id ) {
		$providers[ $id ] = array(
			'key'   => '',
			'model' => '',
		);
	}
	$providers['custom']['base_url'] = '';

	return array(
		'providers'        => $providers,
		'default_provider' => '',
		'images'           => array(
			'engine'           => 'pollinations',
			'count'            => 1,
			'style'            => 'Clean, modern editorial illustration',
			'openai_model'     => 'gpt-image-1',
			'openai_quality'   => 'medium',
			'openrouter_model' => '',
			'stability_key'    => '',
		),
		'defaults'         => array(
			'language'    => 'English',
			'tone'        => 'Professional',
			'words'       => 1500,
			'post_status' => 'draft',
			'category'    => 0,
			'fullscreen'  => 1,
		),
	);
}

/**
 * Read the settings, filled in with defaults.
 *
 * @param bool $refresh Skip the per-request cache.
 * @return array<string,mixed>
 */
function fswrt_get_settings( $refresh = false ) {
	global $fswrt_settings_cache;
	if ( is_array( $fswrt_settings_cache ) && ! $refresh ) {
		return $fswrt_settings_cache;
	}

	$saved = get_option( 'fswrt_settings', null );
	if ( ! is_array( $saved ) ) {
		$saved = fswrt_migrate_legacy_settings();
	}

	$defaults = fswrt_default_settings();
	$settings = $defaults;
	foreach ( $defaults as $group => $value ) {
		if ( ! isset( $saved[ $group ] ) ) {
			continue;
		}
		if ( 'providers' === $group ) {
			foreach ( $value as $id => $fields ) {
				if ( isset( $saved['providers'][ $id ] ) && is_array( $saved['providers'][ $id ] ) ) {
					$settings['providers'][ $id ] = array_merge( $fields, array_intersect_key( $saved['providers'][ $id ], $fields ) );
				}
			}
		} elseif ( is_array( $value ) ) {
			$settings[ $group ] = array_merge( $value, array_intersect_key( (array) $saved[ $group ], $value ) );
		} else {
			$settings[ $group ] = $saved[ $group ];
		}
	}

	$fswrt_settings_cache = $settings;
	return $settings;
}

/**
 * Use the given settings for the rest of this request without saving them.
 * Lets a key that is typed in but not saved yet be tried out.
 *
 * @param array<string,mixed> $settings Full settings array.
 */
function fswrt_use_settings_for_request( $settings ) {
	global $fswrt_settings_cache;
	$fswrt_settings_cache = $settings;
}

/**
 * Save the settings.
 *
 * @param array<string,mixed> $settings Full settings array.
 */
function fswrt_update_settings( $settings ) {
	update_option( 'fswrt_settings', $settings, false );
	fswrt_get_settings( true );
}

/**
 * Carry the version 1.0 options over to the new format the first time the
 * plugin runs after an update.
 *
 * @return array<string,mixed>
 */
function fswrt_migrate_legacy_settings() {
	$settings = fswrt_default_settings();

	$openai    = (string) get_option( 'fswrt_openai_key', '' );
	$gemini    = (string) get_option( 'fswrt_gemini_key', '' );
	$stability = (string) get_option( 'fswrt_stability_key', '' );

	if ( '' === $openai && '' === $gemini && '' === $stability ) {
		return $settings;
	}

	$settings['providers']['openai']['key']  = $openai;
	$settings['providers']['gemini']['key']  = $gemini;
	$settings['images']['stability_key']     = $stability;
	if ( '' !== $openai ) {
		$settings['images']['engine'] = 'openai';
		$settings['images']['count']  = 3;
	}

	update_option( 'fswrt_settings', $settings, false );
	delete_option( 'fswrt_openai_key' );
	delete_option( 'fswrt_gemini_key' );
	delete_option( 'fswrt_stability_key' );
	delete_option( 'fswrt_gemini_model_name' );

	return $settings;
}

/**
 * Whether a provider has what it needs to be used.
 *
 * @param string $id Provider id.
 * @return bool
 */
function fswrt_provider_is_connected( $id ) {
	$settings = fswrt_get_settings();
	if ( ! isset( $settings['providers'][ $id ] ) ) {
		return false;
	}
	if ( 'custom' === $id ) {
		// A local server such as Ollama needs a base URL but no key.
		return '' !== $settings['providers']['custom']['base_url'];
	}
	return '' !== $settings['providers'][ $id ]['key'];
}

/**
 * Ids of the connected providers, the default one first.
 *
 * @return string[]
 */
function fswrt_connected_providers() {
	$settings  = fswrt_get_settings();
	$connected = array();
	foreach ( array_keys( fswrt_providers() ) as $id ) {
		if ( fswrt_provider_is_connected( $id ) ) {
			$connected[] = $id;
		}
	}
	$default = $settings['default_provider'];
	if ( $default && in_array( $default, $connected, true ) ) {
		$connected = array_merge( array( $default ), array_diff( $connected, array( $default ) ) );
	}
	return array_values( $connected );
}

/**
 * Show only the end of a secret.
 *
 * @param string $key Secret.
 * @return string
 */
function fswrt_mask_key( $key ) {
	$key = (string) $key;
	if ( '' === $key ) {
		return '';
	}
	return str_repeat( '•', 8 ) . substr( $key, -4 );
}

/**
 * Running totals shown on the overview.
 *
 * @return array{posts:int,words:int,images:int}
 */
function fswrt_get_stats() {
	$stats = get_option( 'fswrt_stats', array() );
	return array(
		'posts'  => isset( $stats['posts'] ) ? (int) $stats['posts'] : 0,
		'words'  => isset( $stats['words'] ) ? (int) $stats['words'] : 0,
		'images' => isset( $stats['images'] ) ? (int) $stats['images'] : 0,
	);
}

/**
 * Add to a running total.
 *
 * @param string $what   posts, words or images.
 * @param int    $amount Amount to add.
 */
function fswrt_bump_stat( $what, $amount = 1 ) {
	$stats = fswrt_get_stats();
	if ( isset( $stats[ $what ] ) ) {
		$stats[ $what ] += (int) $amount;
		update_option( 'fswrt_stats', $stats, false );
	}
}

/**
 * Admin URL of a workspace page.
 *
 * @param string $page Short page name, for example "write".
 * @return string
 */
function fswrt_page_url( $page = 'overview' ) {
	$slug = 'overview' === $page ? 'fusionwrite-ai' : 'fusionwrite-ai-' . $page;
	return admin_url( 'admin.php?page=' . $slug );
}
