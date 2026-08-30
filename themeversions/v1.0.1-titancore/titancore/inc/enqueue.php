<?php
/**
 * Enqueue scripts and styles
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue scripts and styles.
 */
function titancore_scripts() {
	// Setup version using filemtime for cache busting
	$css_min = get_template_directory() . '/assets/css/style.min.css';
	$css_fallback = get_template_directory() . '/assets/css/style.css';
	$css_file = file_exists( $css_min ) ? $css_min : $css_fallback;
	$css_uri = file_exists( $css_min )
		? get_template_directory_uri() . '/assets/css/style.min.css'
		: get_template_directory_uri() . '/assets/css/style.css';
	$css_version = file_exists( $css_file ) ? filemtime( $css_file ) : false;

	$enhancements_min = get_template_directory() . '/assets/css/enhancements.min.css';
	$enhancements_fallback = get_template_directory() . '/assets/css/enhancements.css';
	$enhancements_file = file_exists( $enhancements_min ) ? $enhancements_min : $enhancements_fallback;
	$enhancements_uri = file_exists( $enhancements_min )
		? get_template_directory_uri() . '/assets/css/enhancements.min.css'
		: get_template_directory_uri() . '/assets/css/enhancements.css';
	$enhancements_version = file_exists( $enhancements_file ) ? filemtime( $enhancements_file ) : false;

	// Enqueue main utility CSS, preferring the minified asset when present.
	wp_enqueue_style( 'titancore-style', $css_uri, array(), $css_version );
	wp_enqueue_style( 'titancore-enhancements', $enhancements_uri, array( 'titancore-style' ), $enhancements_version );

	// Front page preset CSS.
	// Keep this scoped to the actual front page template to avoid dead CSS on blog index pages.
	if ( is_front_page() ) {
		$presets_min = get_template_directory() . '/assets/css/frontpage-presets.min.css';
		$presets_fallback = get_template_directory() . '/assets/css/frontpage-presets.css';
		$presets_file = file_exists( $presets_min ) ? $presets_min : $presets_fallback;
		$presets_uri = file_exists( $presets_min )
			? get_template_directory_uri() . '/assets/css/frontpage-presets.min.css'
			: get_template_directory_uri() . '/assets/css/frontpage-presets.css';
		$presets_version = file_exists( $presets_file ) ? filemtime( $presets_file ) : false;
		wp_enqueue_style( 'titancore-frontpage-presets', $presets_uri, array( 'titancore-enhancements' ), $presets_version );
	}

	// Add dynamic theme variables as inline style on the main stylesheet
	wp_add_inline_style( 'titancore-style', titancore_generate_theme_variables_css() );

	$theme_mode = get_theme_mod( 'theme_color_mode', 'switch' );
	$should_load_navigation = has_nav_menu( 'primary' ) || 'switch' === $theme_mode;

	if ( $should_load_navigation ) {
		// Navigation/theme-toggle JS (use minified if available).
		$js_min = get_template_directory() . '/assets/js/navigation.min.js';
		$js_fallback = get_template_directory() . '/assets/js/navigation.js';
		$js_file = file_exists( $js_min ) ? $js_min : $js_fallback;
		$js_uri = file_exists( $js_min )
			? get_template_directory_uri() . '/assets/js/navigation.min.js'
			: get_template_directory_uri() . '/assets/js/navigation.js';
		$js_version = file_exists( $js_file ) ? filemtime( $js_file ) : false;

		wp_enqueue_script( 'titancore-navigation', $js_uri, array(), $js_version, array(
			'in_footer' => true,
			'strategy'  => 'defer',
		) );
	}

	// Conditionally load comment-reply script only when needed
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'titancore_scripts' );

/**
 * Enable separate core block asset loading on the frontend.
 * WordPress will load only styles for blocks that are actually rendered.
 */
function titancore_enable_separate_core_block_assets( $load_separate_assets ) {
	if ( is_admin() ) {
		return $load_separate_assets;
	}

	return true;
}
add_filter( 'should_load_separate_core_block_assets', 'titancore_enable_separate_core_block_assets' );

/**
 * Determine whether current request needs core block/global frontend assets.
 */
function titancore_should_keep_core_block_assets() {
	if ( is_admin() || is_customize_preview() ) {
		return true;
	}

	if ( ! is_singular() ) {
		return (bool) apply_filters( 'titancore_should_keep_core_block_assets', false );
	}

	$post = get_queried_object();
	$should_keep = ( $post instanceof WP_Post ) && has_blocks( $post->post_content );

	return (bool) apply_filters( 'titancore_should_keep_core_block_assets', $should_keep );
}

/**
 * Remove frontend core assets that are commonly flagged as unused on classic-theme templates.
 */
function titancore_optimize_frontend_core_assets() {
	if ( is_admin() ) {
		return;
	}

	// Not needed for most frontends and often flagged as unused JS.
	wp_dequeue_script( 'wp-embed' );

	// TitanCore itself does not need frontend jQuery, but plugins often still do.
	// Keep it registered by default and make full removal an explicit opt-in.
	if ( apply_filters( 'titancore_disable_frontend_jquery', false ) ) {
		wp_dequeue_script( 'jquery' );
		wp_deregister_script( 'jquery' );
		wp_dequeue_script( 'jquery-core' );
		wp_deregister_script( 'jquery-core' );
		wp_dequeue_script( 'jquery-migrate' );
		wp_deregister_script( 'jquery-migrate' );
	}

	if ( titancore_should_keep_core_block_assets() ) {
		return;
	}

	$handles = array(
		'wp-block-library',
		'wp-block-library-theme',
		'classic-theme-styles',
		'global-styles',
	);

	foreach ( $handles as $handle ) {
		wp_dequeue_style( $handle );
	}
}
add_action( 'wp_enqueue_scripts', 'titancore_optimize_frontend_core_assets', 999 );

/**
 * Check whether a style/script handle or URL points to Font Awesome assets.
 */
function titancore_is_fontawesome_asset( $handle, $src = '' ) {
	$haystack = strtolower( (string) $handle . ' ' . (string) $src );
	return ( false !== strpos( $haystack, 'fontawesome' ) ) || ( false !== strpos( $haystack, 'font-awesome' ) );
}

/**
 * Remove Font Awesome from frontend output unless explicitly allowed.
 */
function titancore_block_fontawesome_assets() {
	if ( is_admin() ) {
		return;
	}

	$should_block = (bool) apply_filters( 'titancore_block_fontawesome_assets', true );
	if ( ! $should_block ) {
		return;
	}

	global $wp_styles, $wp_scripts;

	if ( isset( $wp_styles->queue ) && is_array( $wp_styles->queue ) ) {
		foreach ( $wp_styles->queue as $handle ) {
			$src = '';
			if ( isset( $wp_styles->registered[ $handle ]->src ) ) {
				$src = (string) $wp_styles->registered[ $handle ]->src;
			}

			if ( titancore_is_fontawesome_asset( $handle, $src ) ) {
				wp_dequeue_style( $handle );
				wp_deregister_style( $handle );
			}
		}
	}

	if ( isset( $wp_scripts->queue ) && is_array( $wp_scripts->queue ) ) {
		foreach ( $wp_scripts->queue as $handle ) {
			$src = '';
			if ( isset( $wp_scripts->registered[ $handle ]->src ) ) {
				$src = (string) $wp_scripts->registered[ $handle ]->src;
			}

			if ( titancore_is_fontawesome_asset( $handle, $src ) ) {
				wp_dequeue_script( $handle );
				wp_deregister_script( $handle );
			}
		}
	}
}
add_action( 'wp_print_styles', 'titancore_block_fontawesome_assets', 999 );
add_action( 'wp_print_scripts', 'titancore_block_fontawesome_assets', 999 );

/**
 * Generate dynamic CSS custom properties from Customizer settings.
 * Called via wp_add_inline_style to avoid render-blocking inline <style> in HTML.
 */
function titancore_generate_theme_variables_css() {
	$primary_color    = sanitize_hex_color( get_theme_mod( 'primary_color', '#18181b' ) ) ?: '#18181b';
	$accent_color     = sanitize_hex_color( get_theme_mod( 'accent_color', '#f4f4f5' ) ) ?: '#f4f4f5';
	$light_background = sanitize_hex_color( get_theme_mod( 'page_background_color_light', '#ffffff' ) ) ?: '#ffffff';
	$light_foreground = sanitize_hex_color( get_theme_mod( 'page_foreground_color_light', '#09090b' ) ) ?: '#09090b';
	$dark_background  = sanitize_hex_color( get_theme_mod( 'page_background_color_dark', '#09090b' ) ) ?: '#09090b';
	$dark_foreground  = sanitize_hex_color( get_theme_mod( 'page_foreground_color_dark', '#fafafa' ) ) ?: '#fafafa';

	$primary_foreground = titancore_get_contrast_text_color( $primary_color );
	$accent_foreground  = titancore_get_contrast_text_color( $accent_color );
	$light_secondary    = titancore_mix_hex_colors( $light_background, $accent_color, 0.12 );
	$dark_secondary     = titancore_mix_hex_colors( $dark_background, $accent_color, 0.22 );

	$light_fg_rgb = titancore_hex_to_rgb( $light_foreground );
	$dark_fg_rgb  = titancore_hex_to_rgb( $dark_foreground );
	$light_bg_rgb = titancore_hex_to_rgb( $light_background );
	$dark_bg_rgb  = titancore_hex_to_rgb( $dark_background );

	$light_border      = sprintf( 'rgba(%d,%d,%d,0.14)', $light_fg_rgb['r'], $light_fg_rgb['g'], $light_fg_rgb['b'] );
	$dark_border       = sprintf( 'rgba(%d,%d,%d,0.20)', $dark_fg_rgb['r'], $dark_fg_rgb['g'], $dark_fg_rgb['b'] );
	$light_muted       = sprintf( 'rgba(%d,%d,%d,0.62)', $light_fg_rgb['r'], $light_fg_rgb['g'], $light_fg_rgb['b'] );
	$dark_muted        = sprintf( 'rgba(%d,%d,%d,0.68)', $dark_fg_rgb['r'], $dark_fg_rgb['g'], $dark_fg_rgb['b'] );
	$light_bg_95       = sprintf( 'rgba(%d,%d,%d,0.95)', $light_bg_rgb['r'], $light_bg_rgb['g'], $light_bg_rgb['b'] );
	$dark_bg_95        = sprintf( 'rgba(%d,%d,%d,0.95)', $dark_bg_rgb['r'], $dark_bg_rgb['g'], $dark_bg_rgb['b'] );
	$light_nav         = sprintf( 'rgba(%d,%d,%d,0.62)', $light_fg_rgb['r'], $light_fg_rgb['g'], $light_fg_rgb['b'] );
	$light_nav_h       = sprintf( 'rgba(%d,%d,%d,0.86)', $light_fg_rgb['r'], $light_fg_rgb['g'], $light_fg_rgb['b'] );
	$dark_nav          = sprintf( 'rgba(%d,%d,%d,0.70)', $dark_fg_rgb['r'], $dark_fg_rgb['g'], $dark_fg_rgb['b'] );
	$dark_nav_h        = sprintf( 'rgba(%d,%d,%d,0.94)', $dark_fg_rgb['r'], $dark_fg_rgb['g'], $dark_fg_rgb['b'] );
	$light_border_soft = sprintf( 'rgba(%d,%d,%d,0.16)', $light_fg_rgb['r'], $light_fg_rgb['g'], $light_fg_rgb['b'] );
	$dark_border_soft  = sprintf( 'rgba(%d,%d,%d,0.24)', $dark_fg_rgb['r'], $dark_fg_rgb['g'], $dark_fg_rgb['b'] );

	$css = ':root{';
	$css .= '--background:' . $light_background . ';';
	$css .= '--foreground:' . $light_foreground . ';';
	$css .= '--card:' . $light_background . ';';
	$css .= '--card-foreground:' . $light_foreground . ';';
	$css .= '--popover:' . $light_background . ';';
	$css .= '--popover-foreground:' . $light_foreground . ';';
	$css .= '--primary:' . $primary_color . ';';
	$css .= '--primary-foreground:' . $primary_foreground . ';';
	$css .= '--secondary:' . $light_secondary . ';';
	$css .= '--secondary-foreground:' . $accent_foreground . ';';
	$css .= '--muted:' . $light_secondary . ';';
	$css .= '--muted-foreground:' . $light_muted . ';';
	$css .= '--accent:' . $accent_color . ';';
	$css .= '--accent-foreground:' . $accent_foreground . ';';
	$css .= '--border:' . $light_border . ';';
	$css .= '--input:' . $light_border . ';';
	$css .= '--ring:' . $primary_color . ';';
	$css .= '}';

	$css .= '.dark{';
	$css .= '--background:' . $dark_background . ';';
	$css .= '--foreground:' . $dark_foreground . ';';
	$css .= '--card:' . $dark_background . ';';
	$css .= '--card-foreground:' . $dark_foreground . ';';
	$css .= '--popover:' . $dark_background . ';';
	$css .= '--popover-foreground:' . $dark_foreground . ';';
	$css .= '--primary:' . $primary_color . ';';
	$css .= '--primary-foreground:' . $primary_foreground . ';';
	$css .= '--secondary:' . $dark_secondary . ';';
	$css .= '--secondary-foreground:' . $accent_foreground . ';';
	$css .= '--muted:' . $dark_secondary . ';';
	$css .= '--muted-foreground:' . $dark_muted . ';';
	$css .= '--accent:' . $accent_color . ';';
	$css .= '--accent-foreground:' . $accent_foreground . ';';
	$css .= '--border:' . $dark_border . ';';
	$css .= '--input:' . $dark_border . ';';
	$css .= '--ring:' . $primary_color . ';';
	$css .= '}';

	$css .= '.hover\\:bg-primary\\/90:hover{background-color:color-mix(in srgb,' . $primary_color . ' 90%,transparent)!important}';
	$css .= '.bg-background\\/95{background-color:' . $light_bg_95 . '}';
	$css .= '.dark .bg-background\\/95{background-color:' . $dark_bg_95 . '}';
	$css .= '.border-border\\/40{border-color:' . $light_border_soft . '}';
	$css .= '.dark .border-border\\/40{border-color:' . $dark_border_soft . '}';
	$css .= 'header nav ul.flex li.menu-item a{color:' . $light_nav . '}';
	$css .= '.dark header nav ul.flex li.menu-item a{color:' . $dark_nav . '}';
	$css .= 'header nav ul.flex li.menu-item a:hover{color:' . $light_nav_h . '}';
	$css .= '.dark header nav ul.flex li.menu-item a:hover{color:' . $dark_nav_h . '}';

	return $css;
}

/**
 * Preload locally hosted variable font.
 */
function titancore_should_preload_main_font() {
	/**
	 * Filter whether the local Inter variable font should be preloaded.
	 * Disabled by default because the font file is large and can delay first render on slower connections.
	 */
	return (bool) apply_filters( 'titancore_preload_main_font', false );
}

/**
 * Preload locally hosted variable font.
 */
function titancore_preload_resources( $preload_resources ) {
	if ( ! titancore_should_preload_main_font() ) {
		return $preload_resources;
	}

	$font_file = get_template_directory() . '/assets/fonts/inter/InterVariable.woff2';
	if ( file_exists( $font_file ) ) {
		$preload_resources[] = array(
			'href'        => get_template_directory_uri() . '/assets/fonts/inter/InterVariable.woff2',
			'as'          => 'font',
			'type'        => 'font/woff2',
			'crossorigin' => 'anonymous',
		);
	}

	return $preload_resources;
}
add_filter( 'wp_preload_resources', 'titancore_preload_resources' );

/**
 * Fallback font preload for older WordPress versions.
 */
function titancore_font_preload_fallback() {
	if ( ! titancore_should_preload_main_font() ) {
		return;
	}

	if ( function_exists( 'wp_preload_resources' ) ) {
		return;
	}

	$font_file = get_template_directory() . '/assets/fonts/inter/InterVariable.woff2';
	if ( ! file_exists( $font_file ) ) {
		return;
	}

	echo '<link rel="preload" href="' . esc_url( get_template_directory_uri() . '/assets/fonts/inter/InterVariable.woff2' ) . '" as="font" type="font/woff2" crossorigin="anonymous">' . "\n";
}
add_action( 'wp_head', 'titancore_font_preload_fallback', 1 );

/**
 * Add resource hints for external origins used by the theme.
 */
function titancore_resource_hints( $urls, $relation_type ) {
	if ( 'dns-prefetch' === $relation_type && is_singular() && ( comments_open() || get_comments_number() > 0 ) ) {
		$urls[] = '//secure.gravatar.com';
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'titancore_resource_hints', 10, 2 );
