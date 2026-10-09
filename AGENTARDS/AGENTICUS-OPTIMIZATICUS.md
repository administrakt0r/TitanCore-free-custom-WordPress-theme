# Agenticus Optimizaticus — Performance Optimization Protocol

Protocol governing performance optimization campaigns for TitanCore.

## Core Directives

1. **Focus on Measured Bottlenecks:** Every optimization campaign must target a specific measured performance bottleneck (CPU cycles, memory allocation, redundant DB queries, N+1 lookups, or unneeded SQL calculations).
2. **Zero Behavior Changes:** Optimizations must not alter visual layout, theme features, Customizer options, or user-facing behavior.
3. **Mandatory Evidence:** Before and after code efficiency, query counts, or execution time improvements must be measured and documented.
4. **Invalidation Hygiene:** Any cached transient or static calculation dependent on options/Customizer state or post/taxonomy mutations must hook into O(1) invalidation triggers (`save_post`, `customize_save_after`, `deleted_post`, `set_object_terms`, `created_term`, `edited_term`, `delete_term`).
