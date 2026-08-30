#!/usr/bin/env bash
# TitanCore Release Packager
# Usage: ./bin/package.sh [version] [--publish]
#   version format: X.Y.Z (semver). If omitted, uses Version from the current theme source.
#   --publish  -> creates releases/titancore-{version}-{dmY-Hi}/  (legacy history)
#                 AND themeversions/v{version}-titancore/{v{version}-titancore.zip, changelog.md}
#   without --publish -> dry-run / verify only
#
# Theme source of truth: themeversions/vX.Y.Z-titancore/titancore/
# Naming spec (legacy):  titancore-{version}-{dmY}-{Hi}.zip  e.g. titancore-1.1.0-25082026-1430.zip
# Naming spec (canon):   v{version}-titancore.zip
# WP guidelines: zip must contain a single top-level folder `titancore/`
#              (installable via Appearance > Themes > Upload).
set -euo pipefail

REPO_ROOT="$(cd "$(dirname "$0")/.." && pwd)"
THEME_SLUG="titancore"

# ------------------------------------------------------------
# Helpers
# ------------------------------------------------------------
err() { echo "ERROR: $*" >&2; exit 1; }
info() { echo "==> $*"; }

# ------------------------------------------------------------
# Args
# ------------------------------------------------------------
VERSION=""
PUBLISH=false
for arg in "$@"; do
  case "$arg" in
    --publish) PUBLISH=true ;;
    --help|-h)
      echo "Usage: $0 [X.Y.Z] [--publish]"
      echo "  Without --publish: dry-run validation."
      echo "  With    --publish: releases/titancore-X.Y.Z-dmY-Hi/ + themeversions/vX.Y.Z-titancore/{vX.Y.Z-titancore.zip, changelog.md}"
      exit 0
      ;;
    *) VERSION="$arg" ;;
  esac
done

if [[ -z "$VERSION" ]]; then
  [[ -d "$REPO_ROOT/themeversions/current/titancore" ]] || err "themeversions/current/titancore missing. Run bin/use-version.sh or create a version folder."
  VERSION="$(grep -E '^Version:' "$REPO_ROOT/themeversions/current/titancore/style.css" | sed -E 's/.*Version:[[:space:]]*//')"
  info "No version arg -> using current theme source Version: $VERSION"
fi
[[ "$VERSION" =~ ^[0-9]+\.[0-9]+\.[0-9]+$ ]] || err "Version '$VERSION' must be semver X.Y.Z"

# ------------------------------------------------------------
# Resolve theme source for this version
# ------------------------------------------------------------
SRC="$REPO_ROOT/themeversions/v$VERSION-titancore/titancore"
[[ -d "$SRC" ]] || err "Theme source not found: themeversions/v$VERSION-titancore/titancore
Create the version folder first (see RELEASING.md step 1)."

STABLE_TAG="$(grep -E '^Stable tag:' "$SRC/readme.txt" | sed -E 's/.*Stable tag:[[:space:]]*//')"
if [[ "$STABLE_TAG" != "$VERSION" ]]; then
  err "Version mismatch: style.css=$VERSION vs readme.txt Stable tag=$STABLE_TAG. Bump both files first (see RELEASING.md)."
fi

# ------------------------------------------------------------
# Pre-flight checks (WordPress guidelines)
# ------------------------------------------------------------
info "Pre-flight checks for $VERSION"

# 1. Required files exist
for f in style.css functions.php index.php screenshot.png readme.txt; do
  [[ -f "$SRC/$f" ]] || err "Missing required file: $f"
done

# 2. style.css header has required fields
grep -q "Theme Name:" "$SRC/style.css" || err "style.css missing Theme Name"
grep -q "Text Domain: titancore" "$SRC/style.css" || err "style.css missing Text Domain: titancore"
grep -q "License:" "$SRC/style.css" || err "style.css missing License"

# 3. PHP syntax
info "PHP lint..."
find "$SRC" -name "*.php" -exec php -l {} \; 2>&1 | grep -v "No syntax errors" && err "PHP syntax errors found" || true
info "PHP lint OK"

# 4. Minified assets exist (warning only)
for pair in "assets/css/style.css:assets/css/style.min.css" "assets/css/enhancements.css:assets/css/enhancements.min.css" "assets/css/frontpage-presets.css:assets/css/frontpage-presets.min.css" "assets/js/navigation.js:assets/js/navigation.min.js"; do
  SRC_ASSET="${pair%%:*}"; MIN="${pair##*:}"
  if [[ ! -f "$SRC/$MIN" ]]; then
    echo "WARNING: Missing minified $MIN (WP loads .min versions)" >&2
  fi
done

# 5. Text domain check
if grep -rEn '(__|_e|_x)\(' "$SRC" --include="*.php" | grep -v "titancore" | grep -qE '(__|_e|_x)\('; then
  echo "WARNING: Found translation calls without 'titancore' text domain:" >&2
  grep -rEn '(__|_e|_x)\(' "$SRC" --include="*.php" | grep -v "titancore" | grep -E '(__|_e|_x)\(' | head -5 >&2
fi

if [[ "$PUBLISH" == false ]]; then
  info "Dry-run OK. Run with --publish to create release artifacts."
  info "Next: bump version if needed, update CHANGELOG.md + readme.txt, then: ./bin/package.sh $VERSION --publish"
  exit 0
fi

# Git hygiene warning (repo root, read-only check)
if ! git -C "$REPO_ROOT" diff --quiet 2>/dev/null || ! git -C "$REPO_ROOT" diff --cached --quiet 2>/dev/null; then
  echo "WARNING: git working tree is not clean. Commit or stash before publishing? (continue in 3s)" >&2
  sleep 3
fi

# ------------------------------------------------------------
# Publish: build the zip
# ------------------------------------------------------------
TIMESTAMP="$(date +%d%m%Y-%H%M)"
RELEASE_DIR_NAME="titancore-${VERSION}-${TIMESTAMP}"
RELEASE_DIR="$REPO_ROOT/releases/$RELEASE_DIR_NAME"
ZIP_NAME="titancore-${VERSION}-${TIMESTAMP}.zip"
ZIP_PATH="$RELEASE_DIR/$ZIP_NAME"
ZIP_GENERIC="$RELEASE_DIR/titancore.zip"

info "Creating release: $RELEASE_DIR_NAME"
mkdir -p "$RELEASE_DIR"

TMPDIR="$(mktemp -d)"
trap 'rm -rf "$TMPDIR"' EXIT
STAGE="$TMPDIR/titancore"
mkdir -p "$STAGE"
cp -a "$SRC"/. "$STAGE"/

# Prune junk from the staged theme
find "$STAGE" \( -name ".DS_Store" -o -name "Thumbs.db" -o -name "*.log" -o -name "*.map" -o -name ".git*" -o -name "*.tmp" \) -delete 2>/dev/null || true

# Create zips (top-level folder = titancore/)
( cd "$TMPDIR" && zip -r -q "$ZIP_PATH" "titancore" )
cp "$ZIP_PATH" "$ZIP_GENERIC"

# Checksums
( cd "$RELEASE_DIR" && sha256sum "$ZIP_NAME" titancore.zip > checksums.txt )

# ------------------------------------------------------------
# Changelog handling
# ------------------------------------------------------------
# Extract current version section from repo-root CHANGELOG.md; fallback to full file
awk -v ver="$VERSION" '
  /^## \[/ { capture = ($0 ~ "\\[" ver "\\]") }
  capture { print }
  /^## \[/ && capture && $0 !~ "\\[" ver "\\]" { exit }
' "$REPO_ROOT/CHANGELOG.md" > "$RELEASE_DIR/CHANGELOG.md" || true
if [[ ! -s "$RELEASE_DIR/CHANGELOG.md" ]]; then
  cp "$REPO_ROOT/CHANGELOG.md" "$RELEASE_DIR/CHANGELOG.md"
fi

# INFO.txt
cat > "$RELEASE_DIR/INFO.txt" <<EOF
TitanCore Release $VERSION
Built: $(date -u +"%Y-%m-%d %H:%M UTC") ($TIMESTAMP)
Zip: $ZIP_NAME (also titancore.zip)
Source version: $VERSION (style.css + readme.txt Stable tag)
WordPress: Requires at least 6.0, Tested up to 6.8, Requires PHP 8.0
Text Domain: titancore
License: GPLv2 or later

Contents: $(unzip -l "$ZIP_PATH" | tail -1)
SHA256: $(sha256sum "$ZIP_PATH" | cut -d' ' -f1)

Install: WordPress Admin > Appearance > Themes > Add New > Upload Theme > select titancore.zip
Manual: unzip into wp-content/themes/titancore/
EOF

# Canonical version-first artifacts
VDIR="$REPO_ROOT/themeversions/v$VERSION-titancore"
[[ -d "$VDIR" ]] || err "themeversions/v$VERSION-titancore exists as source but not as folder? Aborting."
cp "$ZIP_PATH" "$VDIR/v$VERSION-titancore.zip"
cp "$RELEASE_DIR/CHANGELOG.md" "$VDIR/changelog.md"

# ------------------------------------------------------------
# Verify zip structure: must have single top-level folder
# ------------------------------------------------------------
TOP_LEVEL="$(unzip -l "$ZIP_PATH" | awk '{print $4}' | grep "^titancore/" | cut -d/ -f1 | sort -u)"
if [[ "$TOP_LEVEL" != "titancore" ]]; then
  echo "WARNING: Zip top-level is '$TOP_LEVEL' expected 'titancore'. WordPress installer requires single folder." >&2
fi
FILE_COUNT="$(unzip -l "$ZIP_PATH" | grep -c "titancore/")"
if [[ "$FILE_COUNT" -lt 10 ]]; then
  echo "WARNING: Zip seems too small ($FILE_COUNT files) — check the staged theme." >&2
fi

SIZE="$(du -h "$ZIP_PATH" | cut -f1)"

info "Done."
echo "  Legacy dir   : $RELEASE_DIR"
echo "  Zip (arch)   : $ZIP_NAME ($SIZE, $FILE_COUNT files)"
echo "  Zip (generic): titancore.zip"
echo "  Canonical    : $VDIR/v$VERSION-titancore.zip + changelog.md"
echo "  Checksums    : $RELEASE_DIR/checksums.txt"
echo ""
echo "Next steps:"
echo "  1. Install-test the EXACT zip: ./bin/up.sh"
echo "     wp-env run cli wp theme install $VDIR/v$VERSION-titancore.zip --activate --allow-root"
echo "     then: ./bin/verify.sh"
echo "  2. Switch the local env: ./bin/use-version.sh $VERSION && ./bin/down.sh && ./bin/up.sh"
echo "  3. Git commit/tag are performed by the human (see RELEASING.md step 5)."
