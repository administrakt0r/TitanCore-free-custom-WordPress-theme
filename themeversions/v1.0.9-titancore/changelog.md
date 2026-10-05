## [1.0.9] - 2026-09-30
### Changed
- `inc/enqueue.php`: Added in-memory static runtime caching and transient caching to `titancore_generate_theme_variables_css()`.
- `inc/template-tags.php`: Hooked `customize_save_after` to `titancore_bump_cache_version()` for O(1) cache version invalidation on Customizer saves; optimized `titancore_get_related_posts()` with `'fields' => 'ids'` and in-memory static per-request runtime cache.
- `front-page.php`: Batch-primed post, term, and thumbnail/meta caches with `_prime_post_caches()` across news, magazine, and blog front-page presets.
