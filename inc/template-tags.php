<?php
/**
 * Custom template tags for this theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Override the default posts per page for public archive-style loops.
 */
function titancore_modify_home_query( $query ) {
	if ( ! is_admin() && $query->is_main_query() ) {
		// Front-page template uses dedicated loops, so skip expensive row-count work on main query.
		if ( is_front_page() ) {
			$query->set( 'no_found_rows', true );
		}

		if ( is_home() || is_front_page() || is_archive() || is_search() ) {
			$limit = get_theme_mod( 'home_post_limit', 10 );
			if ( $limit > 0 ) {
				$query->set( 'posts_per_page', $limit );
			}
		}
	}
}
add_action( 'pre_get_posts', 'titancore_modify_home_query' );

/**
 * Keep a version suffix for transient keys so cache flush can be O(1).
 */
function titancore_get_cache_version() {
	$version = get_option( 'titancore_cache_version', '1' );
	return preg_replace( '/[^0-9]/', '', (string) $version );
}

/**
 * Bump cache version whenever content/taxonomy changes.
 */
function titancore_bump_cache_version( $object_id = 0 ) {
	if ( $object_id && function_exists( 'wp_is_post_revision' ) && wp_is_post_revision( $object_id ) ) {
		return;
	}

	update_option( 'titancore_cache_version', (string) time(), false );
}
add_action( 'save_post', 'titancore_bump_cache_version' );
add_action( 'deleted_post', 'titancore_bump_cache_version' );
add_action( 'set_object_terms', 'titancore_bump_cache_version' );
add_action( 'created_term', 'titancore_bump_cache_version' );
add_action( 'edited_term', 'titancore_bump_cache_version' );
add_action( 'delete_term', 'titancore_bump_cache_version' );

/**
 * Cache top tags list used in home/front headers.
 */
function titancore_get_top_tags( $limit = 5 ) {
	$limit = absint( $limit );

	$cache_key = sprintf(
		'titancore_top_tags_%1$d_v%2$s',
		$limit,
		titancore_get_cache_version()
	);

	$cached_tags = get_transient( $cache_key );
	if ( false !== $cached_tags && is_array( $cached_tags ) ) {
		return $cached_tags;
	}

	$args = array(
		'orderby' => 'count',
		'order'   => 'DESC',
	);

	if ( $limit > 0 ) {
		$args['number'] = $limit;
	}

	$tags = get_tags( $args );
	if ( ! is_array( $tags ) ) {
		$tags = array();
	}

	set_transient( $cache_key, $tags, 6 * HOUR_IN_SECONDS );
	return $tags;
}

/**
 * Cache published post count used in home/front tag headers.
 */
function titancore_get_published_posts_count() {
	$cache_key = sprintf(
		'titancore_published_count_v%s',
		titancore_get_cache_version()
	);

	$cached_count = get_transient( $cache_key );
	if ( false !== $cached_count ) {
		return absint( $cached_count );
	}

	$counts = wp_count_posts( 'post' );
	$count  = isset( $counts->publish ) ? absint( $counts->publish ) : 0;

	set_transient( $cache_key, $count, HOUR_IN_SECONDS );
	return $count;
}

/**
 * Get a reliable URL for the posts index page.
 */
function titancore_get_posts_page_url() {
	$posts_page_id = absint( get_option( 'page_for_posts' ) );
	if ( $posts_page_id > 0 ) {
		$url = get_permalink( $posts_page_id );
		if ( $url ) {
			return $url;
		}
	}

	return home_url( '/' );
}

/**
 * Convert a HEX color to RGB.
 */
function titancore_hex_to_rgb( $hex_color ) {
	$hex = sanitize_hex_color( $hex_color );
	if ( ! $hex ) {
		return array(
			'r' => 0,
			'g' => 0,
			'b' => 0,
		);
	}

	$hex = ltrim( $hex, '#' );
	if ( 3 === strlen( $hex ) ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}

	return array(
		'r' => hexdec( substr( $hex, 0, 2 ) ),
		'g' => hexdec( substr( $hex, 2, 2 ) ),
		'b' => hexdec( substr( $hex, 4, 2 ) ),
	);
}

/**
 * Pick readable foreground color (dark/light) for a given background hex.
 */
function titancore_get_contrast_text_color( $background_hex, $light = '#fafafa', $dark = '#18181b' ) {
	$rgb = titancore_hex_to_rgb( $background_hex );
	$luma = ( 0.2126 * $rgb['r'] ) + ( 0.7152 * $rgb['g'] ) + ( 0.0722 * $rgb['b'] );

	return ( $luma < 145 ) ? $light : $dark;
}

/**
 * Mix two HEX colors by ratio (0..1) where $ratio favors $mix_hex.
 */
function titancore_mix_hex_colors( $base_hex, $mix_hex, $ratio = 0.5 ) {
	$ratio = max( 0, min( 1, (float) $ratio ) );
	$base  = titancore_hex_to_rgb( $base_hex );
	$mix   = titancore_hex_to_rgb( $mix_hex );

	$r = (int) round( ( (1 - $ratio) * $base['r'] ) + ( $ratio * $mix['r'] ) );
	$g = (int) round( ( (1 - $ratio) * $base['g'] ) + ( $ratio * $mix['g'] ) );
	$b = (int) round( ( (1 - $ratio) * $base['b'] ) + ( $ratio * $mix['b'] ) );

	return sprintf( '#%02x%02x%02x', $r, $g, $b );
}

/**
 * Responsive image size map for Core Web Vitals-friendly outputs.
 */
function titancore_get_image_sizes( $context = 'default' ) {
	switch ( $context ) {
		case 'grid-card':
			return '(min-width: 1280px) 400px, (min-width: 1024px) 33vw, (min-width: 768px) 50vw, 100vw';
		case 'news-hero':
			return '(min-width: 1280px) 840px, (min-width: 1024px) 66vw, 100vw';
		case 'magazine-hero':
			return '(min-width: 1280px) 620px, (min-width: 1024px) 50vw, 100vw';
		case 'magazine-row':
			return '(min-width: 1024px) 320px, (min-width: 768px) 50vw, 100vw';
		case 'single-hero':
		case 'page-hero':
		default:
			return '100vw';
	}
}

/**
 * Estimate reading time for a post.
 */
function titancore_get_estimated_reading_time( $post_id = 0 ) {
	$post_id = $post_id ? absint( $post_id ) : get_the_ID();
	if ( ! $post_id ) {
		return 1;
	}

	$content = get_post_field( 'post_content', $post_id );
	$content = wp_strip_all_tags( strip_shortcodes( (string) $content ) );
	$count   = str_word_count( $content );

	return max( 1, (int) ceil( $count / 220 ) );
}

/**
 * Parse post content once and cache generated heading IDs + TOC structure.
 */
function titancore_parse_toc_data( $post_id, $content ) {
	$post_id = absint( $post_id );
	$content = (string) $content;

	if ( '' === trim( $content ) ) {
		return array(
			'content'  => $content,
			'headings' => array(),
		);
	}

	static $runtime_cache = array();
	$cache_hash = md5( $content );
	$runtime_id = $post_id . ':' . $cache_hash;

	if ( isset( $runtime_cache[ $runtime_id ] ) ) {
		return $runtime_cache[ $runtime_id ];
	}

	$transient_key = 'titancore_toc_' . $post_id . '_' . substr( $cache_hash, 0, 12 );
	$cached_data   = get_transient( $transient_key );

	if (
		is_array( $cached_data ) &&
		isset( $cached_data['content'], $cached_data['headings'] ) &&
		is_array( $cached_data['headings'] )
	) {
		$runtime_cache[ $runtime_id ] = $cached_data;
		return $cached_data;
	}

	$result = array(
		'content'  => $content,
		'headings' => array(),
	);

	$dom             = new DOMDocument();
	$previous_errors = libxml_use_internal_errors( true );
	$content_encoded = mb_encode_numericentity( $content, array( 0x80, 0x10FFFF, 0, 0x1fffff ), 'UTF-8' );

	$loaded = $dom->loadHTML(
		'<?xml encoding="utf-8" ?><div>' . $content_encoded . '</div>',
		LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
	);

	libxml_clear_errors();
	libxml_use_internal_errors( $previous_errors );

	if ( ! $loaded ) {
		$runtime_cache[ $runtime_id ] = $result;
		return $result;
	}

	$used_ids       = array();
	$fallback_count = 1;
	$elements       = $dom->getElementsByTagName( '*' );

	foreach ( $elements as $node ) {
		$tag = strtolower( $node->nodeName );
		if ( ! in_array( $tag, array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ), true ) ) {
			continue;
		}

		$text = trim( wp_strip_all_tags( $node->textContent ) );
		$base = '';

		if ( $node->hasAttribute( 'id' ) ) {
			$base = sanitize_title( $node->getAttribute( 'id' ) );
		}
		if ( '' === $base ) {
			$base = sanitize_title( $text );
		}
		if ( '' === $base ) {
			$base = 'section-' . $fallback_count;
			$fallback_count++;
		}

		$unique_id = $base;
		$suffix    = 2;
		while ( isset( $used_ids[ $unique_id ] ) ) {
			$unique_id = $base . '-' . $suffix;
			$suffix++;
		}
		$used_ids[ $unique_id ] = true;

		$node->setAttribute( 'id', $unique_id );

		if ( 'h2' === $tag || 'h3' === $tag ) {
			$result['headings'][] = array(
				'level' => ( 'h2' === $tag ) ? 2 : 3,
				'text'  => $text,
				'id'    => $unique_id,
			);
		}
	}

	$new_content = $dom->saveHTML( $dom->documentElement );
	if ( is_string( $new_content ) ) {
		$stripped_content = preg_replace( '~^<div>(.*)</div>$~s', '$1', $new_content );
		if ( is_string( $stripped_content ) ) {
			$decoded_content = mb_decode_numericentity( $stripped_content, array( 0x80, 0x10FFFF, 0, 0x1fffff ), 'UTF-8' );
			if ( is_string( $decoded_content ) && '' !== $decoded_content ) {
				$result['content'] = $decoded_content;
			}
		}
	}

	set_transient( $transient_key, $result, DAY_IN_SECONDS );
	$runtime_cache[ $runtime_id ] = $result;

	return $result;
}

/**
 * Filter content to add IDs to headings for the Table of Contents.
 */
function titancore_add_ids_to_headings( $content ) {
	if ( ! is_single() || ! get_theme_mod( 'show_toc', true ) || empty( $content ) ) {
		return $content;
	}

	$post_id = get_the_ID();
	if ( ! $post_id ) {
		return $content;
	}

	$parsed = titancore_parse_toc_data( $post_id, $content );
	return ! empty( $parsed['content'] ) ? $parsed['content'] : $content;
}
add_filter( 'the_content', 'titancore_add_ids_to_headings' );

/**
 * Automatically generate a Table of Contents from post content.
 */
function titancore_generate_toc() {
	$post_id = get_the_ID();
	if ( ! $post_id ) {
		echo '<p class="text-sm text-muted-foreground italic">' . esc_html__( 'No headings found.', 'titancore' ) . '</p>';
		return;
	}

	$content = get_post_field( 'post_content', $post_id );
	$parsed  = titancore_parse_toc_data( $post_id, $content );
	$headings = isset( $parsed['headings'] ) && is_array( $parsed['headings'] ) ? $parsed['headings'] : array();

	if ( ! empty( $headings ) ) {
		echo '<ul class="space-y-2 text-sm text-muted-foreground">';
		foreach ( $headings as $heading ) {
			$padding = ( 3 === (int) $heading['level'] ) ? 'pl-4' : '';
			echo sprintf(
				'<li class="%1$s"><a href="#%2$s" class="hover:text-foreground hover:underline">%3$s</a></li>',
				esc_attr( $padding ),
				esc_attr( $heading['id'] ),
				esc_html( $heading['text'] )
			);
		}
		echo '</ul>';
	} else {
		echo '<p class="text-sm text-muted-foreground italic">' . esc_html__( 'No headings found.', 'titancore' ) . '</p>';
	}
}

/**
 * Icon Helper Function.
 */
function titancore_get_icon( $icon_name, $classes = '' ) {
	$svg = '';
	switch ( $icon_name ) {
		case 'moon':
			$svg = '<svg class="' . esc_attr( $classes ) . '" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></svg>';
			break;
		case 'sun':
			$svg = '<svg class="' . esc_attr( $classes ) . '" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/></svg>';
			break;
		case 'menu':
			$svg = '<svg class="' . esc_attr( $classes ) . '" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>';
			break;
		case 'arrow-left':
			$svg = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="' . esc_attr( $classes ) . '"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>';
			break;
		case 'chevron-right':
			$svg = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="' . esc_attr( $classes ) . '"><path d="m9 18 6-6-6-6"/></svg>';
			break;
	}
	return $svg;
}

/**
 * Filter the custom logo HTML to add utility classes.
 */
function titancore_custom_logo_attributes( $html ) {
	$html = str_replace( 'custom-logo-link', 'custom-logo-link mr-6 flex items-center space-x-2 font-medium text-lg tracking-tighter h-8 rounded-md overflow-hidden min-w-0', $html );
	$html = str_replace( 'custom-logo', 'custom-logo w-8 h-8 object-cover', $html );

	// Keep decoding async but avoid forcing global eager/high fetchpriority.
	if ( false === strpos( $html, 'decoding=' ) ) {
		$html = str_replace( 'alt=', 'decoding="async" alt=', $html );
	}

	return $html;
}
add_filter( 'get_custom_logo', 'titancore_custom_logo_attributes' );

/**
 * Filter pagination markup to align with utility classes.
 */
function titancore_pagination_markup( $template, $class ) {
	return '
    <nav class="navigation %1$s" aria-label="%4$s">
        <h2 class="screen-reader-text">%2$s</h2>
        <div class="nav-links flex flex-wrap items-center justify-center gap-2">%3$s</div>
    </nav>';
}
add_filter( 'navigation_markup_template', 'titancore_pagination_markup', 10, 2 );
