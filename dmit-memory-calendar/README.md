# DMIT Memory Calendar

An "on this day" history calendar for WordPress + Elementor. Admins add dated
history events from wp-admin (or approve ones visitors submit), and the
calendar matches them by **month + day only** — so a single day on the
calendar can surface entries from many different years at once, exactly like
a "this day in history" page.

## What's included

- **Custom post type** `History Events` (`dmit_history_event`) with a
  hierarchical `Event Categories` taxonomy, a required event date, an
  optional source/reference link, a featured image, and rich-text
  description.
- **Four Elementor widgets** (category: *Memory Calendar*), each with a
  matching shortcode so they also work outside Elementor:
  | Widget | Shortcode | Purpose |
  |---|---|---|
  | Memory Calendar | `[dmit_memory_calendar]` | Month grid (FullCalendar), click a day to see every year that had an event on it, toggle to a list view |
  | On This Day | `[dmit_on_this_day]` | The homepage timeline — dark minimal style, dot-on-a-line, shows everything that happened on today's date across all years |
  | History Event List | `[dmit_event_list]` | Standalone paginated/filterable list (search by title/year, filter by category) |
  | Submit History Event Form | `[dmit_submit_event_form]` | Public submission form; every submission lands as `Pending` for editor review |
- **REST API** (`/wp-json/dmit-mc/v1/...`) powering all the front-end widgets — public, read-only, published events only.
- **Moderation queue**: `History Events → All Events` flags visitor
  submissions, has a "Visitor submissions only" filter, and shows an
  admin notice with a count whenever something is waiting for review.
- **Spam protection**: an always-on honeypot field, an optional Google
  reCAPTCHA v3 toggle, and a per-IP rate limit (5 submissions/hour) —
  configurable under `History Events → Settings`.
- **i18n-ready**: every plugin-generated string goes through WordPress's
  translation functions. A Tamil translation (`languages/dmit-memory-calendar-ta.mo`)
  ships out of the box for the front-end-facing strings; the full `.pot`
  template is included for translating the rest (admin screens, etc.) with
  [Loco Translate](https://wordpress.org/plugins/loco-translate/) or Poedit.
- **FullCalendar** (MIT-licensed, v6) is bundled locally in
  `assets/lib/fullcalendar/` — no external CDN dependency.

## Installation

1. Zip the `dmit-memory-calendar/` folder (or copy it directly) into
   `wp-content/plugins/`.
2. Activate **DMIT Memory Calendar** under Plugins.
3. Go to **History Events → Add New** and start adding events — the only
   required field is the **Event date** (in the *Event Details* box on
   the right); the calendar uses its month+day to place it every year.
4. Optionally create a few **Event Categories** (History Events → Categories)
   for filtering.
5. In Elementor, drag the **Memory Calendar** widget onto your calendar
   page and the **On This Day** widget onto your homepage.
6. Under **History Events → Settings**, set a notification email and
   decide whether submissions require login, and optionally add reCAPTCHA
   v3 keys.

## How "multi-year, same day" works

Every event stores a full date (`_dmit_event_date`, `YYYY-MM-DD`). The
calendar and "On This Day" widget never query by year — they match the
`MM-DD` portion via a `REGEXP` meta query, so June 13 will show *every*
event ever entered for June 13, regardless of what year it happened. Add
an event dated `1954-06-13` and another dated `2001-06-13` and both will
appear on the June 13 cell every year, forever.

## Front-end submission flow

1. Visitor fills out the **Submit History Event Form** widget/shortcode.
2. The form posts to `admin-ajax.php` (`action=dmit_mc_submit_event`) —
   multipart, so an optional photo upload works.
3. Server checks: nonce → login requirement (if enabled) → honeypot →
   rate limit → reCAPTCHA (if enabled) → field validation.
4. On success, `wp_insert_post()` creates the event with
   `post_status = 'pending'` and `_dmit_is_guest_submission = 1`. It is
   **never** publicly visible until an editor opens it in wp-admin and
   clicks **Publish**.
5. The configured notification email gets a heads-up with a direct edit link.

## Extending / theming

- Every widget exposes an Elementor **Style** tab with colour controls
  that map to CSS custom properties (`--dmit-mc-accent`, `--dmit-mc-today`,
  `--rl-orange`, `--rl-title`), so you can restyle per-instance without
  touching CSS.
- Plugin-wide default colours live under **History Events → Settings**.
- All markup lives in `includes/class-dmit-render.php` if you want to
  fork the HTML structure; the CSS in `assets/css/` targets those exact
  class names.

## REST reference

| Route | Params | Returns |
|---|---|---|
| `GET /wp-json/dmit-mc/v1/month` | `month` (1-12) | Every event in that month, any year, grouped by day-of-month |
| `GET /wp-json/dmit-mc/v1/day` | `month`, `day` | Events for that exact month/day, any year |
| `GET /wp-json/dmit-mc/v1/on-this-day` | `month`, `day` (default: today) | Same as `/day`, sorted oldest → newest |
| `GET /wp-json/dmit-mc/v1/list` | `page`, `per_page`, `category`, `search` | Paginated list of all published events |

## Requirements

- WordPress 6.0+
- PHP 7.4+
- Elementor (free tier is enough — no Elementor Pro required) for the
  widgets; shortcodes work with just WordPress.
