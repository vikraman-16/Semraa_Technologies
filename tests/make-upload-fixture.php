<?php
if(PHP_SAPI!=='cli')exit;
$image=imagecreatetruecolor(160,160);
for($x=0;$x<160;$x++){for($y=0;$y<160;$y++){imagesetpixel($image,$x,$y,random_int(0,16777215));}}
imagepng($image,dirname(__DIR__).'/.runtime/test-upload.png');
