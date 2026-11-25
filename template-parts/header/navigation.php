<?php
/**
 * Primary navigation + mobile toggle.
 *
 * @package NexusForge
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$nexusforge_has_menu = has_nav_menu( 'primary' );
?>
<button
	class="nexusforge-menu-toggle"
	aria-expanded="false"
	aria-controls="nexusforge-primary-nav"
	aria-label="<?php esc_attr_e( 'Menu', 'nexusforge' ); ?>"
>
	<span></span><span></span><span></span>
</button>

<nav id="nexusforge-primary-nav" class="nexusforge-primary-nav" aria-label="<?php esc_attr_e( 'Primary', 'nexusforge' ); ?>">
	<?php
	if ( $nexusforge_has_menu ) {
		wp_nav_menu(
			array(
				'theme_location' => 'primary',
				'menu_id'        => 'primary-menu',
				'menu_class'     => 'nexusforge-menu',
				'container'      => false,
				'walker'         => new Nexusforge_Walker_Nav(),
				'fallback_cb'    => false,
			)
		);
	} else {
		nexusforge_primary_menu_fallback();
	}
	?>
</nav>

<div class="nexusforge-header__actions">
	<a class="nexusforge-signin" href="<?php echo esc_url( wp_login_url() ); ?>"><?php esc_html_e( 'Sign in', 'nexusforge' ); ?></a>
	<a class="nexusforge-btn nexusforge-btn--primary" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Book a call', 'nexusforge' ); ?></a>
</div>
