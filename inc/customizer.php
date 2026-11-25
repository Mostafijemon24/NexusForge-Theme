<?php
/**
 * Customizer controls — the single UI for editing design tokens.
 *
 * Every control here maps to one CSS custom property compiled in
 * customizer-css.php. Controls are intentionally minimal and never duplicated.
 *
 * @package NexusForge
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Default token values (kept in sync with assets/css/tokens.css).
 *
 * @return array
 */
function nexusforge_customizer_defaults() {
	return array(
		'nexusforge_color_brand'   => '#287C70',
		'nexusforge_color_canvas'  => '#FAF8F2',
		'nexusforge_color_text'    => '#243A34',
		'nexusforge_base_font_px'  => 17,
		'nexusforge_content_width' => 1200,
		'nexusforge_button_radius' => 10,
	);
}

/**
 * Register Customizer sections, settings and controls.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager.
 */
function nexusforge_customize_register( $wp_customize ) {
	$defaults = nexusforge_customizer_defaults();

	// Live preview for core identity.
	$wp_customize->get_setting( 'blogname' )->transport        = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';

	// --- Design System panel ---------------------------------------------
	$wp_customize->add_section(
		'nexusforge_design',
		array(
			'title'       => __( 'NexusForge — Design System', 'nexusforge' ),
			'priority'    => 30,
			'description' => __( 'Edit the theme design tokens. Each control updates a single global style.', 'nexusforge' ),
		)
	);

	// Brand color.
	$wp_customize->add_setting(
		'nexusforge_color_brand',
		array(
			'default'           => $defaults['nexusforge_color_brand'],
			'sanitize_callback' => 'sanitize_hex_color',
			'transport'         => 'postMessage',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'nexusforge_color_brand',
			array(
				'label'   => __( 'Brand / primary color', 'nexusforge' ),
				'section' => 'nexusforge_design',
			)
		)
	);

	// Canvas / surface color.
	$wp_customize->add_setting(
		'nexusforge_color_canvas',
		array(
			'default'           => $defaults['nexusforge_color_canvas'],
			'sanitize_callback' => 'sanitize_hex_color',
			'transport'         => 'postMessage',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'nexusforge_color_canvas',
			array(
				'label'   => __( 'Page canvas color', 'nexusforge' ),
				'section' => 'nexusforge_design',
			)
		)
	);

	// Text color.
	$wp_customize->add_setting(
		'nexusforge_color_text',
		array(
			'default'           => $defaults['nexusforge_color_text'],
			'sanitize_callback' => 'sanitize_hex_color',
			'transport'         => 'postMessage',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'nexusforge_color_text',
			array(
				'label'   => __( 'Body text color', 'nexusforge' ),
				'section' => 'nexusforge_design',
			)
		)
	);

	// Base font size.
	$wp_customize->add_setting(
		'nexusforge_base_font_px',
		array(
			'default'           => $defaults['nexusforge_base_font_px'],
			'sanitize_callback' => 'nexusforge_sanitize_int_range',
			'transport'         => 'postMessage',
		)
	);
	$wp_customize->add_control(
		'nexusforge_base_font_px',
		array(
			'label'       => __( 'Base font size (px)', 'nexusforge' ),
			'section'     => 'nexusforge_design',
			'type'        => 'number',
			'input_attrs' => array(
				'min'  => 14,
				'max'  => 22,
				'step' => 1,
			),
		)
	);

	// Content width.
	$wp_customize->add_setting(
		'nexusforge_content_width',
		array(
			'default'           => $defaults['nexusforge_content_width'],
			'sanitize_callback' => 'nexusforge_sanitize_int_range',
			'transport'         => 'postMessage',
		)
	);
	$wp_customize->add_control(
		'nexusforge_content_width',
		array(
			'label'       => __( 'Wide content width (px)', 'nexusforge' ),
			'section'     => 'nexusforge_design',
			'type'        => 'number',
			'input_attrs' => array(
				'min'  => 960,
				'max'  => 1440,
				'step' => 20,
			),
		)
	);

	// Button radius.
	$wp_customize->add_setting(
		'nexusforge_button_radius',
		array(
			'default'           => $defaults['nexusforge_button_radius'],
			'sanitize_callback' => 'nexusforge_sanitize_int_range',
			'transport'         => 'postMessage',
		)
	);
	$wp_customize->add_control(
		'nexusforge_button_radius',
		array(
			'label'       => __( 'Button corner radius (px)', 'nexusforge' ),
			'section'     => 'nexusforge_design',
			'type'        => 'number',
			'input_attrs' => array(
				'min'  => 0,
				'max'  => 40,
				'step' => 1,
			),
		)
	);
}
add_action( 'customize_register', 'nexusforge_customize_register' );

/**
 * Sanitize an integer, clamped to the control's declared min/max when provided.
 *
 * @param mixed                $value   Raw value.
 * @param WP_Customize_Setting $setting Setting instance.
 * @return int
 */
function nexusforge_sanitize_int_range( $value, $setting = null ) {
	$value = (int) $value;

	if ( $setting instanceof WP_Customize_Setting ) {
		$control = $setting->manager->get_control( $setting->id );
		if ( $control && isset( $control->input_attrs['min'], $control->input_attrs['max'] ) ) {
			$min   = (int) $control->input_attrs['min'];
			$max   = (int) $control->input_attrs['max'];
			$value = max( $min, min( $max, $value ) );
		}
	}

	return $value;
}

/**
 * Bind the postMessage live-preview script.
 */
function nexusforge_customize_preview_js() {
	wp_enqueue_script(
		'nexusforge-customizer-preview',
		NEXUSFORGE_URI . '/assets/js/customizer-preview.js',
		array( 'customize-preview' ),
		nexusforge_asset_version( 'assets/js/customizer-preview.js' ),
		true
	);
}
add_action( 'customize_preview_init', 'nexusforge_customize_preview_js' );
