<?php
// Format/size derivative of the approved original brand image for link previews.
declare(strict_types=1);
if (PHP_SAPI !== 'cli') { http_response_code(403); exit; }
$source = imagecreatefrompng(__DIR__ . '/../assets/img/logo-horizontal-new.png');
$target = imagecreatetruecolor(1200, 800);
imagecopyresampled($target, $source, 0, 0, 0, 0, 1200, 800, imagesx($source), imagesy($source));
imageinterlace($target, true);
imagejpeg($target, __DIR__ . '/../assets/img/social-preview.jpg', 85);
echo filesize(__DIR__ . '/../assets/img/social-preview.jpg') . " bytes\n";
