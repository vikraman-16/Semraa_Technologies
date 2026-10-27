# Semraa Technologies website

A multi-page PHP/MySQL website using HTML5, custom CSS and vanilla JavaScript. No Composer, CSS framework or JavaScript framework is required. All source files are included; production pages reference the included minified JavaScript bundles.

## What is included

- Home, About, Services, Products, Portfolio concepts, Blog, Contact, Book a Demo, confirmation, Privacy, Terms and a custom 404.
- Six service detail pages with over 400 words each, benefits, process and FAQs.
- Six distinct location pages and five starter articles.
- Data-driven demo fields, a three-step interface, server validation and a JavaScript-free route.
- PDO persistence, CSRF protection, honeypot, session throttling, escaped output, CSP and secure session settings.
- SMTP integration ready for PHPMailer, optional PHP mail fallback, internal and visitor demo emails, failure logging and delivery statuses.
- Optional password-protected demo administration, filters, status changes, pagination and CSV export.
- Metadata, structured data, canonical links, breadcrumbs, sitemap and robots responses.
- Supplied original logos, a compact WebP header derivative, 16/32/180/512 PNG icons and Apple touch icon.

## Before going live

The site is built, but production operation requires your real domain, contact information, database and email service. Defaults intentionally contain no real credentials. Local mode prevents indexing. Do not switch to production until these steps are complete.

1. Replace the company address, phone, email, postcode, social links, opening hours and optional coordinates in `includes/config.php`.
2. Set `APP_URL` to your final HTTPS origin (include a subdirectory if applicable), and `APP_ENV=production`.
3. Import `database/schema.sql`, configure the database credentials and test both forms.
4. Configure real recipient and sender addresses plus SMTP; verify both notification and visitor emails.
5. Enable the canonical HTTPS/non-www rules in `.htaccess` with your actual hostname.
6. Review the privacy/terms text, real retention period, product descriptions and business details before publishing.

The provided logo says “Semraa Technology”; the configured business name follows your brief, “Semraa Technologies”. Confirm your legal/trading name and use it consistently in business listings.

No employee identities, customer testimonials, ratings, client results or certification claims have been invented. `data/team.php` contains capability groups. `data/testimonials.php` is empty until consented reviews are supplied. Portfolio entries and product dashboards are explicitly illustrative. Replace these with approved material when available. LocalBusiness/ProfessionalService address schema is withheld until complete contact details are configured; Organization schema is present.

## Local setup: XAMPP or WAMP

1. Use PHP 8.2 or later with PDO MySQL, sessions, JSON and OpenSSL enabled. GD is optional and only needed to regenerate image derivatives. Use a supported PHP release for production.
2. Copy this `semraa-website` directory into XAMPP's `htdocs` or WAMP's `www` directory.
3. Start Apache and MySQL/MariaDB.
4. Enable Apache `mod_rewrite`; allow this directory's `.htaccess` with `AllowOverride All`. On hosts that prohibit `Options`, ask the host to set `Indexes` off and `MultiViews` off, then remove only the unsupported `Options` directive.
5. Import the database as described below.
6. The production default is `https://semraa.in`. For local Apache development, explicitly set `APP_URL=http://localhost/semraa-website` (or your actual local path) and `APP_ENV=local`.
7. Visit `http://localhost/semraa-website/`. Do not open PHP files directly from the filesystem.

Environment variables are read using `getenv()`. There is no `.env` loader. Configure variables through Apache/hosting, or edit the corresponding defaults in `includes/config.php`. Keep secrets out of Git and restrict access to server configuration. Never use a database root account for the production application.

For an alternative local PHP preview, run from this directory in PowerShell:

```powershell
$env:APP_URL = 'http://127.0.0.1:8088'
php -S 127.0.0.1:8088 -t . router.php
```

This router supports clean paths and blocks private folders. PHP's built-in server is for development only and does not test Apache redirects, compression or cache directives. A temporary runtime may exist in the parent workspace's ignored `.runtime` directory; it is not part of the deployable site.

## Database import

Create a UTF-8 database named `semraa` using phpMyAdmin or your host's database panel. Select it and import `database/schema.sql`. The script creates `demo_requests` and `contact_requests`. It does not drop existing data.

Configure:

```text
DB_DSN=mysql:host=127.0.0.1;dbname=semraa;charset=utf8mb4
DB_USER=semraa_app
DB_PASSWORD=your-private-password
```

Use MySQL 5.7.8+ or MariaDB 10.4+ for schema compatibility; choose a currently supported release for hosting. Create the application account through your database administrator and grant only the needed SELECT, INSERT and UPDATE rights on these tables. Imports use a separate administrative account.

Request timestamps are UTC. Preferred meeting date/time and the IANA timezone are stored separately. The extra answers are JSON. Email status is `pending`, `sent` or `failed`; `sent` means accepted by the configured mail transport, not proof of inbox delivery. Back up the database and test restores. Apply a documented retention policy to form records, backups and mail copies.

## Email configuration

All settings are in the `mail` section of `includes/config.php`:

| Setting | Purpose |
| --- | --- |
| `DEMO_RECIPIENT_EMAIL` | Array of internal demo recipients |
| `CONTACT_RECIPIENT_EMAIL` | Array of internal contact recipients |
| `CC` | Internal CC recipients |
| `service_recipients` | Optional slug => recipient array overrides |
| `FROM_EMAIL`, `FROM_NAME` | Verified sender identity |
| `SMTP_HOST`, `SMTP_PORT` | Mail server and port |
| `SMTP_USER`, `SMTP_PASS` | Authentication, preferably environment variables |
| `SMTP_ENCRYPTION` | `tls` for STARTTLS, usually 587; `ssl` for implicit TLS, usually 465 |
| `phpmailer_path` | Directory containing PHPMailer source files |
| `allow_mail_fallback` | Explicit opt-in to PHP `mail()` |

For SMTP without Composer, download an official PHPMailer release from [PHPMailer's repository](https://github.com/PHPMailer/PHPMailer), retain its license, and place `Exception.php`, `PHPMailer.php` and `SMTP.php` from its `src` directory in `vendor/PHPMailer/src/`. Enable PHP OpenSSL and provide a working CA certificate configuration. TLS verification is not disabled by this code.

Example recipient override:

```php
'service_recipients' => [
    'cybersecurity' => ['security@your-domain.example'],
],
```

Replace these example addresses with your own. Reserved `@example.com` addresses are deliberately rejected by the mailer. Internal notifications use the visitor as Reply-To. Visitor confirmations use your configured sender as Reply-To. Form inputs never become arbitrary email headers.

If SMTP is not configured and your host supports `mail()`, explicitly set `allow_mail_fallback` to `true`. Delivery reliability depends on hosting configuration. Configure SPF, DKIM and DMARC with your mail provider, then test internal and external delivery.

Requests are saved before notification. Mail failure cannot erase a saved request. Failures write a reference-only entry to `logs/mail_errors.log` without submitted PII or credentials; a server error-log fallback is used if that directory is not writable. Grant the PHP user write access to `logs` without making it world-writable. Monitor failed or pending email statuses and follow up manually; automatic retry and delivery webhooks are not included.

## Booking behaviour and extension

`data/demo_services.php` is the sole definition of service-specific fields, names, types, options and required flags. Add a service there to make it available in the dropdown and both rendering/validation paths. Marketing service pages are separately defined in `data/services.php`.

Supported types: text, select, radio, checkbox, number and textarea. Field names must be unique within a service and use simple letters/underscores. Select/radio/checkbox choices are allowlisted on the server. Numbers are bounded integers. Unknown fields and nested malformed values are rejected.

`/book-demo?service=cybersecurity` preselects a service. `ui-ux-design` is accepted as an alias for the booking slug `ui-ux`. Without JavaScript, use the “Load service questions” form first, then complete all sections of the request. Changing the service requires loading its corresponding questions.

`booking` configuration controls time slots, timezone options, weekends, the maximum booking horizon and the 3-attempt/10-minute session limit. Every validated-CSRF attempt counts, including invalid submissions. This is session-level throttling, not distributed abuse prevention; a production host can add an edge rate limit or CAPTCHA for higher traffic. The `security.captcha_verifier` callable is a fail-closed integration hook. A provider-specific widget and server-side verification implementation must be supplied before enabling it; CAPTCHA is not represented as active by default.

Preferred slots are requests, not reservations. The site does not sync calendars or prevent overlapping requested slots. Staff confirm appointments. No calendar attachment is emitted for an unconfirmed appointment.

## Admin area

Admin is disabled until `ADMIN_PASSWORD_HASH` is configured. Generate a password hash locally with PHP's `password_hash($password, PASSWORD_DEFAULT)` and put only the hash in the environment. Use a long, unique password and HTTPS. Do not put a plaintext password in source code. For an internet-facing admin, add host-level access restrictions and rate limiting; the built-in login throttle is session-scoped.

Visit `/admin/login.php`. Sessions expire after 30 minutes. The admin can filter demos, inspect answers, mark requests contacted/closed and export the matching records. CSV cells beginning with formula operators are neutralised. CSV exports contain enquiry information and should be handled accordingly. Contact enquiries are stored and emailed; they do not have a separate admin screen in this build.

## Shared-hosting deployment

1. Create the database and application database user in the host's control panel.
2. Import the schema and configure the values above.
3. Upload this directory's contents to the domain document root, including `.htaccess`. Exclude `tests`, `tools`, the parent `.runtime`, local preview captures and development archives.
4. Confirm PHP 8.2+, PDO MySQL, OpenSSL and sessions work. PHP must process `.php` files; never publish the directory to a static-only host.
5. Configure `APP_URL` and `APP_ENV`. The `.htaccess` canonical redirects are configured for HTTPS and non-www `semraa.in`. Confirm Apache rewrite support and the SSL/proxy setup before deployment. If TLS terminates at a proxy, use the provider's canonical redirect controls or properly trusted proxy configuration; do not trust arbitrary forwarded headers.
6. Configure the mail transport and `logs` permissions. Keep `includes`, `data`, `logs`, `database` and `vendor` inaccessible over HTTP.
7. Test both forms, email failure behaviour, admin login, 404 status, clean URLs and direct access restrictions. Verify HTTPS and canonical redirects using actual HTTP response headers.
8. Check `/robots.txt` and `/sitemap.xml` contain the production domain and that local `noindex` is gone.
9. Verify `Content-Encoding` and browser cache headers. Apache modules are conditional; availability depends on hosting. Do not cache dynamic session-bearing form HTML at a CDN.

## Editing guide

| Change | File |
| --- | --- |
| Company details, domain, email, SMTP, database, scheduling | `includes/config.php` |
| Brand colours and layouts | `assets/css/style.css` (base), `assets/css/company.css` (company presentation and shared tokens) |
| Service page copy | `data/services.php` |
| Booking fields | `data/demo_services.php` |
| Products and approved feature details | `data/products.php` |
| Team and genuine reviews | `data/team.php`, `data/testimonials.php` |
| Blog articles | `data/blog.php` |
| Location content | `data/locations.php` |
| Titles and descriptions | `data/seo.php` |
| Portfolio concepts | `data/portfolio.php` |
| Reusable header/footer | `includes/header.php`, `includes/footer.php` |

Original logos are preserved. `tools/prepare-assets.php` produces deterministic favicon sizes and the original header derivative with GD. The navbar uses `logo-nav.webp`, an alpha-preserving compact derivative generated by `tools/prepare-header-logo.php` from the transparent logo. Dark footer branding is a text wordmark. The image’s orange/white mark has not been replaced with an invented logo.

Google Fonts provides Inter and Poppins with `font-display: swap`; font origins are preconnected without pinned version-specific preloads. The site has system-font fallbacks. If Google changes font versions, update the two preload URLs to match its stylesheet, or self-host licensed font files. Buttons use dark text over the requested orange gradient for stronger contrast. Reduced motion is respected.

JavaScript source is retained beside `.min.js` bundles. After editing, regenerate with Terser (`npx terser assets/js/main.js -c -m -o assets/js/main.min.js`, likewise for `demo-form.js`) or temporarily point the PHP templates to the readable files. No Node runtime is needed on the hosting server.

## Analytics

Set `GA4_ID` to a valid `G-...` measurement ID and `SEARCH_CONSOLE_VERIFICATION` to your verification token. Analytics is off until configured and consented to. The consent banner offers accept/decline; footer settings reopen it. Withdrawal reloads the page without analytics. Previously stored third-party analytics cookies may require separate browser deletion; the app stops loading/sending subsequent analytics events after withdrawal.

Events: `book_demo_click`, and `generate_lead` with `form_type=demo` or `contact` after successful persistence. Conversion markers use random values and session storage prevents repeated events on refresh. No contact details or service answers are included in event parameters. Mark the desired events as key events in GA4. Test consent and collection in your own analytics property before launch.

## Verification

```text
php tests/validation.php
node tests/http.mjs http://127.0.0.1:8088
```

The HTTP checks target a local server only. They check public routes, single H1s, unique titles, JSON-LD, linked assets, protected directories, pre-rendered fields, CSRF, validation and escaped preserved input. Run in a local environment, not against customer data. Database/email success must additionally be tested with your configured MySQL and SMTP services.

## Post-launch SEO checklist

- [ ] Set consistent real name, address and phone in configuration and Google Business Profile.
- [ ] Verify your Google Business Profile and use the correct categories, service areas and business information. Do not create fictitious branch addresses.
- [ ] Verify the domain in Google Search Console and submit `/sitemap.xml`.
- [ ] Inspect representative URLs, canonical tags, crawl access and HTTPS redirects.
- [ ] Validate Organization, ProfessionalService, Service, FAQ and Breadcrumb JSON-LD where applicable. Structured data does not guarantee a rich result.
- [ ] Create or correct relevant listings on Justdial, Sulekha, IndiaMART, Clutch, GoodFirms and LinkedIn; keep NAP consistent and only use appropriate listings.
- [ ] Seek relevant local partnerships and earned backlinks; avoid bulk spam links.
- [ ] Collect genuine customer reviews with consent. Publish attributed testimonials only after approval. Do not add invented rating schema.
- [ ] Replace illustrative portfolio material with approved case studies and concrete outcomes.
- [ ] Publish useful service and local content; avoid copying the same page for every city.
- [ ] Test Google Maps after adding the verified office address or place ID.
- [ ] Test mobile PageSpeed and accessibility on deployed hosting. Targets: LCP under 2.5s, CLS under 0.1 and INP under 200ms; these require actual measurement and are not guaranteed by source code.
- [ ] Monitor Search Console indexing, real search queries and consented enquiry conversions. Improve pages based on evidence.

Search rankings, traffic and conversion rates are outcomes to measure, not guarantees. Local HTTPS, database, mail and hosting configuration must be verified before describing the installation as production-ready.

## Website audit

See `WEBSITE_AUDIT.md` for the 18 October 2026 audit, changes, executed checks and remaining launch requirements. Company address, postcode, phone and email can be provided with `COMPANY_ADDRESS`, `COMPANY_POSTAL_CODE`, `COMPANY_PHONE` and `COMPANY_EMAIL`. General project CTAs use Contact; service links prefill its subject, while product demos retain the booking workflow.

## SEO implementation

See `SEO_LAUNCH.md` for implemented metadata/schema/internal-link changes, the shared indexing policy, executed checks and permanent-domain launch steps. Additional local checks: `php tests/seo-policy.php`, `php tests/seo-render.php`, `php tests/seo-render.php preview` and `node tests/seo.mjs http://127.0.0.1:8089`. `tools/prepare-seo-assets.php` regenerates the compact social JPEG with GD.

## Confirmed domain: semraa.in

Production defaults now use `https://semraa.in` and production indexing. Explicit local `APP_URL` values keep local mode; APP_PREVIEW=1 always keeps previews excluded. On the server, explicitly setting APP_URL=https://semraa.in, APP_ENV=production and APP_PREVIEW=0 is recommended. The existing .htaccess redirects HTTP/www and normalizes trailing slash public routes on Apache. If TLS terminates at a reverse proxy, configure the canonical redirect at that trusted proxy to avoid loops; no arbitrary forwarded headers are trusted in PHP.

The existing live site was checked on 18 October 2026: its robots/sitemap referenced nixlab.co.in. Deploy the new robots.php and sitemap.php routes with .htaccess to replace them; do not leave the old static sitemap.xml ahead of the PHP routing. Server changes are not applied by editing this local checkout.

## Enquiry, case-study and scheduling features

See `FEATURES_SETUP.md` for the unified enquiry inbox, case-study editor, optional brief builder and managed consultation availability. Existing databases import `database/features.sql`; new installations import the updated `database/schema.sql`. Server/database/admin/SMTP configuration remains required, and no case studies or real availability are pre-populated. WhatsApp is not included.
