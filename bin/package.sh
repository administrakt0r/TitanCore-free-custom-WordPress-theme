#!/usr/bin/env bash
# TitanCore Release Packager
# Usage: ./bin/package.sh [version] [--publish]
#   version format: X.Y.Z (semver). If omitted, uses Version from style.css.
#   --publish  -> creates releases/titancore-{version}-{dmY-Hi}/ with zip + changelog
#   without --publish -> dry-run / verify only
#
# Naming spec: titancore-{version}-{dmY}-{Hi}.zip  e.g. titancore-1.1.0-25082026-1430.zip
# WP guidelines: zip must contain a single top-level folder `titancore/`
#              (installable via Appearance > Themes > Upload).
set -euo pipefail

THEME_ROOT="$(cd "$(dirname "$0")/.." && pwd)"
THEME_SLUG="titancore"

# ------------------------------------------------------------
# Helpers
# ------------------------------------------------------------
err() { echo "ERROR: $*" >&2; exit 1; }
info() { echo "==> $*"; }

get_version_from_style() {
  grep -E "^Version:" "$THEME_ROOT/style.css" | sed -E 's/.*Version:[[:space:]]*//'
}

get_stable_tag() {
  grep -E "^Stable tag:" "$THEME_ROOT/readme.txt" | sed -E 's/.*Stable tag:[[:space:]]*//'
}

validate_semver() {
  [[ "$1" =~ ^[0-9]+\.[0-9]+\.[0-9]+$ ]] || err "Version '$1' must be semver X.Y.Z"
}

check_git_clean() {
  if ! git -C "$THEME_ROOT" diff --quiet || ! git -C "$THEME_ROOT" diff --cached --quiet; then
    echo "WARNING: git working tree is not clean. Commit or stash before publishing? (continue in 3s)" >&2
    sleep 3
  fi
}

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
      echo "  With    --publish: creates versioned release folder + zip."
      exit 0
      ;;
    *) VERSION="$arg" ;;
  esac
done

if [[ -z "$VERSION" ]]; then
  VERSION="$(get_version_from_style)"
  info "No version arg -> using style.css Version: $VERSION"
fi
validate_semver "$VERSION"

STABLE_TAG="$(get_stable_tag)"
if [[ "$STABLE_TAG" != "$VERSION" ]]; then
  err "Version mismatch: style.css=$VERSION vs readme.txt Stable tag=$STABLE_TAG. Bump both files first (see RELEASING.md)."
fi

# ------------------------------------------------------------
# Pre-flight checks (WordPress guidelines)
# ------------------------------------------------------------
info "Pre-flight checks for $VERSION"

# 1. Required files exist
for f in style.css functions.php index.php screenshot.png readme.txt; do
  [[ -f "$THEME_ROOT/$f" ]] || err "Missing required file: $f"
done

# 2. style.css header has required fields
grep -q "Theme Name:" "$THEME_ROOT/style.css" || err "style.css missing Theme Name"
grep -q "Text Domain: titancore" "$THEME_ROOT/style.css" || err "style.css missing Text Domain: titancore"
grep -q "License:" "$THEME_ROOT/style.css" || err "style.css missing License"

# 3. PHP syntax
info "PHP lint..."
find "$THEME_ROOT" -name "*.php" ! -path "*/.wp-env/*" ! -path "*/releases/*" ! -path "*/vendor/*" -exec php -l {} \; 2>&1 | grep -v "No syntax errors" && err "PHP syntax errors found" || true
info "PHP lint OK"

# 4. Minified assets exist and are newer than sources (warning only)
for pair in "assets/css/style.css:assets/css/style.min.css" "assets/css/enhancements.css:assets/css/enhancements.min.css" "assets/css/frontpage-presets.css:assets/css/frontpage-presets.min.css" "assets/js/navigation.js:assets/js/navigation.min.js"; do
  SRC="${pair%%:*}"; MIN="${pair##*:}"
  if [[ ! -f "$THEME_ROOT/$MIN" ]]; then
    echo "WARNING: Missing minified $MIN (WP loads .min versions)" >&2
  fi
done

# 5. Text domain check
if grep -r "__(" "$THEME_ROOT" --include="*.php" | grep -v "titancore" | grep -q "__("; then
  echo "WARNING: Found translation calls without 'titancore' text domain" >&2
fi

if [[ "$PUBLISH" == false ]]; then
  info "Dry-run OK. Run with --publish to create release artifacts."
  info "Next: bump version if needed, update CHANGELOG.md + readme.txt, then: ./bin/package.sh $VERSION --publish"
  exit 0
fi

# ------------------------------------------------------------
# Publish: create versioned folder
# ------------------------------------------------------------
TIMESTAMP="$(date +%d%m%Y-%H%M)"
RELEASE_DIR_NAME="titancore-${VERSION}-${TIMESTAMP}"
RELEASE_DIR="$THEME_ROOT/releases/$RELEASE_DIR_NAME"
ZIP_NAME="titancore-${VERSION}-${TIMESTAMP}.zip"
ZIP_PATH="$RELEASE_DIR/$ZIP_NAME"
ZIP_GENERIC="$RELEASE_DIR/titancore.zip"

info "Creating release: $RELEASE_DIR_NAME"
mkdir -p "$RELEASE_DIR"

# ------------------------------------------------------------
# Build exclude list (WordPress.org + hygiene)
# ------------------------------------------------------------
# Files NOT shipped in the installable zip:
EXCLUDES=(
  ".git"
  ".gitignore"
  ".gitattributes"
  ".wp-env"
  ".omx"
  ".codex"
  "node_modules"
  "releases"
  "bin"
  "docs"
  "TODO.md"
  "CUSTODIAN.md"
  "CUSTODIAN_LOG.md"
  "AGENTS.md"
  "AGENTPROMPT.md"
  "start-guide.md"
  ".DS_Store"
  "Thumbs.db"
  "*.log"
  "*.map"
)

# Build zip: must contain a single top-level `titancore/` folder inside
info "Building zip..."
TMPDIR="$(mktemp -d)"
trap 'rm -rf "$TMPDIR"' EXIT
STAGE="$TMPDIR/titancore"
mkdir -p "$STAGE"

# Copy with rsync-style excludes using bash + cp + find logic
# Use rsync if available, else fallback to tar
if command -v rsync >/dev/null 2>&1; then
  RSYNC_EXCLUDES=()
  for e in "${EXCLUDES[@]}"; do RSYNC_EXCLUDES+=(--exclude="$e"); done
  rsync -a "${RSYNC_EXCLUDES[@]}" "$THEME_ROOT"/ "$STAGE"/
else
  # fallback: copy all then prune
  cp -a "$THEME_ROOT"/. "$STAGE"/
  for e in "${EXCLUDES[@]}"; do
    rm -rf "$STAGE/$e" 2>/dev/null || true
  done
fi

# Ensure no leftover release artifacts inside stage
rm -rf "$STAGE/releases" "$STAGE/bin" 2>/dev/null || true

# Create zip with top-level folder
( cd "$TMPDIR" && zip -r -q "$ZIP_PATH" "titancore" )
cp "$ZIP_PATH" "$ZIP_GENERIC"

# Checksums
( cd "$RELEASE_DIR" && sha256sum "$ZIP_NAME" titancore.zip > checksums.txt )

# ------------------------------------------------------------
# Changelog handling
# ------------------------------------------------------------
# Extract current version section from CHANGELOG.md if exists, else from readme.txt
if [[ -f "$THEME_ROOT/CHANGELOG.md" ]]; then
  # Try to extract ## [VERSION] section; fallback to full file tail
  awk -v ver="$VERSION" '
    /^## \[/ { capture = ($0 ~ "\\[" ver "\\]") }
    capture { print }
    /^## \[/ && capture && $0 !~ "\\[" ver "\\]" { exit }
  ' "$THEME_ROOT/CHANGELOG.md" > "$RELEASE_DIR/CHANGELOG.md" || true
  if [[ ! -s "$RELEASE_DIR/CHANGELOG.md" ]]; then
    cp "$THEME_ROOT/CHANGELOG.md" "$RELEASE_DIR/CHANGELOG.md"
  fi
else
  # Fallback: pull changelog from readme.txt
  awk '/== Changelog ==/,/== Credits ==/' "$THEME_ROOT/readme.txt" > "$RELEASE_DIR/CHANGELOG.md" || echo "# Changelog $VERSION" > "$RELEASE_DIR/CHANGELOG.md"
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

# Verify zip structure: must have single top-level folder
TOP_LEVEL="$(unzip -l "$ZIP_PATH" | awk '{print $4}' | grep "^titancore/" | cut -d/ -f1 | sort -u)"
if [[ "$TOP_LEVEL" != "titancore" ]]; then
  echo "WARNING: Zip top-level is '$TOP_LEVEL' expected 'titancore'. WordPress installer requires single folder." >&2
fi
FILE_COUNT="$(unzip -l "$ZIP_PATH" | grep -c "titancore/")"
if [[ "$FILE_COUNT" -lt 10 ]]; then
  echo "WARNING: Zip seems too small ($FILE_COUNT files) — check EXCLUDES." >&2
fi

# Size report
SIZE="$(du -h "$ZIP_PATH" | cut -f1)"
COUNT="$(unzip -l "$ZIP_PATH" | grep -c "titancore/")"

info "Done."
echo "  Release dir : $RELEASE_DIR"
echo "  Zip (arch)  : $ZIP_NAME ($SIZE, $COUNT files)"
echo "  Zip (generic): titancore.zip"
echo "  Changelog   : CHANGELOG.md"
echo "  Checksums   : checksums.txt"
echo "  Info        : INFO.txt"
echo ""
echo "Next steps:"
echo "  1. Test: wp-env or local WP -> upload $ZIP_GENERIC"
echo "  2. Git tag: git tag v$VERSION && git push origin v$VERSION"
echo "  3. Commit releases/ if you want history (or keep gitignored)."
