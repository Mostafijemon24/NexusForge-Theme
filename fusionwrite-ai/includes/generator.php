<?php
/**
 * Building the prompt, cleaning the answer and saving the post.
 *
 * @package FusionWrite_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Build the prompt for one post.
 *
 * @param array{keyword:string,words:int,language:string,tone:string,custom:string} $args Post options.
 * @return string
 */
function fswrt_build_prompt( $args ) {
	$format = "Formatting rules:\n"
		. '- Write everything in ' . $args['language'] . ", whatever language these instructions are in.\n"
		. "- Start with the post title inside one <h1> tag.\n"
		. "- Use <h2> and <h3> subheadings, short paragraphs in <p> tags, and <ul> or <ol> lists where they help the reader.\n"
		. "- Output clean HTML only. No Markdown, no code fences, no <html>, <head> or <body> tags.\n"
		. '- Give only the post itself, with no commentary before or after it.';

	if ( '' !== $args['custom'] ) {
		$prompt = str_replace( '{keyword}', $args['keyword'], $args['custom'] );
		if ( false === strpos( $args['custom'], '{keyword}' ) ) {
			$prompt .= "\n\nTopic: " . $args['keyword'];
		}
		return $prompt . "\n\n" . $format;
	}

	return "You are an experienced blog writer and editor.\n\n"
		. 'Write a complete, original blog post about: "' . $args['keyword'] . "\".\n\n"
		. 'Tone: ' . $args['tone'] . ".\n"
		. 'Length: about ' . (int) $args['words'] . " words.\n"
		. "Open with a short introduction, cover the topic in clearly separated sections, and end with a brief conclusion.\n\n"
		. $format;
}

/**
 * Very small Markdown to HTML conversion, for models that ignore the
 * "HTML only" rule.
 *
 * @param string $text Markdown text.
 * @return string
 */
function fswrt_markdown_to_html( $text ) {
	$lines = preg_split( '/\r\n|\r|\n/', trim( $text ) );
	$html  = '';
	$para  = array();
	$list  = '';

	$inline = static function ( $line ) {
		$line = esc_html( $line );
		$line = preg_replace( '/\*\*(.+?)\*\*/', '<strong>$1</strong>', $line );
		$line = preg_replace( '/(?<![\*\w])\*(?!\s)(.+?)(?<!\s)\*(?![\*\w])/', '<em>$1</em>', $line );
		$line = preg_replace_callback(
			'/\[([^\]]+)\]\((https?:[^)\s]+)\)/',
			static function ( $m ) {
				return '<a href="' . esc_url( html_entity_decode( $m[2] ) ) . '">' . $m[1] . '</a>';
			},
			$line
		);
		return $line;
	};

	$flush = static function () use ( &$html, &$para, &$list ) {
		if ( $para ) {
			$html .= '<p>' . implode( ' ', $para ) . "</p>\n";
			$para  = array();
		}
		if ( $list ) {
			$html .= '</' . $list . ">\n";
			$list  = '';
		}
	};

	foreach ( $lines as $line ) {
		$line = trim( $line );

		if ( '' === $line ) {
			$flush();
		} elseif ( preg_match( '/^(#{1,4})\s+(.*)$/', $line, $m ) ) {
			$flush();
			$level = strlen( $m[1] );
			$html .= '<h' . $level . '>' . $inline( trim( $m[2], " #\t" ) ) . '</h' . $level . ">\n";
		} elseif ( preg_match( '/^([-*•]|\d+[.)])\s+(.*)$/', $line, $m ) ) {
			$type = preg_match( '/^\d/', $m[1] ) ? 'ol' : 'ul';
			if ( $list !== $type ) {
				$flush();
				$html .= '<' . $type . ">\n";
				$list  = $type;
			}
			$html .= '<li>' . $inline( $m[2] ) . "</li>\n";
		} else {
			if ( $list ) {
				$flush();
			}
			$para[] = $inline( $line );
		}
	}
	$flush();

	return $html;
}

/**
 * Turn a raw model answer into a title and clean post content.
 *
 * @param string $raw     Text the model returned.
 * @param string $keyword Topic, used when the answer has no title.
 * @return array{title:string,content:string}
 */
function fswrt_clean_content( $raw, $keyword ) {
	$content = (string) $raw;

	// Reasoning some models print before the answer.
	$content = preg_replace( '/<think(ing)?>.*?<\/think(ing)?>/is', '', $content );
	// Code fences around the whole answer.
	$content = preg_replace( '/^\s*```[a-zA-Z]*\s*$/m', '', $content );
	$content = trim( $content );

	if ( ! preg_match( '/<(p|h[1-6]|ul|ol)\b/i', $content ) ) {
		$content = fswrt_markdown_to_html( $content );
	}

	if ( preg_match( '/<body\b[^>]*>(.*)<\/body>/is', $content, $body ) ) {
		$content = $body[1];
	}
	$content = preg_replace( '/<!DOCTYPE[^>]*>|<\/?(html|head|body)\b[^>]*>|<title\b[^>]*>.*?<\/title>|<(style|script)\b[^>]*>.*?<\/\2>/is', '', $content );

	// Drop any chatter before the first tag.
	$start = strpos( $content, '<' );
	if ( false !== $start && $start > 0 ) {
		$content = substr( $content, $start );
	}

	$title = '';
	if ( preg_match( '/<h1\b[^>]*>(.*?)<\/h1>/is', $content, $match ) ) {
		$title   = trim( wp_strip_all_tags( $match[1] ) );
		$content = preg_replace( '/<h1\b[^>]*>.*?<\/h1>/is', '', $content, 1 );
	}
	// A post has one title; any other h1 becomes a section heading.
	$content = preg_replace( '/<(\/?)h1\b/i', '<$1h2', $content );

	if ( '' === $title ) {
		$title = function_exists( 'mb_convert_case' ) ? mb_convert_case( $keyword, MB_CASE_TITLE, 'UTF-8' ) : ucwords( $keyword );
	}

	return array(
		'title'   => $title,
		'content' => trim( wp_kses_post( $content ) ),
	);
}

/**
 * Count words in a way that also works for scripts without Latin letters.
 *
 * @param string $html Post content.
 * @return int
 */
function fswrt_count_words( $html ) {
	$text = trim( wp_strip_all_tags( $html ) );
	if ( '' === $text ) {
		return 0;
	}
	return count( preg_split( '/\s+/u', $text ) );
}

/**
 * Write one post and save it.
 *
 * @param array{keyword:string,provider:string,words:int,language:string,tone:string,custom:string} $args Post options.
 * @return array<string,mixed>|WP_Error
 */
function fswrt_write_post( $args ) {
	$result = fswrt_generate_text( $args['provider'], fswrt_build_prompt( $args ) );
	if ( is_wp_error( $result ) ) {
		return $result;
	}

	$clean = fswrt_clean_content( $result['text'], $args['keyword'] );
	if ( '' === $clean['content'] ) {
		return new WP_Error( 'fswrt_empty', __( 'The answer had no usable text. Try again or pick another model.', 'fusionwrite-ai' ) );
	}

	$settings = fswrt_get_settings();
	$status   = $settings['defaults']['post_status'];
	if ( 'publish' === $status && ! current_user_can( 'publish_posts' ) ) {
		$status = 'pending';
	}

	$post = array(
		'post_title'   => $clean['title'],
		'post_content' => $clean['content'],
		'post_status'  => $status,
		'post_type'    => 'post',
		'post_author'  => get_current_user_id(),
	);
	if ( $settings['defaults']['category'] > 0 && term_exists( (int) $settings['defaults']['category'], 'category' ) ) {
		$post['post_category'] = array( (int) $settings['defaults']['category'] );
	}

	$post_id = wp_insert_post( wp_slash( $post ), true );
	if ( is_wp_error( $post_id ) ) {
		return $post_id;
	}

	$providers = fswrt_providers();
	$words     = fswrt_count_words( $clean['content'] );

	update_post_meta( $post_id, '_fswrt_generated', 1 );
	update_post_meta( $post_id, '_fswrt_provider', $providers[ $args['provider'] ]['label'] );
	update_post_meta( $post_id, '_fswrt_model', sanitize_text_field( $result['model'] ) );
	update_post_meta( $post_id, '_fswrt_keyword', $args['keyword'] );
	update_post_meta( $post_id, '_fswrt_words', $words );

	fswrt_bump_stat( 'posts' );
	fswrt_bump_stat( 'words', $words );

	return array(
		'post_id'  => (int) $post_id,
		'title'    => $clean['title'],
		'content'  => $clean['content'],
		'words'    => $words,
		'model'    => $result['model'],
		'status'   => $status,
		'edit_url' => get_edit_post_link( $post_id, 'raw' ),
		'view_url' => 'publish' === $status ? get_permalink( $post_id ) : get_preview_post_link( $post_id ),
	);
}

/**
 * Create one image for a post and place it.
 *
 * @param int $post_id Post id.
 * @param int $slot    0 for the featured image, 1 or 2 for body images.
 * @return array<string,mixed>|WP_Error
 */
function fswrt_add_post_image( $post_id, $slot ) {
	$post = get_post( $post_id );
	if ( ! $post ) {
		return new WP_Error( 'fswrt_post', __( 'That post no longer exists.', 'fusionwrite-ai' ) );
	}

	$plan  = fswrt_image_plan( $post->post_content, $post->post_title, $slot );
	$image = fswrt_generate_image( $plan['prompt'] );
	if ( is_wp_error( $image ) ) {
		return $image;
	}

	$attachment_id = fswrt_save_image( $image['bytes'], $post_id, $plan['subject'] );
	if ( is_wp_error( $attachment_id ) ) {
		return $attachment_id;
	}

	$content = $post->post_content;
	if ( 0 === $slot ) {
		set_post_thumbnail( $post_id, $attachment_id );
	} else {
		$html    = "\n<p>" . wp_get_attachment_image( $attachment_id, 'large', false, array( 'class' => 'aligncenter size-large wp-image-' . $attachment_id ) ) . "</p>\n";
		$content = substr( $content, 0, $plan['offset'] ) . $html . substr( $content, $plan['offset'] );
		wp_update_post(
			wp_slash(
				array(
					'ID'           => $post_id,
					'post_content' => $content,
				)
			)
		);
	}

	fswrt_bump_stat( 'images' );

	return array(
		'slot'          => $slot,
		'attachment_id' => $attachment_id,
		'url'           => wp_get_attachment_image_url( $attachment_id, 'medium_large' ),
		'content'       => $content,
	);
}
