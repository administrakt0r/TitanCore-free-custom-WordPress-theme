=== TitanCore WordPress Theme ===
Contributors: administraktor
Requires at least: 6.0
Tested up to: 6.8
Stable tag: 1.0.0
Requires PHP: 8.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Ultra-fast, clean-coded WordPress theme for blogs and magazines. Dark mode, three front-page presets, built-in SEO, and zero bloat.

== Description ==

TitanCore is a performance-first WordPress theme built for bloggers, publishers, and anyone who wants their content to load near-instantly.

**Key Features:**

* **Three front-page presets** — Modern Blog grid, News Portal (hero + trending sidebar), and Magazine layout. Switch instantly via the Customizer.
* **Dark mode** — System-aware toggle with manual override. Visitors get the mode they prefer, or you can force light-only or dark-only.
* **Built-in SEO** — Fallback meta descriptions, Open Graph, Twitter Cards, canonical URLs, breadcrumbs with JSON-LD, and Article/Organization schema. Automatically suppressed when a dedicated SEO plugin is active.
* **Performance optimised** — No jQuery dependency, no Font Awesome, no emoji scripts, conditional block-library loading, deferred navigation JS, proper `fetchpriority`/`loading`/`sizes` on images, and locally hosted Inter variable font.
* **Fully translatable** — Every user-facing string uses the `titancore` text domain.
* **Accessible** — Skip link, focus management, ARIA attributes, keyboard-navigable mobile menu, `prefers-reduced-motion` support.
* **Customizer controls** — Sticky header, colour palette (light + dark), grid pattern overlay, front-page preset, TOC toggle, post/tag limits, custom header/footer code injection with safe-mode filtering.
* **Clean code** — Hand-written PHP with proper escaping, no build step required for deployment.

== Installation ==

1. In your admin panel, go to Appearance > Themes and click the Add New button.
2. Click Upload Theme and Choose File, then select the theme's .zip file. Click Install Now.
3. Click Activate to use your new theme right away.
4. Head to Appearance > Customize to configure header, colours, and front-page layout.
5. Set up your menus at Appearance > Menus (Primary, Secondary, Footer).

== Frequently Asked Questions ==

= Does this theme support dark mode? =

Yes. Out of the box, TitanCore respects the visitor's system preference and provides a toggle button. You can also force light-only or dark-only via the Customizer.

= How do I change the front-page layout? =

Go to Appearance > Customize > Front Page Options and choose between Modern Blog, News Portal, or Magazine.

= How do I change the logo? =

Go to Appearance > Customize > Site Identity. You can also toggle between logo image and styled text title under Header Options.

= Does this theme work without an SEO plugin? =

Yes. TitanCore outputs fallback meta descriptions, Open Graph tags, canonical URLs, and structured data. When a dedicated SEO plugin (Yoast, Rank Math, SEOPress, AIOSEO) is detected, TitanCore's SEO output is automatically suppressed to avoid duplicates.

= Does this theme need jQuery? =

No. TitanCore's frontend JavaScript is vanilla ES6. jQuery remains registered for plugin compatibility but the theme itself does not load it.

== Changelog ==

= 1.0.0 =
* Initial release.
* Three front-page presets: Blog, News, Magazine.
* Dark mode with system-aware toggle.
* Built-in SEO fallbacks (meta, OG, schema, breadcrumbs).
* Performance: emoji removal, block-library conditional loading, deferred JS.
* Customizer: colours, sticky header, grid pattern, TOC, code injection.
* Accessible mobile menu with focus trapping and Escape key support.

== Credits ==

* Theme by [administraktor.com](https://administraktor.com).
* Font: [Inter](https://rsms.me/inter/) by Rasmus Andersson, licensed under the SIL Open Font License 1.1.
* Hosting partner: [WPinEU.com](https://wpineu.com) — WordPress Hosting in Europe.
* Built with utility-class CSS inspired by Tailwind UI patterns.
* Icons: Lucide (ISC License).
