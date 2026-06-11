# TamilNewsWire

A premium, minimalist news platform concept for the global Sri Lankan Tamil diaspora —
blue & white editorial theme, built with semantic HTML, modern CSS and GSAP animations.

## Pages

- **`index.html`** — Home: breaking-news ticker, featured story, snippet journalism grid,
  "இன்று வரலாற்றில்" (Today in History) widget, community stats, event calendar teaser,
  diaspora regions and newsletter signup.
- **`events.html`** — Community Event Calendar: interactive month calendar with a
  day-detail panel, region filters (Canada / UK / Australia / Europe / Sri Lanka),
  upcoming-events list with "verified" badges, and a moderated event-submission form.

## Stack

- Vanilla HTML/CSS/JS — no build step; open `index.html` or serve the folder statically.
- [GSAP 3 + ScrollTrigger](https://gsap.com) (vendored in `js/vendor/`) for the marquee
  ticker, hero entrance, scroll reveals, animated counters and calendar stagger.
- Google Fonts: Anek Tamil, Noto Sans Tamil, Archivo.
- Fully responsive (desktop / tablet / 390px mobile) with an off-canvas mobile drawer;
  respects `prefers-reduced-motion`.

## Run locally

```bash
npx serve .
# or
python3 -m http.server 8080
```
