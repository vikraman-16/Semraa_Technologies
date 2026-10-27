// Run only against a local preview without production DB credentials.
// Uses Node's built-in fetch; no packages or browser automation needed.
import assert from 'node:assert/strict';
const base = process.argv[2] || 'http://127.0.0.1:8088';
assert(['localhost','127.0.0.1','[::1]'].includes(new URL(base).hostname), 'Local preview only');
let checks = 0;
const check = (value,label) => { assert(value,label); checks++; };
const sitemap = await (await fetch(`${base}/sitemap.xml`)).text();
const urls = [...sitemap.matchAll(/<loc>(.*?)<\/loc>/g)].map(match=>match[1]);
check(urls.length === 27,'Sitemap contains 27 public pages');
const assets = new Set(); const titles = new Set();
for (const url of urls) {
  check(url.startsWith(`${base}/`),'Sitemap uses configured base URL');
  const response = await fetch(url), html = await response.text();
  check(response.status === 200, `${url} returns 200`);
  check((html.match(/<h1(?:\s|>)/g)||[]).length === 1,`${url} has one H1`);
  check(!/Fatal error|Warning:|Parse error/.test(html),`${url} has no PHP error output`);
  const title = html.match(/<title>(.*?)<\/title>/s)?.[1];
  check(title && !titles.has(title),`${url} has unique title`); titles.add(title);
  check(response.headers.get('content-security-policy')?.includes("frame-ancestors 'none'"),'CSP present');
  check(response.headers.get('x-content-type-options') === 'nosniff','MIME protection');
  for (const match of html.matchAll(/<script type="application\/ld\+json"[^>]*>(.*?)<\/script>/gs)) { JSON.parse(match[1]); checks++; }
  for (const match of html.matchAll(/(?:src|href)="([^"]*\/assets\/[^"?]+)"/g)) assets.add(match[1]);
}
for (const asset of assets) check((await fetch(asset)).status === 200,`Asset exists: ${asset}`);
check((await fetch(`${base}/missing-page`)).status === 404,'Custom 404 status');
for (const path of ['includes/config.php','data/demo_services.php','logs/mail_errors.log','database/schema.sql','tests/validation.php']) check((await fetch(`${base}/${path}`)).status === 403,`Private path blocked: ${path}`);
check((await fetch(`${base}/admin/login.php`)).status === 503,'Admin disabled until configured');
const demo = await fetch(`${base}/book-demo?service=cybersecurity`);
const cookie = demo.headers.get('set-cookie').split(';')[0], html = await demo.text();
check(html.includes('name="extra[assessment]"'),'No-JS service fields rendered');
check(html.includes('<noscript>'),'No-JS service loader present');
const token = html.match(/name="csrf" value="([a-f0-9]+)"/)[1];
let response = await fetch(`${base}/book-demo`,{method:'POST',redirect:'manual',headers:{Cookie:cookie},body:new URLSearchParams({csrf:'wrong',name:'HTTP Test'})});
check(response.status === 303,'Invalid submission uses PRG');
let page = await (await fetch(`${base}/book-demo`,{headers:{Cookie:cookie}})).text();
check(page.includes('Your session expired'),'CSRF failure explained');
const invalid = {csrf:token,website_url:'',name:'<script>alert(1)</script>',email:'invalid',phone:'123',company:'Test',service:'cybersecurity',preferred_date:'2000-01-01',preferred_time:'03:00',timezone:'Asia/Kolkata',mode:'Unknown','extra[assessment]':'Unknown','extra[assets]':'-1'};
response = await fetch(`${base}/book-demo`,{method:'POST',redirect:'manual',headers:{Cookie:cookie},body:new URLSearchParams(invalid)});
check(response.status === 303,'Invalid values redirect');
page = await (await fetch(`${base}/book-demo`,{headers:{Cookie:cookie}})).text();
check(page.includes('Enter a valid work email'),'Email validated server-side');
check(page.includes('Choose a future available time'),'Schedule validated server-side');
check(page.includes('&lt;script&gt;alert(1)&lt;/script&gt;'),'Preserved values escaped');
check(!page.includes('value="<script>'),'No reflected script injection');
// Enquiry context and invalid contact submissions must not reach persistence.
const contextual = await (await fetch(`${base}/contact?service=web-development`)).text();
check(contextual.includes('value="Web Development enquiry"'), 'Service context prefills enquiry subject');
check(!contextual.includes('YOUR NEXT CHAPTER STARTS HERE'), 'Contact avoids a redundant conversion section');
const contactResponse = await fetch(`${base}/contact`);
const contactCookie = contactResponse.headers.get('set-cookie').split(';')[0];
const contactHtml = await contactResponse.text();
const contactToken = contactHtml.match(/name="csrf" value="([a-f0-9]+)"/)[1];
const contactInvalid = await fetch(`${base}/contact`, {method:'POST', redirect:'manual', headers:{Cookie:contactCookie}, body:new URLSearchParams({csrf:contactToken, website_url:'', name:'<script>invalid</script>', email:'invalid', phone:'', subject:'Preview validation', message:''})});
check(contactInvalid.status === 303, 'Contact validation uses PRG');
const contactErrors = await (await fetch(`${base}/contact`, {headers:{Cookie:contactCookie}})).text();
check(contactErrors.includes('Enter a valid email address.'), 'Contact reports invalid email');
check(contactErrors.includes('Message is required.'), 'Contact reports required message');
check(contactErrors.includes('&lt;script&gt;invalid&lt;/script&gt;'), 'Contact preserves and escapes input');
check(contactErrors.includes('aria-describedby="email-error"'), 'Contact input is associated with its error');
check(/href="[^"]*\/book-demo"[^>]*>Book a Demo/.test(contextual), 'Demo booking remains accessible from navigation');
console.log(`PASS: ${checks} HTTP, SEO, asset and security checks across ${urls.length} pages.`);
