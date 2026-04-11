# TitanCore Verified Backlog

Last reconciled: 2026-03-17

## Security and hardening
- [x] Harden `custom_header_code` and `custom_footer_code` with `wp_kses` safe mode, raw-mode sanitization, and `unfiltered_html` gating in [`inc/customizer.php`](c:\Users\Da\Desktop\umjetnai-wp-theme-final\inc\customizer.php).
- [ ] Re-evaluate whether safe-mode footer `iframe` support and shared `<style>` allowances are the right long-term trust boundary for TitanCore's intended deployments.
- [ ] Add clearer admin-facing guidance for third-party embed/code snippets pasted into the Customizer header/footer fields.

## Performance and compatibility
- [x] Keep frontend `jquery` registered by default and move removal behind an explicit opt-in filter (`titancore_disable_frontend_jquery`) in [`inc/enqueue.php`](c:\Users\Da\Desktop\umjetnai-wp-theme-final\inc\enqueue.php).
- [x] Rework `front-page.php` `news` and `magazine` presets to use dedicated `WP_Query` instances instead of consuming one main query stream.
- [x] Remove globally forced eager/high-priority loading from the custom logo filter so only real LCP images opt into it.
- [ ] Add optional modern image format workflow (WebP/AVIF generation plus fallback strategy).
- [ ] Audit whether `wp-embed` and core block asset stripping need finer-grained opt-outs for plugin-heavy or block-heavy sites.
- [ ] Review front-page queries for safe caching and any low-risk metadata/query optimizations that do not break pagination or editorial ordering.

## UI/UX
- [x] Bring [`index.php`](c:\Users\Da\Desktop\umjetnai-wp-theme-final\index.php) onto the shared loop-container/content-none flow used by archive/search templates.
- [x] Consolidate the shared home/front intro header into [`template-parts/front-header.php`](c:\Users\Da\Desktop\umjetnai-wp-theme-final\template-parts\front-header.php).
- [ ] Introduce a more consistent card visual system across the `blog`, `news`, and `magazine` presets.
- [ ] Improve empty/search states with curated recovery links or content suggestions.
- [ ] Strengthen mobile menu open-state affordance and active-link indication without breaking the current visual language.
- [ ] Add a richer single-post side rail or related content module that does not crowd reading flow.

## Accessibility
- [x] Point the skip link at semantic `<main id="main-content">` targets across the public templates.
- [ ] Add active-section highlighting for the single-post TOC using `IntersectionObserver`.
- [ ] Add automated contrast guardrails for Customizer color selections.
- [x] Improve mobile navigation focus management and focus return in [`header.php`](c:\Users\Da\Desktop\umjetnai-wp-theme-final\header.php) and [`assets/js/navigation.js`](c:\Users\Da\Desktop\umjetnai-wp-theme-final\assets\js\navigation.js); reduced-motion transitions were already covered globally in [`assets/css/enhancements.css`](c:\Users\Da\Desktop\umjetnai-wp-theme-final\assets\css\enhancements.css).
- [ ] Normalize comment form labels and validation feedback for better assistive-technology clarity.

## SEO and metadata
- [x] Provide fallback meta description, robots, canonical, and Open Graph/Twitter tags when no dedicated SEO plugin is active.
- [x] Provide fallback `WebSite` plus `SearchAction` schema in [`inc/seo-schema.php`](c:\Users\Da\Desktop\umjetnai-wp-theme-final\inc\seo-schema.php).
- [x] Provide fallback front-page `Organization` schema with optional custom logo image.
- [x] Suppress TitanCore's single-post `Article` schema when a dedicated SEO plugin is active to avoid duplicate structured data.
- [ ] Extend canonical handling to remaining archive/search/date contexts and confirm paginated archive behavior.
- [ ] Add a default OG image fallback plus richer Organization/Publisher social profile support.
- [ ] Expand breadcrumb/schema handling for hierarchical pages and custom taxonomy archives.
- [ ] Add optional per-post reading-time metadata and expose it in schema/meta output.

## WordPress and editor integration
- [x] Ship [`theme.json`](c:\Users\Da\Desktop\umjetnai-wp-theme-final\theme.json) with palette, typography, spacing, and layout tokens.
- [ ] Add editor stylesheet parity so block content in the editor matches frontend prose spacing and typography.
- [ ] Register block patterns for the front-page hero/trending/feature layouts used by TitanCore presets.
- [ ] Decide whether to render the registered footer menu in [`footer.php`](c:\Users\Da\Desktop\umjetnai-wp-theme-final\footer.php) or remove the unused location from [`functions.php`](c:\Users\Da\Desktop\umjetnai-wp-theme-final\functions.php).
- [ ] Audit which Customizer options should be surfaced or mirrored in editor contexts for stronger editor/frontend parity.

## DX and testing
- [ ] Add PHP lint, PHPCS (WordPress standards), and lightweight JS/CSS lint scripts.
- [ ] Add visual smoke tests for `blog`, `news`, and `magazine` presets at desktop and mobile breakpoints.
- [ ] Add a lightweight release QA checklist covering schema validation, accessibility checks, and performance budgets.
- [ ] Add a reproducible process for verifying JSON-LD output and plugin compatibility in a real WordPress install.

## Docs and release work
- [x] Document that TitanCore ships committed minified runtime assets and source files must stay in sync with them in [`start-guide.md`](c:\Users\Da\Desktop\umjetnai-wp-theme-final\start-guide.md).
- [ ] Update [`readme.txt`](c:\Users\Da\Desktop\umjetnai-wp-theme-final\readme.txt) compatibility metadata after a verified WordPress/PHP test pass.
- [ ] Add a real changelog/release workflow instead of leaving the public readme at `1.0.0`.
- [ ] Document the new `titancore_disable_frontend_jquery` compatibility/performance filter for integrators.

## Future product and content improvements
- [ ] Add a related-posts module with lightweight query caching.
- [ ] Enhance the author card with an archive link and optional social/profile fields.
- [ ] Add newsletter or CTA insertion points on archive and single templates.
- [ ] Add optional popular tags/category shortcuts to empty states and archive headers.
