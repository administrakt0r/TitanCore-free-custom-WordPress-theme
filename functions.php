<?php
/**
 * Theme functions and definitions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

function titancore_setup() {
	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	// Let WordPress manage the document title.
	add_theme_support( 'title-tag' );

	// Enable support for Post Thumbnails on posts and pages.
	add_theme_support( 'post-thumbnails' );
    
    // Custom logo support
    add_theme_support( 'custom-logo', array(
        'height'               => 40,
        'width'                => 40,
        'flex-height'          => true,
        'flex-width'           => true,
        'header-text'          => array( 'site-title', 'site-description' ),
    ) );

	// Switch default core markup for search form, comment form, and comments to output valid HTML5.
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Modern block editor support
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'align-wide' );

	// Register menu locations
	register_nav_menus(
		array(
			'primary'   => esc_html__( 'Primary Menu (Top)', 'titancore' ),
			'secondary' => esc_html__( 'Secondary Menu (Tags/Categories)', 'titancore' ),
			'footer'    => esc_html__( 'Footer Menu', 'titancore' ),
		)
	);
}
add_action( 'after_setup_theme', 'titancore_setup' );

/**
 * Performance: Remove unnecessary head clutter.
 */
function titancore_cleanup_head() {
	remove_action( 'wp_head', 'wp_generator' );              // Remove WP version meta
	remove_action( 'wp_head', 'wlwmanifest_link' );           // Remove Windows Live Writer
	remove_action( 'wp_head', 'rsd_link' );                   // Remove RSD link
	remove_action( 'wp_head', 'wp_shortlink_wp_head' );       // Remove shortlink
	remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10 ); // Remove prev/next post links
	remove_action( 'wp_head', 'wp_oembed_add_discovery_links' ); // Remove oEmbed discovery links
	remove_action( 'wp_head', 'wp_oembed_add_host_js' ); // Remove oEmbed host JS loader
}
add_action( 'after_setup_theme', 'titancore_cleanup_head' );

/**
 * Performance: Disable WordPress emoji scripts and styles.
 * Saves ~2 HTTP requests and ~15KB on every page.
 */
function titancore_disable_emojis() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );

	add_filter( 'tiny_mce_plugins', function ( $plugins ) {
		return is_array( $plugins ) ? array_diff( $plugins, array( 'wpemoji' ) ) : array();
	} );

	add_filter( 'wp_resource_hints', function ( $urls, $relation_type ) {
		if ( 'dns-prefetch' === $relation_type ) {
			$urls = array_filter( $urls, function ( $url ) {
				return ! is_string( $url ) || false === strpos( $url, 'https://s.w.org/images/core/emoji/' );
			} );
		}
		return $urls;
	}, 10, 2 );
}
add_action( 'init', 'titancore_disable_emojis' );

/**
 * Include core theme structure components natively decoupled
 */
require_once get_template_directory() . '/inc/enqueue.php';
require_once get_template_directory() . '/inc/customizer.php';
require_once get_template_directory() . '/inc/template-tags.php';
require_once get_template_directory() . '/inc/seo-schema.php';

if ( is_admin() ) {
    require_once get_template_directory() . '/inc/admin.php';
}
