# Agenticus Purgatorius — Dead Code & Asset Removal Protocol

> The governed protocol for evidence-based dead code, unused CSS/JS rules, and orphaned asset removal campaigns in TitanCore.

## Purpose

Agenticus Purgatorius provides a standardized, evidence-based procedure for identifying and removing dead code, unused CSS selectors or utility rules, dead JavaScript functions/variables, unused PHP helper functions, and orphaned template files. Every removal under this protocol must be backed by verifiable proof of zero references across all theme source files and documented in `AGENTARDS/CUSTODIAN_LOG.md`.

## Mandatory Rules & Requirements

1. **Proof of Zero References:**
   - Before removing any CSS class, utility rule, JS function/variable, PHP function, or template file, run an exhaustive grep search across all files in the active theme source directory (`themeversions/vX.Y.Z-titancore/titancore/`).
   - Every target item must have strictly **0 references** outside its own definition.

2. **Source-First Modification & Asset Synchronization:**
   - Always edit original source files in `assets/css/` and `assets/js/`.
   - Never edit minified build artifacts (`.min.css` / `.min.js`) directly without updating the unminified source.
   - When source CSS or JS files are modified, regenerate or synchronize the minified counterpart side-by-side (`assets/css/*.min.css` and `assets/js/*.min.js`).

3. **Validation & Verification:**
   - Execute PHP syntax checks (`php -l`) across all PHP templates and includes.
   - Run verification via `./bin/verify.sh` when a local wp-env instance is active.
   - Confirm HTTP responses and clean error logs (`debug.log`).

4. **Custodian Logging:**
   - Append one row per removal or audit pass to `AGENTARDS/CUSTODIAN_LOG.md`.
   - The log entry must include:
     - **Date:** ISO format (YYYY-MM-DD)
     - **Target Version:** e.g. `v1.0.8`
     - **Item Removed:** Specific CSS class, function name, or audit status
     - **Category:** `CSS`, `JS`, `PHP`, or `Audit`
     - **File(s):** Affected file paths
     - **Justification & Verification Evidence:** Commit hash or detailed proof of 0 references across source templates.

## Execution Checklist

- [ ] Audit target theme version source files (`themeversions/vX.Y.Z-titancore/titancore/`).
- [ ] Run grep verification passes for each candidate item across all PHP, CSS, JS, and config files.
- [ ] Remove confirmed dead code from source files.
- [ ] Synchronize minified counterpart files (`.min.css` / `.min.js`).
- [ ] Verify PHP syntax (`php -l`) and run `./bin/verify.sh` if environment is active.
- [ ] Log entry in `AGENTARDS/CUSTODIAN_LOG.md`.
