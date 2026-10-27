# SEO, performance and security verification

Reviewed 27 October 2026. Scope: the PHP source, isolated synthetic database workflows and the public read-only Render preview. This is a bounded engineering audit, not a guarantee of rankings or an external penetration-test certification.

## Changes and verification

- All eight dedicated request pages are published in GitHub and deployed. The 35-page baseline sitemap has distinct titles, descriptions, canonical URLs, connected structured data and internal links. Legacy demo query URLs redirect to their dedicated pages. Invalid slugs return 404. Product concepts remain explicitly labelled.
- Production-policy rendering checks confirm semraa.in canonicals and indexable public content when production is enabled. Confirmation, admin and consultation utility pages remain excluded. No invented office address, ratings or case-study results are published.
- Fonts are hosted locally with their OFL licenses and font-display: swap. Critical fonts are preloaded. CSS and JavaScript URLs include content digests so changed assets invalidate browser caches. Images use modern formats, scripts are deferred, and the deployed server compresses responses.
- Login attempts now have a locked, persistent per-address counter in addition to session throttling. Clearing cookies cannot bypass that counter. The limiter fails closed if its storage is unavailable. Proxy/multi-instance installations still need appropriate edge throttling; the application deliberately does not trust arbitrary forwarding headers.
- CSRF checks, session rotation, escaped HTML, prepared SQL statements, strict field/option validation, booking uniqueness/transactions and authenticated image re-encoding were reviewed. Login remains disabled without a configured password hash.
- Apache denies private paths, setup documentation, deployment files, dotfiles and database/backup files. Uploaded screenshots cannot execute scripts. The Render preview denies admin access and all methods except GET/HEAD. A live test caught an Apache authorization-merging conflict; it was fixed and the deployed test now verifies both file and method restrictions together.
- The container suppresses PHP version/error disclosure, limits upload/request sizes, disables TRACE and minimizes server identification. CSP, secure HttpOnly cookies, HSTS, MIME protection and frame protection were checked on the deployed site.
- A pattern scan of tracked application source found no matching GitHub tokens, AWS access keys or private-key headers. This is not an exhaustive secret-detection service or dependency/CVE scan.

## Checks passed

- 321 HTTP, asset, content and security assertions across 35 local public pages.
- 329 SEO metadata, structured-data and crawl-policy assertions.
- 59 dedicated form-page assertions, including fixed service fields, old-URL redirects and error routing.
- 40 validation/security assertions; 20 indexing-policy assertions; 10 production/preview rendering assertions; 5 saved-enquiry rendering assertions.
- 20 isolated database feature checks; 27 authenticated admin/booking/enquiry workflow checks; 8 image-upload/publication checks.
- 6 persistent limiter checks plus a real HTTP check showing fresh-cookie login attempts return 429 after the shared limit is reached.
- 21 deployed preview access, header, method, cache-version and font checks.
- PHP syntax checks, Git whitespace checks and JavaScript regression suites passed. Synthetic test data and uploads were excluded from Git and removed from public assets after testing.

## Mobile Lighthouse measurements

Measured on the public Render preview with Lighthouse mobile defaults. Scores vary with network, load and free-host wake-up time; these are lab measurements, not field Core Web Vitals.

| Page | Performance | Accessibility | Best practices | SEO | LCP | CLS | Blocking time |
| --- | ---: | ---: | ---: | ---: | ---: | ---: | ---: |
| Home | 99 | 100 | 100 | 69 | 1.4 s | 0 | 0 ms |
| Website development enquiry | 98 | 100 | 100 | 69 | 1.6 s | 0 | 30 ms |

The enquiry page performance score was 87 before removing the external font request. Both preview SEO scores are reduced by the deliberate noindex/crawl block. This block must remain on the temporary preview; it should not be removed just to raise an audit score. Raw reports remain in ignored local .runtime files.

## Production launch still required

Deploy to semraa.in on PHP/MySQL hosting using the documented migration, configure actual company contact details, a strong private admin password hash, database credentials and SMTP, and verify real email delivery. The current Docker container is deliberately a read-only preview; changing APP_PREVIEW alone does not remove its Apache restrictions. Production hosting needs its own configuration.

Verify the production property in Google Search Console and submit the production sitemap after launch. Confirm the exact approved business address before adding local business address schema/listings. Rankings and indexing depend on search engines, relevant content, competition and the live deployment, not code alone.

Production MySQL migration, SMTP delivery, container/CVE scanning, an independent penetration test, external calendar integration and production field performance were not verified in this audit. Keep PHP, server packages and any future mail dependencies updated; use edge/WAF rate limits appropriate to the hosting network and monitor failed sign-ins. No audit can establish that there are zero present or future vulnerabilities.

References: [Google SEO starter guide](https://developers.google.com/search/docs/fundamentals/seo-starter-guide), [OWASP authentication guidance](https://cheatsheetseries.owasp.org/cheatsheets/Authentication_Cheat_Sheet.html), [Apache authorization documentation](https://httpd.apache.org/docs/2.4/mod/mod_authz_core.html).
