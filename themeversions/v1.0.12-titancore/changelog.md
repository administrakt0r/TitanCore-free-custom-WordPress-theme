## [1.0.12] - 2026-10-21
### Changed
- `inc/template-tags.php`: Added `titancore_prime_post_author_cache()` helper to batch-prime user author caches using `update_post_author_caches()`.
- `front-page.php`: Batch-primed post author caches for `$headline_post_ids` and `$grid_query->posts` (News preset), `$featured_post_ids` and `$list_query->posts` (Magazine preset), and `$blog_query->posts` (Blog preset) to eliminate N+1 SQL author queries per card.
- `template-parts/loop-container.php`: Batch-primed post author caches for `$wp_query->posts` prior to loop execution across archive and search pages.
- `inc/template-tags.php`: Integrated `titancore_prime_post_author_cache()` execution inside `titancore_get_related_posts()`.

