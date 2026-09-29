## [1.0.9] - 2026-09-29
### Changed
- `inc/enqueue.php`: Added static in-memory per-request runtime caching and transient caching to `titancore_generate_theme_variables_css()` to eliminate redundant hex parsing, color mixing, luma contrast calculations, and string concatenations on every request (~247x execution speedup).
- `front-page.php`: Batch-primed post, term, and thumbnail/meta caches using `_prime_post_caches()` for headline posts (`news` preset) and featured posts (`magazine` preset), and added `'no_found_rows' => true` to the headline `get_posts()` call to suppress unnecessary SQL row count calculations.
