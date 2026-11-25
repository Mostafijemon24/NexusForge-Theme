<?php
/**
 * Front page — testimonial (theme starter fallback).
 *
 * @package NexusForge
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section class="nexusforge-section">
	<div class="nexusforge-container">
		<figure class="nexusforge-quote">
			<blockquote style="border:none;padding:0;font-size:inherit;">
				<p><?php esc_html_e( 'NexusForge gave us a layout we could actually ship. Fast, flexible, and it still looks sharp on every device.', 'nexusforge' ); ?></p>
			</blockquote>
			<figcaption class="nexusforge-quote__person">
				<span class="nexusforge-avatar" aria-hidden="true"></span>
				<span>
					<strong><?php esc_html_e( 'Dana Whitfield', 'nexusforge' ); ?></strong><br>
					<span style="color:var(--nexusforge-color-text-2);font-size:var(--nexusforge-fs-small);"><?php esc_html_e( 'Operations Lead', 'nexusforge' ); ?></span>
				</span>
			</figcaption>
		</figure>
	</div>
</section>
