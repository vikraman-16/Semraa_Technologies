<?php
declare(strict_types=1);

// Shared on-disk counters survive a browser clearing its session cookie.
// Multi-instance production hosts should additionally enforce an edge/WAF limit.
function consume_rate_limit(string $key, int $limit, int $window, string $directory): bool
{
    if ($limit < 1 || $window < 1) { throw new InvalidArgumentException('Invalid rate limit.'); }
    if (!is_dir($directory) && !@mkdir($directory, 0700, true) && !is_dir($directory)) {
        throw new RuntimeException('Rate limit storage is unavailable.');
    }
    $file = $directory . '/' . hash('sha256', $key) . '.json';
    $handle = @fopen($file, 'c+');
    if ($handle === false) { throw new RuntimeException('Rate limit storage is unavailable.'); }
    @chmod($file, 0600);
    try {
        if (!flock($handle, LOCK_EX)) { throw new RuntimeException('Rate limit storage is unavailable.'); }
        $stored = stream_get_contents($handle, 65536);
        $attempts = json_decode($stored ?: '[]', true);
        if (!is_array($attempts)) { throw new RuntimeException('Rate limit storage is invalid.'); }
        $cutoff = time() - $window;
        $attempts = array_values(array_filter($attempts, static fn($time) => is_int($time) && $time > $cutoff));
        if (count($attempts) >= $limit) { return false; }
        $attempts[] = time();
        $encoded = json_encode($attempts, JSON_THROW_ON_ERROR);
        if (!rewind($handle) || !ftruncate($handle, 0) || fwrite($handle, $encoded) !== strlen($encoded) || !fflush($handle)) {
            throw new RuntimeException('Rate limit storage is unavailable.');
        }
        return true;
    } finally {
        flock($handle, LOCK_UN);
        fclose($handle);
    }
}
