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
 * Allowed integer range for each numeric setting.
 *
 * Shared by the Customizer controls (input attributes), the sanitize callback
 * (before saving to the database) and the CSS compiler (before output).
 *
 * @return array
 */
function nexusforge_customizer_ranges() {
	return array(
		'nexusforge_base_font_px'  => array(
			'min'  => 14,
			'max'  => 22,
			'step' => 1,
		),
		'nexusforge_content_width' => array(
			'min'  => 960,
			'max'  => 1440,
			'step' => 20,
		),
		'nexusforge_button_radius' => array(
			'min'  => 0,
			'max'  => 40,
			'step' => 1,
		),
	);
}

/**
 * Register Customizer sections, settings and controls.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager.
 */
function nexusforge_customize_register( $wp_customize ) {
	$defaults = nexusforge_customizer_defaults();
	$ranges   = nexusforge_customizer_ranges();

	// Live preview for core identity.
	foreach ( array( 'blogname', 'blogdescription' ) as $nexusforge_core_setting ) {
		$nexusforge_setting = $wp_customize->get_setting( $nexusforge_core_setting );
		if ( $nexusforge_setting ) {
			$nexusforge_setting->transport = 'postMessage';
		}
	}

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
			'input_attrs' => $ranges['nexusforge_base_font_px'],
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
			'input_attrs' => $ranges['nexusforge_content_width'],
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
			'input_attrs' => $ranges['nexusforge_button_radius'],
		)
	);
}
add_action( 'customize_register', 'nexusforge_customize_register' );

/**
 * Sanitize a numeric Customizer value before it is saved to the database.
 *
 * The value is validated as a number, cast to an integer and clamped to the
 * range declared for the setting in nexusforge_customizer_ranges(). Anything
 * that is not numeric falls back to the setting's default.
 *
 * @param mixed                $value   Raw, untrusted value.
 * @param WP_Customize_Setting $setting Setting instance.
 * @return int
 */
function nexusforge_sanitize_int_range( $value, $setting = null ) {
	$id       = ( $setting instanceof WP_Customize_Setting ) ? $setting->id : '';
	$defaults = nexusforge_customizer_defaults();
	$ranges   = nexusforge_customizer_ranges();
	$default  = isset( $defaults[ $id ] ) ? (int) $defaults[ $id ] : 0;

	if ( ! is_numeric( $value ) ) {
		return $default;
	}

	$value = (int) $value;

	if ( isset( $ranges[ $id ] ) ) {
		$value = max( $ranges[ $id ]['min'], min( $ranges[ $id ]['max'], $value ) );
	} else {
		$value = absint( $value );
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
