# Folder Hygiene — TitanCore

> Where everything lives, why it stays there, and what not to move. Read before moving or creating any file.

## Root is sacred (WordPress template hierarchy)

WordPress loads hierarchy templates **only from the theme root**. Moving them breaks the theme.

**Must stay in root** — do not move into subfolders:

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
titancore/                              # theme root (= folder inside titancore.zip)
├── assets/
│   ├── css/
│   │   ├── style.css / style.min.css
│   │   ├── enhancements.css / enhancements.min.css
│   │   └── frontpage-presets.css / frontpage-presets.min.css
│   ├── js/
│   │   ├── navigation.js / navigation.min.js
│   │   └── (future: add alongside, never replace min without source)
│   └── fonts/inter/InterVariable.woff2
├── inc/
│   ├── enqueue.php        # enqueue + perf + inline CSS vars
│   ├── customizer.php     # all Customizer IDs + sanitizers
│   ├── template-tags.php  # helpers, TOC, icons, pagination
│   ├── seo-schema.php     # SEO plugin detection + meta/schema
│   └── admin.php          # is_admin() only — welcome notice
├── template-parts/        # see above
├── languages/             # .pot/.po/.mo (text domain: titancore)
├── docs/
│   ├── FOLDER_HYGIENE.md  # this file
│   └── AI_CODER_GUIDE.md  # scaling guide for AI/human contributors
├── bin/
│   └── package.sh         # release packager (do not move)
├── releases/              # versioned artifacts (see below)
│   ├── README.md
│   └── titancore-X.Y.Z-dmY-Hi/
│       ├── titancore-X.Y.Z-dmY-Hi.zip
│       ├── titancore.zip
│       ├── CHANGELOG.md
│       ├── checksums.txt
│       └── INFO.txt
├── style.css              # header only
├── functions.php          # setup + require inc/*
├── theme.json             # tokens
├── readme.txt             # WP.org readme (Stable tag == Version)
├── CHANGELOG.md           # Keep a Changelog source of truth
├── RELEASING.md           # version bump + publish steps
├── README.md              # feature/docs entry point
├── AGENTS.md              # local env + mandatory checks
├── screenshot.png         # 1200×900
└── (root PHP templates — see list above)
```

## Rules

1. **No build artifacts in root.** No `node_modules`, `dist/`, `build/`, `vendor/` committed. Sources + `.min` live side-by-side in `assets/`.
2. **No new top-level folders without a doc update.** If you add `patterns/`, `blocks/`, or `woocommerce/`, update this file + `README.md` File Structure + `AGENTS.md`.
3. **`releases/` is history, not source.** Each publish creates a new folder `titancore-X.Y.Z-dmY-Hi/` (spec: `titancore-versionnumber-dmY-Hm.zip` without `.zip` for the folder, with `.zip` for the timestamped file). Never reuse a folder, never overwrite a zip, never edit a past `CHANGELOG.md`.
4. **Do not commit `.wp-env/` or OS junk.** See `.gitignore`.
5. **Languages:** keep `titancore.pot` in `languages/`; never rename text domain.
6. **Naming:** PHP `titancore_*`, CSS `tc-*`, filters `titancore_*`, theme mods as documented in `AGENTPROMPT.md` mapping. New options must be added to `inc/customizer.php` + referenced in `inc/enqueue.php` or the template that reads them — grep both before merging.
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
| Release artifact | `releases/titancore-X.Y.Z-dmY-Hi/` via `bin/package.sh --publish` |

## Anti-patterns to avoid

- Moving `single.php` / `archive.php` into `templates/` or `template-parts/` — WP won't find them.
- Putting styles in `style.css` — it must stay header-only.
- Adding `composer.json` / `package.json` unless you also document and justify the toolchain (theme is zero-build by design).
- Creating `releases/1.0.0/` generic folders — always use full `titancore-X.Y.Z-dmY-Hi` naming for traceability.
- Editing a published `releases/*/CHANGELOG.md` — changelog is append-only.

## For AI coders

Before touching structure, read `docs/AI_CODER_GUIDE.md`. For cleanup passes, use `CUSTODIAN.md` (two removals per run) and log in `CUSTODIAN_LOG.md`. For features/fixes, use `AGENTPROMPT.md`.
