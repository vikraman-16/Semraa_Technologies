// Non-destructive checks for the read-only public preview we own.
import assert from 'node:assert/strict';
const base='https://semraa-preview.onrender.com';
let checks=0;
const check=(value,label)=>{assert(value,label);checks++;};
const response=await fetch(base+'/book-demo/web-development'),html=await response.text();
check(response.status===200,'Public enquiry page renders');
check(html.includes('noindex, nofollow'),'Preview is not indexed');
check(html.includes('assets/fonts/inter-latin.woff2')&&!html.includes('fonts.googleapis.com'),'Fonts are hosted locally');
check(/assets\/css\/style\.css\?v=[a-f0-9]{12}/.test(html),'Stylesheet URLs invalidate stale caches');
check(response.headers.get('x-powered-by')===null,'PHP version is not exposed');
check(response.headers.get('content-security-policy')?.includes("frame-ancestors 'none'"),'CSP blocks framing');
check(response.headers.get('content-security-policy')?.includes("font-src 'self';"),'Fonts restricted to same origin');
check(response.headers.get('strict-transport-security')?.includes('max-age=31536000'),'HTTPS transport policy');
check(response.headers.get('set-cookie')?.includes('HttpOnly')&&response.headers.get('set-cookie')?.includes('secure'),'Secure session cookie');
for(const path of ['/FEATURES_SETUP.md','/Dockerfile','/render.yaml','/.git/config','/logs/','/includes/config.php','/admin/login.php']){
 check((await fetch(base+path)).status===403,'Private file blocked: '+path);
}
for(const path of ['/contact','/book-demo/web-development','/consultation']){
 check((await fetch(base+path,{method:'POST',body:''})).status===403,'Preview submissions blocked: '+path);
}
check((await fetch(base+'/assets/fonts/inter-latin.woff2')).status===200,'Local font served');
check((await fetch(base+'/this-page-does-not-exist')).status===404,'Unknown pages return 404');
console.log(`PASS: ${checks} deployed preview access, header and font checks.`);
