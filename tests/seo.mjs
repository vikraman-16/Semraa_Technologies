// SEO regression checks against the non-production local preview only.
import assert from 'node:assert/strict';
const base=process.argv[2]||'http://127.0.0.1:8089';
assert(['127.0.0.1','localhost','[::1]'].includes(new URL(base).hostname),'Local server only');
let checks=0;
function check(value,message){assert(value,message);checks++;}
const xml=await(await fetch(base+'/sitemap.xml')).text();
const urls=[...xml.matchAll(/<loc>(.*?)<\/loc>/g)].map(m=>m[1]);
const descriptions=new Set();
for(const url of urls){
 const r=await fetch(url),html=await r.text();
 const canonical=html.match(/rel="canonical" href="([^"]+)"/)?.[1];
 const description=html.match(/name="description" content="([^"]+)"/)?.[1];
 check(canonical===url,`${url}: sitemap URL is its canonical`);
 check(description&&!descriptions.has(description),`${url}: distinct description`);descriptions.add(description);
 check(html.includes('content="noindex, nofollow"'),`${url}: local HTML excluded`);
 check(r.headers.get('x-robots-tag')==='noindex, nofollow',`${url}: local HTTP exclusion`);
 check(html.includes('property="og:image" content="'+base+'/assets/img/social-preview.jpg"'),`${url}: compact social image`);
 check(html.includes('property="og:image:width" content="1200"'),`${url}: explicit image dimensions`);
 const scripts=[...html.matchAll(/<script type="application\/ld\+json"[^>]*>(.*?)<\/script>/gs)].map(m=>JSON.parse(m[1]));
 const graph=scripts.flatMap(s=>s['@graph']||[s]);
 check(graph.some(n=>n['@id']===canonical+'#webpage'&&n.isPartOf?.['@id']===base+'/#website'),`${url}: connected WebPage entity`);
 const organization=graph.find(n=>n['@id']===base+'/#organization');
 check(organization?.logo?.url===base+'/assets/img/logo-nav.webp',`${url}: optimized organization logo`);
 check(!organization?.address,`${url}: no placeholder business address schema`);
 if(url.includes('/locations/'))check(graph.some(n=>n['@type']==='Service'&&n.areaServed?.['@type']==='City'),`${url}: location service schema`);
 if(url.includes('/blog/'))check(graph.some(n=>n['@type']==='BlogPosting'&&n.author?.['@id']===organization['@id']),`${url}: article author references real organization`);
}
const response=await fetch(base+'/assets/img/social-preview.jpg');
check(response.status===200&&response.headers.get('content-type')?.includes('image/jpeg'),'Social JPEG served with correct MIME');
check((await response.arrayBuffer()).byteLength<100000,'Social image under 100KB');
const robots=await(await fetch(base+'/robots.txt')).text();
check(robots.includes('Disallow: /'),'Preview crawling stays blocked');
console.log(`PASS: ${checks} SEO metadata, schema and crawl checks across ${urls.length} pages.`);
