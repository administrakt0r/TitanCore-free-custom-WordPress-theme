# v1.0.1 — TitanCore WordPress Theme
## [1.0.1] - 2026-08-25
### Fixed
- Accessibility: desktop primary nav links now meet 44×44px tap target (`assets/css/style.css`), active state uses `var(--accent)` background (`assets/css/enhancements.css`) for clearer affordance beyond color change.
- Accessibility: all decorative SVG icons now carry `aria-hidden="true" focusable="false"` (`inc/template-tags.php`) to eliminate screen-reader noise (pagination, theme toggle, menu, empty states).
- Mobile menu backdrop now fades via `opacity`+`visibility` (`assets/css/enhancements.css`, `header.php`) instead of instant `display:none`; sticky-aware offset (`top-14` when sticky, `top-0` otherwise) fixes misaligned overlay when `sticky_header` is disabled.

### Changed
- Typography: `.prose-lg` is responsive — `1rem` on mobile with `1.125rem` from `640px` (`assets/css/style.css`), improving readability and line-length on small screens.

