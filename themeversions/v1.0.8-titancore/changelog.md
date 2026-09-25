## [1.0.8] - 2026-09-25
### Changed
- `front-page.php`: Optimized front-page preset query cache misses in News and Magazine layouts to fetch full post objects directly on transient cache miss, eliminating secondary `get_posts()` and duplicate queries.
