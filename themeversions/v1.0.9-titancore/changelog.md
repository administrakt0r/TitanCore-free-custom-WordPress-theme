## [1.0.9] - 2026-09-27
### Changed
- `inc/template-tags.php`: Added static in-memory per-request runtime caching to `titancore_get_related_posts()` and `titancore_get_posts_page_url()` to eliminate redundant transient/query lookups and option calls.
- `inc/seo-schema.php`: Added static in-memory per-request runtime caching to `titancore_get_og_image_url()` to avoid repeated thumbnail URL resolutions and Customizer option lookups.
