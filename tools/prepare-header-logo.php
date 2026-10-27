<?php
// Header-sized, alpha-preserving derivative. Originals remain unchanged.
if (PHP_SAPI !== 'cli') { http_response_code(403); exit; }
$source = imagecreatefrompng(__DIR__ . '/../assets/img/logo-header-transparent.png');
$target = imagecreatetruecolor(576, 164);
imagealphablending($target, false);
imagesavealpha($target, true);
imagefill($target, 0, 0, imagecolorallocatealpha($target, 0, 0, 0, 127));
imagecopyresampled($target, $source, 0, 0, 0, 0, 576, 164, imagesx($source), imagesy($source));
imagewebp($target, __DIR__ . '/../assets/img/logo-nav.webp', 90);
echo 'Header logo: ' . filesize(__DIR__ . '/../assets/img/logo-nav.webp') . " bytes\n";
