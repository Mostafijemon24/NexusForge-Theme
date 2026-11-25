<?php
/**
 * The footer: closes #page, prints footer chrome and wp_footer().
 *
 * Defers to an Elementor Pro "footer" template when one exists.
 *
 * @package NexusForge
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! ( function_exists( 'nexusforge_elementor_did_location' ) && nexusforge_elementor_did_location( 'footer' ) ) ) :
	if ( ! nexusforge_is_elementor_canvas() ) :
		?>
		<footer id="colophon" class="nexusforge-footer">
			<div class="nexusforge-container">
				<div class="nexusforge-footer__grid">
					<div class="nexusforge-footer__brand">
						<div class="nexusforge-branding">
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
								<span class="site-name"><?php bloginfo( 'name' ); ?></span><span class="nexusforge-dot">.</span>
							</a>
						</div>
						<?php
						$nexusforge_desc = get_bloginfo( 'description', 'display' );
						if ( $nexusforge_desc ) :
							?>
							<p class="nexusforge-footer__tagline"><?php echo esc_html( $nexusforge_desc ); ?></p>
						<?php endif; ?>
					</div>

					<?php if ( is_active_sidebar( 'footer-1' ) ) : ?>
						<?php dynamic_sidebar( 'footer-1' ); ?>
					<?php endif; ?>

					<?php if ( has_nav_menu( 'footer' ) ) : ?>
						<nav class="nexusforge-footer__nav" aria-label="<?php esc_attr_e( 'Footer', 'nexusforge' ); ?>">
							<h2 class="nexusforge-footer__col-title"><?php esc_html_e( 'Links', 'nexusforge' ); ?></h2>
							<?php
							wp_nav_menu(
								array(
									'theme_location' => 'footer',
									'menu_class'     => 'nexusforge-footer__menu',
									'container'      => false,
									'depth'          => 1,
									'fallback_cb'    => false,
								)
							);
							?>
						</nav>
					<?php endif; ?>
				</div>

				<div class="nexusforge-footer__bottom">
					<span>
						<?php
						printf(
							/* translators: 1: year, 2: site name. */
							esc_html__( '© %1$s %2$s. All rights reserved.', 'nexusforge' ),
							esc_html( gmdate( 'Y' ) ),
							esc_html( get_bloginfo( 'name' ) )
						);
						?>
					</span>
				</div>
			</div>
		</footer>
		<?php
	endif;
endif;
?>
</div><!-- #page -->
<?php wp_footer(); ?>
</body>
</html>
