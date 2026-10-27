<?php
declare(strict_types=1);

// One indexing policy for HTML, robots responses and launch checks.
function seo_indexing_enabled(array $settings): bool
{
    $host = strtolower((string) parse_url($settings['base_url'], PHP_URL_HOST));
    return $settings['environment'] === 'production'
        && !($settings['preview_mode'] ?? false)
        && parse_url($settings['base_url'], PHP_URL_SCHEME) === 'https'
        && $host !== ''
        && !in_array($host, ['localhost', '127.0.0.1', '[::1]'], true)
        && !str_ends_with($host, '.trycloudflare.com');
}

function seo_excluded_page(string $key): bool
{
    return in_array($key, ['404', 'booking-success', 'admin', 'consultation'], true);
}
