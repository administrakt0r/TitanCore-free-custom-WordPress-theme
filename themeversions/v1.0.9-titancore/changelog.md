## [1.0.9] - 2026-10-11
### Added
- `inc/enqueue.php`: Transient caching and static runtime caching for dynamic theme CSS variables generation in `titancore_generate_theme_variables_css()`.
- `inc/template-tags.php`: Static per-request runtime caching to `titancore_get_cache_version()`, `titancore_get_posts_page_url()`, and `titancore_get_related_posts()`.
- `inc/template-tags.php`: Automatic cache version invalidation on Customizer save (`customize_save_after`).
- `inc/seo-schema.php`: Static per-request runtime caching to `titancore_get_og_image_url()`.
- `template-parts/content-none.php`: Standardized empty and search query fallbacks across archive and search views with dedicated contextual icons (`search-x` / `inbox`), recovery messaging, and popular category pills with post counts and focus rings.
- `comments.php`: Accessibility metadata connecting comment form notes via `aria-describedby` to input fields and textarea, plus responsive flex wrapping for reply titles and cancel-reply link container.
- `assets/css/enhancements.css`: Nested comment thread hierarchy styling (`.comment-list .children`) with border accent and responsive indentation, comment card hover elevation, and cancel-reply button styles.
- `assets/css/enhancements.css` & `assets/js/navigation.js`: Mobile navigation drawer transitions, elevation shadows, button inset outline, and `aria-current` link state management (`page` and `location`).

### Changed
- `front-page.php`: Added `no_found_rows => true` to headline and featured non-paginated queries to bypass expensive SQL row calculation.
- `inc/template-tags.php`: Switched `titancore_get_related_posts()` to query with `fields => ids` and `no_found_rows => true`, preventing full `WP_Post` object instantiation.
- `archive.php` and `search.php`: Replaced duplicated inline empty-state markup with shared `template-parts/content-none.php`.

### Removed
- Removed orphaned `get_template_part( 'template-parts/background', 'grid' )` calls across 8 templates (`404.php`, `archive.php`, `front-page.php`, `home.php`, `index.php`, `page.php`, `search.php`, `single.php`) under Agenticus Purgatorius protocol.

