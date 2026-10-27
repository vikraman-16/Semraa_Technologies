<?php
declare(strict_types=1);
require __DIR__ . '/../includes/seo-policy.php';
$checks = 0;
function check_policy(bool $condition, string $message): void {
    global $checks;
    if (!$condition) { throw new RuntimeException($message); }
    $checks++;
}
$production = ['environment'=>'production','preview_mode'=>false,'base_url'=>'https://semraa.in'];
check_policy(seo_indexing_enabled($production), 'Production domain allows indexing');
check_policy(!seo_indexing_enabled(array_replace($production,['base_url'=>'http://semraa.in'])), 'Production requires HTTPS');
check_policy(!seo_indexing_enabled(array_replace($production,['environment'=>'local'])), 'Local mode blocks indexing');
check_policy(!seo_indexing_enabled(array_replace($production,['preview_mode'=>true])), 'Preview overrides production mode');
foreach (['http://localhost:8089','http://127.0.0.1:8089','http://[::1]:8089','https://test.trycloudflare.com'] as $origin) {
    check_policy(!seo_indexing_enabled(array_replace($production,['base_url'=>$origin])), 'Development origin stays excluded: ' . $origin);
}
foreach (['404','admin','booking-success','consultation'] as $key) { check_policy(seo_excluded_page($key), 'Private/utility page excluded: ' . $key); }
foreach (['home','about','contact','location:tenkasi','service:web-development'] as $key) { check_policy(!seo_excluded_page($key), 'Public content eligible: ' . $key); }
// Confirm deployment works without retaining a development URL in defaults.
putenv('APP_URL'); putenv('APP_ENV'); putenv('APP_PREVIEW');
$defaults = require __DIR__ . '/../includes/config.php';
check_policy($defaults['base_url'] === 'https://semraa.in', 'Confirmed production origin is the default');
check_policy(seo_indexing_enabled($defaults), 'Permanent-domain defaults are eligible for indexing');
putenv('APP_URL=http://127.0.0.1:8089');
$local = require __DIR__ . '/../includes/config.php';
check_policy($local['environment'] === 'local' && !seo_indexing_enabled($local), 'Explicit development URL keeps safe local defaults');
echo "PASS: $checks SEO indexing policy checks.\n";
