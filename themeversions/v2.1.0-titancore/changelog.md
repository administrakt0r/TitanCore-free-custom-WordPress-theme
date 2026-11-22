## [2.1.0] - 2026-11-22
### Changed
- Whole theme reformatted to WordPress-Extra coding standards via PHPCBF; added `phpcs.xml.dist`.
- Yoda conditions, no short ternaries (`??`/explicit checks), DOM-property phpcs annotations, `$tag` global no longer overwritten in loops (`single.php`, `front-header.php`).
- `titancore_pagination_markup()` no longer declares unused parameters.
### Security
- `single.php`: related-post thumbnail output escaped with `wp_kses_post()`; `comments.php`: comment count escaped.
### Fixed
- Added missing `translators:` comments (`single.php`, `content.php`, `seo-schema.php`).
### Removed
- `titancore_cleanup_head()` (removal of wp_head actions is plugin-territory per Theme Review).
### Added
- `style.css` header: `Requires at least`, `Tested up to`, `Requires PHP`, copyright notice; alignment/caption CSS classes.
### Docs
- AGENTS.md skills/standards section, AGENTARDS README + Maximus protocol, TODO upload blockers.

