<?php
// Deterministic size/format derivatives of supplied logos; originals are preserved.
// Run with GD enabled: php -d extension=gd tools/prepare-assets.php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') { http_response_code(403); exit; }
if (!extension_loaded('gd')) { throw new RuntimeException('Enable the GD extension to prepare images.'); }
$directory = dirname(__DIR__) . '/assets/img/';
$icon = imagecreatefrompng($directory . 'logo-icon.png');
foreach ([16,32,180,512] as $size) {
    $image = imagecreatetruecolor($size,$size);
    imagealphablending($image,false); imagesavealpha($image,true);
    imagefill($image,0,0,imagecolorallocatealpha($image,255,255,255,127));
    imagecopyresampled($image,$icon,0,0,0,0,$size,$size,imagesx($icon),imagesy($icon));
    imagepng($image,$directory . 'favicon-' . $size . '.png',9);
    if ($size === 180) { imagepng($image,$directory . 'apple-touch-icon.png',9); }
}
$original = imagecreatefrompng($directory . 'logo-horizontal-new.png');
// Supplied 1536x1024 composition: preserve mark and lettering, trim outer padding.
$crop = imagecrop($original,['x'=>38,'y'=>285,'width'=>1440,'height'=>410]);
$header = imagecreatetruecolor(576,164);
imagefill($header,0,0,imagecolorallocate($header,255,255,255));
imagecopyresampled($header,$crop,0,0,0,0,576,164,1440,410);
imagewebp($header,$directory . 'logo-header.webp',88);
echo "Created four favicon sizes, Apple icon and compact WebP header logo.\n";
