## [1.0.9] - 2026-10-10
### Changed
- `inc/template-tags.php`: Added static in-memory per-request runtime caching to `titancore_get_cache_version()`, `titancore_get_posts_page_url()`, and `titancore_get_related_posts()`.
- `inc/template-tags.php`: Optimized `titancore_get_related_posts()` database queries with `fields => ids` parameter to fetch post IDs directly without instantiating full `WP_Post` objects.
- `inc/seo-schema.php`: Added static in-memory per-request runtime caching to `titancore_get_og_image_url()`.
