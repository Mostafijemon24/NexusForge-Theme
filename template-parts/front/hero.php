<?php
/**
 * Front page — hero section (theme starter fallback).
 *
 * @package NexusForge
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section class="nexusforge-hero">
	<div class="nexusforge-container">
		<div class="nexusforge-hero__inner">
			<p class="nexusforge-eyebrow"><?php echo esc_html( get_bloginfo( 'description' ) ? get_bloginfo( 'description' ) : __( 'Welcome', 'nexusforge' ) ); ?></p>
			<h1><?php esc_html_e( 'Clarity for your most complex decisions.', 'nexusforge' ); ?></h1>
			<p class="nexusforge-hero__lead"><?php esc_html_e( 'A lightweight, fluidly responsive theme built around clarity, flexibility and performance — edit every section with your page builder or standard WordPress blocks.', 'nexusforge' ); ?></p>
			<div class="nexusforge-hero__actions">
				<a class="nexusforge-btn nexusforge-btn--primary" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Get started', 'nexusforge' ); ?></a>
				<a class="nexusforge-btn nexusforge-btn--secondary" href="<?php echo esc_url( home_url( '/about/' ) ); ?>"><?php esc_html_e( 'Learn more', 'nexusforge' ); ?></a>
			</div>
		</div>
	</div>
	<span class="nexusforge-hero__orb" aria-hidden="true"></span>
</section>
