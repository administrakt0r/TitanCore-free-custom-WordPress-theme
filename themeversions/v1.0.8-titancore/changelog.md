## [1.0.8] - 2026-09-26
### Changed
- `inc/template-tags.php`: Added static in-memory per-request runtime caching to `titancore_get_estimated_reading_time()`, `titancore_get_top_tags()`, and `titancore_get_published_posts_count()`, plus multibyte character fallback calculation for non-Latin script content.
- `inc/seo-schema.php`: Added static in-memory per-request runtime caching to `titancore_get_meta_description()` to eliminate duplicate post content extraction and word trimming across `wp_head` meta description and Open Graph / Twitter card output.
