#!/usr/bin/env bash
#
# TitanCore: prepare the WordPress MCP endpoint for AI agents.
#
# Installs the WordPress mcp-adapter plugin into wp-env, creates an
# application password for the admin user and stores it in .mcp/ (git-ignored).
#
# After running this, restart opencode so the WordPress MCP server picks up
# the credentials.
#
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT"

PORT=8889
USER_LOGIN="admin"
PASS_FILE=".mcp/wp-app-password"

# mcp-adapter is distributed via GitHub releases (not on WordPress.org).
MCP_ADAPTER_URL="https://github.com/WordPress/mcp-adapter/releases/download/v0.6.1/mcp-adapter.zip"

# ---------------------------------------------------------------
# Make sure the environment is running
# ---------------------------------------------------------------
if ! curl -s -o /dev/null "http://localhost:${PORT}/" 2>/dev/null; then
	echo "Environment not reachable on port ${PORT}; starting it..."
	./bin/up.sh
fi

# ---------------------------------------------------------------
# Install + activate the MCP adapter plugin
# ---------------------------------------------------------------
if wp-env run cli wp plugin is-installed mcp-adapter --allow-root >/dev/null 2>&1; then
	echo "mcp-adapter already installed."
else
	echo "Installing mcp-adapter from ${MCP_ADAPTER_URL}..."
	wp-env run cli wp plugin install "$MCP_ADAPTER_URL" --activate --allow-root
fi

wp-env run cli wp plugin activate mcp-adapter --allow-root >/dev/null 2>&1 || true

# ---------------------------------------------------------------
# Application password for MCP
# ---------------------------------------------------------------
mkdir -p .mcp

if [ -f "$PASS_FILE" ] && [ -s "$PASS_FILE" ]; then
	echo "Application password already stored at ${PASS_FILE}; leaving it in place."
else
	echo "Creating application password for '${USER_LOGIN}'..."
	APP_PASSWORD="$(wp-env run cli wp user application-password create "$USER_LOGIN" titancore-mcp --porcelain --allow-root | tr -d '\r\n' | sed 's/^[[:space:]]*//;s/[[:space:]]*$//')"

	if [ -z "$APP_PASSWORD" ]; then
		echo "ERROR: could not create an application password." >&2
		exit 1
	fi

	printf '%s' "$APP_PASSWORD" > "$PASS_FILE"
	chmod 600 "$PASS_FILE"
	echo "Stored application password in ${PASS_FILE} (chmod 600)."
fi

# ---------------------------------------------------------------
# Report
# ---------------------------------------------------------------
MCP_URL="http://localhost:${PORT}/wp-json/mcp/mcp-adapter-default-server"
STATUS="$(curl -s -o /dev/null -w '%{http_code}' "$MCP_URL" || echo 000)"

echo
echo "MCP endpoint: ${MCP_URL} (HTTP ${STATUS}; 401/403 without auth is expected)"
echo "Credentials : ${PASS_FILE} (user: ${USER_LOGIN})"
echo
echo "opencode.json wires this endpoint through @automattic/mcp-wordpress-remote."
echo "Restart opencode to load the WordPress and Playwright MCP servers."
