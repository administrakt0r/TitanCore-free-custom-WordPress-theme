## [1.0.13] - 2026-10-22
### Changed
- `template-parts/content.php` & `front-page.php`: Cached `$post_permalink` per card across card templates and front-page layout presets to eliminate redundant `get_permalink()` calculation calls per post.
- `single.php`: Cached author metadata (`$author_id`, `$author_url`, `$author_name`) for author card rendering to prevent multiple `get_the_author()` / `get_author_posts_url()` calls, and refactored Article JSON-LD schema generation to reuse the static in-memory meta description from `titancore_get_meta_description()`.
