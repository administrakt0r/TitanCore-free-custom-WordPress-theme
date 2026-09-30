## [1.0.9] - 2026-09-30
### Changed
- `inc/enqueue.php`: Added static in-memory runtime caching and transient caching (`titancore_theme_vars_css_v...`) to `titancore_generate_theme_variables_css()`.
- `inc/template-tags.php`: Hooked `customize_save_after` to `titancore_bump_cache_version()` for O(1) cache version invalidation on Customizer saves.
- `front-page.php`: Batch-primed post, term, and thumbnail/meta caches with `_prime_post_caches()` and enforced `no_found_rows` query optimization for news and magazine presets.
