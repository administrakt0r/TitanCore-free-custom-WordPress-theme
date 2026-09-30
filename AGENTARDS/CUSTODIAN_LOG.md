# Custodian Log — Dead Code & Asset Removals

Log of evidence-based dead code and asset removals conducted under the Agenticus Purgatorius protocol.

| Date | Target Version | Item Removed | Category | File(s) | Justification & Verification Evidence |
| --- | --- | --- | --- | --- | --- |
| 2026-09-25 | v1.0.7 | `.size-full` & `.h-9` utility rules | CSS | `assets/css/style.css`, `assets/css/style.min.css` | Unused CSS utility rules removed in commit `ccd6181667e543cf104524a01583d05bd05a36a0` (0 references across all templates/scripts). |
| 2026-09-25 | v1.0.8 | Complete source audit pass | Audit | All 36 theme source files in `v1.0.8-titancore` | Conducted evidence-based code audit under Agenticus Purgatorius protocol. Confirmed 0 dead code/unreferenced CSS/JS/PHP assets across all templates, scripts, and configuration files in v1.0.8. |
| 2026-09-28 | v1.0.8 | `.min-h-[80px]` utility rule | CSS | `assets/css/style.css`, `assets/css/style.min.css` | Orphaned CSS utility rule removed (0 references across all theme templates/scripts; replaced by `min-h-[100px]` in `comments.php`). |
