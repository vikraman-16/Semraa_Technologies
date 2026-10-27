<?php
// PHP built-in server router for local development only.
declare(strict_types=1);
$config = require __DIR__ . '/includes/config.php';
$base = rtrim(parse_url($config['base_url'],PHP_URL_PATH) ?: '', '/');
$path = rawurldecode(parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH) ?: '/');
if ($base !== '' && ($path === $base || str_starts_with($path,$base . '/'))) { $path = substr($path,strlen($base)); }
$path = trim($path,'/');
if (preg_match('~(^|/)(?:\.|includes|data|logs|database|vendor|tests|tools)(?:/|$)|\.\.|\\\\~i',$path) || preg_match('~\.(sql|log|ini|bak|md)$~i',$path) || $path === 'router.php') {
    http_response_code(403); exit('Forbidden');
}
if (str_starts_with($path,'assets/') || $path === 'site.webmanifest') {
    $file = __DIR__ . '/' . $path;
    if (is_file($file)) {
        $types=['css'=>'text/css','js'=>'application/javascript','jpg'=>'image/jpeg','jpeg'=>'image/jpeg','png'=>'image/png','webp'=>'image/webp','svg'=>'image/svg+xml','ico'=>'image/x-icon','webmanifest'=>'application/manifest+json'];
        $extension = pathinfo($file,PATHINFO_EXTENSION);
        if (!isset($types[$extension])) { http_response_code(403); exit; }
        header('Content-Type: ' . $types[$extension]); header('X-Content-Type-Options: nosniff'); readfile($file); return true;
    }
}
$routes=[''=>'index.php','index.php'=>'index.php','sitemap.xml'=>'sitemap.php','robots.txt'=>'robots.php','admin'=>'admin/inbox.php','admin/login.php'=>'admin/login.php','admin/demos.php'=>'admin/demos.php','admin/inbox.php'=>'admin/inbox.php','admin/calendar.php'=>'admin/calendar.php','admin/cases.php'=>'admin/cases.php'];
foreach (['about','services','products','portfolio','blog','contact','consultation','book-demo','booking-success','privacy','terms'] as $page) { $routes[$page]=$page . '.php'; $routes[$page . '.php']=$page . '.php'; }
if (isset($routes[$path])) { require __DIR__ . '/' . $routes[$path]; return true; }
if (preg_match('~^(services|locations|blog|case-studies)/([a-z0-9-]+)$~D',$path,$matches)) {
    $_GET['slug']=$matches[2]; require __DIR__ . '/' . ['services'=>'service.php','locations'=>'location.php','blog'=>'post.php','case-studies'=>'case-study.php'][$matches[1]]; return true;
}
require __DIR__ . '/404.php';
