# Shoe Distribution System

Wholesale shoe distribution management — products, customers & suppliers, wholesale invoicing,
payment tracking and reports.

**Developed by EdzStudio.**

- Laravel 10 · PHP 8.1+ · MySQL 5.7+/MariaDB 10.3+
- No Node/npm build step: plain CSS & JS in `public/`
- Light theme, blue brand, responsive (desktop, tablet, phone)

## Features

| Module | What it does |
| --- | --- |
| **Login** | Email + password, "Remember me", brute-force throttling, inactive accounts blocked |
| **Business settings** | Name, logo, tagline, address, phones, email, website, registration & tax numbers, currency, invoice prefix, default credit days *(admin)* |
| **Products** | Code, name, description, colour & size (optional), cost, price, active flag. Search & filter. No stock tracking yet |
| **Customers & Suppliers** | Optional code (printed as Customer Code). One page with tabs (All / Customers / Suppliers), search, profile page with balance, invoices and payment history |
| **Wholesale invoicing** | Pick a customer and (optionally) the supplier fulfilling the goods, add products by code/name, **override the price per line**, per-line discount (Rs.), extra discount, internal notes. A4 portrait "Sales Invoice" print / PDF with item code, qty, unit price, discount, total, package and signature lines |
| **Payments – customers** | On an invoice: enter amount, method (cash / card / cheque / bank transfer) with reference, bank & cheque date. Partial payments supported; full payment marks the invoice **Paid** |
| **Payments – suppliers** | "Supplier Payments" tab and a **Pay** button on every supplier. Reduces what you owe |
| **Reports** | Sales (date range, profit, top products), Receivables, Payables, Dues (overdue ageing 1-30 / 31-60 / 61-90 / 90+) — all printable |
| **System users** | Admin / Staff roles, activate/deactivate *(admin)* |

### How the money flows

- **Receivable** (customer owes you) = invoice total − payments received on that invoice.
- **Payable** (you owe supplier) = cost of goods on invoices that name the supplier − supplier payments.
- Invoice status is calculated automatically: Unpaid → Partially Paid → Paid. **Overdue** = unpaid past due date.
- Invoices can be edited/deleted only until the first payment is recorded (protects your books).

### Roles

| | Staff | Admin |
| --- | :-: | :-: |
| Products, contacts, invoices, record payments, reports | ✓ | ✓ |
| Delete products / contacts / invoices / payments | | ✓ |
| Business settings, system users | | ✓ |

## Installation

Requirements: PHP 8.1+ with `pdo_mysql, mbstring, openssl, tokenizer, xml, ctype, fileinfo, gd`, Composer 2, MySQL.

```bash
git clone <repo> shoe-distribution && cd shoe-distribution
composer install --no-dev --optimize-autoloader
cp .env.example .env
php artisan key:generate
```

Edit `.env`: `APP_URL`, `DB_*`, `ADMIN_EMAIL`, `ADMIN_PASSWORD` (leave the password empty to have a
strong one generated and printed once). Then:

```bash
php artisan migrate --force
php artisan db:seed --force          # business profile + first administrator
php artisan storage:link             # needed for logo uploads
php artisan config:cache && php artisan route:cache && php artisan view:cache
```

Optional sample products & contacts: `php artisan db:seed --class=DemoSeeder`

**Point the web server's document root at the `public/` folder.**
On cPanel/shared hosting where that is not possible, the included root `.htaccess` forwards requests
to `public/` and blocks access to `.env`, `vendor/`, `storage/` etc.

`storage/` and `bootstrap/cache/` must be writable by the web server.

## Security

- CSRF protection on every form; session ID regenerated on login; sessions stored in DB & encrypted
- Login rate limiting (5/min per email+IP, 20/min per IP), generic error messages (no account enumeration)
- Deactivated users are signed out on their next request; their sessions are deleted
- Passwords hashed with bcrypt; minimum 8 chars with letters & numbers
- All input validated with Form Requests; mass-assignment locked down (`role`, totals, status are never fillable)
- All money calculated server-side in integer cents; client-sent totals are ignored; overpayment blocked with row locks
- Invoice numbers issued under a row lock (no duplicates under concurrency)
- Output escaped everywhere (Blade `{{ }}`); JSON embedded with HTML-safe encoding; JS never uses `innerHTML`
- Logo uploads: PNG/JPG/WEBP only (no SVG), size & dimension limits, random file names
- Headers: CSP (`script-src 'self'`, no inline scripts), `X-Frame-Options: DENY`, `nosniff`,
  `Referrer-Policy`, `Permissions-Policy`, HSTS on HTTPS, `no-store` on authenticated pages
- Admin-only routes guarded by a `can:admin` gate

**Production checklist:** `APP_ENV=production`, `APP_DEBUG=false`, HTTPS with `SESSION_SECURE_COOKIE=true`,
strong DB password, run `composer audit` regularly.

> Note: Laravel 10 is the last Laravel release that supports PHP 8.1 and no longer receives security fixes.
> The open advisories (`composer audit`) concern the debug error page (debug is off in production),
> temporary signed URLs (not used) and the default `email` rule (this app uses `email:filter`, which rejects
> line breaks). When the server moves to PHP 8.2+, upgrade to a supported Laravel release.

## Troubleshooting

**First step for any problem on the server:** run

```bash
php artisan app:check
```

It checks the PHP version & extensions, `.env`, the database connection, missing tables/migrations,
folder permissions, the storage link and the cache, and tells you how to fix each failed item.

**"Something went wrong… error code XXXXXXXX"** — the full technical details are written to
`storage/logs/laravel-YYYY-MM-DD.log`. Search that file for the code.

**Page shows but has no styles / images are broken**
The CSS, JS and images are plain files in `public/` — **Node/npm is not needed**. If they don't load,
the links are pointing to the wrong address. Open the page source and check the `app.css` link:

- It starts with `https://` but the site is `http://` → set `FORCE_HTTPS=false` (default).
- The folder is missing (e.g. site is at `http://localhost/DMIT/` but links go to `http://localhost/css/...`)
  → point the web server / virtual host at `public/`, or run `php artisan serve` and open
  `http://localhost:8000`, or set `ASSET_URL=http://localhost/DMIT/public`.
- After changing `.env` on a server where you ran `config:cache`, run `php artisan config:clear`.

**"Page expired" (419) when logging in** — the address in the browser must match `APP_URL`, and leave
`SESSION_SECURE_COOKIE` empty unless you need to force it.

## Development

```bash
cp .env.example .env   # set APP_ENV=local, APP_DEBUG=true, DB settings
php artisan migrate --seed
php artisan serve
php artisan test       # feature + unit tests (uses in-memory SQLite)
vendor/bin/pint        # code style
```

### Project structure

```
app/
  Enums/            ContactType, InvoiceStatus, PaymentMethod, UserRole
  Http/Controllers/ thin controllers, one per module
  Http/Requests/    validation & authorisation for every form
  Http/Middleware/  SecurityHeaders, EnsureUserIsActive
  Models/           Eloquent models
  Services/         InvoiceService, InvoiceNumberGenerator, PaymentService, ReportService (business logic)
  Support/          Money (cents maths) + money() helper
resources/views/
  components/       reusable UI: card, stat, badge, icon, form fields, pagination, ...
  layouts/ partials/ shell, sidebar, head
  <module>/         pages per module
public/css/app.css  UI styles · public/css/print.css A4 invoice · public/js/app.js behaviour
```

Ideas for later: stock/inventory per product & size, purchase orders, returns/credit notes,
customer statements, SMS/email reminders for dues, multi-branch.
