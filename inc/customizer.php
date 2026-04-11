<?php
/**
 * Customizer settings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function titancore_customize_register( $wp_customize ) {
	// Header Options Section
	$wp_customize->add_section( 'header_options', array(
		'title'    => __( 'Header Options', 'titancore' ),
		'priority' => 30,
	) );

	// Sticky Header Setting
	$wp_customize->add_setting( 'sticky_header', array(
		'default'           => true,
		'sanitize_callback' => 'rest_sanitize_boolean',
	) );

	$wp_customize->add_control( 'sticky_header', array(
		'label'    => __( 'Enable Sticky Header', 'titancore' ),
		'section'  => 'header_options',
		'type'     => 'checkbox',
	) );
    
    // Custom Header Code
    $wp_customize->add_setting( 'custom_header_code', array(
        'default'           => '',
        'sanitize_callback' => 'titancore_sanitize_custom_header_code',
    ) );

    $wp_customize->add_control( 'custom_header_code', array(
        'label'       => __( 'Custom Header Code (before </header>)', 'titancore' ),
        'description' => __( 'Safe mode by default. Only the allowed HTML tags/attributes are kept.', 'titancore' ),
        'section'     => 'header_options',
        'type'        => 'textarea',
    ) );

    $wp_customize->add_setting( 'custom_header_code_raw', array(
        'default'           => false,
        'sanitize_callback' => 'titancore_sanitize_custom_code_raw_mode',
    ) );

    $wp_customize->add_control( 'custom_header_code_raw', array(
        'label'       => __( 'Enable raw header code output', 'titancore' ),
        'description' => __( 'Only for trusted admins. Disables safe-mode filtering for header code.', 'titancore' ),
        'section'     => 'header_options',
        'type'        => 'checkbox',
    ) );
    
    // Navigation Brand Text vs Logo
    $wp_customize->add_setting( 'nav_brand_text', array(
        'default'           => true,
        'sanitize_callback' => 'rest_sanitize_boolean',
    ) );
    
    $wp_customize->add_control( 'nav_brand_text', array(
        'label'       => __( 'Show Site Title text instead of Logo', 'titancore' ),
        'description' => __( 'If a Custom Logo is uploaded, this will override it.', 'titancore' ),
        'section'     => 'header_options',
        'type'        => 'checkbox',
    ) );

    // Theme Colors / Mode Section
    $wp_customize->add_section( 'theme_colors', array(
        'title'    => __( 'Theme Colors & Mode', 'titancore' ),
        'priority' => 31,
    ) );

    // Light / Dark Mode Toggle
    $wp_customize->add_setting( 'theme_color_mode', array(
        'default'           => 'switch',
        'sanitize_callback' => 'titancore_sanitize_select',
    ) );

    $wp_customize->add_control( 'theme_color_mode', array(
        'label'    => __( 'Theme Mode', 'titancore' ),
        'section'  => 'theme_colors',
        'type'     => 'select',
        'choices'  => array(
            'switch' => __( 'Switch (Default)', 'titancore' ),
            'light'  => __( 'Always Light Mode', 'titancore' ),
            'dark'   => __( 'Always Dark Mode', 'titancore' ),
        ),
    ) );

    // Primary/Accent Color
    $wp_customize->add_setting( 'primary_color', array(
        'default'           => '#18181b', // Default primary color
        'sanitize_callback' => 'sanitize_hex_color',
    ) );

    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'primary_color', array(
        'label'    => __( 'Primary Brand Color', 'titancore' ),
        'section'  => 'theme_colors',
    ) ) );

    // Accent Surface Color
    $wp_customize->add_setting( 'accent_color', array(
        'default'           => '#f4f4f5',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );

    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'accent_color', array(
        'label'       => __( 'Accent Surface Color', 'titancore' ),
        'description' => __( 'Used for accent backgrounds, pills, and UI highlights.', 'titancore' ),
        'section'     => 'theme_colors',
    ) ) );

    // Light mode page colors
    $wp_customize->add_setting( 'page_background_color_light', array(
        'default'           => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );

    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'page_background_color_light', array(
        'label'       => __( 'Light Mode Background', 'titancore' ),
        'description' => __( 'Main page background color for light mode.', 'titancore' ),
        'section'     => 'theme_colors',
    ) ) );

    $wp_customize->add_setting( 'page_foreground_color_light', array(
        'default'           => '#09090b',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );

    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'page_foreground_color_light', array(
        'label'       => __( 'Light Mode Text Color', 'titancore' ),
        'description' => __( 'Main text color for light mode.', 'titancore' ),
        'section'     => 'theme_colors',
    ) ) );

    // Dark mode page colors
    $wp_customize->add_setting( 'page_background_color_dark', array(
        'default'           => '#09090b',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );

    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'page_background_color_dark', array(
        'label'       => __( 'Dark Mode Background', 'titancore' ),
        'description' => __( 'Main page background color for dark mode.', 'titancore' ),
        'section'     => 'theme_colors',
    ) ) );

    $wp_customize->add_setting( 'page_foreground_color_dark', array(
        'default'           => '#fafafa',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );

    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'page_foreground_color_dark', array(
        'label'       => __( 'Dark Mode Text Color', 'titancore' ),
        'description' => __( 'Main text color for dark mode.', 'titancore' ),
        'section'     => 'theme_colors',
    ) ) );

    // Grid Pattern Color
    $wp_customize->add_setting( 'grid_pattern_color', array(
        'default'           => '#6b7280',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );

    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'grid_pattern_color', array(
        'label'    => __( 'Grid Pattern Color', 'titancore' ),
        'section'  => 'theme_colors',
    ) ) );

    // Grid Pattern Opacity
    $wp_customize->add_setting( 'grid_pattern_opacity', array(
        'default'           => 0.4,
        'sanitize_callback' => 'titancore_sanitize_float',
    ) );
    
    $wp_customize->add_control( 'grid_pattern_opacity', array(
        'label'       => __( 'Grid Pattern Opacity', 'titancore' ),
        'description' => __( 'Values from 0.0 (invisible) to 1.0 (solid).', 'titancore' ),
        'section'     => 'theme_colors',
        'type'        => 'number',
        'input_attrs' => array(
            'min'  => 0,
            'max'  => 1,
            'step' => 0.05,
        ),
    ) );

    // Single Post Options Section
	$wp_customize->add_section( 'post_options', array(
		'title'    => __( 'Single Post Options', 'titancore' ),
		'priority' => 35,
	) );

    // Table of Contents Toggle
	$wp_customize->add_setting( 'show_toc', array(
		'default'           => true,
		'sanitize_callback' => 'rest_sanitize_boolean',
	) );

	$wp_customize->add_control( 'show_toc', array(
		'label'    => __( 'Show Automatic Table of Contents in Sidebar', 'titancore' ),
		'section'  => 'post_options',
		'type'     => 'checkbox',
	) );

    // Tag Limit Configuration
	$wp_customize->add_section( 'tag_options', array(
		'title'    => __( 'Front Page Options', 'titancore' ),
		'priority' => 36,
	) );

    $wp_customize->add_setting( 'tag_limit', array(
        'default'           => 5,
        'sanitize_callback' => 'absint',
    ) );
    
    $wp_customize->add_control( 'tag_limit', array(
        'label'       => __( 'Number of Tags to Show', 'titancore' ),
        'description' => __( 'Limit the number of top tags shown on the front page/home. Set to 0 to show all.', 'titancore' ),
        'section'     => 'tag_options',
        'type'        => 'number',
        'input_attrs' => array(
            'min' => 0,
            'max' => 100,
        ),
    ) );

    // Post Limit Configuration
    $wp_customize->add_setting( 'home_post_limit', array(
        'default'           => 10,
        'sanitize_callback' => 'absint',
    ) );
    
    $wp_customize->add_control( 'home_post_limit', array(
        'label'       => __( 'Number of Posts to Show', 'titancore' ),
        'description' => __( 'Limit the number of posts displayed on the blog index and archives. Overrides WP Reading settings.', 'titancore' ),
        'section'     => 'tag_options',
        'type'        => 'number',
        'input_attrs' => array(
            'min' => 1,
            'max' => 100,
        ),
    ) );

    // Frontpage Preset
    $wp_customize->add_setting( 'frontpage_preset', array(
        'default'           => 'blog',
        'sanitize_callback' => 'titancore_sanitize_select',
    ) );

    $wp_customize->add_control( 'frontpage_preset', array(
        'label'    => __( 'Frontpage Layout Preset', 'titancore' ),
        'section'  => 'tag_options',
        'type'     => 'select',
        'choices'  => array(
            'blog'     => __( 'Modern Blog (Grid)', 'titancore' ),
            'news'     => __( 'News Portal', 'titancore' ),
            'magazine' => __( 'Magazine', 'titancore' ),
        ),
    ) );

    // Footer Options Section
    $wp_customize->add_section( 'footer_options', array(
        'title'    => __( 'Footer Options', 'titancore' ),
        'priority' => 45,
    ) );

    // Custom Footer Code
    $wp_customize->add_setting( 'custom_footer_code', array(
        'default'           => '',
        'sanitize_callback' => 'titancore_sanitize_custom_footer_code',
    ) );

    $wp_customize->add_control( 'custom_footer_code', array(
        'label'       => __( 'Custom Footer Code (before </footer>)', 'titancore' ),
        'description' => __( 'Safe mode by default. Only the allowed HTML tags/attributes are kept.', 'titancore' ),
        'section'     => 'footer_options',
        'type'        => 'textarea',
    ) );

    $wp_customize->add_setting( 'custom_footer_code_raw', array(
        'default'           => false,
        'sanitize_callback' => 'titancore_sanitize_custom_code_raw_mode',
    ) );

    $wp_customize->add_control( 'custom_footer_code_raw', array(
        'label'       => __( 'Enable raw footer code output', 'titancore' ),
        'description' => __( 'Only for trusted admins. Disables safe-mode filtering for footer code.', 'titancore' ),
        'section'     => 'footer_options',
        'type'        => 'checkbox',
    ) );
}
add_action( 'customize_register', 'titancore_customize_register' );

/**
 * Sanitize select inputs by checking against valid choices.
 */
function titancore_sanitize_select( $input, $setting ) {
    $input = sanitize_text_field( $input );
    $choices = $setting->manager->get_control( $setting->id )->choices;
    return ( array_key_exists( $input, $choices ) ? $input : $setting->default );
}

/**
 * Raw mode toggle sanitizer for custom code controls.
 * Only users with unfiltered_html may enable raw output mode.
 */
function titancore_sanitize_custom_code_raw_mode( $enabled ) {
    if ( ! current_user_can( 'unfiltered_html' ) ) {
        return false;
    }

    return rest_sanitize_boolean( $enabled );
}

/**
 * Get the allowed HTML tags/attributes for custom code safe mode.
 */
function titancore_get_custom_code_allowed_tags( $slot ) {
    $allowed_tags = array(
        'meta'     => array(
            'name'       => true,
            'content'    => true,
            'property'   => true,
            'charset'    => true,
            'http-equiv' => true,
        ),
        'link'     => array(
            'rel'            => true,
            'href'           => true,
            'type'           => true,
            'media'          => true,
            'crossorigin'    => true,
            'integrity'      => true,
            'referrerpolicy' => true,
            'fetchpriority'  => true,
            'sizes'          => true,
        ),
        'noscript' => array(),
        'style'    => array(
            'type'  => true,
            'media' => true,
            'nonce' => true,
        ),
    );

    if ( 'footer' === $slot ) {
        $allowed_tags['iframe'] = array(
            'src'             => true,
            'width'           => true,
            'height'          => true,
            'style'           => true,
            'loading'         => true,
            'referrerpolicy'  => true,
            'allow'           => true,
            'allowfullscreen' => true,
            'title'           => true,
        );
    }

    return apply_filters( 'titancore_custom_code_allowed_tags', $allowed_tags, $slot );
}

/**
 * Resolve whether raw mode is enabled for a custom code slot.
 */
function titancore_is_custom_code_raw_mode_enabled( $slot, $customize_manager = null ) {
    $setting_id = ( 'footer' === $slot ) ? 'custom_footer_code_raw' : 'custom_header_code_raw';

    if ( is_object( $customize_manager ) && method_exists( $customize_manager, 'post_value' ) ) {
        $pending_value = $customize_manager->post_value( $setting_id );
        if ( null !== $pending_value ) {
            return rest_sanitize_boolean( $pending_value );
        }
    }

    return rest_sanitize_boolean( get_theme_mod( $setting_id, false ) );
}

/**
 * Shared sanitizer for custom code fields by output slot.
 */
function titancore_sanitize_custom_code_by_slot( $input, $slot, $customize_manager = null ) {
    $input = (string) $input;
    if ( '' === trim( $input ) ) {
        return '';
    }

    if ( titancore_is_custom_code_raw_mode_enabled( $slot, $customize_manager ) && current_user_can( 'unfiltered_html' ) ) {
        return $input;
    }

    return wp_kses( $input, titancore_get_custom_code_allowed_tags( $slot ) );
}

/**
 * Sanitize custom header code.
 */
function titancore_sanitize_custom_header_code( $input, $setting = null ) {
    $manager = ( is_object( $setting ) && isset( $setting->manager ) ) ? $setting->manager : null;
    return titancore_sanitize_custom_code_by_slot( $input, 'header', $manager );
}

/**
 * Sanitize custom footer code.
 */
function titancore_sanitize_custom_footer_code( $input, $setting = null ) {
    $manager = ( is_object( $setting ) && isset( $setting->manager ) ) ? $setting->manager : null;
    return titancore_sanitize_custom_code_by_slot( $input, 'footer', $manager );
}

/**
 * Get custom code output for frontend rendering.
 */
function titancore_get_custom_code_output( $slot ) {
    $slot = ( 'footer' === $slot ) ? 'footer' : 'header';
    $setting_id = ( 'footer' === $slot ) ? 'custom_footer_code' : 'custom_header_code';
    $code = (string) get_theme_mod( $setting_id, '' );

    if ( '' === trim( $code ) ) {
        return '';
    }

    if ( titancore_is_custom_code_raw_mode_enabled( $slot ) ) {
        return $code;
    }

    return wp_kses( $code, titancore_get_custom_code_allowed_tags( $slot ) );
}

/**
 * Sanitize float for opacity settings
 */
function titancore_sanitize_float( $input ) {
    return floatval( $input );
}
