<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/seo-policy.php';
$config = require __DIR__ . '/includes/config.php';
header('Content-Type: text/plain; charset=UTF-8');
$base = rtrim(parse_url($config['base_url'],PHP_URL_PATH) ?: '', '/');
echo "User-agent: *\n";
if (!seo_indexing_enabled($config)) { echo "Disallow: /\n"; }
else {
    foreach (['admin/','includes/','data/','logs/','database/','vendor/','tests/','tools/','booking-success'] as $path) { echo 'Disallow: ' . $base . '/' . $path . "\n"; }
}
echo 'Sitemap: ' . $config['base_url'] . "/sitemap.xml\n";
