<?php
/**
 * Plugin Name: FusionWrite AI
 * Plugin URI: https://mostafijemon.com/fusionwrite-ai
 * Description: Write blog posts with AI from a focused, full-page workspace. Works with OpenRouter, Google Gemini, Groq, OpenAI and any OpenAI-compatible API, including free ones.
 * Version: 2.0.0
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Author: Mostafij Emon
 * Author URI: https://mostafijemon.com/
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: fusionwrite-ai
 *
 * @package FusionWrite_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'FSWRT_VERSION', '2.0.0' );
define( 'FSWRT_FILE', __FILE__ );
define( 'FSWRT_DIR', plugin_dir_path( __FILE__ ) );
define( 'FSWRT_URL', plugin_dir_url( __FILE__ ) );

require_once FSWRT_DIR . 'includes/options.php';
require_once FSWRT_DIR . 'includes/icons.php';
require_once FSWRT_DIR . 'includes/providers.php';
require_once FSWRT_DIR . 'includes/images.php';
require_once FSWRT_DIR . 'includes/generator.php';

if ( is_admin() ) {
	require_once FSWRT_DIR . 'includes/admin.php';
	require_once FSWRT_DIR . 'includes/ajax.php';
}

register_activation_hook( __FILE__, 'fswrt_activate' );

/**
 * Remember that the plugin was just activated so the first admin page load
 * can open the workspace.
 */
function fswrt_activate() {
	set_transient( 'fswrt_just_activated', 1, 60 );
}
