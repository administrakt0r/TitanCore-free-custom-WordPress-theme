# AI Coder Guide — TitanCore Scaling Playbook

> How to ship work on TitanCore without breaking WordPress guidelines, hygiene, or future agents. Start here before any edit.

## 0. Prime directives

1. **WordPress guidelines first.** Template hierarchy, text domain, GPL, `style.css` header, `readme.txt` Stable tag, accessibility, and escaping are non-negotiable.
2. **Zero-build theme.** No `npm`, `composer`, `webpack`. Vanilla PHP + CSS + JS. Minified assets are committed side-by-side and versioned by `filemtime()`.
3. **Conventions are contracts:** `titancore_` (PHP), `tc-` (CSS), `titancore` (text domain). Never introduce a new prefix.
4. **Two verification passes + wp-env smoke** before saying done (`AGENTS.md` checklist).

## 1. Read order (5 min)

```
AGENTS.md              # env, wp-env, mandatory error checks
docs/FOLDER_HYGIENE.md # where files live
RELEASING.md           # version bump + packaging
CHANGELOG.md           # what shipped
TODO.md                # approved backlog
AGENTARDS/CUSTODIAN_LOG.md         # what was removed, don't re-add
AGENTARDS/README.md                # Agenticus prompt family (full protocols)
```

Then read the file(s) you will touch. Never edit without reading.

## 2. Workflow by task type

| Task | Use | Scope limit |
|------|-----|-------------|
| Dead code / unused CSS/JS | `AGENTARDS/AGENTICUS-PURGATORIUS.md` | One cleanup campaign per session, evidence-based, log each |
| Bug / a11y / small fix | `AGENTARDS/AGENTICUS-MAXIMUS.md` | One primary task per session, version + release on success |
| UI/UX, contrast, visual & interaction polish | `AGENTARDS/AGENTICUS-AESTHETICUS.md` | One vertical slice, established visual language only, no new features/settings |
| Performance optimization | `AGENTARDS/AGENTICUS-OPTIMIZATICUS.md` | One measured bottleneck, mandatory before/after evidence, no behavior change |
| New feature / preset / option | This guide + `RELEASING.md` | One feature, doc + changelog + version bump |
| Release | `RELEASING.md` + `bin/package.sh` | Script creates `releases/` folder |

## 3. Adding code — checklists

### PHP

- [ ] `if (!defined('ABSPATH')) exit;` in new includes? Existing files guard via `functions.php` load order — follow same.
- [ ] Escape late: `esc_html`, `esc_attr`, `esc_url`, `wp_kses_post` / `wp_kses` with `titancore_custom_code_allowed_tags`.
- [ ] i18n: `__('string','titancore')`, `esc_html__`, etc. Never hard-code user-facing English without domain.
- [ ] Prefix: `titancore_` for functions/filters, `titancore-` for handles where WP allows.
- [ ] No namespaces (global scope by design).
- [ ] Hook into `after_setup_theme` / `init` at correct priority; check the hook inventory in `AGENTARDS/AGENTICUS-PURGATORIUS.md` before adding new `add_action`/`add_filter`.

### Customizer

- [ ] Add `add_setting` + `add_control` + `add_section` in `inc/customizer.php` with sanitizer.
- [ ] If color → wire into `titancore_generate_theme_variables_css()` in `inc/enqueue.php` (grep setting ID there).
- [ ] If toggle → gate template output (`header.php`, `single.php`, `template-parts/*`, `front-page.php`) — document mapping in the `AGENTARDS/AGENTICUS-LOGICUS.md` settings map.
- [ ] Update `docs/FOLDER_HYGIENE.md` if setting affects load order.

### CSS

- [ ] Source in `assets/css/*.css`, then regenerate `.min.css` — WP loads `.min` if present.
- [ ] `tc-` prefix for new components; respect load order `style → enhancements → frontpage-presets`.
- [ ] Add `prefers-reduced-motion` guard for animations.
- [ ] Check contrast for new color tokens.

### JS

- [ ] Vanilla ES6, no jQuery. `assets/js/navigation.js` → regenerate `navigation.min.js` (`strategy: defer` in `inc/enqueue.php`).
- [ ] No blocking writes; handle `localStorage` safely.

### SEO / Schema

- [ ] `inc/seo-schema.php` detects Yoast/Rank Math/SEOPress/AIOSEO — your fallback must suppress when any is active.
- [ ] Test JSON-LD at https://validator.schema.org + view-source for duplicates.

## 4. Template hygiene

- Hierarchy files in root only — never move `archive.php`, `single.php`, etc.
- Reusable markup → `template-parts/` + `get_template_part()`.
- `front-page.php` uses separate `WP_Query` per preset — don't reuse main query; don't break pagination.
- Image sizes `titancore-card` / `titancore-hero` defined in `functions.php` — use them.

## 5. Scaling without bloat

- **Before adding a dependency**, prove you can't do it in ~20 lines of PHP/CSS/JS.
- **Before adding a Customizer option**, consider: can it be a `theme.json` token or filter?
- **Before adding a template**, consider: can it be a `template-parts/` partial?
- Future folders (`patterns/`, `blocks/`, `woocommerce/`, `inc/blocks/`) are allowed but must be documented in `docs/FOLDER_HYGIENE.md` + `README.md` before merge.

## 6. Versioning & releases

- Follow `RELEASING.md`: bump `style.css` Version + `readme.txt` Stable tag together, update `CHANGELOG.md` + `readme.txt` Changelog, run `./bin/package.sh X.Y.Z --publish`.
- Each publish creates `releases/titancore-X.Y.Z-dmY-Hi/` with `titancore.zip` + timestamped zip + `CHANGELOG.md` + `checksums.txt` + `INFO.txt`. Never edit a past release folder.
- Git tag `vX.Y.Z` after release commit.

## 7. Verification (mandatory)

After ANY edit:

1. Grep pass 1: exact name of every changed function/hook/setting/class/handle.
2. Grep pass 2: related names, aliases, setting IDs, CSS classes.
3. Run `./bin/verify.sh` (own wp-env instance in this repo, port 8889 — see `AGENTS.md`).
   Exit code 0 = all checks clean. Fix anything it reports before reporting done.

## 8. AI-specific gotchas

- Don't re-add anything listed in `AGENTARDS/CUSTODIAN_LOG.md`.
- `theme_color_mode` controls both JS load (`inc/enqueue.php`) and body class (`header.php`) — check both when touching dark mode.
- `titancore_should_keep_core_block_assets` filter — verify before modifying block CSS loading.
- `fontawesome` is blocked by default — opt-out is via `titancore_block_fontawesome_assets` filter, not by enqueueing it.
- `style.css` has no styles — never add CSS there.
- Languages: run `wp i18n make-pot` if you add strings, commit `languages/titancore.pot`.

## 9. When stuck

- Check `TODO.md` for the approved backlog — prefer those tasks.
- Open `AGENTARDS/AGENTICUS-PURGATORIUS.md` for the safe-removal proof checklist.
- Ask the human before: changing requirements (WP/PHP), adding tooling, or renaming prefixes.
