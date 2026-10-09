## [1.0.9] - 2026-10-09
### Performance
- `inc/enqueue.php`: Added static in-memory runtime caching and transient caching to `titancore_generate_theme_variables_css()` to reduce dynamic inline CSS calculation overhead.
- `inc/template-tags.php`: Added `customize_save_after` hook to `titancore_bump_cache_version()` for O(1) cache version invalidation on Customizer saves.
- `front-page.php`: Batch-primed post, taxonomy term, and thumbnail metadata caches with `_prime_post_caches()` for `news` and `magazine` presets to eliminate N+1 database queries.
