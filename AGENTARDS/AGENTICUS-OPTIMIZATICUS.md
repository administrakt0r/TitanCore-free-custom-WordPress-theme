# Agenticus Optimizaticus — Performance Optimization Protocol

> The governed protocol for evidence-based performance optimization campaigns in TitanCore.

## Purpose

Agenticus Optimizaticus provides a standardized, evidence-based procedure for identifying and optimizing performance bottlenecks in TitanCore, including CPU cycles, DB queries, N+1 lookups, SQL calculations, and dynamic CSS/data caching. Every optimization must maintain 100% visual and functional parity while providing measurable before/after performance evidence.

## Mandatory Rules & Requirements

1. **Focus on Measured Bottlenecks:**
   - Every performance optimization campaign must target a specific measured performance bottleneck (e.g. redundant SQL queries, missing `no_found_rows => true` on non-paginated queries, unneeded `fields => 'all'` vs `fields => 'ids'`, or recalculating dynamic CSS rules or categories on every request).

2. **Zero Behavior Changes:**
   - Optimizations must not alter visual layout, theme features, Customizer settings, or user-facing behavior.

3. **Invalidation Hygiene:**
   - Any cached transient or static calculation dependent on options, Customizer state, or post/taxonomy mutations must hook into O(1) invalidation triggers (`save_post`, `customize_save_after`, `deleted_post`, `set_object_terms`, `created_term`, `edited_term`, `delete_term`).

4. **Validation & Verification:**
   - Execute PHP syntax checks (`php -l`) across all PHP templates and includes.
   - Execute `./bin/package.sh [version]` dry run verification.
