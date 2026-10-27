<?php
// Read-only public preview; normal development and hosting routes are unchanged.
declare(strict_types=1);
$previewPath = trim(rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/'), '/');
if (!in_array($_SERVER['REQUEST_METHOD'], ['GET', 'HEAD'], true)) {
    http_response_code(405);
    header('Allow: GET, HEAD');
    header('Content-Type: text/plain; charset=UTF-8');
    exit('This is a website preview. Enquiries and bookings are not active yet.');
}
if ($previewPath === 'admin' || str_starts_with($previewPath, 'admin/')) {
    http_response_code(404);
    exit('Not available in this preview.');
}
require dirname(__DIR__) . '/router.php';
