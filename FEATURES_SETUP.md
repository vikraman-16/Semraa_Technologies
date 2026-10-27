# Feature setup: enquiries, case studies, project briefs and consultation booking

Implemented 19 October 2026. WhatsApp was intentionally left for a later change.

## What is implemented

1. **Unified enquiry inbox** at `/admin/inbox.php`: contacts, demo requests and consultation bookings in one list; request-type/status filters, details, internal email-delivery state, contact/demo status updates and internal notes. Existing demo administration and CSV export remain available. Notes are not emailed to visitors.
2. **Real case-study editor** at `/admin/cases.php`: save drafts, upload approved screenshots, and publish evidence-based case pages. Publishing requires substantive content, approval confirmation and a project URL or screenshot. Screenshots are validated and re-encoded as JPEG. Drafts and unapproved entries are not exposed publicly or included in the sitemap. Published work appears on Home and Our Work, with its own metadata, sharing image and page. No real case studies were invented or published.
3. **Optional project brief builder** in Contact: goal, timeline and budget preference. It works without JavaScript; JavaScript adds a live summary. It is not a price calculator or delivery promise. Briefs are saved transactionally with the enquiry and shown in admin and internal enquiry emails. The quick form still requires only name, email and message.
4. **Managed consultation booking** at `/consultation`: visitors choose only published, future 30-minute slots. Admin publishes, blocks and cancels slots at `/admin/calendar.php`. Unbooked blocked slots can be reopened. An atomic conditional reservation plus a unique booking constraint prevents two bookings for the same slot. Failed inserts roll back the claim. Cancelled bookings remain recorded and their slots blocked. Times are stored in UTC and displayed in Asia/Kolkata. This is an internal availability calendar, with no external calendar or video meeting integration. Staff provide call/meeting details separately.

## Activate on the server

- Back up the existing database before applying changes.
- For an existing installation, import `database/features.sql` into the same MySQL/MariaDB database as the original tables. It is additive and does not drop existing data. Fresh installations use the updated `database/schema.sql`, which now includes the feature tables.
- Continue using PHP 8.2+ with PDO MySQL. SQLite is used only by the isolated integration tests; the shipped migration targets MySQL/MariaDB and has not been executed against a live MySQL server in this session.
- Configure `DB_DSN`, `DB_USER`, `DB_PASSWORD` and a real `ADMIN_PASSWORD_HASH`. No production admin password is shipped. Admin remains disabled until configured.
- Configure `CONTACT_RECIPIENT_EMAIL` and `DEMO_RECIPIENT_EMAIL` as comma-separated recipient lists, `FROM_EMAIL`, `SMTP_HOST`, `SMTP_PORT`, `SMTP_USER`, `SMTP_PASS`, and `SMTP_ENCRYPTION`. Install the existing PHPMailer integration as described in README.md. Email acceptance and inbox delivery still need an end-to-end check with your actual provider.
- Enable PHP GD and make `assets/img/case-studies` writable by PHP for uploads. Set PHP `upload_max_filesize` to at least 4M and `post_max_size` to at least 5M. The application permits this larger body only for authenticated case-editor uploads; ordinary form requests retain their smaller limit.
- Open `/admin/login.php`, then the inbox, case studies or calendar. Publish only times you can attend. Do not copy the test fixture database or test-only password to production.
- Keep `APP_URL=https://semraa.in`, `APP_ENV=production`, `APP_PREVIEW=0` for the final site. The temporary public review route still blocks all submissions and admin access.
- Replace/deploy the updated routes and `.htaccess`; public case pages use `/case-studies/{slug}`. Consultation is a utility page and remains excluded from search indexing and the sitemap. Only approved, published case pages enter the sitemap.

## What still needs real input

- Approved case-study content/screenshots. Without it, the editor is ready but nothing is published; existing illustrative concepts remain labelled.
- Real consultation availability, published by the administrator. No times are invented on the real site.
- The actual database, admin hash and mail credentials. No live server or DNS was modified.

## Tests executed

- Existing 40 validation/security/content checks, 255 HTTP checks, 257 SEO checks, 20 indexing-policy checks, 10 production/preview render checks, and 5 contact-confirmation render checks.
- 20 feature integration checks against an isolated in-memory SQLite database: optional briefs, atomic enquiry/brief storage, published availability, duplicate slots, booking, rejected double bookings, rollback and case-publication controls.
- 27 end-to-end HTTP checks against a dedicated localhost-only SQLite fixture server: admin login/CSRF, publish/book/cancel, repeated-slot rejection, internal inbox notes/status, brief storage, draft/published pages and sitemap changes. No SMTP transport was configured, and email-failure behavior was checked without sending messages.
- 8 additional screenshot-upload checks on the same isolated fixture: authenticated upload larger than the normal form limit, JPEG re-encoding, actual image serving, metadata dimensions and unpublishing.
- Browser checks: optional brief summary, admin inbox/editor/calendar, published test availability, and 30 page/viewport combinations at 375, 430, 768, 1024, 1366 and 1440px, with no document overflow detected.
- PHP syntax checks across root/includes/admin/data/tests and JavaScript syntax/bundle regeneration passed.

## Reproducing feature integration tests

Use the bundled or your local PHP with PDO SQLite enabled for tests only:

```text
php -d extension=pdo_sqlite tests/features.php
php -d extension=pdo_sqlite tests/features.php fixture
```

The second command creates a new database under `.runtime` and prints its path. Start a separate local server using this fixture DSN, APP_URL=http://127.0.0.1:8091, APP_ENV=local, APP_PREVIEW=0 and an ADMIN_PASSWORD_HASH matching the explicitly synthetic `Local-feature-test-only` test password. Enable GD for the upload test. Do not use a production database or mail provider. Run `node tests/features-http.mjs http://127.0.0.1:8091` once on the fresh fixture; then generate an upload fixture with `php -d extension=gd tests/make-upload-fixture.php` and run `node tests/features-upload.mjs`. The fixture records and image are synthetic test data, not client work. Stop that local test server afterward.

The normal HTTP and SEO suites continue to target the unconfigured local preview with no published database-backed case records. Their 27-URL count describes the baseline content; a published case study adds a URL dynamically.
