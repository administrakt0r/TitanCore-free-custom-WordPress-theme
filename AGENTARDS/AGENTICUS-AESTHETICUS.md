# Agenticus Aestheticus — Visual & Interaction Design Polish Protocol

> The governed protocol for UI/UX, contrast, and visual/interaction design polish tasks in TitanCore.

## Purpose

Agenticus Aestheticus provides a standardized procedure for refining UI/UX, visual hierarchy, micro-interactions, focus visibility, contrast, and responsive layout consistency within TitanCore. Tasks executed under this protocol must enhance aesthetics and accessibility while maintaining strict fidelity to the theme's zero-bloat, vanilla PHP/CSS/JS design system.

## Mandatory Rules & Requirements

1. **Scope Restriction:**
   - Limit work to a single vertical slice (e.g. navigation, post content/TOC, comments, card grid, or footer/empty-state components).
   - Work within the established visual language — do not introduce new features, external visual libraries, or Customizer settings.

2. **Source-First Modification & Asset Synchronization:**
   - Always modify original source CSS (`assets/css/*.css`) or JS (`assets/js/*.js`).
   - Never edit minified build artifacts directly.
   - Always synchronize/minity minified counterparts side-by-side (`assets/css/*.min.css`, `assets/js/*.min.js`).

3. **Accessibility & Contrast:**
   - Ensure WCAG AA contrast compliance for text, controls, focus rings, and badge/pill states.
   - Preserve or enhance keyboard focus visibility (`:focus-visible`) and tap targets (minimum 44px for interactive controls).
   - Respect `prefers-reduced-motion` for all transitions and animations.

4. **Validation & Verification:**
   - Run PHP syntax checks (`php -l`) and verification script (`./bin/package.sh [version]`).
   - Confirm layout stability and clean error logs (`debug.log`).

## Execution Checklist

- [ ] Select a single target vertical slice in the active theme source (`themeversions/vX.Y.Z-titancore/titancore/`).
- [ ] Inspect source templates and CSS for contrast, alignment, typography, micro-interactions, and focus states.
- [ ] Implement targeted visual/interaction polish in source files (`assets/css/*.css`, `assets/js/*.js`, templates).
- [ ] Regenerate/synchronize minified counterpart files (`.min.css` / `.min.js`).
- [ ] Verify PHP syntax (`php -l`) and package pre-flight checks (`./bin/package.sh`).
- [ ] Verify layout, contrast, and focus states across desktop and mobile views.
