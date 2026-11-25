<?php
/**
 * Front page — services / features grid (theme starter fallback).
 *
 * @package NexusForge
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$nexusforge_services = array(
	array(
		'title' => __( 'Lightweight by design', 'nexusforge' ),
		'body'  => __( 'No framework bloat, no icon libraries — just lean CSS and vanilla JavaScript loaded only where needed.', 'nexusforge' ),
	),
	array(
		'title' => __( 'Fluid on every screen', 'nexusforge' ),
		'body'  => __( 'Content-driven breakpoints with clamp() and fluid grids keep layouts intact from narrow mobile to ultrawide.', 'nexusforge' ),
	),
	array(
		'title' => __( 'Easy to customize', 'nexusforge' ),
		'body'  => __( 'Change colors, type and layout from the Customizer — each control maps to a single design token.', 'nexusforge' ),
	),
);
?>
<section class="nexusforge-section">
	<div class="nexusforge-container">
		<header class="nexusforge-section__head">
			<p class="nexusforge-eyebrow"><?php esc_html_e( 'What you get', 'nexusforge' ); ?></p>
			<h2><?php esc_html_e( 'Focused by design, measurable in practice.', 'nexusforge' ); ?></h2>
		</header>
		<div class="nexusforge-grid" style="margin-top:var(--nexusforge-space-8);">
			<?php foreach ( $nexusforge_services as $nexusforge_service ) : ?>
				<article class="nexusforge-feature">
					<span class="nexusforge-feature__icon" aria-hidden="true">
						<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
					</span>
					<h3><?php echo esc_html( $nexusforge_service['title'] ); ?></h3>
					<p><?php echo esc_html( $nexusforge_service['body'] ); ?></p>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
