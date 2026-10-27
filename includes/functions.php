<?php
declare(strict_types=1);

function config(?string $key = null): mixed
{
    static $settings = null;
    $settings ??= require __DIR__ . '/config.php';
    if ($key === null) {
        return $settings;
    }
    $value = $settings;
    foreach (explode('.', $key) as $part) {
        if (!is_array($value) || !array_key_exists($part, $value)) {
            throw new InvalidArgumentException('Unknown configuration key: ' . $key);
        }
        $value = $value[$part];
    }
    return $value;
}

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function url(string $path = ''): string
{
    return config('base_url') . '/' . ltrim($path, '/');
}

function redirect(string $path, int $status = 303): never
{
    header('Location: ' . url($path), true, $status);
    exit;
}

function json_for_html(mixed $value): string
{
    return json_encode($value, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS
        | JSON_HEX_QUOT | JSON_THROW_ON_ERROR);
}

function db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $pdo = new PDO(config('database.dsn'), config('database.user'),
            config('database.password'), [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_TIMEOUT => 3,
            ]);
        if ($pdo->getAttribute(PDO::ATTR_DRIVER_NAME) === 'mysql') { $pdo->exec("SET time_zone = '+00:00'"); }
    }
    return $pdo;
}

function csrf_token(): string
{
    return $_SESSION['csrf'] ??= bin2hex(random_bytes(32));
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function flash(string $key, mixed $value): void
{
    $_SESSION['flash'][$key] = $value;
}

function take_flash(string $key, mixed $default = null): mixed
{
    $value = $_SESSION['flash'][$key] ?? $default;
    unset($_SESSION['flash'][$key]);
    return $value;
}

// Keep raw text in storage and escape at output. Do not HTML-encode twice.
function input_text(array $source, string $key, int $max = 2000): string
{
    $value = $source[$key] ?? '';
    if (!is_string($value)) {
        throw new InvalidArgumentException('Please check the submitted fields.');
    }
    $value = trim(str_replace(["\r\n", "\r"], "\n", $value));
    if (strlen($value) > $max || preg_match('//u', $value) !== 1
        || preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', $value)) {
        throw new InvalidArgumentException('A field contains invalid or excessive text.');
    }
    return $value;
}

function valid_email(string $email): bool
{
    return strlen($email) <= 254 && !preg_match('/[\r\n]/', $email)
        && filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function valid_phone(string $phone): bool
{
    $digits = preg_replace('/\D/', '', $phone);
    return preg_match('/^\+?[0-9 ()-]+$/D', $phone) === 1
        && strlen($digits) >= 7 && strlen($digits) <= 15;
}

function reject_unknown_fields(array $post, array $allowed): void
{
    if (array_diff(array_keys($post), $allowed)) {
        throw new InvalidArgumentException('Unexpected form fields. Please reload the form.');
    }
}

// Call before form-specific validation and persistence. Every attempt counts.
function guard_submission(array $post): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
        throw new InvalidArgumentException('Please submit the form.');
    }
    $token = $post['csrf'] ?? null;
    if (!is_string($token) || !isset($_SESSION['csrf'])
        || !hash_equals($_SESSION['csrf'], $token)) {
        throw new InvalidArgumentException('Your session expired. Reload and try again.');
    }
    $now = time();
    $cutoff = $now - config('booking.rate_window_seconds');
    $attempts = array_values(array_filter($_SESSION['form_attempts'] ?? [],
        static fn (int $time): bool => $time > $cutoff));
    $_SESSION['form_attempts'] = $attempts;
    if (count($attempts) >= config('booking.rate_limit')) {
        throw new InvalidArgumentException('Please wait ten minutes before trying again.');
    }
    $_SESSION['form_attempts'][] = $now;
    if (input_text($post, 'website_url', 500) !== '') {
        throw new InvalidArgumentException('Unable to accept this submission.');
    }
    $verifier = config('security.captcha_verifier');
    if ($verifier !== null && (!is_callable($verifier) || $verifier($post) !== true)) {
        throw new InvalidArgumentException('Verification failed. Please try again.');
    }
}

function valid_booking_slot(string $date, string $time, string $timezone): bool
{
    if (!in_array($timezone, config('booking.timezones'), true)
        || !in_array($time, config('booking.time_slots'), true)) {
        return false;
    }
    $zone = new DateTimeZone($timezone);
    $slot = DateTimeImmutable::createFromFormat('!Y-m-d H:i', $date . ' ' . $time, $zone);
    $errors = DateTimeImmutable::getLastErrors();
    if (!$slot || ($errors !== false && ($errors['warning_count'] || $errors['error_count']))
        || $slot->format('Y-m-d H:i') !== $date . ' ' . $time) {
        return false;
    }
    $now = new DateTimeImmutable('now', $zone);
    $last = $now->setTime(23, 59, 59)->modify('+' . config('booking.max_days_ahead') . ' days');
    return $slot > $now && $slot <= $last
        && (!config('booking.block_weekends') || (int) $slot->format('N') < 6);
}

function log_failure(string $category, string $reference): void
{
    // Do not write submitted PII, credentials, or SMTP transcripts to logs.
    $line = gmdate('c') . ' ' . preg_replace('/[^a-zA-Z0-9_-]/', '', $category)
        . ' reference=' . preg_replace('/[^a-zA-Z0-9_-]/', '', $reference) . PHP_EOL;
    $file = config('logs_path') . '/mail_errors.log';
    if (@file_put_contents($file, $line, FILE_APPEND | LOCK_EX) === false) {
        error_log(trim($line));
    }
}

function data(string $name): array
{
    static $cache = [];
    if (!preg_match('/^[a-z_]+$/D', $name)) { throw new InvalidArgumentException('Invalid data file'); }
    return $cache[$name] ??= require dirname(__DIR__) . '/data/' . $name . '.php';
}

function icon(string $name): string
{
    $paths = [
        'web'=>'<rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 9h18M7 6.5h.01M10 6.5h.01M8 13l-2 2 2 2m8-4 2 2-2 2m-3-5-2 7"/>',
        'code'=>'<path d="m8 5-7 7 7 7m8-14 7 7-7 7M14 3l-4 18"/>',
        'cloud'=>'<path d="M6 18a5 5 0 0 1-1-10 7 7 0 0 1 13-1 5.5 5.5 0 0 1 0 11M12 12v9m-3-6 3-3 3 3"/>',
        'shield'=>'<path d="m12 2 9 4v6c0 5-9 10-9 10S3 17 3 12V6zM8 12l3 3 5-6"/>',
        'design'=>'<rect x="3" y="3" width="18" height="18" rx="3"/><path d="M3 9h18M9 9v12m4-5 2-3 3 5"/>',
        'compass'=>'<circle cx="12" cy="12" r="9"/><path d="m16 8-2 6-6 2 2-6z"/>',
        'database'=>'<ellipse cx="12" cy="5" rx="8" ry="3"/><path d="M4 5v14c0 4 16 4 16 0V5M4 12c0 4 16 4 16 0"/>',
        'pulse'=>'<path d="M2 12h5l3-8 4 16 3-8h5"/>',
        'arrow'=>'<path d="M4 12h16m-6-6 6 6-6 6"/>',
        'check'=>'<path d="m5 12 4 4L19 6"/>',
    ];
    return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . ($paths[$name] ?? $paths['code']) . '</svg>';
}

function demo_url(string $service = ''): string
{
    return url('book-demo') . ($service ? '?service=' . rawurlencode($service) : '');
}

function page_start(string $key, string $active = '', array $crumbs = [], array $metadata = []): void
{
    $GLOBALS['page_key'] = $key;
    $seo = $metadata ?: (data('seo')[$key] ?? data('seo')['404']);
    require __DIR__ . '/header.php';
}

function page_end(): void { $key = $GLOBALS['page_key'] ?? ''; require __DIR__ . '/footer.php'; }

function project_url(string $service = ''): string
{
    return url('contact') . ($service !== '' ? '?service=' . rawurlencode($service) : '');
}

function page_hero(string $eyebrow, string $heading, string $description): void
{
    echo '<section class="page-hero"><div class="container"><p class="eyebrow">' . e($eyebrow)
        . '</p><h1>' . e($heading) . '</h1><p class="lead">' . e($description) . '</p></div></section>';
}

function service_cards(int $headingLevel = 3): void
{
    $heading = $headingLevel === 2 ? 'h2' : 'h3';
    foreach (data('services') as $slug => $service) {
        echo '<article class="service-card reveal"><span class="icon-box">' . icon($service['icon']) . '</span><' . $heading . '><a href="' . e(url('services/' . $slug)) . '">' . e($service['name']) . '</a></' . $heading . '><p>' . e($service['summary']) . '</p><div class="card-links"><a class="text-link" href="' . e(url('services/' . $slug)) . '">Explore service ' . icon('arrow') . '</a><a class="small-link" href="' . e(project_url($slug)) . '">Discuss this service</a></div></article>';
    }
}

function faqs(array $items): void
{
    echo '<section class="section"><div class="container narrow"><p class="eyebrow">GOOD QUESTIONS</p><h2>A little more clarity.</h2><div class="faq-list">';
    foreach ($items as [$question, $answer]) {
        echo '<details><summary>' . e($question) . '</summary><p>' . e($answer) . '</p></details>';
    }
    echo '</div></div></section>';
    $schema = ['@context'=>'https://schema.org','@type'=>'FAQPage','mainEntity'=>array_map(static fn($item)=>['@type'=>'Question','name'=>$item[0],'acceptedAnswer'=>['@type'=>'Answer','text'=>$item[1]]], $items)];
    echo '<script type="application/ld+json" nonce="' . e($GLOBALS['csp_nonce']) . '">' . json_for_html($schema) . '</script>';
}

function project_visual(string $category): void
{
    $concepts = [
        'web' => ['SERVICE WEBSITE', 'Find the right service.', ['Explore services', 'View the approach', 'Send an enquiry']],
        'software' => ['OPERATIONS PORTAL', 'Every request, accounted for.', ['New request', 'Review & approve', 'Ready for delivery']],
        'cloud' => ['RELEASE WORKFLOW', 'A reviewable route to release.', ['Build & test', 'Review changes', 'Deploy & monitor']],
        'design' => ['TEAM WORKSPACE', 'Know what needs you next.', ['Your priorities', 'Team progress', 'Upcoming reviews']],
    ];
    [$label, $heading, $steps] = $concepts[$category] ?? $concepts['web'];
    echo '<div class="concept-visual"><div class="concept-bar"><span class="concept-mark" aria-hidden="true">S<span>■</span></span><span>' . e($label) . '</span><span class="pill">Concept</span></div><div class="concept-body"><strong>' . e($heading) . '</strong><ol>';
    foreach ($steps as $step) { echo '<li>' . e($step) . '</li>'; }
    echo '</ol><p>Illustrative interface · proposed workflow</p></div></div>';
}

function case_cards(array $cases): void
{
 foreach($cases as $case){
  echo '<article class="project-card">';
  if($case['image_path'])echo '<img class="published-case-image" src="'.e(url($case['image_path'])).'" alt="'.e($case['image_alt']).'" loading="lazy">';
  echo '<div class="project-copy"><p class="eyebrow">CASE STUDY</p><h3><a href="'.e(url('case-studies/'.$case['slug'])).'">'.e($case['title']).'</a></h3><p>'.e($case['summary']).'</p><a class="text-link" href="'.e(url('case-studies/'.$case['slug'])).'">Read case study '.icon('arrow').'</a></div></article>';
 }
}

function related_links(array $links): void
{
    echo '<section class="section surface"><div class="container narrow"><p class="eyebrow">CONTINUE EXPLORING</p><h2>Related services and guides.</h2><ul class="related-links">';
    foreach ($links as [$path, $label]) {
        echo '<li><a href="' . e(url($path)) . '">' . e($label) . ' ' . icon('arrow') . '</a></li>';
    }
    echo '</ul></div></section>';
}

function product_mockup(string $slug): void
{
    $nexus = $slug === 'semraa-nexus-db';
    echo '<div class="product-screen" aria-label="Illustrative ' . ($nexus ? 'database' : 'team workflow') . ' dashboard"><div class="screen-top"><span class="screen-dots">● ● ●</span><span>' . ($nexus ? 'NEXUS / WORKSPACE' : 'WORK PULSE / OVERVIEW') . '</span></div><div class="screen-body"><div class="screen-side"><b>ST<span>■</span></b><i></i><i></i><i></i><i></i></div><div class="screen-content"><div class="screen-title">' . ($nexus ? 'Your data. Connected.' : 'A good day to make progress.') . '<span class="pill">Concept</span></div><div class="screen-stats"><div><small>' . ($nexus ? 'Data sources' : 'Tasks planned') . '</small><strong>' . ($nexus ? '08' : '24') . '</strong></div><div><small>' . ($nexus ? 'Collections' : 'In progress') . '</small><strong>' . ($nexus ? '32' : '08') . '</strong></div><div><small>' . ($nexus ? 'Workspace' : 'Completed') . '</small><strong>' . ($nexus ? '01' : '16') . '</strong></div></div><div class="chart"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></div><div class="screen-row"><span class="status-dot"></span><span>' . ($nexus ? 'Business records' : 'Website review') . '</span><span>Overview</span></div><div class="screen-row"><span class="status-dot"></span><span>' . ($nexus ? 'Reporting workspace' : 'Product planning') . '</span><span>Workspace</span></div></div></div><p class="mockup-caption">Illustrative interface · sample data</p></div>';
}

date_default_timezone_set(config('timezone'));
$secure = parse_url(config('base_url'), PHP_URL_SCHEME) === 'https';
if (config('environment') === 'production' && !$secure) {
    throw new RuntimeException('Production APP_URL must use HTTPS.');
}
ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
session_name('semraa_session');
session_set_cookie_params([
    'lifetime' => 0,
    'path' => (parse_url(config('base_url'), PHP_URL_PATH) ?: '') . '/',
    'secure' => $secure,
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();
$GLOBALS['csp_nonce'] = bin2hex(random_bytes(16));
header('Content-Type: text/html; charset=UTF-8');
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
$analyticsHosts = config('analytics.ga4_id') !== '' ? ' https://www.googletagmanager.com https://www.google-analytics.com https://region1.google-analytics.com' : '';
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'nonce-" . $GLOBALS['csp_nonce'] . "'" . $analyticsHosts . "; "
    . "style-src 'self' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; "
    . "img-src 'self' data:" . $analyticsHosts . "; frame-src https://www.google.com; connect-src 'self'" . $analyticsHosts . "; "
    . "object-src 'none'; base-uri 'self'; form-action 'self'; frame-ancestors 'none'");
require_once __DIR__ . '/seo-policy.php';
$seoSettings = require __DIR__ . '/config.php';
if (!seo_indexing_enabled($seoSettings)) { header('X-Robots-Tag: noindex, nofollow'); }
header('Cache-Control: no-store');
if ($secure) {
    header('Strict-Transport-Security: max-age=31536000');
}
$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '';
$adminImageUpload = preg_match('~/admin/cases\.php$~D', $requestPath) === 1 && config('admin.password_hash') !== '' && ($_SESSION['admin_until'] ?? 0) >= time();
$maxBodyBytes = $adminImageUpload ? 5 * 1024 * 1024 : config('security.max_post_bytes');
if ((int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > $maxBodyBytes) {
    http_response_code(413);
    exit('The submitted form is too large.');
}
