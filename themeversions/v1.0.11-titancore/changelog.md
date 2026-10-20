## [1.0.11] - 2026-10-20
### Changed
- `front-page.php`: Batch-primed post thumbnail attachment caches for `$grid_query->posts` (News preset), `$list_query->posts` (Magazine preset), and `$blog_query->posts` (Blog preset) using `titancore_prime_post_thumbnail_cache()` to eliminate N+1 SQL attachment queries per card.
- `template-parts/loop-container.php`: Batch-primed post thumbnail attachment caches for `$wp_query->posts` prior to loop execution across archive and search pages.
- `inc/template-tags.php`: Switched `titancore_get_related_posts()` to use `get_the_terms()` (reading from the in-memory post term cache) before falling back to `wp_get_post_terms()`, avoiding redundant database queries.
- `inc/template-tags.php`: Encapsulated `_prime_post_caches()` and `titancore_prime_post_thumbnail_cache()` execution directly inside `titancore_get_related_posts()` and `titancore_get_recent_posts()`.
- `single.php`, `404.php`, and `template-parts/content-none.php`: Streamlined template priming logic by delegating post/thumbnail cache priming to helper functions.
