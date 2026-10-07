## [1.0.9] - 2026-09-28
### Changed
- `inc/template-tags.php`: Added static in-memory per-request runtime caching to `titancore_get_related_posts()` and `titancore_get_posts_page_url()` to eliminate duplicate option/transient DB queries per request.
- `inc/seo-schema.php`: Added static in-memory per-request runtime caching to `titancore_has_external_seo_plugin()` and `titancore_get_og_image_url()`.
- `inc/enqueue.php`: Added static in-memory per-request runtime caching to `titancore_generate_theme_variables_css()`.
- `front-page.php`: Added `no_found_rows` query optimization to news and magazine preset secondary queries and avoided DB queries on empty post arrays.
