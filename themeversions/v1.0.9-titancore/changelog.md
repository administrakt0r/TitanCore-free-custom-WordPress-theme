## [1.0.9] - 2026-10-04
### Changed
- `inc/template-tags.php`: Capped main query `posts_per_page` to 1 and disabled term/meta cache priming on `is_front_page()` since custom front-page presets render dedicated query instances.
- `front-page.php`: Added `no_found_rows` and cache-priming disables for empty dummy queries in `magazine` preset, and omitted empty `post__not_in` parameters in `news` and `magazine` grid queries.
