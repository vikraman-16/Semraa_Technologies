# Semraa website audit and implementation

Audit date: 18 October 2026. Preview: http://127.0.0.1:8089/

## Assessment

The site had a useful starting point: PHP-rendered pages, six substantive service descriptions, stable routes, an established orange/charcoal identity, labelled forms, CSRF protection, server validation, no-JavaScript booking support, and restrained dependencies. These were retained.

Its main weakness was credibility and consistency across the full enquiry journey. The homepage was ahead of the inner pages: generic demo CTAs persisted on service pages, large introductions pushed actions down, product concepts stood in for unrelated portfolio visuals, and animated capability counts looked more like social proof than useful decision-making information. The CSS had accumulated multiple rounds of overrides, and the newly transparent logo was much larger than its display size required.

The business is presented as a Tenkasi-area technology partner for businesses seeking websites, custom software, clearer workflows, design, infrastructure and consulting. The main conversion is a project enquiry; product demos are a separate journey.

## Prioritized findings, changes and verification

| Priority | Finding and location | Why it matters | Fix or recommendation | Verification / state |
| --- | --- | --- | --- | --- |
| P0 — launch blocker | Database and mail defaults remain unconfigured; contact and booking depend on them. | A visible form is not a working lead channel until persistence and delivery are verified. | Preserve validation, storage and email logic; configure actual MySQL and SMTP, sender and recipients before launch. Do not fabricate a successful submission. | Error and validation paths tested. Successful storage, delivery and production success screens remain unverified. |
| P1 | Service cards, detail pages, locations and article endings mixed project discussions with demos. | Visitors were asked to schedule before they could make a general enquiry, and service context was lost. | Added shared project_url(), routed general project actions to Contact, preserved product-specific demo links, safely prefilled the service name in Subject. | Browser service-to-contact navigation and subject checked; HTTP regression added. |
| P1 | Long inner-page heroes and lack of an early service CTA. | Visitors had to scroll past an introduction before acting or seeing useful detail. | Reduced shared hero sizing and spacing; shortened Contact heading; added a service CTA with a jump to its approach. | Desktop/mobile screenshots and all-page viewport matrix. |
| P1 | Mobile forms placed sidebar content before the form. | Extra explanation competed with the visitor's intended action. | Form now precedes sidebar on mobile; font sizes are 16px and field gaps are consistent. | Contact and booking inspected at mobile widths, validation and three-step progression checked. |
| P1 | Four portfolio concepts reused only two unrelated product dashboards; no challenge/approach structure. | Repetition obscured capability and risked implying unrelated work was evidence. | Added reusable category-specific concept visuals plus challenge, proposed approach and intended value fields. Explicit concept labels remain. | Portfolio filtering and visible count verified; screenshots reviewed. |
| P1 | Homepage capability counters resembled achievement statistics without demonstrating results. | They added visual emphasis without helping a hiring decision. | Replaced counters with concrete engagement expectations and removed the counter animation code. | Homepage browser review; no fabricated proof added. |
| P1 | Header logo was a 772,222-byte PNG for a roughly 193px-wide display. | Every page paid an avoidable asset cost. | Generated an alpha-preserving 576x164 WebP derivative; original assets retained. | New file is 21,132 bytes, a 97.3% size reduction. Browser checked against dark navbar. This is not a measured page-speed gain. |
| P2 | Small secondary links, filter buttons, form text and focus treatment. | Touch and keyboard use were unnecessarily difficult. | Added shared minimum heights, stable text-link hover behavior, dark-surface focus colors and readable form sizing. Retained skip link and reduced-motion rules. | Mobile menu keyboard and Escape behavior checked; selected token contrasts measured. Not a full WCAG certification. |
| P2 | Grouped form errors lacked explicit associations; error summary links could target hidden booking steps. | Assistive technology and keyboard users could lose error context. | Added fieldset tabindex, invalid/error associations and summary links that reveal and focus the relevant step. Added enquiry loading/busy state and restored controls on pageshow. | PHP lint, JS syntax and server validation tests. Hidden-step summary handler reviewed in code; not exercised in a browser with a server-generated booking error. |
| P2 | Services listing skipped from H1 to H3. | Heading structure was less useful to assistive navigation. | Reusable service_cards() supports an H2 heading on the listing; matching card typography retained. | Rendered headings and PHP lint checked. |
| P2 | Products did not immediately distinguish concepts from available products. | Buyers could mistake proposed capabilities for released features. | Clarified ownership, concept status, availability and scope in the Products introduction. | Products screenshot and route check. |
| P2 | CSS contained obsolete code-editor art and tightly coupled presentation overrides. | Further changes were difficult to reason about. | Removed obsolete hero-art rules, separated company presentation into company.css, introduced shared spacing/color/radius tokens, formatted base rules for editing. | Asset tests and six-width route matrix. Legacy base rules remain; a full CSS rewrite was deliberately avoided. |
| P2 | Manually pinned Google font preloads could diverge from the requested font stylesheet. | Duplicate or unused font downloads were possible. | Removed hardcoded font-version preloads and unused Poppins 800 request; retained display=swap, preconnects and system fallbacks. | Browser console review and rendered typography. No before/after font performance metric was collected. |
| P3 | Contact repeated a large project invitation after the enquiry form; footer duplicated the same contact destination. | Repetition added length without a distinct next action. | Suppressed the shared CTA on Contact and 404; removed duplicate footer enquiry link. | HTTP assertion and visual review. |

## Verification

- `php tests/validation.php`: 33 checks passed.
- `node tests/http.mjs http://127.0.0.1:8089`: 255 checks passed across 27 public pages. Includes SEO metadata, structured-data parsing, local assets, protected paths, CSRF, invalid booking/contact submissions, escaped preserved input and service context.
- PHP syntax checks passed for all 34 PHP files in the root, includes and data directories.
- JavaScript syntax checks passed; Terser regenerated both existing production bundles. No application dependency or framework was added.
- 27 pages checked at 375, 430, 768, 1024, 1366 and 1440px: 162 page/viewport combinations, no detected horizontal overflow, broken completed images or H1-count failures. These are DOM geometry checks, supplemented by screenshots of representative homepage, service, portfolio, contact, product and booking layouts; not 162 separate visual inspections.
- 35 unique local destinations discovered from public-page links returned HTTP 200.
- Browser interactions exercised: portfolio category filtering, FAQ expansion, service context into Contact, required contact validation, booking requirements/details/final-step progression, Back retaining input, mobile menu open/close and Escape restoring focus.
- Captured browser warning/error logs were empty during the recorded interaction check. This is not proof that third-party resources can never fail.
- Sample token contrast ratios: orange text/white 5.63:1; navigation text/charcoal 11.64:1; active navigation/charcoal 7.86:1; dark CTA text/darker gradient endpoint 5.78:1; muted text/white 6.42:1. These checks do not cover every composited background or every browser state.
- No framework build step exists for this PHP project. PHP/JS syntax and the executable suites are the applicable checks.
- No Lighthouse, field Core Web Vitals, production latency, screen-reader certification or conversion-rate improvement is claimed.

## Main changed components

- `assets/css/style.css`: base style cleanup and obsolete illustration removal.
- `assets/css/company.css`: shared company presentation, responsive rules and accessible interaction sizing.
- `includes/header.php`, `includes/footer.php`: font requests, logo delivery and shared conversion presentation.
- `includes/functions.php`: enquiry URL helper, service heading support and reusable project visuals.
- `index.php`, `service.php`, `services.php`, `location.php`, `post.php`: coherent enquiry journey and homepage engagement content.
- `portfolio.php`, `data/portfolio.php`, `products.php`: clearer concepts and structured portfolio briefs.
- `contact.php`, `includes/forms.php`, `assets/js/main.js`, `assets/js/demo-form.js`: context, grouped errors, loading state, step navigation and menu behavior.
- `includes/config.php`: optional COMPANY_ADDRESS, COMPANY_POSTAL_CODE, COMPANY_PHONE and COMPANY_EMAIL environment values.
- `tools/prepare-header-logo.php`, `assets/img/logo-nav.webp`: reproducible compact logo derivative.
- `tests/http.mjs`: meaningful enquiry regression coverage.

## Remaining launch requirements and recommendations

1. Configure and verify MySQL/PDO MySQL, SMTP, actual recipients, verified sender and production success flows. Test both form submissions end to end and confirm received emails, not just HTTP success. Keep local noindex until deployment is ready.
2. Supply the actual domain, business address, postcode, phone, email and approved legal/privacy details. Set production HTTPS/canonical rules and verify Apache behavior; the local PHP server cannot validate hosting configuration. Environment overrides now support contact details without source edits.
3. Supply consented client case studies: context, actual screenshots, scope, delivery evidence and measured results. The concept layout is ready to accommodate stronger evidence; current concepts must not be relabelled as client work.
4. Supply real team information, approved photographs and verifiable testimonials where appropriate. Confirm the brand's legal/trading name versus the logo's singular Technology wording.
5. Once traffic and consented analytics exist, measure enquiry starts, completion/drop-off and lead quality. Test the outcome-oriented hero and Contact journey with actual prospective customers before claiming conversion gains. Product click and lead events still need an operational analytics property.
6. Run Lighthouse on the production host, test a slower mobile connection, and perform keyboard/screen-reader testing with users. Consider licensed self-hosted fonts if external font requests prove unreliable.
7. Remove development runtime files, captures and audit materials from a deployment package. Preserve and test the existing private-directory rules. Admin remains intentionally disabled without credentials.
