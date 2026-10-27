<?php
require_once __DIR__ . '/includes/forms.php';
$services = data('demo_services');
$demoPages = data('demo_pages');
$landingSlug = $demoLandingSlug ?? ($_GET['demo_page'] ?? '');
if ($landingSlug !== '' && (!is_string($landingSlug) || !isset($demoPages[$landingSlug], $services[$landingSlug]))) { require __DIR__.'/404.php'; exit; }
$landing = $landingSlug !== '' ? $demoPages[$landingSlug] : null;
$formPath = 'book-demo' . ($landing ? '/' . $landingSlug : '');
$formServices = $landing ? [$landingSlug=>$services[$landingSlug]] : $services;
$legacyService = is_string($_GET['service'] ?? null) ? $_GET['service'] : '';
if ($legacyService === 'ui-ux-design') { $legacyService = 'ui-ux'; }
if (!$landing && ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET' && isset($demoPages[$legacyService])) {
    redirect('book-demo/'.$legacyService, 301);
}
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $values = preserve_safe_values($_POST); $errors = [];
    try { guard_submission($_POST); [$values,$errors] = validate_demo($_POST); if ($landing && ($values['service'] ?? '') !== $landingSlug) { $errors['service'] = 'Choose the service for this page.'; } }
    catch (InvalidArgumentException $exception) { $errors['_form'] = $exception->getMessage(); }
    catch (Throwable $exception) { $errors['_form'] = 'Verification is temporarily unavailable. Please try again later.'; }
    if (!$errors) {
        try {
            $statement = db()->prepare('INSERT INTO demo_requests (service_slug,name,email,phone,company,job_title,preferred_date,preferred_time,timezone,mode,extra_data,message) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)');
            $statement->execute([$values['service'],$values['name'],$values['email'],$values['phone'],$values['company'],$values['job_title'],$values['preferred_date'],$values['preferred_time'],$values['timezone'],$values['mode'],json_encode($values['extra'],JSON_THROW_ON_ERROR),$values['message']]);
            $id = (int)db()->lastInsertId();
        } catch (Throwable $exception) {
            log_failure('demo_storage',bin2hex(random_bytes(6)));
            $errors['_form'] = 'We could not save your request. Your answers are kept below. Please try again later.';
        }
    }
    if (!$errors) {
        // Once persisted, email failure must never turn the submission into an error.
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
        $mailStates = ['failed','failed'];
        try {
            require_once __DIR__ . '/includes/mailer.php';
            $mailStates = notify_demo($id,$values);
            db()->prepare('UPDATE demo_requests SET internal_email_status=?, confirmation_email_status=? WHERE id=?')->execute([$mailStates[0],$mailStates[1],$id]);
        } catch (Throwable $exception) { log_failure('demo_notification',(string)$id); }
        $_SESSION['booking_summary'] = ['id'=>$id,'service'=>$services[$values['service']]['name'],'name'=>$values['name'],'preferred_date'=>$values['preferred_date'],'preferred_time'=>$values['preferred_time'],'timezone'=>$values['timezone'],'mode'=>$values['mode'],'email_sent'=>$mailStates[1] === 'sent','expires'=>time()+1800,'conversion_id'=>bin2hex(random_bytes(12))];
        redirect('booking-success');
    }
    flash('demo_values',$values); flash('demo_errors',$errors);
    redirect($formPath);
}
$values = take_flash('demo_values',[]); $errors = take_flash('demo_errors',[]);
$queryService = is_string($_GET['service'] ?? null) ? $_GET['service'] : '';
if ($queryService === 'ui-ux-design') { $queryService = 'ui-ux'; }
$selected = $landingSlug !== '' ? $landingSlug : ($values['service'] ?? (isset($services[$queryService]) ? $queryService : 'web-development'));
if (!is_string($selected) || !isset($services[$selected])) { $selected = 'web-development'; }
$values['service'] = $selected; $values['timezone'] ??= config('timezone');
$crumbs = [['Book a Demo','book-demo']];
if ($landing) { $crumbs[] = [$services[$landingSlug]['name'],$formPath]; }
page_start($landing ? 'demo:'.$landingSlug : 'book-demo','',$crumbs);
page_hero('LET’S MAKE IT TANGIBLE',$landing['heading'] ?? 'Book a software or IT demo.',$landing['intro'] ?? 'Tell us a little about your business. We’ll shape the conversation around what matters to you.');
?>
<?php if ($landing): ?>
<section class="section"><div class="container content-layout"><div class="prose"><?php foreach ($landing['sections'] as $title=>$text): ?><h2><?= e($title) ?></h2><p><?= e($text) ?></p><?php endforeach; ?><details class="service-detail"><summary><?= e($landing['question']) ?></summary><p><?= e($landing['answer']) ?></p></details></div><aside class="aside-card"><h2>Prepare your discussion.</h2><p>Use the tailored form below to explain your requirements. Preferred meeting times are confirmed after review.</p><a class="button" href="#request-form">Start your request <?= icon('arrow') ?></a><p><a class="text-link" href="<?= e(url($landing['related'])) ?>">Explore <?= e($services[$landingSlug]['name']) ?></a></p><a class="text-link" href="<?= e(url('book-demo')) ?>">Choose another service or product</a></aside></div></section>
<?php else: ?>
<section class="section"><div class="container"><h2>Choose your service or product.</h2><p>Each page explains what to prepare and includes questions tailored to your request.</p><div class="grid-3"><?php foreach ($services as $slug=>$item): ?><article class="service-card"><h3><a href="<?= e(demo_url($slug)) ?>"><?= e($item['name']) ?></a></h3><p><?= e($item['description']) ?></p><a class="text-link" href="<?= e(demo_url($slug)) ?>">View request page <?= icon('arrow') ?></a></article><?php endforeach; ?></div></div></section>
<?php endif; ?>
<section class="section surface" id="request-form"><div class="container form-layout"><div>
<?php errors_summary($errors); ?>
<?php if (!$landing): ?><noscript><form method="get" action="<?= e(url('book-demo')) ?>" class="form-card"><label for="load-service">First, select the service to load its questions</label><select id="load-service" name="service"><?php foreach ($services as $slug=>$service): ?><option value="<?= e($slug) ?>" <?= $slug === $selected ? 'selected' : '' ?>><?= e($service['name']) ?></option><?php endforeach; ?></select><button type="submit" class="button button-small">Load service questions</button></form></noscript><?php endif; ?>
<form class="form-card" id="demo-form" method="post" action="<?= e(url($formPath)) ?>">
<?php hidden_security(); ?>
<ol class="step-indicators" aria-label="Request progress"><li aria-current="step"><b>1</b> Your requirements</li><li><b>2</b> Your details</li><li><b>3</b> Let’s meet</li></ol><progress class="step-progress" max="3" value="1" aria-label="Step 1 of 3"></progress>
<p class="form-note">Fields marked * are required.</p>
<fieldset class="form-step" data-step="0"><legend tabindex="-1">What can we help you build?</legend>
<?php field('service','Service or product','select',$values,$errors,true,array_map(static fn($s)=>$s['name'],$formServices)); ?>
<p class="note" id="service-description"><?= e($services[$selected]['description']) ?></p><div class="form-grid" id="extra-fields"><?php extra_fields($services[$selected],$values,$errors); ?></div></fieldset>
<fieldset class="form-step" data-step="1"><legend tabindex="-1">A little about you.</legend><div class="form-grid">
<?php field('name','Full name','text',$values,$errors,true,[],'autocomplete="name" maxlength="120"'); field('email','Work email','email',$values,$errors,true,[],'autocomplete="email" maxlength="254"'); field('phone','Phone','tel',$values,$errors,true,[],'autocomplete="tel" maxlength="40"'); field('company','Company','text',$values,$errors,true,[],'autocomplete="organization" maxlength="160"'); field('job_title','Job title','text',$values,$errors,false,[],'autocomplete="organization-title" maxlength="120"'); ?>
</div></fieldset>
<fieldset class="form-step" data-step="2"><legend tabindex="-1">Find a time to connect.</legend><p class="note">Choose a preferred slot. We’ll confirm availability with you.</p><div class="form-grid">
<?php field('preferred_date','Preferred date','date',$values,$errors,true,[],'min="' . date('Y-m-d') . '" max="' . date('Y-m-d',strtotime('+' . config('booking.max_days_ahead') . ' days')) . '"'); field('preferred_time','Preferred time','select',$values,$errors,true,config('booking.time_slots')); field('timezone','Timezone','select',$values,$errors,true,config('booking.timezones')); field('mode','Meeting mode','select',$values,$errors,true,config('booking.modes')); field('message','Anything else we should know?','textarea',$values,$errors); ?>
</div></fieldset>
<div class="step-actions"><button class="button button-outline" type="button" id="previous-step" hidden>Back</button><button class="button" type="button" id="next-step" hidden>Continue <?= icon('arrow') ?></button><button class="button" type="submit" id="submit-demo">Send demo request <?= icon('arrow') ?></button></div>
<p class="form-note">By sending this request, you ask us to contact you about it. Read our <a href="<?= e(url('privacy')) ?>">privacy policy</a>. A requested slot is not a confirmed appointment.</p>
</form></div><aside class="booking-side"><span class="icon-box"><?= icon('compass') ?></span><h2>A conversation.<br>A clearer next step.</h2><p>You bring the context. We’ll bring the right questions.</p><ol><li><strong>Tell us what matters.</strong><br>Choose a service and share your requirements.</li><li><strong>We review your request.</strong><br>Our team follows up to confirm the conversation.</li><li><strong>Explore the possibilities.</strong><br>Discuss scope, fit and a practical way forward.</li></ol><p class="note">Prefer a general enquiry? <a class="text-link" href="<?= e(url('contact')) ?>">Get in touch</a></p></aside></div></section>
<script type="application/json" id="demo-config" nonce="<?= e($GLOBALS['csp_nonce']) ?>"><?= json_for_html(['services'=>$formServices,'blockWeekends'=>config('booking.block_weekends'),'hasErrors'=>(bool)$errors]) ?></script>
<script src="<?= e(url('assets/js/demo-form.min.js')) ?>" defer></script>
<?php page_end(); ?>
