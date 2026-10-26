## [1.0.13] - 2026-10-26
### Changed
- `template-parts/content.php`, `front-page.php`, and `template-parts/content-none.php`: Cached post permalinks, titles, dates, and author metadata in local loop variables to eliminate redundant calculation calls per card.
- `single.php`: Cached author metadata (`$author_id`, `$author_url`, `$author_name`) for author card rendering to prevent multiple `get_the_author()` / `get_author_posts_url()` calls, and refactored Article JSON-LD schema generation to reuse the static in-memory meta description from `titancore_get_meta_description()`.
- `inc/template-tags.php`: Batch-primed post objects in `titancore_prime_post_thumbnail_cache()` and `titancore_prime_post_author_cache()` using `_prime_post_caches()`; populated core `terms` and `term_taxonomy_id` object caches in `titancore_prime_term_cache()`; and verified term cache presence in `titancore_get_related_posts()`.
- Comments UI & Form Polish: Refined layout hierarchy, full-width notices, cookies consent toggle, and accessibility focus states across comment components.

