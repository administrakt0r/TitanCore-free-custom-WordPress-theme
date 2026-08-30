# Releasing TitanCore

Single source for version bumping, packaging, and WordPress.org compliance. Read with `AGENTS.md` and `docs/FOLDER_HYGIENE.md`.

## Versioning

- **SemVer** `MAJOR.MINOR.PATCH`:
  - `MAJOR` — breaking template/filter/option change or WP/PHP requirement bump.
  - `MINOR` — new preset, block pattern, Customizer option, or non-breaking enhancement.
  - `PATCH` — bugfix, a11y, perf, copy, or security fix.
- Pre-release tags (`-beta.1`, `-rc.1`) allowed for internal `releases/` only, never as `Stable tag` on wordpress.org.

## Single source of truth

| File | Field | Example |
|------|-------|---------|
| `style.css` | `Version:` header | `Version: 1.1.0` |
| `readme.txt` | `Stable tag:` | `Stable tag: 1.1.0` |
| `CHANGELOG.md` | `## [1.1.0] - YYYY-MM-DD` | section heading |
| `readme.txt` | `== Changelog ==` `= 1.1.0 =` | WP.org mirror |

All four MUST match before `./bin/package.sh --publish` succeeds (script aborts on mismatch).

## Step-by-step release

### 1. Prepare

The source of truth is the version folder. Create the new one from the current version, then edit inside it:

```bash
# copy the current version folder to the new version name (adjust X.Y.Z)
cp -a themeversions/v1.0.1-titancore themeversions/vX.Y.Z-titancore
# point the local env at it
./bin/use-version.sh X.Y.Z && ./bin/down.sh && ./bin/up.sh

git status            # clean tree
find themeversions/current/titancore -name "*.php" -exec php -l {} \;
```

Update (inside `themeversions/vX.Y.Z-titancore/titancore/`):
- `style.css` header (`Version`, `Tested up to` if you verified).
- `readme.txt` (`Stable tag`, `Tested up to`, `== Changelog ==`).

Update (repo root):
- `CHANGELOG.md` (new `## [X.Y.Z]` on top, Keep a Changelog categories: Added/Changed/Fixed/Security).

Regenerate minified assets if you touched sources:

```
assets/css/style.css              → assets/css/style.min.css
assets/css/enhancements.css       → assets/css/enhancements.min.css
assets/css/frontpage-presets.css  → assets/css/frontpage-presets.min.css
assets/js/navigation.js           → assets/js/navigation.min.js
# any minifier is fine (csso, terser, esbuild, online tool). Keep source + min in sync.
```

### 2. Dry-run validation

```bash
./bin/package.sh 1.1.0
# checks: required files, style.css headers, text domain, PHP lint, minified presence
```

Fix any warnings/errors before continuing.

### 3. Publish (creates versioned folder + zip)

```bash
./bin/package.sh 1.1.0 --publish
```

Creates:

```
releases/                                    # legacy timestamped history (append-only)
  titancore-1.1.0-25082026-1430/
    titancore-1.1.0-25082026-1430.zip
    titancore.zip
    CHANGELOG.md
    checksums.txt
    INFO.txt

themeversions/v1.1.0-titancore/              # canonical version-first deliverable
    v1.1.0-titancore.zip
    changelog.md
```

Naming spec (legacy): `titancore-{semver}-{dmY}-{Hi}.zip` e.g. `titancore-1.1.0-25082026-1430.zip`. Naming spec (canonical): `v{semver}-titancore.zip`. `titancore.zip` in the legacy folder is the file WordPress users actually upload; the version-first zip in `themeversions/` is what GitHub/Updater consumes.

Never hand-zip — always use the script so the top-level folder inside is `titancore/` (WP installer requires a single folder).

### 4. Smoke test

```bash
./bin/up.sh
wp-env run cli wp theme install themeversions/v1.1.0-titancore/v1.1.0-titancore.zip --activate --allow-root
curl -s http://localhost:8889 | head
./bin/verify.sh
```

Or manual: Appearance > Themes > Add New > Upload Theme > the version zip.

### 5. Tag & push

`.gitignore` is a whitelist — only `themeversions/`, `README.md`, and `CHANGELOG.md` are tracked:

```bash
git add themeversions/ CHANGELOG.md README.md
git commit -m "release: 1.1.0"
git tag v1.1.0
git push origin main --tags
```

For wordpress.org: `Stable tag` is what WP reads — no extra SVN step needed if you deploy via GitHub Updater or direct zip. If you publish to .org SVN, copy `trunk` from the `titancore/` folder inside the zip.

## WordPress guidelines checklist (every release)

- [ ] `style.css` header complete: Theme Name, Theme URI, Author, Description, Version, License, Text Domain (`titancore`), Tags.
- [ ] `readme.txt` headers: Requires at least (6.0), Tested up to (e.g. 6.8), Requires PHP (8.0), License, Stable tag == Version.
- [ ] License is GPLv2 or later; `screenshot.png` is 1200×900, PNG, no branding spam.
- [ ] No PHP errors/warnings with `WP_DEBUG` on; `php -l` clean for all files.
- [ ] No minified-only assets — source + min both shipped.
- [ ] Text domain `titancore` on all i18n calls; `load_theme_textdomain` present.
- [ ] `inc/seo-schema.php` suppression still works when Yoast/Rank Math/SEOPress/AIOSEO active.
- [ ] Accessibility: skip link, focus management, ARIA, keyboard nav, `prefers-reduced-motion`.
- [ ] Scripts/styles enqueued with `filemtime()` versioning, no hard-coded versions.

## Changelog discipline

- `CHANGELOG.md` is the source; `readme.txt` mirrors it (WP.org has no markdown).
- One `## [X.Y.Z]` per version, date in `YYYY-MM-DD`.
- Keep `Unreleased` section at top if you prefer (optional), but move to versioned heading at release.
- Never rewrite history — append, don't edit old version notes.

## Hotfix

Branch from tag: `git checkout -b hotfix/1.1.1 v1.1.0`, bump to `1.1.1`, follow same steps, tag `v1.1.1`.

## Troubleshooting

- `Version mismatch` from `package.sh`: edit both `style.css` and `readme.txt`.
- `Missing minified` warning: regenerate `.min` or commit if intentionally removed.
- Zip has wrong top-level: you hand-zipped the folder contents instead of the folder — use the script.
