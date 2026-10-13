#!/usr/bin/env bash
#
# TitanCore: PHP lint and code quality check wrapper script.
#
# Usage: ./bin/lint.sh [TARGET_DIR]
#
set -uo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT"

TARGET_DIR="${1:-themeversions/current/titancore}"

if [ ! -d "$TARGET_DIR" ]; then
	if [ -d "themeversions/current/titancore" ]; then
		TARGET_DIR="themeversions/current/titancore"
	else
		TARGET_DIR="."
	fi
fi

echo "== Running PHP syntax lint on: $TARGET_DIR =="

FAIL=0

SYNTAX_ERRORS=0
TOTAL_FILES=0

while IFS= read -r -d '' file; do
	TOTAL_FILES=$((TOTAL_FILES + 1))
	ERR_MSG="$(php -l "$file" 2>&1 | grep -v '^No syntax errors' || true)"
	if [ -n "$ERR_MSG" ]; then
		echo "ERROR in $file:"
		echo "$ERR_MSG"
		SYNTAX_ERRORS=$((SYNTAX_ERRORS + 1))
		FAIL=1
	fi
done < <(find "$TARGET_DIR" -type f -name '*.php' -print0)

if [ "$SYNTAX_ERRORS" -eq 0 ]; then
	echo "OK: $TOTAL_FILES PHP file(s) lint clean"
else
	echo "FAIL: $SYNTAX_ERRORS file(s) with PHP syntax errors"
fi

# Check for PHPCS
PHPCS_BIN=""
if command -v phpcs >/dev/null 2>&1; then
	PHPCS_BIN="$(command -v phpcs)"
elif [ -x "$ROOT/vendor/bin/phpcs" ]; then
	PHPCS_BIN="$ROOT/vendor/bin/phpcs"
fi

if [ -n "$PHPCS_BIN" ]; then
	echo ""
	echo "== Running PHPCS on: $TARGET_DIR =="
	if "$PHPCS_BIN" -p --standard=WordPress "$TARGET_DIR"; then
		echo "OK: PHPCS check passed"
	else
		echo "FAIL: PHPCS check reported issues"
		FAIL=1
	fi
else
	echo ""
	echo "NOTE: PHPCS is not installed (optional). Skipping PHPCS checks."
fi

if [ "$FAIL" -eq 0 ]; then
	echo ""
	echo "LINT PASSED"
	exit 0
else
	echo ""
	echo "LINT FAILED"
	exit 1
fi
