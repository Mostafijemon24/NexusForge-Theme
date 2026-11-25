<?php
/**
 * Front page — conversion CTA band (theme starter fallback).
 *
 * @package NexusForge
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section class="nexusforge-section nexusforge-band--brand">
	<div class="nexusforge-container nexusforge-container--narrow">
		<h2><?php esc_html_e( 'Let\'s talk about what\'s next.', 'nexusforge' ); ?></h2>
		<p><?php esc_html_e( 'A free 30-minute conversation — no pitch, just a clear read on where you are and what is possible.', 'nexusforge' ); ?></p>
		<div style="margin-top:var(--nexusforge-space-6);">
			<a class="nexusforge-btn" style="background:var(--nexusforge-white);color:var(--nexusforge-teal-deep);" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">
				<?php esc_html_e( 'Get in touch', 'nexusforge' ); ?>
			</a>
		</div>
	</div>
</section>
