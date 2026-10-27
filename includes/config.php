<?php
declare(strict_types=1);

// Production defaults target the confirmed domain. Local previews set APP_URL explicitly.
// Set secrets in hosting configuration; never commit real credentials.
$baseUrl = rtrim(getenv('APP_URL') ?: (getenv('RENDER_EXTERNAL_URL') ?: 'https://semraa.in'), '/');
$environment = getenv('APP_ENV') ?: ($baseUrl === 'https://semraa.in' ? 'production' : 'local');
return [
    'preview_mode' => getenv('APP_PREVIEW') === '1',
    'environment' => $environment,
    'base_url' => $baseUrl,
    'timezone' => 'Asia/Kolkata',
    'company' => [
        'name' => 'Semraa Technologies',
        'address' => getenv('COMPANY_ADDRESS') ?: '[verified full address, Tenkasi area, Tamil Nadu, PIN]',
        'city' => getenv('COMPANY_CITY') ?: 'Tenkasi area',
        'region' => 'Tamil Nadu',
        'country' => 'IN',
        'postal_code' => getenv('COMPANY_POSTAL_CODE') ?: '',
        'phone' => getenv('COMPANY_PHONE') ?: '[number]',
        'email' => getenv('COMPANY_EMAIL') ?: '[email]',
        'latitude' => null,
        'longitude' => null,
        'opening_hours' => [],
        'social' => [],
    ],
    'database' => [
        'dsn' => getenv('DB_DSN') ?: 'mysql:host=127.0.0.1;dbname=semraa;charset=utf8mb4',
        'user' => getenv('DB_USER') ?: 'semraa_app',
        'password' => getenv('DB_PASSWORD') ?: '',
    ],
    'mail' => [
        'DEMO_RECIPIENT_EMAIL' => array_values(array_filter(array_map('trim', explode(',', getenv('DEMO_RECIPIENT_EMAIL') ?: 'demo@example.com')))),
        'CONTACT_RECIPIENT_EMAIL' => array_values(array_filter(array_map('trim', explode(',', getenv('CONTACT_RECIPIENT_EMAIL') ?: 'hello@example.com')))),
        'CC' => [],
        'service_recipients' => [],
        'FROM_EMAIL' => getenv('FROM_EMAIL') ?: 'website@example.com',
        'FROM_NAME' => 'Semraa Technologies',
        'SMTP_HOST' => getenv('SMTP_HOST') ?: '',
        'SMTP_PORT' => (int) (getenv('SMTP_PORT') ?: 587),
        'SMTP_USER' => getenv('SMTP_USER') ?: '',
        'SMTP_PASS' => getenv('SMTP_PASS') ?: '',
        'SMTP_ENCRYPTION' => getenv('SMTP_ENCRYPTION') ?: 'tls',
        // Put PHPMailer src files here if SMTP is enabled; Composer is optional.
        'phpmailer_path' => dirname(__DIR__) . '/vendor/PHPMailer/src',
        'allow_mail_fallback' => false,
    ],
    'booking' => [
        'block_weekends' => true,
        'max_days_ahead' => 180,
        // Slots are always interpreted in the selected timezone.
        'time_slots' => ['10:00', '11:00', '14:00', '15:00', '16:00'],
        'timezones' => ['Asia/Kolkata'],
        'modes' => ['Google Meet', 'Zoom', 'Phone', 'On-site'],
        'rate_limit' => 3,
        'rate_window_seconds' => 600,
    ],
    'security' => [
        'max_post_bytes' => 32768,
        // Optional callable: function (array $post): bool. Fail closed if enabled.
        'captcha_verifier' => null,
    ],
    'analytics' => [
        'ga4_id' => getenv('GA4_ID') ?: '',
        'search_console_verification' => getenv('SEARCH_CONSOLE_VERIFICATION') ?: '',
    ],
    'admin' => ['password_hash' => getenv('ADMIN_PASSWORD_HASH') ?: ''],
    'logs_path' => dirname(__DIR__) . '/logs',
];
