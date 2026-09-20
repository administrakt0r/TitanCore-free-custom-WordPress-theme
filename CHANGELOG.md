# Changelog

All notable changes to TitanCore are documented here. Format follows [Keep a Changelog](https://keepachangelog.com/en/1.0.0/) and [Semantic Versioning](https://semver.org/).

This file is the source of truth. `readme.txt` `== Changelog ==` must stay in sync (WordPress.org reads it).

## [1.0.4] - 2026-08-30
### Removed
- `comments.php`: dead `'class'` argument to `wp_list_comments()` (WordPress core accepts no such argument) and dead `peer-disabled:*` utility classes on the comment textarea label (no element with the required `peer` class exists anywhere). Comment item rendering re-proved with a live test comment.
- `assets/css/style.css`: three static color rules made unreachable by the always-attached dynamic inline stylesheet (`titancore_generate_theme_variables_css()` re-declares `.bg-background/95`, `.border-border/40` and `.hover:bg-primary/90` later in the cascade from Customizer values, the hover rule with `!important`): statics and their `.dark` variants removed. Head order and rule presence verified in live HTML.
- `assets/js/navigation.js`: `[data-lucide]` selector fragment removed from the menu icon `querySelectorAll` (no markup emits that attribute).

## [1.0.3] - 2026-08-30
### Removed
- Dead CSS: 41 unused utility blocks in `assets/css/style.css` (spacing, sizing, positioning, typography, color, state and responsive variants no template, partial, script or rendered view can emit) and dead selector groups in `assets/css/enhancements.css` (`.tc-pagination`/`.nav-links--pagination` members of the pagination rules plus `.sm:items-center`/`.sm:justify-between`). Class-token output verified identical across front page (all three presets), archive, search, 404 and single views; minified CSS regenerated.

## [1.0.2] - 2026-08-30
### Added
- Default Open Graph image fallback: a new `titancore_get_og_image_url()` helper resolves `og:image`/`twitter:image` for every view via the chain featured image (singular views) → Customizer "Default Open Graph Image" → 512px site icon — previously archive views and posts without a thumbnail emitted no image tags at all. Filterable via `titancore_og_image_url` (`inc/seo-schema.php`).
- Organization/Publisher JSON-LD now includes `sameAs` social profiles configured in a new Customizer "SEO & Social" section (Facebook, X/Twitter, Instagram, YouTube, LinkedIn), completing Google's social-profile eligibility data (`inc/customizer.php`, `inc/seo-schema.php`).
- Active-section highlighting for the single-post Table of Contents: navigation.js uses an IntersectionObserver to mark the heading currently in view (`.is-active` class + `aria-current="true"`) in both the desktop sidebar TOC and the mobile TOC; TOC markup now carries the `tc-toc` class so its dedicated styles actually apply (`assets/js/navigation.js`, `assets/css/enhancements.css`, `inc/template-tags.php`).
- Consistent card visual system across the three front-page presets: shared card tokens (radius, padding, resting/hover shadows), uniform hover elevation with border accent for pointer and keyboard users (`:focus-within`), focus-visible outlines for card links, unified card content padding, and reduced-motion handling (`assets/css/frontpage-presets.css`).

### Fixed
- Canonical URLs: date archives (day/month/year) and custom post type archives now emit canonical links; previously these contexts had none (`inc/seo-schema.php`, new `titancore_get_date_archive_link()` helper).
- Canonical URLs: paginated archive URLs now use self-referencing canonicals (page N canonicalizes to the URL of page N) instead of always pointing to page 1 — front page, posts page, and term/author/CPT/date archives.
- Canonical URLs: fixed duplicate canonical tags on singular pages. TitanCore no longer emits a second canonical on posts, pages, static front pages and `<!--nextpage-->` splits — WordPress core's own `rel_canonical()` (self-referencing and pagination-aware) owns those views.
- BreadcrumbList JSON-LD is no longer emitted when a dedicated SEO plugin (Yoast, Rank Math, SEOPress, AIOSEO) is active, avoiding duplicate breadcrumb schema; the visible breadcrumb navigation always renders (`inc/seo-schema.php`).

### Changed
- navigation.js now also loads on singular views with the TOC enabled, even when no primary menu is assigned and the color-mode switcher is off, so TOC active-section highlighting works on every site (`inc/enqueue.php`).
- Search results and 404 pages remain noindexed and now intentionally emit no canonical and no rel prev/next, matching major SEO plugin behavior.

## [1.0.1] - 2026-08-25
### Fixed
- Accessibility: desktop primary nav links now meet 44×44px tap target (`assets/css/style.css`), active state uses `var(--accent)` background (`assets/css/enhancements.css`) for clearer affordance beyond color change.
- Accessibility: all decorative SVG icons now carry `aria-hidden="true" focusable="false"` (`inc/template-tags.php`) to eliminate screen-reader noise (pagination, theme toggle, menu, empty states).
- Mobile menu backdrop now fades via `opacity`+`visibility` (`assets/css/enhancements.css`, `header.php`) instead of instant `display:none`; sticky-aware offset (`top-14` when sticky, `top-0` otherwise) fixes misaligned overlay when `sticky_header` is disabled.

### Changed
- Typography: `.prose-lg` is responsive — `1rem` on mobile with `1.125rem` from `640px` (`assets/css/style.css`), improving readability and line-length on small screens.

## [1.0.0] - 2026-06-22
### Added
- Initial release.
- Three front-page presets: Modern Blog, News Portal, Magazine (`front-page.php` with dedicated `WP_Query` per preset).
- Dark mode: system-aware (`prefers-color-scheme`) with manual toggle and Customizer `theme_color_mode` (light / dark / switch).
- Built-in SEO fallbacks: meta description, robots, canonical, Open Graph, Twitter Cards, JSON-LD (`WebSite` + `SearchAction`, `Organization`, `Article`, `BreadcrumbList`), `noindex` on search/404. Auto-suppressed when Yoast / Rank Math / SEOPress / AIOSEO detected (`inc/seo-schema.php`).
- Performance: emoji removal, conditional block-library loading, deferred `navigation.js` (`strategy: defer`), `fetchpriority`/`loading`/`sizes` on images, locally hosted Inter variable font, Font Awesome blocking, jQuery kept registered (opt-out via `titancore_disable_frontend_jquery`).
- Customizer: sticky header, `primary_color`/`accent_color`/`page_background_color_*`/`page_foreground_color_*`, `grid_pattern_color`/`grid_pattern_opacity`, `frontpage_preset`/`tag_limit`/`home_post_limit`, `show_toc`, `custom_header_code`/`custom_footer_code` with safe-mode `wp_kses` + `unfiltered_html` gating.
- Accessibility: skip link to `<main id="main-content">`, focus trapping, ARIA, keyboard mobile menu, `prefers-reduced-motion`.
- Editor parity: `theme.json` palette/typography/spacing/layout tokens + `add_editor_style`.

### Notes
- WordPress 6.0+, PHP 8.0+, text domain `titancore`, license GPLv2+.
- Minified assets shipped alongside sources (`assets/css/*.min.css`, `assets/js/navigation.min.js`) with `filemtime()` busting.

---

## How to add a new version

1. Bump `Version:` in `style.css` and `Stable tag:` in `readme.txt` (must match).
2. Add a new `## [X.Y.Z] - YYYY-MM-DD` section above (newest on top).
3. Mirror the same bullets under `readme.txt` `== Changelog ==` (`= X.Y.Z =`).
4. Run `./bin/package.sh X.Y.Z --publish` — creates `releases/titancore-X.Y.Z-dmY-Hi/` with `titancore.zip` + `CHANGELOG.md` + `checksums.txt`.
5. Test zip via Appearance > Themes > Upload or `wp-env` (see `RELEASING.md`).
