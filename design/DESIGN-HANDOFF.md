# Dawnsellshomes — Design v2 Handoff ("ink & red" system)

Paste this whole document to your coding agent (Claude Code, etc.) inside the
`jsims692/Dawnsellshomes` Laravel repo. Three companion files ship with it —
add them at the paths below before starting. The goal is to restyle the site
from the current navy/gold/Georgia look to the ink/red/Fraunces design system,
**progressively**, using the repo's existing progressive-rewrite machinery.

## 0. Files to add first (provided alongside this doc)

| File you received | Put it at |
|---|---|
| `site-v2.css` | `public/css/site-v2.css` — the full design-v2 stylesheet, source of truth |
| `plat-bg.blade.php` | `resources/views/components/site/plat-bg.blade.php` — animated plat-map background |
| `dawn-simmons-site.html` | `design/prototype.html` — clickable prototype of all six pages; open it in a browser for pixel reference. It is a hash-routed single file (`#/sell`, `#/buy`, `#/blog`, `#/property-management`, `#/contact`). |

## 1. Mission

Reskin the **Blade-rendered** pages to design v2. Do NOT touch the verbatim
imported legacy pages (cities/neighborhoods/condos/schools and old blog posts
served straight from the `pages` table) — they carry their own styles via
`css_key`/`css_override` and must keep rendering byte-for-byte. This mirrors
how the site was rebuilt in the first place: page by page, safely.

## 2. Hard constraints — do not break these

1. **Lead pipeline.** The sitewide contact form POSTs urlencoded to `/` with
   `form-name=contact` + honeypot `bot-field` (CSRF-exempt). Keep every form's
   field names (`name,email,phone,interest,message`) and this contract intact.
2. **SEO heads come from the DB.** `x-site.layout` renders `{!! $head !!}`
   built from `pages.head_html` (with `<!--STYLE-->` marker). Never hardcode
   `<title>`/meta in views; head changes go through migrations only.
3. **PageController machinery.** Blade override lookup (`pages/{path}`),
   homepage component swaps, and the city sold-data injection stay as-is.
4. **Home-value widget contracts** (`components/home/value-widget.blade.php`):
   it expects a global `heroSearch()` on the page, a `#search` anchor to
   exist, the `window.__gmapsReady` loader, and `/home-value/nearby`. The
   `/sell` page already satisfies these — preserve that when restyling.
5. **/sold interactive map** (`components/sales/map*.blade.php`) keeps working.
6. Livewire/Alpine come from `@livewireStyles`/`@livewireScripts` in the
   layout. Alpine `x-data` usage in calculators/forms must keep functioning.
7. Sitemap/IndexNow, lead delivery, and routes are untouched.

## 3. Design system

Fonts (add to layout head; system currently uses no webfonts):

```html
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Archivo:wght@400;500;600;700;800&family=Fraunces:ital,wght@0,600;1,600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/css/site-v2.css">
```

Tokens (already in `site-v2.css`):

```css
:root{
  --ink:#0F1E2E; --ink-deep:#0B1622; --slate:#48586B; --faint:#8A99AA;
  --paper:#FFFFFF; --mist:#F2F5F9; --line:#DEE6EE;
  --red:#C8102E; --red-deep:#A50D24; --red-tint:#FBEBEE; --red-soft:#F1637C;
  --radius:18px;
  --shadow:0 14px 34px rgba(15,30,46,.10);
  --shadow-sm:0 4px 14px rgba(15,30,46,.07);
}
```

Usage rules: **Fraunces** (weight 600, italic accents in `--red-soft`/`--red`)
for display headings and the step numerals; **Archivo** for body, UI, labels,
buttons. Eyebrow labels are uppercase Archivo with letter-spacing. Plat-map
status colors: red `#C8102E` = sold, yellow `#E8B93B` = buyer-side, blue
`#3E7FBE` = for sale.

## 4. Class vocabulary — old (navy) → new (v2)

The rewritten Blade pages currently use the imported navy system's shared
classes. Replace them with the v2 classes from `site-v2.css`:

| Old (navy system) | New (v2) |
|---|---|
| `.hero` + `.breadcrumb` | `.page-hero` + `.crumb` (dark gradient hero, Fraunces h1 with `<em>` italic) |
| `.inner` | `.wrap` inside `.section` / `.section--mist` / `.section--ink` |
| `.sec-label` + `h2` | `.sec-head` > `.eyebrow` + `.h2` (+ optional `.lead`) |
| `.fp-grid` / `.fp-card` | `.cards3` / `.c-card` |
| `.search-btn` / `.outline-btn` | `.btn.btn--primary` / `.btn.btn--ghost` (`.btn--light` on dark) |
| `sl-steps` / `by-steps` | `.steps` / `.step` (auto-numbered Fraunces circles) |
| `sl-faq` / `by-faq` / `pm-faq` | `.faq` (`details`/`summary`) |
| `by-chips` | `.chips` |
| `.pm-price` | `.price-card` |
| review/quote cards | `.rev-card` (`.rev-stars`, `.rev-quote`, `.rev-name`, `.rev-role`) |
| blog cards | `.blog-grid` / `.blog-card` / `.blog-cat` / `.link-arrow` |
| contact split + form | `.contact-grid`, `.direct`/`.direct-item`/`.icon`, `.form-card` + `.form-grid`/`.field` (+ `.success` shown by adding `.done` to the card) |

Exact markup for every one of these exists in `design/prototype.html` — copy
structure from there rather than inventing new patterns.

## 5. Wire the layout (Phase 1)

In `resources/views/components/site/layout.blade.php`: add the fonts +
stylesheet links from §3 into `<head>` (after `{!! $head !!}` so the DB head
stays first), and drop the background layer in as the first thing in `<body>`:

```blade
<body>
<x-site.plat-bg />
<x-site.nav />
{{ $slot }}
...
```

`site-v2.css` already handles layering (`main,.footer{position:relative;
z-index:1}`) and makes tinted sections slightly translucent so the map shows
through. The plat component respects `prefers-reduced-motion`.

Then replace the **nav** component contents with:

```blade
<header class="header">
  <div class="wrap">
    <nav class="nav" id="nav">
      <a class="brand" href="/" aria-label="Dawn Simmons Team home">
        <span class="brand-name">DAWN SIMMONS TEAM</span>
        <span class="brand-sub">RE/MAX Suburban</span>
      </a>
      <button class="menu-btn" id="menuBtn" aria-label="Open menu" aria-expanded="false">
        <span></span><span></span><span></span>
      </button>
      <div class="nav-links" id="navLinks">
        <a href="/sell">Sell</a>
        <a href="/buy">Buy</a>
        <a href="/#neighborhoods">Neighborhoods</a>
        <a href="/blog">Blog</a>
        <a href="/property-management">Rentals</a>
        <a href="/contact">Contact</a>
        <a class="btn btn--primary" href="/sell">Free Home Valuation</a>
      </div>
    </nav>
  </div>
</header>
<script>
(function () {
  var nav = document.getElementById('nav'), btn = document.getElementById('menuBtn');
  if (!nav || !btn) return;
  btn.addEventListener('click', function () {
    var open = nav.classList.toggle('open');
    btn.setAttribute('aria-expanded', open ? 'true' : 'false');
  });
  document.querySelectorAll('#navLinks a').forEach(function (a) {
    a.addEventListener('click', function () { nav.classList.remove('open'); btn.setAttribute('aria-expanded', 'false'); });
  });
})();
</script>
```

And the **footer** component with the prototype's footer (see `design/
prototype.html` `<footer class="footer">…`), swapping its links to real URLs:
`/sell`, `/buy`, `/#search`, `/#neighborhoods`, `/#condos`, `/team`,
`/reviews`, `/blog`, `/property-management`, `/contact`, plus the existing
compliance line ("Information deemed reliable… Equal Housing Opportunity") and
the office links. Keep `components/site/text-josh.blade.php` as-is (restyle
colors to `--ink`/`--red` only).

**Caution:** the shared nav/footer also render on already-rewritten pages that
still carry navy-era DB styles. Phase 1 and Phase 2 should land together (one
deploy) so chrome and pages don't mismatch.

## 6. Restyle the Blade pages (Phase 2)

Convert each view under `resources/views/pages/` to the v2 vocabulary using
its matching prototype page as the visual spec:

1. `sell.blade.php` → prototype `#/sell`. Keep the `<x-home.value-widget />`,
   its gmaps loader `@if`, the `heroSearch()` script, the `id="search"`
   anchor, and the `<x-site.contact-form …/>` include exactly as they are —
   only the classes and page-scoped `<style>` change (most page-scoped CSS
   can be deleted once v2 classes are used; keep the `.sl-val` widget-panel
   styles, re-themed to `--ink`).
2. `buy.blade.php` → `#/buy` (steps, chips, FAQ, quotes).
3. `contact.blade.php` → `#/contact` (direct items + map iframe + form card).
4. `blog.blade.php` → `#/blog` (`.blog-grid` cards; keep the `$posts` loop
   and date/title logic).
5. `property-management.blade.php` → `#/property-management` (`.price-card`).
6. `team.blade.php`, `reviews.blade.php` → restyle with `.cards3`,
   `.rev-card`, `.member` patterns from the prototype's home Team/Reviews
   sections; keep all copy, photos, and the `$reviews` loop.
7. `seller-net-sheet.blade.php`, `mortgage-calculator.blade.php`,
   `sold.blade.php` → re-theme shells (hero → `.page-hero`, buttons, cards)
   without touching the Alpine logic or the sales map component.
8. `components/site/contact-form.blade.php` → restyle to `.form-card` +
   `.form-grid`/`.field`/`.success`; keep the Alpine submit, field names,
   honeypot, and hidden `form-name` exactly.
9. `components/cities/sold-in-city.blade.php` → re-theme its self-contained
   styles to ink/red tokens (it must stay fully self-contained because it
   injects into legacy pages).

## 7. Homepage (Phase 3 — its own PR)

Create `resources/views/pages/home.blade.php` (the override hook already maps
`/` → `pages.home`). Rebuild it from the prototype's home page (`pg-home` in
`design/prototype.html`): hero with plat corner motif + valuation handoff,
stats band, why-cards, sell/buy split, results, region tabs (`#areas` /
`#neighborhoods`), condo accordions (`#condos`), MLS search (`#search`), team,
reviews, blog teasers, video band, PM strip, contact (`#contact`). Embed
`<x-home.value-widget />` (with the gmaps loader + a `heroSearch()`
implementation that prefills the home contact form) and `<x-sales.map
:height="'480px'" :compact="true" />` directly — once this view exists, the
controller's string-swap methods simply never run for `/`. Keep every legacy
anchor id (`#neighborhoods`, `#search`, `#condos`, `#team`, `#contact`)
because imported pages and components link to them.

## 8. Acceptance checklist (run after each phase)

Contact forms on `/`, `/sell`, `/buy`, `/contact`, `/property-management`
create a `leads` row and show the success state. The `/sell` widget
autocompletes, shows nearby sales, and its CTA prefills + scrolls to the form.
`/sold` map filters and renders. Legacy pages (any `cities/*`, old blog posts)
render pixel-identical, still showing the injected sold-in-city band. Mobile
nav opens/closes; no horizontal scroll at 360px. Plat background: parcels
pulse ~every 1.5s and on scroll, is static under reduced-motion, and text
contrast stays AA on translucent sections. Lighthouse (mobile) ≥ 90
performance on `/sell`.

## 9. Working style

Small commits in the repo's imperative style ("Restyle /sell and /buy to
design v2"), one phase per PR, `php artisan migrate` unaffected (no migrations
needed for this work). When a visual call is ambiguous, the prototype wins.
