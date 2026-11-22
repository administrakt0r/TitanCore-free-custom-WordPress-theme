# AGENTS.md — TitanCore WordPress Theme

## What this is

A classic WordPress theme (no block theme). No build tools, no package.json, no composer.json, no test framework, no CI. PHP + CSS + vanilla JS only. Requirements: WordPress 6.0+, PHP 8.0+.

## Local Development (wp-env + Docker, this repo)

### Prerequisites
```bash
# Docker Engine (required by wp-env)
curl -fsSL https://get.docker.com | sh
systemctl start docker

# wp-env (WordPress dev environment)
npm -g install @wordpress/env
ln -sf $(which wp-env) /usr/local/bin/wp-env
```

### Start / stop / verify
```bash
./bin/up.sh                  # start  -> http://localhost:8889 (admin: /wp-admin, wp-env default admin/password)
./bin/down.sh                # stop (containers kept for fast restart)
./bin/verify.sh              # MANDATORY post-edit verification
./bin/use-version.sh X.Y.Z   # point the env at themeversions/vX.Y.Z-titancore, then restart
```

- The theme source lives at `themeversions/vX.Y.Z-titancore/titancore/` (symlinked as `themeversions/current`). wp-env maps it into `wp-content/themes/titancore` via `.wp-env.json` — edits to the version folder are live on refresh, no sync step.
- This is an isolated wp-env instance **in this repo** (port 8889) — not shared with any other project.
- Full reset: `wp-env destroy && ./bin/up.sh`.
- wp-cli: `wp-env run cli wp ... --allow-root`.

## Asset minification (manual)

WordPress loads `.min` versions. If you edit a source file, regenerate the minified counterpart before shipping. Cache busting uses `filemtime()`.

```
assets/css/style.css        → assets/css/style.min.css
assets/css/enhancements.css → assets/css/enhancements.min.css
assets/css/frontpage-presets.css → assets/css/frontpage-presets.min.css
assets/js/navigation.js     → assets/js/navigation.min.js
```

No minification script is committed. Use any CSS/JS minifier (e.g. `terser`, `csso`, or online tool). Keep both source and minified in the repo.

## Entry point and includes

`functions.php` bootstraps the theme and loads:
- `inc/enqueue.php` — script/style registration, performance hooks, inline CSS custom properties
- `inc/customizer.php` — all Customizer settings and sanitizers
- `inc/template-tags.php` — helper functions, TOC, caching, icons
- `inc/seo-schema.php` — SEO meta, OG tags, JSON-LD schema, breadcrumbs
- `inc/admin.php` — admin welcome notice (loaded conditionally, `is_admin()` only)

## Key conventions

- Text domain: `titancore`
- Theme prefix for all functions/filters: `titancore_`
- CSS component classes use `tc-` prefix (e.g. `tc-post-card`, `tc-single__header`)
- No jQuery on frontend by default (registered but not dequeued); full removal is opt-in via `titancore_disable_frontend_jquery` filter
- Font Awesome blocked from plugin injection by default; opt-out via `titancore_block_fontawesome_assets` filter
- Inline SVG icons only (Lucide set). No icon fonts.
- `style.css` contains only the theme metadata header — no actual styles. All CSS lives in `assets/css/`.

## Three front-page presets

`front-page.php` renders one of three layouts (Modern Blog, News Portal, Magazine) controlled by a Customizer dropdown. Each preset uses its own `WP_Query` instance. Frontpage-specific CSS is conditionally loaded only on `is_front_page()`.

## SEO plugin detection

`inc/seo-schema.php` checks for Yoast, Rank Math, SEOPress, AIOSEO. If any is active, TitanCore suppresses its own SEO output to avoid duplicates.

## Error checking — MANDATORY before every "done"

After ANY edit session, run 2 exhaustive grep verification passes (checking all PHP/JS/CSS in the theme source for every pattern name related to the changed feature), then run:

```bash
./bin/verify.sh
```

It executes the mandatory sequence against the local wp-env instance (port 8889): PHP syntax check over `themeversions/current/titancore`, theme activation via wp-cli, debug.log clear + front page/Customizer/Widgets loads (HTTP 200/302), and an empty debug.log assertion. Exit code 0 = clean.

If ANY check reports a problem, fix it before reporting "done" to the user.

## Systematized cleanup

Use `AGENTARDS/AGENTICUS-PURGATORIUS.md` for safe dead code/CSS/JS cleanup. Copy the prompt into any AI agent. It runs evidence-based removal campaigns, verifies with the mandatory error check above, and appends one row per removal to `AGENTARDS/CUSTODIAN_LOG.md`.

Use `AGENTARDS/AGENTICUS-MAXIMUS.md` for general autonomous development tasks (bug fixes, accessibility, performance, improvements) — including versioning and release creation. See `AGENTARDS/README.md` for the full prompt family.

## Gotchas

- `style.css` has no styles — it is metadata only. All CSS is in `assets/css/`.
- CSS load order matters: `style.css` -> `enhancements.css` -> `frontpage-presets.css` (front-page only).
- Inline CSS custom properties are generated dynamically in `inc/enqueue.php` from Customizer color settings — grep setting IDs before removing color controls.
- `theme_color_mode` controls both JS loading (`inc/enqueue.php`) and body class (`header.php`) — check both.
- The `titancore_should_keep_core_block_assets` filter controls block library CSS loading — verify before modifying.
- jQuery is kept registered by default for plugin compat; removal is opt-in via `titancore_disable_frontend_jquery`.
- No testing framework or CI — verify changes manually on wp-env.
- TitanCore has its own wp-env instance in this repo (port 8889) — start/stop with `./bin/up.sh` / `./bin/down.sh`; not shared with any other project.

## Release & folder hygiene

- **Never move hierarchy templates** (`404.php`, `archive.php`, `single.php`, etc.) out of the theme root `themeversions/current/titancore/` — WordPress resolves them only there. Reusable parts go in `template-parts/`. See `docs/FOLDER_HYGIENE.md`.
- **Version is SemVer** and lives in 4 places that MUST match before packaging: `style.css` `Version:`, `readme.txt` `Stable tag:`, `CHANGELOG.md` `## [X.Y.Z]`, `readme.txt` `== Changelog ==`. See `RELEASING.md`.
- **Packaging:** never hand-zip. Use `./bin/package.sh X.Y.Z --publish` — validates, then creates `releases/titancore-X.Y.Z-dmY-Hi/` (legacy history) **and** the canonical `themeversions/vX.Y.Z-titancore/` with `vX.Y.Z-titancore.zip` + `changelog.md`. New version = new folder in `themeversions/`; switch the local env with `./bin/use-version.sh X.Y.Z`.
- **Releases are append-only:** do not edit or delete a published `releases/titancore-*/` folder; new version = new folder. Each folder is self-contained (zip + changelog per `releases/README.md`).
- **Minified assets must stay in sync:** `assets/css/*.min.css` and `assets/js/navigation.min.js` are what WordPress loads; commit source + min together with `filemtime()` busting.

## Agent skills — use these BEFORE editing

Discover installed skills first (they vary per harness; `~/.agents/skills/`, plugin skills). A missing skill is "unavailable", never silently skipped. Preferred for this repo:

| Task | Skill |
|---|---|
| Entry point / routing | `wordpress`, `wordpress-router`, `wp-project-triage` |
| Classic theme work (templates, hierarchy, child themes) | `wordpress-theme-development` |
| theme.json / editor styles | `wp-block-themes` |
| Patterns | `wp-patterns` |
| Performance (queries, autoload, caching) | `wp-performance` |
| CSS bloat / !important / breakpoints | `wp-css-audit` |
| Static analysis | `wp-phpstan` |
| WP-CLI / ops | `wp-wpcli-and-ops`, `wp-env` |
| Directory/license rules before submission | `wp-plugin-directory-guidelines` (GPL/licensing parts), Theme Review Handbook |
| SEO output | `seo-optimization` |

Version-sensitive WordPress API questions: check Context7 or developer.wordpress.org before relying on memory.

## WordPress.org theme standards (must hold before any release)

- Theme Check / Theme Review Handbook clean: GPLv2+ license on everything shipped (bundled Inter font is OFL — document in `readme.txt` "Resources"), no plugin-territory functionality, `screenshot.png` 1200x900 (done in 2.1.0), `readme.txt` complete, `wp_head`/`wp_footer`/`wp_body_open`, `body_class`/`post_class`, `wp_link_pages`, `comments.php`.
- Escape late (`esc_*`, `wp_kses_post`), sanitize input, nonces on any form handling, `/* translators: */` comment before every placeholder string, text domain `titancore` only.
- Run `phpcs --standard=WordPress-Extra` (and `WordPress-Docs`) on `themeversions/current/titancore`; known false positive: `titancore_get_icon()` returns trusted inline SVG. Report fixes, never weaken rules.
- Check tool availability with `command -v phpcs php docker wp-env`; unavailable != passed.

## Agent hygiene

- Do not add yet another "audit pass" row to `CUSTODIAN_LOG.md` unless something was actually removed or a *new* finding exists. Automated no-op log PRs are closed, not merged.
- Edit source in `themeversions/current/titancore/`; a published version folder is immutable — bump version via `RELEASING.md` instead of silently changing a released zip.
- Port 8889 may be held by another wp-env project (check `docker ps`); never stop other projects' containers — use `WP_ENV_PORT=NNNN` (verify.sh now checks the served version). `verify.sh` needs `wp-env` on PATH; if wp-cli steps fail while lint/HTTP pass, report it as an environment failure.

## Relevant docs

- `README.md` — full feature list, filters, file structure, release notes
- `RELEASING.md` — version bumping + packaging steps (mandatory for any release)
- `CHANGELOG.md` — Keep a Changelog source of truth (sync to `readme.txt`)
- `docs/FOLDER_HYGIENE.md` — where every file lives and what not to move
- `docs/AI_CODER_GUIDE.md` — scaling playbook for AI/human contributors
- `releases/README.md` — naming spec and history
- `start-guide.md` — setup and configuration steps
- `TODO.md` — verified backlog (last reconciled 2026-03-17)
- `themeversions/` — versioned theme sources (`vX.Y.Z-titancore/titancore/`) + zips; the `current` symlink is what wp-env loads. New versions = new folder.
- `bin/` — `up.sh` / `down.sh` / `verify.sh` (local env) and `package.sh` (release packaging)
- `AGENTARDS/README.md` — Agenticus autonomous-agent prompt family (master protocol, cleanup, prompt maintenance, project memory)
- `AGENTARDS/CUSTODIAN_LOG.md` — log of every removal (append one row per removal)
