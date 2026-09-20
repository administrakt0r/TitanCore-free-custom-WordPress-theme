## [1.0.3] - 2026-08-30
### Removed
- Dead CSS: 41 unused utility blocks in `assets/css/style.css` (spacing, sizing, positioning, typography, color, state and responsive variants no template, partial, script or rendered view can emit) and dead selector groups in `assets/css/enhancements.css` (`.tc-pagination`/`.nav-links--pagination` members of the pagination rules plus `.sm:items-center`/`.sm:justify-between`). Class-token output verified identical across front page (all three presets), archive, search, 404 and single views; minified CSS regenerated.

