<?php
require_once __DIR__ . '/seo-policy.php';
$canonical = url($seo['path']);
$settings = require __DIR__ . '/config.php';
$socialImage = $seo['image'] ?? 'assets/img/social-preview.jpg';
$socialAlt = $seo['image_alt'] ?? 'Semraa Technologies logo';
$socialSize = isset($seo['image']) ? (@getimagesize(dirname(__DIR__) . '/' . $socialImage) ?: [1200,800]) : [1200,800];
$noindex = seo_excluded_page((string) $key) || !seo_indexing_enabled($settings);
?>
<title><?= e($seo['title']) ?></title>
<meta name="description" content="<?= e($seo['description']) ?>">
<meta name="robots" content="<?= $noindex ? 'noindex, nofollow' : 'index, follow, max-image-preview:large' ?>">
<link rel="canonical" href="<?= e($canonical) ?>">
<link rel="alternate" hreflang="en-IN" href="<?= e($canonical) ?>">
<meta name="theme-color" content="#FF6B1A">
<meta property="og:type" content="<?= str_starts_with($key, 'post:') ? 'article' : 'website' ?>">
<meta property="og:locale" content="en_IN">
<meta property="og:site_name" content="Semraa Technologies">
<meta property="og:title" content="<?= e($seo['title']) ?>">
<meta property="og:description" content="<?= e($seo['description']) ?>">
<meta property="og:url" content="<?= e($canonical) ?>">
<meta property="og:image" content="<?= e(url($socialImage)) ?>">
<meta property="og:image:width" content="<?= (int)$socialSize[0] ?>">
<meta property="og:image:height" content="<?= (int)$socialSize[1] ?>">
<meta property="og:image:type" content="image/jpeg">
<meta property="og:image:alt" content="<?= e($socialAlt) ?>">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= e($seo['title']) ?>">
<meta name="twitter:description" content="<?= e($seo['description']) ?>">
<meta name="twitter:image" content="<?= e(url($socialImage)) ?>">
<?php if (config('analytics.search_console_verification')): ?><meta name="google-site-verification" content="<?= e(config('analytics.search_console_verification')) ?>"><?php endif; ?>
<?php
$company = config('company');
$org = ['@type'=>'Organization','@id'=>url() . '#organization','name'=>$company['name'],'url'=>url(),'logo'=>['@type'=>'ImageObject','url'=>url('assets/img/logo-nav.webp'),'width'=>576,'height'=>164],'areaServed'=>[['@type'=>'City','name'=>'Tirunelveli'],['@type'=>'City','name'=>'Tenkasi']]];
// Only publish complete, verified NAP as local business data.
if ($company['postal_code'] !== '' && valid_phone($company['phone']) && !str_contains($company['address'], '[')) {
 $org['@type'] = ['Organization','ProfessionalService'];
 $org['address'] = ['@type'=>'PostalAddress','streetAddress'=>$company['address'],'addressLocality'=>$company['city'],'addressRegion'=>$company['region'],'postalCode'=>$company['postal_code'],'addressCountry'=>'IN'];
 $org['telephone'] = $company['phone'];
 if ($company['latitude'] !== null && $company['longitude'] !== null) { $org['geo']=['@type'=>'GeoCoordinates','latitude'=>$company['latitude'],'longitude'=>$company['longitude']]; }
 if ($company['opening_hours']) { $org['openingHours'] = $company['opening_hours']; }
}
if (valid_email($company['email'])) { $org['email'] = $company['email']; }
$socialLinks = array_values(array_filter($company['social'], static fn($link) => is_string($link) && filter_var($link, FILTER_VALIDATE_URL) && str_starts_with($link, 'https://')));
if ($socialLinks) { $org['sameAs'] = $socialLinks; }
$graph = [$org, ['@type'=>'WebSite','@id'=>url() . '#website','name'=>$company['name'],'url'=>url()]];
$pageType = match ($key) {
 'about' => 'AboutPage', 'contact' => 'ContactPage',
 'services', 'products', 'portfolio', 'blog' => 'CollectionPage',
 default => 'WebPage',
};
$webPage = ['@type'=>$pageType, '@id'=>$canonical . '#webpage', 'url'=>$canonical,
 'name'=>$seo['title'], 'description'=>$seo['description'], 'inLanguage'=>'en-IN',
 'isPartOf'=>['@id'=>url() . '#website'], 'about'=>['@id'=>url() . '#organization']];
$graph[] = $webPage;
if ($crumbs) {
 $entries = [['@type'=>'ListItem','position'=>1,'name'=>'Home','item'=>url()]];
 foreach ($crumbs as $i=>$crumb) { $entries[]=['@type'=>'ListItem','position'=>$i+2,'name'=>$crumb[0],'item'=>url($crumb[1])]; }
 $graph[]=['@type'=>'BreadcrumbList','itemListElement'=>$entries];
}
if (str_starts_with($key, 'service:')) {
 $service = data('services')[substr($key, 8)];
 $graph[]=['@type'=>'Service','@id'=>$canonical . '#service','mainEntityOfPage'=>['@id'=>$canonical . '#webpage'],'name'=>$service['name'],'description'=>$service['summary'],'url'=>$canonical,'provider'=>['@id'=>url() . '#organization'],'areaServed'=>[['@type'=>'City','name'=>'Tirunelveli'],['@type'=>'City','name'=>'Tenkasi']]];
}
if (str_starts_with($key, 'location:')) {
 $location = data('locations')[substr($key, 9)];
 $graph[] = ['@type'=>'Service', '@id'=>$canonical . '#service',
  'name'=>'Software and IT services for ' . $location['name'], 'description'=>$location['intro'],
  'url'=>$canonical, 'serviceType'=>['Website development','Custom software development','IT consulting'],
  'provider'=>['@id'=>url() . '#organization'],
  'areaServed'=>['@type'=>'City','name'=>$location['name']],
  'mainEntityOfPage'=>['@id'=>$canonical . '#webpage']];
}
if (str_starts_with($key, 'case:') && isset($seo['case'])) {
 $caseRecord = $seo['case'];
 $article = ['@type'=>'Article','@id'=>$canonical.'#article','headline'=>$caseRecord['title'],
  'description'=>$caseRecord['summary'],'author'=>['@id'=>url().'#organization'],
  'publisher'=>['@id'=>url().'#organization'],'mainEntityOfPage'=>['@id'=>$canonical.'#webpage'],'inLanguage'=>'en-IN'];
 if($caseRecord['image_path'])$article['image']=url($caseRecord['image_path']);
 $graph[]=$article;
}
if (str_starts_with($key, 'post:')) {
 $post = data('blog')[substr($key, 5)];
 $graph[]=['@type'=>'BlogPosting','@id'=>$canonical . '#article','description'=>$post['excerpt'],'inLanguage'=>'en-IN','headline'=>$post['title'],'datePublished'=>$post['date'] . 'T09:00:00+05:30','author'=>['@id'=>url() . '#organization'],'publisher'=>['@id'=>url() . '#organization'],'mainEntityOfPage'=>['@id'=>$canonical . '#webpage']];
}
?>
<script type="application/ld+json" nonce="<?= e($GLOBALS['csp_nonce']) ?>"><?= json_for_html(['@context'=>'https://schema.org','@graph'=>$graph]) ?></script>
