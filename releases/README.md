# Releases

Versioned, timestamped build history for TitanCore. Every publish creates a **new folder** — never overwrite a past release.

## Naming spec (required)

```
releases/titancore-{X.Y.Z}-{dmY}-{Hi}/
  titancore-{X.Y.Z}-{dmY}-{Hi}.zip   # timestamped archive (history)
  titancore.zip                      # generic installable (what users upload)
  CHANGELOG.md                       # slice for this version (from root CHANGELOG.md)
  checksums.txt                      # sha256 for both zips
  INFO.txt                           # build metadata
```

Example:

```
releases/titancore-1.0.0-25082026-1200/
releases/titancore-1.1.0-15092026-0930/
```

- `X.Y.Z` — SemVer, must equal `style.css` Version and `readme.txt` Stable tag.
- `dmY` — `25082026` (DDMMYYYY).
- `Hi` — `1200` (HHMM, 24h).

Folder name uses `titancore-versionnumber-dmY-Hm` (no `.zip`). The timestamped zip inside appends `.zip`. This is the user-requested spec — keep it exact.

## How a release is created

Never hand-zip. Always:

```bash
./bin/package.sh 1.1.0 --publish
```

The script:
1. Validates `style.css` Version == `readme.txt` Stable tag.
2. Lints PHP, checks required files (WordPress guidelines).
3. Copies theme into `titancore/` staging (excludes `releases/`, `bin/`, `docs/`, `.git`, dev docs).
4. Zips as `titancore/` single top-level folder (WordPress installer requirement).
5. Writes `checksums.txt` + `INFO.txt` + `CHANGELOG.md` slice.

## What is excluded from the zip

See `bin/package.sh` `EXCLUDES`: `.git`, `releases/`, `bin/`, `docs/`, `TODO.md`, `CUSTODIAN*`, `AGENTS.md`, `start-guide.md`, OS junk. `style.css` header + `screenshot.png` + `readme.txt` are always included (WordPress.org requirements).

## Installing a release

- **Users:** upload `titancore.zip` via Appearance > Themes > Add New > Upload Theme, or unzip into `wp-content/themes/titancore/`.
- **Devs (wp-env):**
  ```bash
  cd /home/q/Desktop/wordpressth/teme/news-titan
  su wpuser -c "wp-env run cli wp theme install /path/to/releases/titancore-1.1.0-*/titancore.zip --activate --allow-root"
  ```

## History is append-only

- Never edit a published `releases/*/CHANGELOG.md` or `checksums.txt`.
- Never delete a `releases/` folder to "clean up" — git history preserves it.
- If a release is broken, publish a new `PATCH` version (e.g. `1.1.1`).

## Current releases

| Version | Folder | Zip | Date |
|---------|--------|-----|------|
| 1.0.0 | `titancore-1.0.0-25082026-0000` | `titancore-1.0.0-25082026-0000.zip` + `titancore.zip` | 2026-06-22 (initial) |
| 1.0.1 | `titancore-1.0.1-25082026-0303` | `titancore-1.0.1-25082026-0303.zip` + `titancore.zip` | 2026-08-25 (a11y + responsive prose) |

Add a row here for each new release (or generate via `ls releases/`).

## Git tracking

`releases/` is intentionally **tracked** (not gitignored) so history survives clones. If a release grows large, GitHub Releases can host the zip instead — but keep the `CHANGELOG.md` + `INFO.txt` in git.

See `RELEASING.md` for the full bump → publish → tag flow.
