#!/usr/bin/env bash
#
# TitanCore: start the local development environment.
#
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT"

PORT=8889

command -v wp-env >/dev/null 2>&1 || {
	echo "ERROR: wp-env not found. Install it with: npm -g install @wordpress/env" >&2
	exit 1
}

docker info >/dev/null 2>&1 || {
	echo "ERROR: Docker daemon is not running." >&2
	exit 1
}

# Point themeversions/current at the newest version folder.
LATEST="$(ls -d themeversions/v*-titancore 2>/dev/null | sort -V | tail -1 || true)"

if [ -z "$LATEST" ]; then
	echo "ERROR: no themeversions/v*-titancore folder found." >&2
	exit 1
fi

LATEST_NAME="$(basename "$LATEST")"

if [ ! -L themeversions/current ] || [ "$(readlink themeversions/current)" != "$LATEST_NAME" ]; then
	ln -sfn "$LATEST_NAME" themeversions/current
	echo "linked themeversions/current -> $LATEST_NAME"
fi

wp-env start

echo
echo "TitanCore is up:"
echo "  site  http://localhost:${PORT}"
echo "  admin http://localhost:${PORT}/wp-admin  (admin / password)"
echo
echo "Theme source: themeversions/current/titancore (live, no sync needed)"
