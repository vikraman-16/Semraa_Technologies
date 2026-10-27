# Semraa SEO implementation and launch

Updated: 18 October 2026.

## Implemented

- Distinct, benefit-focused titles and descriptions for all public routes, including six service pages and six regional pages. Company name is consistent in metadata.
- Tenkasi-area / Tirunelveli-serving positioning in header, footer and relevant page content. No exact office address or map pin is claimed until verified.
- Canonical URLs derived from APP_URL. Query variations retain the clean page canonical.
- Connected Organization, WebSite, WebPage, AboutPage, ContactPage, CollectionPage, Service, BlogPosting and breadcrumb entities where appropriate. Location services identify the city served, rather than inventing branch businesses. Local business address markup remains withheld until complete contact details are provided.
- Contextual, crawlable links between service pages and useful articles.
- Compact 1200x800 JPEG social preview: 42,556 bytes versus the previous 2,200,797-byte original. Explicit social-image dimensions and MIME metadata; compact WebP logo in organization markup. Originals are retained.
- Shared indexing rules for HTML, HTTP headers and robots responses. Public production pages become eligible only with HTTPS, APP_ENV=production and APP_PREVIEW disabled. Localhost and temporary trycloudflare hosts stay excluded. Admin, 404 and booking confirmation pages remain excluded from the sitemap and search indexing.
- Production sitemap contains the 27 canonical public URLs. No fabricated change timestamps, review ratings or product availability schema.

## Executed checks

- 255 HTTP/SEO/security checks across 27 public pages.
- 257 dedicated metadata/schema/crawl checks across those pages.
- 19 indexing policy cases, including preview overriding production and temporary-host exclusions.
- 5 rendered production and 5 rendered preview checks, with a confirmed semraa.in HTTPS origin in local CLI only. No production server was modified.
- 33 existing validation/content/security checks.
- PHP syntax checks passed across root, includes, data, tests and tools.
- Public preview's page title, canonical, preview noindex and related links verified in the browser.

Total: 574 executable assertions across the suites above; syntax checks are additional. These do not establish actual Google indexing, rich-result eligibility, field performance or ranking positions.

## Before search launch

1. Deploy the PHP site on its permanent HTTPS domain. The confirmed origin is https://semraa.in. Production defaults and .htaccess redirects now use it. Explicitly set APP_URL=https://semraa.in, APP_ENV=production and APP_PREVIEW=0 on the host. Test both forms with real database/email services.
2. Supply actual COMPANY_ADDRESS, COMPANY_POSTAL_CODE, COMPANY_PHONE and COMPANY_EMAIL. Confirm the trading name and approved business social profiles. Do not enter hypothetical branch addresses.
3. Verify the permanent-domain Search Console property using SEARCH_CONSOLE_VERIFICATION or DNS. Submit its /sitemap.xml and inspect representative home, service and location pages. Check that robots.txt no longer contains a blanket Disallow and that public pages have no noindex header/tag.
4. Validate rendered structured data with Google's Rich Results Test and Schema.org's validator. No Google validation-tool result has been claimed from the local tests.
5. Complete a Google Business Profile if the business is eligible, using its genuine location and service arrangements. Obtain real customer reviews and consented case studies. Do not create a second location profile without a legitimate eligible location.
6. Measure relevant organic queries and qualified enquiries after launch. Focus on software/web development intent; a generic search for 'company near me' does not identify what the searcher needs. Use actual Search Console data to refine content instead of adding repetitive location pages.
7. Run production Lighthouse / PageSpeed and field Core Web Vitals checks. Only image byte reduction was measured here; no ranking or page-speed gain is promised.

## Run checks

```text
php tests/validation.php
php tests/seo-policy.php
php tests/seo-render.php
php tests/seo-render.php preview
node tests/http.mjs http://127.0.0.1:8089
node tests/seo.mjs http://127.0.0.1:8089
```

The HTTP suites target a non-production local server only. The render suite uses a confirmed origin internally without connecting to it without publishing it.

## References

- [Google: canonical URLs](https://developers.google.com/search/docs/crawling-indexing/consolidate-duplicate-urls)
- [Google: building sitemaps](https://developers.google.com/search/docs/crawling-indexing/sitemaps/build-sitemap)
- [Google: Organization structured data](https://developers.google.com/search/docs/appearance/structured-data/organization)
- [Google: local ranking](https://support.google.com/business/answer/7091)

No ranking position is guaranteed. The temporary public review link remains intentionally excluded from search indexing.

## Domain migration findings (18 October 2026)

The live `https://semraa.in/robots.txt` advertised `https://nixlab.co.in/sitemap.xml`; the live sitemap listed nixlab.co.in URLs. Both are corrected in this checkout's dynamic responses. The live HTTP and www variants returned HTTP 200 without a canonical redirect in the read-only check. The configured Apache rules must be deployed and checked on the real host. No live files, DNS, SSL settings or server configuration were changed.

After upload verify https://semraa.in/robots.txt, https://semraa.in/sitemap.xml and canonical tags; HTTP/www must return a redirect to https://semraa.in with the path preserved. Verify clean URLs before submitting the sitemap in the semraa.in Search Console property. Keep existing verified business/profile data rather than inventing a new address. Real form persistence and delivery are still deployment prerequisites.


## Dedicated demo and enquiry pages (27 October 2026)

Eight pages under `/book-demo/{service}` provide request-specific preparation guidance, a relevant question and answer, and the matching form. They cover web development, software development, cloud/DevOps, cybersecurity, UI/UX, IT consulting, Nexus DB and Work Pulse. The main Book a Demo page links to all eight. Service and product links use the clean request URLs; old `?service=` links redirect permanently to them.

Each page has a distinct title, description, canonical, social metadata and breadcrumbs, and is included in the 35-page baseline sitemap. These request pages explain the initial discussion; the existing service pages describe the service delivery. Product concepts remain labelled as concepts, without fabricated availability or outcomes. Unknown request types return 404 and invalid submissions stay on the appropriate page.

The Render review deployment remains noindex and read-only. Search visibility requires the production domain, indexing enabled, and search-engine discovery; these changes do not guarantee rankings. Run `node tests/demo-pages.mjs http://127.0.0.1:8088` for dedicated form-page regression checks.
