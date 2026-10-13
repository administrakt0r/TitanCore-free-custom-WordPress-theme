## [1.0.10] - 2026-10-15
### Added
- `inc/template-tags.php`: Added `titancore_get_top_categories()` with static runtime and 6-hour transient caching.
- `inc/template-tags.php`: Added `titancore_get_recent_posts()` with static runtime and transient caching (`fields => 'ids'`, `no_found_rows => true`).

### Changed
- `inc/seo-schema.php`: Added static per-request runtime caching to `titancore_has_external_seo_plugin()` to eliminate duplicate `defined()` checks on SEO hooks.
- `inc/seo-schema.php`: Cached `$post_title` and `$categories[0]` in `titancore_breadcrumbs()` on single posts to avoid redundant function calls.
- `inc/enqueue.php`: Added static per-request runtime caching to `titancore_should_keep_core_block_assets()` to avoid re-parsing queried post objects and block structures.
- `template-parts/content-none.php`: Updated popular topics and recent articles modules to use `titancore_get_top_categories(6)` and `titancore_get_recent_posts(3)` for zero-uncached database query overhead on empty state rendering.
- `front-page.php`: Replaced `WP_Query` with `get_posts()` (`no_found_rows => true`) when fetching cached featured posts in the Magazine preset.
