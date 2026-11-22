## [2.1.1] - 2026-11-23
### Fixed
- Dark mode contrast: primary, ring, accent and secondary colours are derived per mode via new `titancore_ensure_contrast()` (WCAG ratio helpers; dark primary kept ≥6:1 against the page so button text also passes 4.5:1); fixes dark-on-dark buttons, links, badges and focus rings. CSS-vars transient key renamed to `tc_css_vars2_*`.
- Theme toggle and form controls inherit text colour (no more black icon on dark header); text-colour picker chooses light/dark by highest WCAG ratio.
- UI: added ~120 missing utility classes used by templates (spacing, grid, borders, shadows, responsive variants) so front page, posts, pages and footer lay out correctly; front-page wrapper padding; comment form, sidebar list and footer spacing; in-content links readable in dark mode.
### Changed
- Comment "Author" badge is now translatable (was hard-coded CSS content); comment header wraps cleanly on mobile; removed stray left border on pages.
- Comments: 44px tap targets for timestamp links and cookie-consent label; hover transforms disabled under `prefers-reduced-motion`.
### Removed
- Deprecated `titancore_get_popular_categories()` alias; unused `startlocal.sh`/`stoplocal.sh` wrappers; stale `AGENTICUS-LOGICUS.md` reference in `opencode.json`.

