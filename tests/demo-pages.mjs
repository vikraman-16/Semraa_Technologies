// Run against local development only; invalid submissions never write records.
import assert from 'node:assert/strict';
const base=process.argv[2]||'http://127.0.0.1:8088';
assert(['127.0.0.1','localhost'].includes(new URL(base).hostname));
const pages={'web-development':'website_type','software-development':'workflow','cloud-devops':'cloud_provider','cybersecurity':'assessment','ui-ux':'audience','it-consulting':'decision','semraa-nexus-db':'use_case','work-pulse':'use_case'};
let checks=0;
const check=(value,label)=>{assert(value,label);checks++;};
const hub=await(await fetch(base+'/book-demo')).text();
for(const [slug,field] of Object.entries(pages)){
 const path='/book-demo/'+slug,r=await fetch(base+path),html=await r.text();
 check(r.status===200,slug+' renders');
 check(html.includes('rel="canonical" href="'+base+path+'"'),slug+' canonical');
 check(html.includes('action="'+base+path+'"'),slug+' posts to own page');
 check(html.includes('name="extra['+field+']"'),slug+' tailored fields without JavaScript');
 const serviceSelect=html.match(/<select[^>]*name="service"[^>]*>([\s\S]*?)<\/select>/)?.[1]||'';
 check((serviceSelect.match(/<option value="[^"]+"/g)||[]).length===1&&serviceSelect.includes('value="'+slug+'"'),slug+' fixed service');
 check(hub.includes('href="'+base+path+'"'),slug+' linked from hub');
 const legacy=await fetch(base+'/book-demo?service='+slug,{redirect:'manual'});
 check(legacy.status===301&&legacy.headers.get('location')===base+path,slug+' old URL redirects');
}
check((await fetch(base+'/book-demo/not-a-service')).status===404,'Unknown service is 404');
const r=await fetch(base+'/book-demo/cybersecurity'),html=await r.text(),cookie=r.headers.get('set-cookie').split(';')[0];
const invalid=await fetch(base+'/book-demo/cybersecurity',{method:'POST',redirect:'manual',headers:{Cookie:cookie},body:new URLSearchParams({csrf:'invalid',service:'web-development'})});
check(invalid.status===303&&invalid.headers.get('location')===base+'/book-demo/cybersecurity','Invalid request stays on same landing page');
const failed=await(await fetch(base+'/book-demo/cybersecurity',{headers:{Cookie:cookie}})).text();
check(failed.includes('Your session expired')&&failed.includes('name="extra[assessment]"'),'Errors preserve page context');
console.log(`PASS: ${checks} dedicated demo page checks.`);
