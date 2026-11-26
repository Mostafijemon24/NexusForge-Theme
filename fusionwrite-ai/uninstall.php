<?php
/**
 * Remove the plugin's settings when it is deleted. Posts and images it
 * created are the site's content and are left alone.
 *
 * @package FusionWrite_AI
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

foreach ( array( 'fswrt_settings', 'fswrt_stats', 'fswrt_openai_key', 'fswrt_gemini_key', 'fswrt_stability_key', 'fswrt_gemini_model_name' ) as $fswrt_option ) {
	delete_option( $fswrt_option );
}

delete_metadata( 'user', 0, 'fswrt_tour_done', '', true );
delete_transient( 'fswrt_just_activated' );

global $wpdb;
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Removes this plugin's cached model lists, whose names contain a hash.
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '\_transient\_fswrt\_models\_%' OR option_name LIKE '\_transient\_timeout\_fswrt\_models\_%'" );
