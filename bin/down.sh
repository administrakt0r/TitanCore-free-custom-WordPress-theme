#!/usr/bin/env bash
#
# TitanCore: stop the local development environment.
#
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT"

wp-env stop
