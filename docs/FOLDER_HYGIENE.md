# Folder Hygiene — TitanCore

> Where everything lives, why it stays there, and what not to move. Read before moving or creating any file.

## Two roots

**Repo root** (this folder) holds orchestration only: AGENTARDS prompts, docs, scripts, version folders, release history. Nothing here is loaded by WordPress.

**Theme root** = `themeversions/vX.Y.Z-titancore/titancore/` (symlinked as `themeversions/current`, mapped by `.wp-env.json` into `wp-content/themes/titancore`). WordPress resolves the template hierarchy only inside the theme root.

**Must stay in the theme root** — do not move into subfolders:

```
404.php, archive.php, comments.php, footer.php, front-page.php, functions.php,
header.php, home.php, index.php, page.php, search.php, single.php,
style.css, screenshot.png, theme.json, readme.txt, index.php (fallback)
```

`functions.php` bootstraps `inc/*.php`. `style.css` in root is **metadata only** (no styles — all CSS in `assets/css/`).

**Reusable partials only** live in `template-parts/`:

```
template-parts/
  background-grid.php   # dot grid overlay
  content.php           # post card
  content-none.php      # empty state
  front-header.php      # front-page intro + tag bar
  loop-container.php    # grid + pagination
```

Rule: if WordPress calls it via `get_template_part()` — it belongs in `template-parts/`. If WP resolves it via hierarchy — it stays in root.

## Canonical tree

```
TitanCore-free-custom-WordPress-theme/   # repo root (orchestration)
├── themeversions/                        # versioned theme sources (canonical deliverable)
│   ├── current -> v1.0.4-titancore       # symlink, what wp-env loads (not committed)
│   └── vX.Y.Z-titancore/
│       ├── titancore/                    # THEME ROOT (= folder inside the zip)
│       │   ├── assets/css/...            # style.css → enhancements.css → frontpage-presets.css (+ .min)
│       │   ├── assets/js/navigation.js + .min
│       │   ├── inc/                      # enqueue, customizer, template-tags, seo-schema, admin
│       │   ├── template-parts/
│       │   ├── languages/
│       │   ├── style.css                 # header only
│       │   ├── functions.php
│       │   ├── theme.json
│       │   ├── readme.txt                # WP.org readme (Stable tag == Version)
│       │   ├── screenshot.png
│       │   └── (root PHP templates — see list above)
│       ├── vX.Y.Z-titancore.zip          # created by bin/package.sh --publish
│       └── changelog.md                  # created by bin/package.sh --publish
├── AGENTARDS/                            # Agenticus prompt family + CUSTODIAN_LOG.md
├── docs/                                 # FOLDER_HYGIENE.md, AI_CODER_GUIDE.md
├── bin/                                  # up.sh / down.sh / verify.sh / use-version.sh / package.sh
├── releases/                             # legacy timestamped history (append-only)
├── .wp-env.json                          # local env: port 8889, theme mapping
├── CHANGELOG.md                          # Keep a Changelog source of truth
├── RELEASING.md                          # version bump + publish steps
├── README.md                             # repo guide
├── AGENTS.md                             # local env + mandatory checks
└── .gitignore / .gitattributes           # whitelist: themeversions/ + README + CHANGELOG
```

## Rules

1. **No build artifacts in root.** No `node_modules`, `dist/`, `build/`, `vendor/` committed. Sources + `.min` live side-by-side in `assets/`.
2. **No new top-level folders without a doc update.** If you add `patterns/`, `blocks/`, or `woocommerce/`, update this file + `README.md` File Structure + `AGENTS.md`.
3. **`releases/` is history, not source.** Each publish creates a new folder `titancore-X.Y.Z-dmY-Hi/` (spec: `titancore-versionnumber-dmY-Hm.zip` without `.zip` for the folder, with `.zip` for the timestamped file). Never reuse a folder, never overwrite a zip, never edit a past `CHANGELOG.md`.
4. **Do not commit `.wp-env/` or OS junk.** See `.gitignore`.
5. **Languages:** keep `titancore.pot` in `languages/`; never rename text domain.
6. **Naming:** PHP `titancore_*`, CSS `tc-*`, filters `titancore_*`, theme mods as documented in the `AGENTARDS/AGENTICUS-LOGICUS.md` settings map. New options must be added to `inc/customizer.php` + referenced in `inc/enqueue.php` or the template that reads them — grep both before merging.
7. **Load order matters:** `style.css` → `enhancements.css` → `frontpage-presets.css` (front-page only). Inline CSS vars from `inc/enqueue.php` override tokens.

## What goes where (quick)

| You want to… | Put it… |
|--------------|---------|
| New hierarchy template (e.g. `author.php`) | Root |
| Reusable card/section | `template-parts/` |
| New Customizer option | `inc/customizer.php` + sync `inc/enqueue.php` if it affects CSS vars |
| New perf/SEO logic | `inc/enqueue.php` / `inc/seo-schema.php` |
| New helper | `inc/template-tags.php` |
| New style | `assets/css/` + regenerate `.min` |
| New script | `assets/js/` + regenerate `.min` |
| New doc | `docs/` + link from `README.md` |
| New version | `themeversions/vX.Y.Z-titancore/titancore/` (copy the previous version folder, bump files) |
| Release artifact | `bin/package.sh X.Y.Z --publish` → `releases/` (legacy) + `themeversions/vX.Y.Z-titancore/` (canonical) |

## Anti-patterns to avoid

- Moving `single.php` / `archive.php` into `templates/` or `template-parts/` — WP won't find them.
- Putting styles in `style.css` — it must stay header-only.
- Adding `composer.json` / `package.json` unless you also document and justify the toolchain (theme is zero-build by design).
- Creating `releases/1.0.0/` generic folders — always use full `titancore-X.Y.Z-dmY-Hi` naming for traceability.
- Editing a published `releases/*/CHANGELOG.md` — changelog is append-only.

## For AI coders

Before touching structure, read `docs/AI_CODER_GUIDE.md`. For cleanup passes, use `AGENTARDS/AGENTICUS-PURGATORIUS.md` and log in `AGENTARDS/CUSTODIAN_LOG.md`. For features/fixes and full governed sessions, use `AGENTARDS/AGENTICUS-MAXIMUS.md`.
