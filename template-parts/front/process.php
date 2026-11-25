<?php
/**
 * Front page — process section (theme starter fallback).
 *
 * @package NexusForge
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$nexusforge_steps = array(
	array( '01', __( 'Discovery', 'nexusforge' ), __( 'We listen, map the terrain and agree on what success looks like.', 'nexusforge' ) ),
	array( '02', __( 'Diagnosis', 'nexusforge' ), __( 'A clear-eyed read of what is working and what is holding you back.', 'nexusforge' ) ),
	array( '03', __( 'Design', 'nexusforge' ), __( 'A prioritized plan your team can actually execute.', 'nexusforge' ) ),
	array( '04', __( 'Delivery', 'nexusforge' ), __( 'We stay through adoption and measure the outcomes together.', 'nexusforge' ) ),
);
?>
<section class="nexusforge-section nexusforge-band--mint">
	<div class="nexusforge-container">
		<div class="nexusforge-grid nexusforge-grid--2" style="align-items:start;">
			<div>
				<p class="nexusforge-eyebrow"><?php esc_html_e( 'How it works', 'nexusforge' ); ?></p>
				<h2><?php esc_html_e( 'A simple, transparent process.', 'nexusforge' ); ?></h2>
				<p><?php esc_html_e( 'Four stages, fixed scope, no surprises. You always know where things stand.', 'nexusforge' ); ?></p>
			</div>
			<div class="nexusforge-steps">
				<?php foreach ( $nexusforge_steps as $nexusforge_step ) : ?>
					<div class="nexusforge-step">
						<span class="nexusforge-step__num"><?php echo esc_html( $nexusforge_step[0] ); ?></span>
						<div>
							<h3 style="margin-bottom:var(--nexusforge-space-1);"><?php echo esc_html( $nexusforge_step[1] ); ?></h3>
							<p style="margin:0;color:var(--nexusforge-color-text-2);"><?php echo esc_html( $nexusforge_step[2] ); ?></p>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</section>
