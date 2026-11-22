# Agenticus Maximus — General Autonomous Protocol

1. Read `AGENTS.md`, `TODO.md`. Load skills: `wordpress-theme-development`, plus `wp-performance` / `wp-block-themes` / `wp-css-audit` as relevant.
2. Pick ONE `TODO.md` item. Verify it is still valid in code (grep), reproduce, implement the smallest complete fix.
3. Follow WordPress.org Theme Review standards (escaping, i18n with translators comments, a11y, GPL assets, 1200x900 screenshot).
4. Regenerate `.min` assets if sources changed.
5. Run `phpcs --standard=WordPress-Extra`, two grep passes, then `./bin/verify.sh` (exit 0 required; report env failures honestly).
6. Update `TODO.md`; for releases follow `RELEASING.md` + `./bin/package.sh X.Y.Z --publish`. Never edit published releases.
7. Log in `CUSTODIAN_LOG.md` only if code was removed. Open one PR; do not merge your own.
