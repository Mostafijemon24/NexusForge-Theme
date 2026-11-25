=== NexusForge ===
Requires at least: 6.4
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.0.4
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Tags: block-styles, custom-background, custom-colors, custom-header, custom-logo, custom-menu, editor-style, featured-images, footer-widgets, threaded-comments, translation-ready, wide-blocks

A premium, lightweight, fluidly responsive hybrid WordPress theme with a soft-vibrant visual identity. Elementor-friendly, never Elementor-dependent.

== Description ==

NexusForge is a hybrid classic theme (PHP templates + theme.json + block support) built on a
single source of design tokens (muted teal, fresh mint, warm ivory, Manrope). It is
designed to be fast, accessible (WCAG 2.2 AA target; no formal conformance claim), and
fluidly responsive from narrow mobile to ultrawide. Elementor (Free) is supported as an
optional enhancement: every page and post renders correctly with Elementor absent or
deactivated. Elementor Pro Theme Builder header/footer/single/archive locations are
integrated at the code level but are NOT independently runtime-verified in this release.

== Installation ==

1. Upload the `nexusforge` folder to `/wp-content/themes/`, or install the ZIP via
   Appearance → Themes → Add New → Upload Theme.
2. Activate the theme through the Themes menu in WordPress.
3. Set menus under Appearance → Menus (locations: Primary, Footer).
4. Adjust design tokens under Appearance → Customize → NexusForge — Design System.

== Documentation ==

A full illustrated user guide ships with the theme. Open `docs/index.html` from the theme
folder in any web browser (it works offline and loads nothing from other servers). It
covers installation, menus, the homepage, the Design System controls, the footer, block
styles and patterns, Elementor, accessibility, translation, a developer reference and
troubleshooting, with numbered screenshots throughout.

== Frequently Asked Questions ==

= Does it require Elementor? =
No. The theme is fully functional without Elementor. If Elementor is installed it is used
only where you choose to build with it.

= Where do I change colors and fonts? =
Appearance → Customize → NexusForge — Design System. Each control maps to one design token.

== Changelog ==

= 1.0.4 =
Addresses the three items raised in the WordPress.org theme review of 1.0.3.

* Skip link (review item 1): the "Skip to content" link no longer overlaps the WordPress
  admin bar. When focused it is pinned to the top left of the viewport, offset below the
  toolbar (32px on desktop, 46px at 782px and below), with its own visible focus outline.
* Keyboard navigation — mobile menu (review item 2):
  * The mobile menu is now a proper modal drawer with a visible, labelled "Close menu"
    button. Opening it moves focus into the drawer; Tab and Shift+Tab are trapped inside
    it and wrap around; Escape, the Close button and the overlay all close it; closing
    returns focus to the menu button. `aria-expanded` / `aria-controls` are kept in sync
    and the rest of the page is made inert while the drawer is open.
  * Fixed the tinted overlay being painted on top of the menu itself: the overlay now sits
    behind the drawer and is a neutral dark scrim (no green tint).
  * Links inside the closed drawer and inside collapsed submenus can no longer receive
    keyboard focus.
* Keyboard navigation — menus and focus (review item 2):
  * Every level of the dropdown menu is reachable with Tab; each parent item has a toggle
    button whose `aria-expanded` state matches what is shown; Escape closes an open
    dropdown and returns focus to its toggle. Third-level submenus now fly out to the
    side on desktop and expand as an accordion in the mobile drawer.
  * A clearly visible 3px focus indicator is shown on all links, buttons and form fields
    (light on dark surfaces such as the footer). The theme no longer uses `outline: none`
    anywhere; hover styles are mirrored for keyboard focus.
  * The navigation walker now extends the core walker output, so `aria-current`, link
    attributes and menu filters are preserved.
* Security (review item 3): `inc/customizer-css.php` now validates every Customizer value
  again at output time (hex colours via sanitize_hex_color(); integers cast and clamped to
  a fixed range; hard-coded property names; final wp_strip_all_tags()), in addition to the
  sanitize callbacks that run before saving. Numeric settings share one range definition
  between the controls, the sanitize callback and the CSS compiler. `searchform.php` and
  `footer.php` use explicit late escaping.
* Accessibility extras found while re-testing: the search results template now has a
  level-one heading; the starter "trusted by" names use the secondary text colour
  (contrast 2.5:1 -> 6.4:1) and the process step numbers use deep teal (4.45:1 -> 7:1+).
* Documentation: added an illustrated, offline user guide at `docs/index.html` with
  numbered screenshots (see the Documentation section above). `screenshot.png` was
  retaken from the theme itself so it shows the current NexusForge name and typeface.
* Fixes found while documenting the theme:
  * A static homepage built with blocks (or a page builder) is now a full-width canvas:
    regular blocks sit in the reading column, Wide blocks in the wide container and Full
    width blocks edge to edge. Previously the content was held in a narrow column at the
    left edge. Wide alignment now also works on pages and posts.
  * Headings in footer widgets were dark on the dark footer and could not be read; they
    now use the footer column-title style.
  * The block editor canvas now loads the theme font, design tokens and block styles, so
    it matches the front end.
  * The "Call to action band" pattern button is now a white pill (it blended into the
    band), and the theme's button style is labelled "Brand Outline" so it is no longer
    listed as a second "Outline" next to the core style.
* Other: native submit buttons (e.g. the comment form) are styled as theme buttons with a
  44px target; the page-list menu fallback is limited to top-level pages; the translation
  template (`languages/nexusforge.pot`) was regenerated.

= 1.0.3 =
* Rebranding: the theme identity was changed to NexusForge. Theme name, directory slug,
  text domain (`nexusforge`), function/constant/class prefixes, script/style handles and
  CSS custom-property/class names were renamed consistently. This is a distinct theme
  identity, not an in-place upgrade of an existing install.
* New: genuine block styles — Outline and Pill buttons, Card group, Framed image, and
  Panel quote (registered with register_block_style(); styled in assets/css/blocks.css on
  both the front end and in the editor).
* New: original starter block patterns in a "NexusForge" category — Hero with call to
  action, Three feature columns, and Call to action band (register_block_pattern(); built
  from the theme palette with no external images or premium dependencies).
* New: Custom Header support — an optional, responsive full-width banner rendered beneath
  the site header when an image is set (header text disabled; theme branding is unchanged).
* New: Custom Background support — adds background-image capability; no default colour is
  declared so the Customizer "Page canvas colour" remains authoritative until a visitor
  opts into a custom background.
* Metadata: style.css/readme Tags corrected to match real features (removed
  `full-width-template` and `rtl-language-support`; added custom-background, custom-header,
  editor-style, footer-widgets, threaded-comments, wide-blocks). Theme URI, Author URI and
  the WordPress.org Contributors username are intentionally left blank pending verified
  values rather than being invented.
* Privacy / WordPress.org compliance: the Manrope web font is now bundled locally
  (variable WOFF2, SIL OFL 1.1) under `assets/fonts/` and declared via @font-face in
  `assets/css/fonts.css`. The theme no longer requests fonts from
  fonts.googleapis.com or fonts.gstatic.com, and the gstatic preconnect resource
  hint has been removed. Typography, weights (400–800) and responsive behavior are
  unchanged; the system-font fallback stack is retained.
* Theme header metadata: removed placeholder `example.com` Theme URI / Author URI;
  `Tested up to` updated to 7.1; translation template (`nexusforge.pot`) header cleaned.
* Tags cleanup: removed the invalid `hybrid-theme` tag, and removed `accessibility-ready`
  pending a formal manual WCAG 2.2 AA review (no accessibility-conformance claim is made).
  All existing accessibility features remain unchanged.
* No palette, layout, or template-logic changes from 1.0.2; the verified Elementor
  Full Width behavior is preserved.

= 1.0.2 =
* Fix: the "Elementor Full Width" page template (`elementor_header_footer`) now
  correctly keeps the theme header and footer. `nexusforge_is_elementor_canvas()`
  previously grouped it with Canvas and suppressed the chrome, but that template
  deliberately calls get_header()/get_footer(), so Full Width pages were rendering
  with no header/footer. Only the blank "Elementor Canvas" (`elementor_canvas`)
  template now suppresses chrome.
* Verified at runtime on a disposable WP 7.1.3 / PHP 8.2.29 / MySQL 8.4.0 site with
  Elementor (Free) 4.3.5: Full Width header/footer 0/0 → 1/1; Canvas stays 0/0;
  native rendering with Elementor deactivated unchanged (1/1); no PHP errors.
* No design, palette, or accessibility changes from 1.0.1.
* Elementor Pro Theme Builder compatibility remains NOT runtime-verified (awaits
  testing in a licensed environment).

= 1.0.1 =
* Accessibility (WCAG 2.2 AA) fixes verified by the Phase 9 QA harness:
  * Footer column titles and the footer legal/copyright line now use a new
    `--nexusforge-color-footer-muted` (#93a89f) token — contrast on the ink footer
    improves from 3.77:1 to 4.82:1 (AA for normal text).
  * The `.nexusforge-eyebrow` label now uses deep teal (`--nexusforge-color-brand-2`); the
    previous brand teal was 4.45:1 on mint-pale bands (below AA). Deep teal is
    ≥7:1 on ivory, white and mint-pale.
  * The no-menu primary-navigation fallback now wraps its items in a proper
    `<ul>` (valid list markup; resolves an axe `listitem` serious finding,
    WCAG 1.3.1).
* No brand palette changes. Result on a disposable WP 7.1.3 / PHP 8.2.29 /
  MySQL 8.4.0 site: Lighthouse Performance 99–100, Accessibility 100 on all
  tested pages; axe-core 0 violations; design-token contrast 0 hard failures.
* Elementor Pro Theme Builder compatibility is NOT yet runtime-verified (awaits
  testing in a licensed environment).

= 1.0.0 =
* Initial release: core hybrid theme, design system, all core templates, Customizer
  tokens, accessible navigation, and Elementor compatibility layer.

== Copyright ==

NexusForge WordPress Theme, Copyright 2026 NexusForge.
NexusForge is distributed under the terms of the GNU GPL v2 or later.

This theme, like WordPress itself, is licensed under the GPL.
Use it to make something cool, have fun, and share what you've learned.

NexusForge bundles or references the following resources:

* Manrope font (Copyright 2019 The Manrope Project Authors)
  License: SIL Open Font License, 1.1 — https://scripts.sil.org/OFL
  Source: https://github.com/sharanda-m/manrope
  Bundled locally as variable WOFF2 under `assets/fonts/` (latin + latin-ext
  subsets); the full license text is at `assets/fonts/LICENSE-Manrope.txt`.
  The theme makes no external/Google Fonts requests.

* Lucide-style inline SVG icons (hand-authored subset)
  License: ISC — https://lucide.dev/license

All other code and assets are original to the NexusForge theme and licensed under the
GNU General Public License v2 or later.

== Notes ==

* Demo/starter content and the one-click importer are delivered separately as an
  optional companion plugin — the core theme ships no demo content.
