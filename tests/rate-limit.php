<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') { http_response_code(403); exit; }
require __DIR__.'/../includes/rate-limit.php';
$directory=__DIR__.'/../.runtime/rate-test-'.bin2hex(random_bytes(8));
$checks=0;
function rate_check(bool $condition): void { global $checks; if(!$condition)throw new RuntimeException('Rate limit regression.');$checks++; }
try {
 rate_check(consume_rate_limit('test-client',2,60,$directory));
 rate_check(consume_rate_limit('test-client',2,60,$directory));
 $_SESSION=[]; // Clearing visitor cookies/session state cannot clear the shared counter.
 rate_check(!consume_rate_limit('test-client',2,60,$directory));
 rate_check(consume_rate_limit('different-client',2,60,$directory));
 $file=$directory.'/'.hash('sha256','test-client').'.json';
 file_put_contents($file,json_encode([time()-61]));
 rate_check(consume_rate_limit('test-client',2,60,$directory));
 file_put_contents($file,'corrupt');
 try { consume_rate_limit('test-client',2,60,$directory); rate_check(false); }
 catch(RuntimeException $e) { rate_check(true); }
 echo "PASS: $checks persistent rate-limit checks.\n";
} finally {
 foreach(glob($directory.'/*.json')?:[] as $file)unlink($file);
 if(is_dir($directory))rmdir($directory);
}
