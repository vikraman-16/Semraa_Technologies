<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/seo-policy.php';
$config = require __DIR__ . '/includes/config.php';
$pages = require __DIR__ . '/data/seo.php';
require_once __DIR__.'/includes/case-content.php';
foreach(case_records() as $case){$pages['case:'.$case['slug']]=['path'=>'case-studies/'.$case['slug']];}
header('Content-Type: application/xml; charset=UTF-8');
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
foreach ($pages as $key=>$page) {
    if (seo_excluded_page((string) $key)) { continue; }
    $url = $config['base_url'] . '/' . $page['path'];
    echo '<url><loc>' . htmlspecialchars($url,ENT_XML1 | ENT_QUOTES,'UTF-8') . '</loc></url>';
}
echo '</urlset>';
