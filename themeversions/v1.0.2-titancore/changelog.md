# v1.0.2 — TitanCore WordPress Theme

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

