<?php
// Render a saved-request state without writing a request or sending email.
declare(strict_types=1);
if (PHP_SAPI !== 'cli') { http_response_code(403); exit; }
putenv('APP_URL=http://127.0.0.1:8089');
putenv('APP_ENV=local');
require_once __DIR__ . '/../includes/functions.php';
flash('contact_success',['id'=>123,'conversion_id'=>'render-test-only']);
ob_start(); require __DIR__ . '/../contact.php'; $html = ob_get_clean();
$checks=0;
foreach ([
 str_contains($html,'Thanks for sharing your project.'),
 str_contains($html,'CONTACT-123'),
 str_contains($html,'What happens next?'),
 !str_contains($html,'id="contact-form"'),
 str_contains($html,'data-conversion="contact"'),
] as $passed) { if (!$passed) { throw new RuntimeException('Saved enquiry rendering failed'); } $checks++; }
echo "PASS: $checks saved-enquiry rendering checks (no persistence or email).\n";
