#!/usr/bin/env bash
#
# TitanCore: mandatory verification after any theme change.
#
# Runs: PHP lint, theme activation, HTTP smoke tests and a WP_DEBUG log check.
# Exit 0 means clean.
#
set -uo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT"

PORT=8889
SLUG="titancore"
THEME_DIR="themeversions/current/${SLUG}"

FAIL=0

step() { printf '\n== %s\n' "$1"; }
fail() { echo "FAIL: $*"; FAIL=1; }

if [ ! -d "$THEME_DIR" ]; then
	echo "ERROR: $THEME_DIR not found. Run ./bin/up.sh first." >&2
	exit 1
fi

# ---------------------------------------------------------------
step "1/6 PHP syntax"
# ---------------------------------------------------------------
SYNTAX_OUT="$(find "$THEME_DIR" -name '*.php' -print0 | xargs -0 -n1 php -l 2>&1 | grep -v '^No syntax errors' || true)"

if [ -n "$SYNTAX_OUT" ]; then
	echo "$SYNTAX_OUT"
	fail "PHP syntax errors found"
else
	echo "OK: all PHP files lint clean"
fi

# ---------------------------------------------------------------
step "2/6 Activate theme"
# ---------------------------------------------------------------
if wp-env run cli wp theme activate "$SLUG" --allow-root >/dev/null 2>&1; then
	echo "OK: $SLUG active"
else
	fail "could not activate $SLUG (is wp-env running?)"
fi

# ---------------------------------------------------------------
step "3/6 Clear debug.log"
# ---------------------------------------------------------------
wp-env run cli wp eval 'file_put_contents( WP_CONTENT_DIR . "/debug.log", "" );' --allow-root >/dev/null 2>&1 \
	&& echo "OK: debug.log cleared" \
	|| fail "could not clear debug.log"

# ---------------------------------------------------------------
step "4/6 HTTP smoke tests"
# ---------------------------------------------------------------
check_url() {
	local path="$1"
	local expect="$2"
	local code

	code="$(curl -s -o /dev/null -w '%{http_code}' "http://localhost:${PORT}${path}" || echo 000)"

	if [ "$code" = "$expect" ]; then
		echo "OK: $path -> $code"
	else
		fail "$path -> $code (expected $expect)"
	fi
}

check_url "/" 200
check_url "/wp-admin/customize.php" 302
check_url "/wp-admin/widgets.php" 302

# ---------------------------------------------------------------
step "5/6 Version sync"
# ---------------------------------------------------------------
CSS_VERSION="$(grep -m1 '^Version:' "$THEME_DIR/style.css" | sed 's/^Version:[[:space:]]*//' | tr -d '\r')"
README_VERSION="$(grep -m1 '^Stable tag:' "$THEME_DIR/readme.txt" | sed 's/^Stable tag:[[:space:]]*//' | tr -d '\r')"

if [ "$CSS_VERSION" = "$README_VERSION" ]; then
	echo "OK: version $CSS_VERSION (style.css = readme.txt)"
else
	fail "version mismatch: style.css=$CSS_VERSION readme.txt=$README_VERSION"
fi

# ---------------------------------------------------------------
step "6/6 debug.log empty"
# ---------------------------------------------------------------
LOG_CONTENT="$(wp-env run cli wp eval 'echo file_get_contents( WP_CONTENT_DIR . "/debug.log" );' --allow-root 2>/dev/null | tr -d '[:space:]' || true)"

if [ -z "$LOG_CONTENT" ]; then
	echo "OK: no PHP notices, warnings or errors"
else
	echo "debug.log contents:"
	wp-env run cli wp eval 'echo file_get_contents( WP_CONTENT_DIR . "/debug.log" );' --allow-root 2>/dev/null | tail -n 40
	fail "debug.log is not empty"
fi

# ---------------------------------------------------------------
printf '\n'
if [ "$FAIL" -eq 0 ]; then
	echo "VERIFY PASSED"
	exit 0
fi

echo "VERIFY FAILED"
exit 1
