<?php
/**
 * Site branding: custom logo or text title + tagline.
 *
 * @package NexusForge
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="nexusforge-branding">
	<?php
	if ( has_custom_logo() ) {
		the_custom_logo();
	} else {
		$home_url = home_url( '/' );
		if ( is_front_page() && is_home() ) {
			printf(
				'<h1 class="site-title"><a href="%1$s" rel="home"><span class="site-name">%2$s</span><span class="nexusforge-dot">.</span></a></h1>',
				esc_url( $home_url ),
				esc_html( get_bloginfo( 'name' ) )
			);
		} else {
			printf(
				'<p class="site-title"><a href="%1$s" rel="home"><span class="site-name">%2$s</span><span class="nexusforge-dot">.</span></a></p>',
				esc_url( $home_url ),
				esc_html( get_bloginfo( 'name' ) )
			);
		}
	}
	?>
</div>
