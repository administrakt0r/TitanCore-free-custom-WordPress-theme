## [1.0.4] - 2026-08-30
### Removed
- `comments.php`: dead `'class'` argument to `wp_list_comments()` (WordPress core accepts no such argument) and dead `peer-disabled:*` utility classes on the comment textarea label (no element with the required `peer` class exists anywhere). Comment item rendering re-proved with a live test comment.
- `assets/css/style.css`: three static color rules made unreachable by the always-attached dynamic inline stylesheet (`titancore_generate_theme_variables_css()` re-declares `.bg-background/95`, `.border-border/40` and `.hover:bg-primary/90` later in the cascade from Customizer values, the hover rule with `!important`): statics and their `.dark` variants removed. Head order and rule presence verified in live HTML.
- `assets/js/navigation.js`: `[data-lucide]` selector fragment removed from the menu icon `querySelectorAll` (no markup emits that attribute).

