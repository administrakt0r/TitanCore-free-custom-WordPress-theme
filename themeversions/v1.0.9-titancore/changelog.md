## [1.0.9] - 2026-09-27
### Changed
- `inc/template-tags.php`: Optimized `titancore_get_related_posts()` queries with `'fields' => 'ids'`, `'update_post_term_cache' => false`, and `'update_post_meta_cache' => false` to eliminate unneeded `WP_Post` object hydration and term/meta cache database queries.
- `front-page.php`: Added `'update_post_term_cache' => false` and `'update_post_meta_cache' => false` to ID queries in `news` (`$headline_query`) and `magazine` (`$featured_ids_query`) presets to bypass redundant database query execution on transient cache misses.
