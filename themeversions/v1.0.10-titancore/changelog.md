## [1.0.10] - 2026-10-15
### Added
- `inc/template-tags.php`: Added `titancore_get_top_categories()` (with `titancore_get_popular_categories()` compatibility alias) with static runtime and 6-hour transient caching.
- `inc/template-tags.php`: Added `titancore_get_recent_posts()` with static runtime and transient caching (`fields => 'ids'`, `no_found_rows => true`).
- `assets/css/enhancements.css`: Single post table of contents active section indicator left border and accent background, comment reply link subtle hover elevation, and comment form inputs focus-visible styling.
- `assets/css/style.css` & `assets/css/enhancements.css`: Standardized navigation tokens (`var(--muted-foreground)`, `var(--foreground)`) and added explicit 2px focus-visible rings across header navigation, secondary navigation, and footer links.

### Changed
- `inc/seo-schema.php`: Added static per-request runtime caching to `titancore_has_external_seo_plugin()` to eliminate duplicate `defined()` checks on SEO hooks.
- `inc/seo-schema.php`: Cached `$post_title` and `$categories[0]` in `titancore_breadcrumbs()` on single posts to avoid redundant function calls.
- `inc/enqueue.php`: Added static per-request runtime caching to `titancore_should_keep_core_block_assets()` to avoid re-parsing queried post objects and block structures, and versioned dynamic CSS variables transient keys.
- `template-parts/content-none.php`: Updated popular topics and recent articles modules to use `titancore_get_top_categories(6)` and `titancore_get_recent_posts(3)` for zero-uncached database query overhead on empty state rendering.
- `front-page.php`, `404.php`, and `template-parts/content-none.php`: Optimized post fetching from cached transient ID lists using `_prime_post_caches()` with fallback to `get_posts()` for backward compatibility.
- `inc/customizer.php`: Added static runtime caching to `titancore_get_custom_code_output()` to eliminate redundant `get_theme_mod()` and `wp_kses()` execution.
- `inc/seo-schema.php`: Added static runtime caching to `titancore_get_date_archive_link()` to cache canonical date archive permalinks per request.
- `template-parts/content.php`: Streamlined excerpt generation to use standard `get_the_excerpt()`.
- `front-page.php`: Replaced `WP_Query` with `get_posts()` (`no_found_rows => true`) when fetching cached featured posts in the Magazine preset.

