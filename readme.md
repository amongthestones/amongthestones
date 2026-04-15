# Sinxelo

A minimal classic WordPress theme. *Sinxelo* is Galician for "simple."

An updated continuation of [Simppeli](https://foxland.fi/downloads/simppeli/) by Sami Keijonen: combined, modernized, and extended with Gutenberg block support.

---

## Why This Exists

For several years I used **Simppeli** (parent) + a customized child theme on my personal website. Over time the parent/child relationship became a liability:

- The parent was last updated in 2017
- Every template change required tracking which file "won"
- Site-specific integrations (SSP, Jetpack, year archives, taxonomies) were tangled into the child theme
- The theme had no Gutenberg block support

Rather than migrate to Full Site Editing, which I want to explore as a separate project, I took a different path: **merge everything into one standalone classic theme**, add block editor support where it makes sense (content area only), and extract site-specific code into its own plugin.

### What This Is Not

This is not an FSE theme. There is no Site Editor. No block templates. No `templates/*.html` files. Layout decisions are made in PHP, which is where I want them.

Blocks are used as **content tools**, not layout tools. The block editor is active in posts and pages. Custom blocks (callout box) live in `blocks/`. The Site Editor is not available.

---

## File Structure

```
sinxelo/
├── style.css                    # Theme header + merged CSS (parent base + child additions)
├── theme.json                   # Minimal block editor tokens: 6 colors, 3 fonts, no free-form picker
├── functions.php                # Theme setup, enqueue, blocks, shortcodes, content filters
│
├── index.php                    # Fallback template
├── single.php                   # Single post
├── page.php                     # Page
├── archive.php                  # Archive
├── search.php                   # Search results
├── 404.php                      # 404 (custom messaging)
├── comments.php                 # Comments template
├── header.php                   # Header
├── footer.php                   # Footer (custom links + motto)
├── menu-primary.php             # Primary nav
├── page-titleless.php           # Custom template: page without title
│
├── template-parts/
│   ├── content.php              # Default post (stream view)
│   ├── content-single.php       # Single post
│   ├── content-page.php         # Page
│   ├── content-search.php       # Search result item
│   └── content-none.php        # No results
│
├── inc/
│   ├── template-tags.php        # sinxelo_posted_on(), sinxelo_entry_footer(), etc.
│   ├── extras.php               # Body classes
│   ├── customizer.php           # Customizer live preview
│   ├── custom-header.php        # Custom header image support
│   └── custom-background.php   # Custom background support
│
├── blocks/
│   └── callout/                 # sinxelo/callout — InnerBlocks callout box
│       ├── block.json
│       ├── index.js
│       └── index.asset.php
│
├── assets/
│   ├── css/
│   │   └── editor-style.css     # Block editor styles (typography, colors, callout)
│   └── fonts/                   # Locally hosted Noto Sans + Noto Serif (woff2)
│       ├── noto-sans-normal-latin.woff2
│       ├── noto-sans-normal-latin-ext.woff2
│       ├── noto-sans-italic-latin.woff2
│       ├── noto-sans-italic-latin-ext.woff2
│       ├── noto-serif-normal-latin.woff2
│       ├── noto-serif-normal-latin-ext.woff2
│       ├── noto-serif-italic-latin.woff2
│       └── noto-serif-italic-latin-ext.woff2
│
└── js/
    ├── skip-link-focus-fix.js   # Accessibility
    └── customizer.js            # Customizer live preview
```

---

## Theme / Site Plugin Boundary

Site-specific code (SSP podcast integrations, Jetpack config, year archive rewrites, language taxonomy, `[ola]` shortcode) lives in a separate plugin: **[ats-site](../ats-site/)**.

**In this theme:**
- Presentation, typography, layout templates
- `sinxelo/callout` block + `[callout]` shortcode (backward compat)
- Generic shortcodes: `[searchform]`, `[tag_cloud]`, `[category_cloud]` (posts only)
- Easy anchors (heading ID injection, folded in from standalone plugin)
- Archive title cleanup filter
- Disable Twemoji

**In ats-site plugin:**
- Seriously Simple Podcasting integrations
- Jetpack related posts configuration
- Year archive URL rewrites (`/year/####/`)
- `language` taxonomy
- Sitemap filters
- `[ola]` Galician greeting shortcode + `ats/ola` block
- `[category_cloud]` with podcast CPT override

---

## Block Editor

`theme.json` locks the editor to the theme's design system:
- **Colors:** Text, Heading, Background, Border, Highlight (gold `#F4E28A`), Muted — no free-form color picker
- **Fonts:** Noto Serif, Noto Sans, Monospace — no arbitrary font choices
- **No gradients, no custom spacing controls**

Fonts are served locally from `assets/fonts/`. The OMGF plugin can be deactivated.

### sinxelo/callout block

A yellow highlight box that wraps any inner blocks. Registered in `blocks/callout/`. The classic `[callout]...[/callout]` shortcode remains active as a fallback for existing content.

---

## Credits

- **Simppeli** by [Sami Keijonen](https://foxland.fi/) — the parent theme this is based on
- **Underscores** by Automattic — the foundation Simppeli was built on
- Licensed under [GPL v2 or later](http://www.gnu.org/licenses/gpl-2.0.html)
