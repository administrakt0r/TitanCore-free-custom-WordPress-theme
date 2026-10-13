#!/usr/bin/env bash
#
# TitanCore: switch themeversions/current to a version folder.
#
# Usage: ./bin/use-version.sh X.Y.Z
#
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT"

VERSION="${1:-}"

if [ -z "$VERSION" ]; then
	echo "Usage: ./bin/use-version.sh X.Y.Z" >&2
	exit 1
fi

TARGET="v${VERSION}-titancore"

if [ ! -d "themeversions/${TARGET}/titancore" ]; then
	echo "ERROR: themeversions/${TARGET}/titancore does not exist." >&2
	exit 1
fi

ln -sfn "$TARGET" themeversions/current
echo "themeversions/current -> $TARGET"
echo "Restart the environment if wp-env is already running: ./bin/down.sh && ./bin/up.sh"
