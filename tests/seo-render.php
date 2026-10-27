<?php
// Render production-policy branches locally without starting a public production server.
declare(strict_types=1);
if (PHP_SAPI !== 'cli') { http_response_code(403); exit; }
$preview = ($argv[1] ?? '') === 'preview';
putenv('APP_ENV=production');
putenv('APP_URL=https://semraa.in');
putenv('APP_PREVIEW=' . ($preview ? '1' : '0'));
require __DIR__ . '/../includes/functions.php';
$checks = 0;
function check_render(bool $condition, string $message): void {
    global $checks;
    if (!$condition) { throw new RuntimeException($message); }
    $checks++;
}
ob_start(); page_start('home','home'); page_end(); $html = ob_get_clean();
check_render(str_contains($html, $preview ? 'content="noindex, nofollow"' : 'content="index, follow, max-image-preview:large"'), 'Correct rendered robots policy');
check_render(str_contains($html, 'rel="canonical" href="https://semraa.in/"'), 'Production canonical uses configured origin');
check_render(!str_contains($html, '127.0.0.1'), 'No development origin in production HTML');
ob_start(); require __DIR__ . '/../robots.php'; $robots = ob_get_clean();
check_render(str_contains($robots, "Disallow: /\n") === $preview, 'Correct rendered crawler policy');
ob_start(); require __DIR__ . '/../sitemap.php'; $xml = ob_get_clean();
check_render(substr_count($xml, '<loc>https://semraa.in/') === 27, 'Sitemap uses production canonicals');
echo 'PASS: ' . $checks . ' rendered ' . ($preview ? 'preview' : 'production') . " SEO checks.\n";
