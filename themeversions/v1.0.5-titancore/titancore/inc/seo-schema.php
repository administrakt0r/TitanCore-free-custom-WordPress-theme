<?php
/**
 * SEO and Schema.org functionalities
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Detect if a dedicated SEO plugin is active to avoid duplicate meta output.
 */
function titancore_has_external_seo_plugin() {
    return defined( 'WPSEO_VERSION' ) ||
        defined( 'RANK_MATH_VERSION' ) ||
        defined( 'SEOPRESS_VERSION' ) ||
        defined( 'AIOSEO_VERSION' );
}

/**
 * Build a contextual meta description fallback.
 */
function titancore_get_meta_description() {
    $description = '';

    if ( is_singular() ) {
        if ( has_excerpt() ) {
            $description = get_the_excerpt();
        } else {
            $post_content = get_post_field( 'post_content', get_queried_object_id() );
            $description  = wp_trim_words( wp_strip_all_tags( strip_shortcodes( $post_content ) ), 28, '' );
        }
    } elseif ( is_home() || is_front_page() ) {
        $description = get_bloginfo( 'description' );
    } elseif ( is_category() || is_tag() || is_tax() ) {
        $description = term_description();
    } elseif ( is_search() ) {
        /* translators: 1: search query, 2: site title */
        $description = sprintf(
            __( 'Search results for "%1$s" on %2$s.', 'titancore' ),
            get_search_query( false ),
            get_bloginfo( 'name' )
        );
    } elseif ( is_archive() ) {
        /* translators: %s: site title */
        $description = sprintf( __( 'Archive page on %s.', 'titancore' ), get_bloginfo( 'name' ) );
    } elseif ( is_404() ) {
        /* translators: %s: site title */
        $description = sprintf( __( 'Page not found on %s.', 'titancore' ), get_bloginfo( 'name' ) );
    }

    return trim( wp_strip_all_tags( $description ) );
}

/**
 * Output meta description fallback.
 */
function titancore_output_meta_description() {
    if ( titancore_has_external_seo_plugin() ) {
        return;
    }

    $description = titancore_get_meta_description();
    if ( '' === $description ) {
        return;
    }

    echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n";
}
add_action( 'wp_head', 'titancore_output_meta_description', 1 );

/**
 * Output noindex for low-value pages like search and 404.
 */
function titancore_output_robots_meta() {
    if ( titancore_has_external_seo_plugin() ) {
        return;
    }

    if ( is_search() || is_404() ) {
        echo '<meta name="robots" content="noindex,follow,max-snippet:-1,max-image-preview:large,max-video-preview:-1">' . "\n";
    }
}
add_action( 'wp_head', 'titancore_output_robots_meta', 2 );

/**
 * Build the canonical link for a date archive (day, month, or year).
 *
 * @return string Date archive URL, or empty string when no date is queried.
 */
function titancore_get_date_archive_link() {
    $year  = absint( get_query_var( 'year' ) );
    $month = absint( get_query_var( 'monthnum' ) );
    $day   = absint( get_query_var( 'day' ) );

    if ( $year > 0 && $month > 0 && $day > 0 ) {
        return get_day_link( $year, $month, $day );
    }

    if ( $year > 0 && $month > 0 ) {
        return get_month_link( $year, $month );
    }

    if ( $year > 0 ) {
        return get_year_link( $year );
    }

    return '';
}

/**
 * Output canonical URL and pagination rel links when no SEO plugin present.
 *
 * Canonicals are self-referencing on paginated URLs (page N canonicalizes to
 * the URL of page N) per current search-engine guidance, so paginated content
 * is not incorrectly consolidated into page 1. Search results and 404 pages
 * are noindexed and intentionally emit no canonical, matching major SEO
 * plugin behavior. Rel prev/next links remain for archive-style pagination as
 * a discovery hint for engines that still read them.
 *
 * Singular views (posts, pages, attachments, the static front page and
 * <!--nextpage--> splits) are left to WordPress core's own rel_canonical(),
 * which already emits a self-referencing, pagination-aware canonical there;
 * emitting a second one would output duplicate canonical tags. TitanCore
 * covers the archive-style views core does not.
 */
function titancore_output_canonical() {
    if ( titancore_has_external_seo_plugin() ) {
        return;
    }

    if ( is_search() || is_404() ) {
        // Noindexed contexts: no canonical and no pagination rel links.
        return;
    }

    if ( is_singular() ) {
        // Core's rel_canonical() owns singular canonicals; see PHPDoc above.
        return;
    }

    $paged     = max( 1, (int) get_query_var( 'paged' ) );
    $canonical = '';

    if ( is_front_page() ) {
        // Posts-index front page; static front pages are singular and handled
        // by core. Mirrors front-page.php, which reads both paged and page.
        $paged     = max( $paged, (int) get_query_var( 'page' ) );
        $canonical = ( $paged > 1 ) ? get_pagenum_link( $paged ) : home_url( '/' );
    } elseif ( is_home() ) {
        $canonical = ( $paged > 1 ) ? get_pagenum_link( $paged ) : titancore_get_posts_page_url();
    } elseif ( is_category() || is_tag() || is_tax() ) {
        $term_link = get_term_link( get_queried_object() );
        if ( ! is_wp_error( $term_link ) && is_string( $term_link ) && '' !== $term_link ) {
            $canonical = ( $paged > 1 ) ? get_pagenum_link( $paged ) : $term_link;
        }
    } elseif ( is_author() ) {
        $canonical = ( $paged > 1 ) ? get_pagenum_link( $paged ) : get_author_posts_url( get_queried_object_id() );
    } elseif ( is_post_type_archive() ) {
        $post_type    = get_query_var( 'post_type' );
        $archive_link = ( is_string( $post_type ) && '' !== $post_type ) ? get_post_type_archive_link( $post_type ) : '';

        if ( is_string( $archive_link ) && '' !== $archive_link ) {
            $canonical = ( $paged > 1 ) ? get_pagenum_link( $paged ) : $archive_link;
        }
    } elseif ( is_date() ) {
        $date_link = titancore_get_date_archive_link();

        if ( '' !== $date_link ) {
            $canonical = ( $paged > 1 ) ? get_pagenum_link( $paged ) : $date_link;
        }
    }

    if ( is_string( $canonical ) && '' !== $canonical ) {
        echo '<link rel="canonical" href="' . esc_url( $canonical ) . '">' . "\n";
    }

    // Pagination rel prev/next
    if ( $paged > 1 ) {
        echo '<link rel="prev" href="' . esc_url( get_pagenum_link( $paged - 1 ) ) . '">' . "\n";
    }
    global $wp_query;
    if ( isset( $wp_query->max_num_pages ) && $paged < (int) $wp_query->max_num_pages ) {
        echo '<link rel="next" href="' . esc_url( get_pagenum_link( $paged + 1 ) ) . '">' . "\n";
    }
}
add_action( 'wp_head', 'titancore_output_canonical', 1 );

/**
 * Resolve the Open Graph image URL for the current view.
 *
 * Fallback chain: featured image (singular views) → Customizer default OG
 * image (`og_default_image`) → site icon (512px). Returns '' when nothing
 * is available. Filterable via `titancore_og_image_url`.
 */
function titancore_get_og_image_url() {
    $image_url = '';

    if ( is_singular() ) {
        $image_url = get_the_post_thumbnail_url( get_queried_object_id(), 'full' );
    }

    if ( ! $image_url ) {
        $default_image_id = absint( get_theme_mod( 'og_default_image' ) );
        if ( $default_image_id ) {
            $image_url = wp_get_attachment_image_url( $default_image_id, 'full' );
        }
    }

    if ( ! $image_url ) {
        $image_url = get_site_icon_url( 512 );
    }

    return apply_filters( 'titancore_og_image_url', is_string( $image_url ) ? $image_url : '' );
}

/**
 * Output lightweight OpenGraph and Twitter card fallback tags.
 */
function titancore_output_social_meta() {
    if ( titancore_has_external_seo_plugin() ) {
        return;
    }

    $title       = wp_strip_all_tags( wp_get_document_title() );
    $description = titancore_get_meta_description();
    $site_name   = get_bloginfo( 'name' );
    $url         = home_url( '/' );
    $type        = is_singular( 'post' ) ? 'article' : 'website';

    if ( is_singular() ) {
        $url = get_permalink();
    } elseif ( is_search() ) {
        $url = get_search_link();
    } elseif ( is_archive() || is_home() ) {
        $url = get_pagenum_link( max( 1, get_query_var( 'paged' ) ) );
    }

    echo '<meta property="og:type" content="' . esc_attr( $type ) . '">' . "\n";
    echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
    echo '<meta property="og:description" content="' . esc_attr( $description ) . '">' . "\n";
    echo '<meta property="og:url" content="' . esc_url( $url ) . '">' . "\n";
    echo '<meta property="og:site_name" content="' . esc_attr( $site_name ) . '">' . "\n";
    echo '<meta property="og:locale" content="' . esc_attr( get_locale() ) . '">' . "\n";
    echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
    echo '<meta name="twitter:title" content="' . esc_attr( $title ) . '">' . "\n";
    echo '<meta name="twitter:description" content="' . esc_attr( $description ) . '">' . "\n";

    // Article-specific OG tags
    if ( is_singular( 'post' ) ) {
        echo '<meta property="article:published_time" content="' . esc_attr( get_the_date( 'c' ) ) . '">' . "\n";
        echo '<meta property="article:modified_time" content="' . esc_attr( get_the_modified_date( 'c' ) ) . '">' . "\n";
    }

    // Open Graph / Twitter image with fallback chain (featured → default → site icon)
    $og_image_url = titancore_get_og_image_url();
    if ( '' !== $og_image_url ) {
        echo '<meta property="og:image" content="' . esc_url( $og_image_url ) . '">' . "\n";
        echo '<meta name="twitter:image" content="' . esc_url( $og_image_url ) . '">' . "\n";
    }
}
add_action( 'wp_head', 'titancore_output_social_meta', 3 );

/**
 * Output WebSite + SearchAction schema for SERP search box eligibility.
 */
function titancore_output_website_schema() {
    if ( titancore_has_external_seo_plugin() ) {
        return;
    }

    $schema = array(
        '@context' => 'https://schema.org',
        '@type'    => 'WebSite',
        'name'     => get_bloginfo( 'name' ),
        'url'      => home_url( '/' ),
    );

    // Add SearchAction if search is available
    $schema['potentialAction'] = array(
        '@type'       => 'SearchAction',
        'target'      => array(
            '@type'        => 'EntryPoint',
            'urlTemplate'  => home_url( '/?s={search_term_string}' ),
        ),
        'query-input' => 'required name=search_term_string',
    );

    echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
}
add_action( 'wp_head', 'titancore_output_website_schema', 5 );

/**
 * Output Organization/Publisher schema with site name, optional logo, and sameAs social profiles.
 */
function titancore_output_organization_schema() {
    if ( titancore_has_external_seo_plugin() ) {
        return;
    }

    // Only output on the front page to avoid repetition
    if ( ! is_front_page() ) {
        return;
    }

    $schema = array(
        '@context'    => 'https://schema.org',
        '@type'       => 'Organization',
        'name'        => get_bloginfo( 'name' ),
        'url'         => home_url( '/' ),
        'description' => get_bloginfo( 'description' ),
    );

    // Add logo if custom logo is set
    $custom_logo_id = get_theme_mod( 'custom_logo' );
    if ( $custom_logo_id ) {
        $logo_url = wp_get_attachment_image_url( $custom_logo_id, 'full' );
        if ( $logo_url ) {
            $schema['logo'] = array(
                '@type'      => 'ImageObject',
                'url'        => esc_url_raw( $logo_url ),
            );
        }
    }

    // Social profiles (sameAs) — Customizer "SEO & Social" section
    $same_as = array();
    foreach ( array( 'facebook', 'x', 'instagram', 'youtube', 'linkedin' ) as $social_network ) {
        $profile_url = get_theme_mod( "social_profile_{$social_network}", '' );
        if ( is_string( $profile_url ) && '' !== trim( $profile_url ) ) {
            $same_as[] = esc_url_raw( trim( $profile_url ) );
        }
    }
    if ( ! empty( $same_as ) ) {
        $schema['sameAs'] = $same_as;
    }

    echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
}
add_action( 'wp_head', 'titancore_output_organization_schema', 6 );

/**
 * SEO Breadcrumbs
 */
function titancore_breadcrumbs() {
    if ( is_front_page() || is_home() ) {
        return;
    }

    $schema_items = array(
        array(
            '@type'    => 'ListItem',
            'position' => 1,
            'name'     => esc_html__( 'Home', 'titancore' ),
            'item'     => home_url( '/' ),
        )
    );
    $categories = array();
    $position = 2;

    echo '<nav aria-label="breadcrumb" class="mb-6 text-sm text-muted-foreground flex flex-wrap items-center gap-2">';
    echo '<a href="' . esc_url( home_url( '/' ) ) . '" class="hover:text-foreground transition-colors">' . esc_html__( 'Home', 'titancore' ) . '</a>';

    if ( is_category() || is_single() ) {
        echo titancore_get_icon('chevron-right', 'w-4 h-4 opacity-50');

        if ( is_single() ) {
            $categories = get_the_category();
            if ( ! empty( $categories ) ) {
                $schema_items[] = array(
                    '@type'    => 'ListItem',
                    'position' => $position++,
                    'name'     => wp_strip_all_tags( $categories[0]->name ),
                    'item'     => get_category_link( $categories[0]->term_id ),
                );

                echo '<a href="' . esc_url( get_category_link( $categories[0]->term_id ) ) . '" class="hover:text-foreground transition-colors">' . esc_html( $categories[0]->name ) . '</a>';
                echo titancore_get_icon('chevron-right', 'w-4 h-4 opacity-50');
            }

            $schema_items[] = array(
                '@type'    => 'ListItem',
                'position' => $position,
                'name'     => wp_strip_all_tags( get_the_title() ),
            );

            echo '<span class="text-foreground font-medium" aria-current="page">' . esc_html( get_the_title() ) . '</span>';
        } elseif ( is_category() ) {
            $cat_title = single_cat_title('', false);
            $schema_items[] = array(
                '@type'    => 'ListItem',
                'position' => $position,
                'name'     => wp_strip_all_tags( $cat_title ),
            );

            echo '<span class="text-foreground font-medium" aria-current="page">' . esc_html( $cat_title ) . '</span>';
        }
    } elseif ( is_tag() ) {
        echo titancore_get_icon('chevron-right', 'w-4 h-4 opacity-50');
        $tag_title = single_tag_title( '', false );
        $schema_items[] = array(
            '@type'    => 'ListItem',
            'position' => $position,
            'name'     => wp_strip_all_tags( $tag_title ),
        );
        echo '<span class="text-foreground font-medium" aria-current="page">' . esc_html( $tag_title ) . '</span>';
    } elseif ( is_author() ) {
        echo titancore_get_icon('chevron-right', 'w-4 h-4 opacity-50');
        $schema_items[] = array(
            '@type'    => 'ListItem',
            'position' => $position,
            'name'     => get_the_author(),
        );
        echo '<span class="text-foreground font-medium" aria-current="page">' . esc_html( get_the_author() ) . '</span>';
    } elseif ( is_page() ) {
        $schema_items[] = array(
            '@type'    => 'ListItem',
            'position' => $position,
            'name'     => wp_strip_all_tags( get_the_title() ),
        );
        echo titancore_get_icon('chevron-right', 'w-4 h-4 opacity-50');
        echo '<span class="text-foreground font-medium" aria-current="page">' . esc_html( get_the_title() ) . '</span>';
    } elseif ( is_search() ) {
        echo titancore_get_icon('chevron-right', 'w-4 h-4 opacity-50');
        $schema_items[] = array(
            '@type'    => 'ListItem',
            'position' => $position,
            'name'     => __( 'Search Results', 'titancore' ),
        );
        echo '<span class="text-foreground font-medium" aria-current="page">' . esc_html__( 'Search Results', 'titancore' ) . '</span>';
    } elseif ( is_date() ) {
        echo titancore_get_icon('chevron-right', 'w-4 h-4 opacity-50');
        $schema_items[] = array(
            '@type'    => 'ListItem',
            'position' => $position,
            'name'     => get_the_archive_title(),
        );
        echo '<span class="text-foreground font-medium" aria-current="page">' . esc_html( get_the_archive_title() ) . '</span>';
    }
    echo '</nav>';

    // Output JSON-LD BreadcrumbList.
    // Suppressed when a dedicated SEO plugin is active so its own breadcrumb
    // schema takes over without duplicates; the visible breadcrumb nav above
    // is a UI feature and always renders.
    if ( ! titancore_has_external_seo_plugin() && ( count( $schema_items ) > 1 || is_page() || ( is_single() && empty( $categories ) ) ) ) {
        $breadcrumb_schema = array(
            '@context'        => 'https://schema.org',
            '@type'           => 'BreadcrumbList',
            'itemListElement' => $schema_items,
        );
        echo '<script type="application/ld+json">' . wp_json_encode( $breadcrumb_schema, JSON_UNESCAPED_SLASHES ) . '</script>';
    }
}
