<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') { http_response_code(403); exit; }
require_once dirname(__DIR__) . '/includes/forms.php';
$checks=0;
function check(bool $condition,string $label): void { global $checks; if (!$condition) { throw new RuntimeException('FAIL: ' . $label); } $checks++; }
$date = (new DateTimeImmutable('tomorrow'))->modify('next monday')->format('Y-m-d');
$base=['name'=>'Test User','email'=>'test@example.org','phone'=>'+91 98765 43210','company'=>'Test Company','job_title'=>'Owner','service'=>'web-development','preferred_date'=>$date,'preferred_time'=>'10:00','timezone'=>'Asia/Kolkata','mode'=>'Google Meet','message'=>'Test only','extra'=>['website_type'=>'Business website','page_count'=>'5','existing_site'=>'https://example.org','budget'=>'Help me estimate']];
[$values,$errors]=validate_demo($base); check(!$errors,'Valid complete demo accepted');
foreach (data('demo_services') as $slug=>$service) {
    $input=$base; $input['service']=$slug; $input['extra']=[];
    foreach ($service['extra_fields'] as $field) { $input['extra'][$field['name']]=match($field['type']) { 'select','radio'=>$field['options'][0], 'checkbox'=>[$field['options'][0]], 'number'=>'5', default=>$field['name']==='existing_site' ? 'https://example.org' : 'Example answer' }; }
    [, $errors]=validate_demo($input); check(!$errors,'Valid ' . $slug . ' accepted');
}
$bad=$base; $bad['extra']['website_type']='Injected option'; [, $errors]=validate_demo($bad); check(isset($errors['extra[website_type]']),'Unknown select rejected');
$bad=$base; $bad['extra']['page_count']='-1'; [, $errors]=validate_demo($bad); check(isset($errors['extra[page_count]']),'Negative count rejected');
$bad=$base; $bad['extra']['page_count']='2.5'; [, $errors]=validate_demo($bad); check(isset($errors['extra[page_count]']),'Fractional count rejected');
$bad=$base; $bad['email']="test@example.org\r\nBcc: injected@example.org"; [, $errors]=validate_demo($bad); check(isset($errors['email']),'Header injection rejected');
$bad=$base; $bad['name']=['array']; [, $errors]=validate_demo($bad); check(isset($errors['name']),'Array field rejected');
$bad=$base; $bad['extra']['existing_site']='javascript:alert(1)'; [, $errors]=validate_demo($bad); check(isset($errors['extra[existing_site]']),'Unsafe URL rejected');
foreach (['unknown','extra'] as $type) {
 $bad=$base; if ($type === 'unknown') { $bad['admin']='1'; } else { $bad['extra']['admin']='1'; }
 try { validate_demo($bad); check(false,'Unknown field rejected'); } catch (InvalidArgumentException $e) { check(true,'Unknown field rejected'); }
}
check(!valid_booking_slot('2000-01-01','10:00','Asia/Kolkata'),'Past date rejected');
check(!valid_booking_slot('2026-02-31','10:00','Asia/Kolkata'),'Invalid calendar date rejected');
check(!valid_booking_slot($date,'03:00','Asia/Kolkata'),'Unknown slot rejected');
check(!valid_booking_slot($date,'10:00','Not/AZone'),'Unknown timezone rejected');
check(!valid_booking_slot((new DateTimeImmutable('next saturday'))->format('Y-m-d'),'10:00','Asia/Kolkata'),'Weekend rejected');
check(!valid_booking_slot((new DateTimeImmutable('+200 days'))->format('Y-m-d'),'10:00','Asia/Kolkata'),'Horizon enforced');
$_SERVER['REQUEST_METHOD']='POST'; $_SESSION['form_attempts']=[];
try { guard_submission(['csrf'=>'wrong']); check(false,'CSRF rejected'); } catch (InvalidArgumentException $e) { check(true,'CSRF rejected'); }
try { guard_submission(['csrf'=>csrf_token(),'website_url'=>'bot']); check(false,'Honeypot rejected'); } catch (InvalidArgumentException $e) { check(true,'Honeypot rejected'); }
$_SESSION['form_attempts']=[];
for ($i=0;$i<3;$i++) { guard_submission(['csrf'=>csrf_token(),'website_url'=>'']); }
try { guard_submission(['csrf'=>csrf_token(),'website_url'=>'']); check(false,'Fourth attempt rejected'); } catch (InvalidArgumentException $e) { check(true,'Fourth attempt rejected'); }
check(!str_contains(e('<script>"&'),'<script>'),'HTML escaped');
foreach (data('services') as $slug=>$service) {
 $text=implode(' ',array_merge([$service['intro']],array_values($service['sections']),$service['benefits'],$service['process'],array_merge(...$service['faqs'])));
 check(count(preg_split('/\s+/u',trim($text)))>=400,$slug . ' has at least 400 words');
}
$quickContact = ['name'=>'Preview User','email'=>'preview@example.org','message'=>'We need a clearer website.'];
[$contactValues,$contactErrors] = validate_contact($quickContact);
check(!$contactErrors, 'Quick enquiry needs only name, email and message');
check($contactValues['subject'] === 'General project enquiry', 'Optional subject receives a storage-safe default');
check($contactValues['phone'] === '', 'Phone stays optional');
[$contactValues,$contactErrors] = validate_contact($quickContact + ['subject'=>'Web Development enquiry','phone'=>'+91 98765 43210']);
check(!$contactErrors && $contactValues['subject'] === 'Web Development enquiry', 'Service enquiry context is preserved');
[, $contactErrors] = validate_contact(array_replace($quickContact,['email'=>'invalid','message'=>'']));
check(isset($contactErrors['email'],$contactErrors['message']), 'Quick enquiry errors identify email and message');
try { validate_contact($quickContact + ['unexpected'=>'value']); check(false,'Unexpected enquiry field rejected'); }
catch (InvalidArgumentException $e) { check(true,'Unexpected enquiry field rejected'); }
[, $contactErrors] = validate_contact($quickContact + ['subject'=>str_repeat('x',201)]);
check(isset($contactErrors['subject']), 'Optional subject retains server length validation');
echo 'PASS: ' . $checks . " validation, security and content checks.\n";
