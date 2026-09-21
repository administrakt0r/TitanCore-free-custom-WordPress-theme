# TitanCore WordPress Theme

Ultra-fast, clean-coded WordPress theme for blogs and magazines. Dark mode, three front-page presets, built-in SEO, and zero bloat. No AI-generated slop, no bloated dependencies — just clean PHP, minimal CSS, and vanilla JavaScript.

![TitanCore Screenshot](themeversions/current/titancore/screenshot.png)

## Features

- **Performance** — zero jQuery on the frontend, inline SVG icons only (Lucide), emoji scripts removed, block CSS only when blocks are present, deferred navigation JS, proper `fetchpriority`/`loading`/`sizes` on images, locally hosted Inter variable font.
- **Dark mode** — system-preference aware, manual toggle with localStorage, force light/dark via Customizer, full palette per mode.
- **Three front-page presets** — Modern Blog, News Portal, Magazine; one Customizer dropdown, dedicated `WP_Query` per preset.
- **Built-in SEO with plugin suppression** — meta, Open Graph, JSON-LD schema, breadcrumbs; auto-suppressed when Yoast, Rank Math, SEOPress, or AIOSEO is active.
- **Accessibility** — 44×44px tap targets, skip links, focus-visible rings, `prefers-reduced-motion`, WCAG-AA contrast defaults.
- **Table of Contents**, sticky header, custom header/footer code with sanitization, translation-ready (`titancore`), `theme.json` editor parity.
- **Requirements** — WordPress 6.0+, PHP 8.0+, GPLv2 or later.

## Repository layout

The repo root is **orchestration only** — WordPress never loads anything from it. The installable theme lives in version folders:

```
themeversions/
  current -> v1.0.4-titancore        # symlink, what wp-env loads (machine-local)
  v1.0.4-titancore/
    titancore/                       # THEME ROOT — the actual theme source
    vX.Y.Z-titancore.zip             # created by bin/package.sh --publish
    changelog.md

AGENTARDS/     # Agenticus prompt family (AI autonomous sessions) + CUSTODIAN_LOG.md
bin/           # up.sh, down.sh, verify.sh, use-version.sh, package.sh
docs/          # FOLDER_HYGIENE.md, AI_CODER_GUIDE.md
releases/      # legacy timestamped release history (append-only)
.wp-env.json   # local env config (port 8889, theme mapping)
```

## Local development (wp-env + Docker)

Prerequisites: Docker Engine (`curl -fsSL https://get.docker.com | sh`) and wp-env (`npm -g install @wordpress/env`).

```bash
./bin/up.sh                  # start  -> http://localhost:8889  (admin: /wp-admin, default admin/password)
./bin/down.sh                # stop (containers kept for fast restart)
./bin/verify.sh              # MANDATORY post-edit verification
./bin/use-version.sh X.Y.Z   # point the env at another version, then restart
wp-env destroy               # full reset, then ./bin/up.sh again
```

The environment is isolated in this repo (port 8889). The theme is mounted live from `themeversions/current/titancore` — edit and refresh, no sync step. wp-cli: `wp-env run cli wp ... --allow-root`.

### Testing

`./bin/verify.sh` runs the mandatory sequence and exits 0 only when everything is clean:

1. PHP syntax check over the whole theme source (`php -l`)
2. Theme activation via wp-cli
3. `debug.log` cleared, then front page + Customizer + Widgets admin loaded (HTTP 200/302)
4. `debug.log` asserted empty

Run it after **any** edit session. See `AGENTS.md` for the full contract.

## Versioning & releases

- Versions are SemVer and must match in 4 places: `style.css` `Version:`, `readme.txt` `Stable tag:`, `CHANGELOG.md` `## [X.Y.Z]`, `readme.txt == Changelog ==`.
- New version = new folder: copy `themeversions/vOLD-titancore` to `themeversions/vX.Y.Z-titancore`, bump the files, then:

```bash
./bin/package.sh X.Y.Z --publish
```

This validates (required files, style.css header, text domain, PHP lint, minified assets) and creates both `releases/titancore-X.Y.Z-dmY-Hi/` (legacy history) and the canonical `themeversions/vX.Y.Z-titancore/` with `vX.Y.Z-titancore.zip` + `changelog.md`. Every zip contains a single top-level `titancore/` folder and is install-tested via `./bin/up.sh` + `wp theme install .../vX.Y.Z-titancore.zip --activate`.

Full procedure: `RELEASING.md`. Changelog: `CHANGELOG.md` (Keep a Changelog, append-only).

## GitHub contents

`.gitignore` is a **whitelist** — only `themeversions/`, `README.md`, and `CHANGELOG.md` are tracked. Prompts, docs, scripts, local env, and release history stay local. If this repo previously tracked other files, run once:

```bash
git rm -r --cached . && git add . && git commit -m "chore: whitelist tracking (themeversions only)"
```


## License & credits

GPLv2 or later. Inter font (SIL OFL 1.1), Lucide icons (ISC), Tailwind-inspired utility CSS. By [administraktor.com](https://administraktor.com).
