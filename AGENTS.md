# AGENTS.md — TitanCore WordPress Theme

## What this is

A classic WordPress theme (no block theme). No build tools, no package.json, no composer.json, no test framework, no CI. PHP + CSS + vanilla JS only. Requirements: WordPress 6.0+, PHP 8.0+.

## Local Development (wp-env + Docker)

### Prerequisites
```bash
# Docker Engine (required by wp-env)
curl -fsSL https://get.docker.com | sh
systemctl start docker

# wp-env (WordPress dev environment)
npm -g install @wordpress/env

# Symlink wp-env so all users can find it
ln -sf $(which wp-env) /usr/local/bin/wp-env
```

### First-time setup
```bash
# wpuser should already exist from news-titan setup
id wpuser 2>/dev/null || useradd -m -s /bin/bash wpuser
usermod -aG docker wpuser
chmod o+x /home/q /home/q/Desktop

# TitanCore shares the news-titan wp-env instance
# Both themes are registered in news-titan/.wp-env.json
cd /home/q/Desktop/wordpressth/teme/news-titan
su wpuser -c "wp-env start"
# Site runs at http://localhost:8888
```

### Useful wp-env commands
```bash
cd /home/q/Desktop/wordpressth/teme/news-titan

# Run wp-cli commands (always use --allow-root)
su wpuser -c "wp-env run cli wp theme list --allow-root"
su wpuser -c "wp-env run cli wp theme activate TitanCore-free-custom-WordPress-theme --allow-root"
su wpuser -c "wp-env run cli wp theme activate news-titan --allow-root"

# Restart after config changes
su wpuser -c "wp-env stop && wp-env start"

# Destroy and rebuild fresh
su wpuser -c "wp-env destroy && wp-env start"
```

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

After ANY edit session, run 2 exhaustive grep verification passes (checking all PHP/JS/CSS for every pattern name related to the changed feature), then run this exact sequence and report results:

```bash
# 1. PHP syntax check all files
find /home/q/Desktop/wordpressth/teme/TitanCore-free-custom-WordPress-theme -name "*.php" \
  ! -path "*/.wp-env/*" \
  -exec php -l {} \; 2>&1 | grep -v "No syntax errors"

# 2. Activate theme (triggers fatal errors if functions/classes broken)
su wpuser -c "wp-env run cli wp theme activate TitanCore-free-custom-WordPress-theme --allow-root 2>&1"

# 3. Clear debug log, then trigger page loads (frontpage + customizer + widgets admin)
su wpuser -c "wp-env run cli bash -- -c 'truncate -s 0 wp-content/debug.log' --allow-root 2>&1"
curl -s http://localhost:8888 > /dev/null 2>&1
curl -s "http://localhost:8888/wp-admin/customize.php" > /dev/null 2>&1
curl -s "http://localhost:8888/wp-admin/widgets.php" > /dev/null 2>&1

# 4. Check debug log
su wpuser -c "wp-env run cli bash -- -c 'cat wp-content/debug.log 2>/dev/null' --allow-root 2>&1"
```

If ANY step produces errors, fix them before reporting "done" to the user. Every step must produce zero output.

## Systematized cleanup

Use `CUSTODIAN.md` for safe dead code/CSS/JS cleanup. Copy the prompt into any AI agent. Each invocation should remove two related proven-dead items when safe, or one item when only one safe target exists, then verify with the mandatory error check above and append one row per removal to `CUSTODIAN_LOG.md`.

Use `AGENTPROMPT.md` for general maintenance tasks (bug fixes, accessibility, performance, small improvements).

## Gotchas

- `style.css` has no styles — it is metadata only. All CSS is in `assets/css/`.
- CSS load order matters: `style.css` -> `enhancements.css` -> `frontpage-presets.css` (front-page only).
- Inline CSS custom properties are generated dynamically in `inc/enqueue.php` from Customizer color settings — grep setting IDs before removing color controls.
- `theme_color_mode` controls both JS loading (`inc/enqueue.php`) and body class (`header.php`) — check both.
- The `titancore_should_keep_core_block_assets` filter controls block library CSS loading — verify before modifying.
- jQuery is kept registered by default for plugin compat; removal is opt-in via `titancore_disable_frontend_jquery`.
- No testing framework or CI — verify changes manually on wp-env.
- TitanCore shares the same wp-env instance as news-titan (port 8888). Both themes are registered in `news-titan/.wp-env.json`. Run wp-env commands from the `news-titan` directory.

## Release & folder hygiene

- **Never move hierarchy templates** (`404.php`, `archive.php`, `single.php`, etc.) out of root — WordPress resolves them only there. Reusable parts go in `template-parts/`. See `docs/FOLDER_HYGIENE.md`.
- **Version is SemVer** and lives in 4 places that MUST match before packaging: `style.css` `Version:`, `readme.txt` `Stable tag:`, `CHANGELOG.md` `## [X.Y.Z]`, `readme.txt` `== Changelog ==`. See `RELEASING.md`.
- **Packaging:** never hand-zip. Use `./bin/package.sh X.Y.Z --publish` — validates, then creates `releases/titancore-X.Y.Z-dmY-Hi/` with `titancore.zip` + timestamped `titancore-X.Y.Z-dmY-Hi.zip` + `CHANGELOG.md` + `checksums.txt` + `INFO.txt`. Naming spec is `titancore-versionnumber-dmY-Hm.zip`.
- **Releases are append-only:** do not edit or delete a published `releases/titancore-*/` folder; new version = new folder. Each folder is self-contained (zip + changelog per `releases/README.md`).
- **Minified assets must stay in sync:** `assets/css/*.min.css` and `assets/js/navigation.min.js` are what WordPress loads; commit source + min together with `filemtime()` busting.

## Relevant docs

- `README.md` — full feature list, filters, file structure, release notes
- `RELEASING.md` — version bumping + packaging steps (mandatory for any release)
- `CHANGELOG.md` — Keep a Changelog source of truth (sync to `readme.txt`)
- `docs/FOLDER_HYGIENE.md` — where every file lives and what not to move
- `docs/AI_CODER_GUIDE.md` — scaling playbook for AI/human contributors
- `releases/README.md` — naming spec and history
- `start-guide.md` — setup and configuration steps
- `TODO.md` — verified backlog (last reconciled 2026-03-17)
- `CUSTODIAN.md` — prompt for systematic cleanup passes
- `CUSTODIAN_LOG.md` — log of every removal (append one row per removal)
- `AGENTPROMPT.md` — prompt for general maintenance tasks
