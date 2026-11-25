<?php
/**
 * The header: opens the document, prints <head>, site header chrome.
 *
 * When Elementor Pro Theme Builder owns the "header" location, the theme defers
 * to it and prints no duplicate header.
 *
 * @package NexusForge
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="page" class="nexusforge-site">
	<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'nexusforge' ); ?></a>

	<?php
	// Defer to Elementor Pro header template when one exists.
	if ( ! ( function_exists( 'nexusforge_elementor_did_location' ) && nexusforge_elementor_did_location( 'header' ) ) ) :
		if ( ! nexusforge_is_elementor_canvas() ) :
			?>
			<header id="masthead" class="nexusforge-header">
				<div class="nexusforge-container nexusforge-header__inner">
					<?php get_template_part( 'template-parts/header/branding' ); ?>
					<?php get_template_part( 'template-parts/header/navigation' ); ?>
				</div>
			</header>
			<?php
			// Optional Custom Header image (WordPress "custom-header" support).
			// Renders a responsive full-width banner only when the user has set one.
			if ( has_header_image() ) :
				?>
				<div class="nexusforge-custom-header">
					<?php
					the_header_image_tag(
						array(
							'class'   => 'nexusforge-custom-header__img',
							'loading' => 'eager',
						)
					);
					?>
				</div>
				<?php
			endif;
		endif;
	endif;
	?>
