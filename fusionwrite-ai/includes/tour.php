<?php
/**
 * Steps of the guided tour, in order. `page` is the workspace page the step
 * is shown on; `target` is the element it points at.
 *
 * @package FusionWrite_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	array(
		'page'   => 'overview',
		'target' => '',
		'title'  => __( 'Welcome to FusionWrite', 'fusionwrite-ai' ),
		'text'   => __( 'This short tour walks through the workspace page by page. Press Esc to leave it at any time.', 'fusionwrite-ai' ),
	),
	array(
		'page'   => 'overview',
		'target' => '[data-tour="nav"]',
		'title'  => __( 'Everything is in this menu', 'fusionwrite-ai' ),
		'text'   => __( 'Writing pages are at the top, setup pages in the middle and the guide below them. Each one opens as its own page.', 'fusionwrite-ai' ),
	),
	array(
		'page'   => 'providers',
		'target' => '#provider-openrouter',
		'title'  => __( 'Connect a provider first', 'fusionwrite-ai' ),
		'text'   => __( 'Open a provider, paste its API key and choose Save and test. OpenRouter, Gemini and Groq have free plans; the Guide page shows how to get a key.', 'fusionwrite-ai' ),
	),
	array(
		'page'   => 'images',
		'target' => '[data-tour="image-engines"]',
		'title'  => __( 'Choose how images are made', 'fusionwrite-ai' ),
		'text'   => __( 'Pollinations is free and needs no key. The other services use their own key or the one saved under Providers.', 'fusionwrite-ai' ),
	),
	array(
		'page'   => 'preferences',
		'target' => '[data-tour="prefs"]',
		'title'  => __( 'Set your usual options once', 'fusionwrite-ai' ),
		'text'   => __( 'Language, tone, length and whether new posts are saved as drafts. Every new post starts from these.', 'fusionwrite-ai' ),
	),
	array(
		'page'   => 'write',
		'target' => '[data-tour="topic"]',
		'title'  => __( 'Type a topic', 'fusionwrite-ai' ),
		'text'   => __( 'One line is enough: a title idea, a question or a few keywords.', 'fusionwrite-ai' ),
	),
	array(
		'page'   => 'write',
		'target' => '[data-tour="write-options"]',
		'title'  => __( 'Adjust this post', 'fusionwrite-ai' ),
		'text'   => __( 'Pick who writes it, the language, tone, length and number of images. Custom instructions replace the standard brief.', 'fusionwrite-ai' ),
	),
	array(
		'page'   => 'write',
		'target' => '[data-tour="write-submit"]',
		'title'  => __( 'Write the post', 'fusionwrite-ai' ),
		'text'   => __( 'The post is saved to WordPress as soon as it is written, then opens in an editor on this page.', 'fusionwrite-ai' ),
	),
	array(
		'page'   => 'bulk',
		'target' => '[data-tour="bulk-topics"]',
		'title'  => __( 'Write many posts at once', 'fusionwrite-ai' ),
		'text'   => __( 'Put one topic on each line. They are written one after another while the tab stays open.', 'fusionwrite-ai' ),
	),
	array(
		'page'   => 'history',
		'target' => '[data-tour="history"]',
		'title'  => __( 'Find everything again', 'fusionwrite-ai' ),
		'text'   => __( 'History lists each post with the model that wrote it and links to edit or view it.', 'fusionwrite-ai' ),
	),
	array(
		'page'   => 'history',
		'target' => '[data-fw-tour-start]',
		'title'  => __( 'That is the whole workspace', 'fusionwrite-ai' ),
		'text'   => __( 'You can take this tour again from here whenever you like.', 'fusionwrite-ai' ),
	),
);
