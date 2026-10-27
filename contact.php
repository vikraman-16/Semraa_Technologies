<?php
require_once __DIR__ . '/includes/features.php';
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $errors = []; $values = preserve_safe_values($_POST);
    try {
        guard_submission($_POST);
        [$values, $errors] = validate_contact($_POST);
        [$brief,$briefErrors] = validate_brief($_POST); $errors = array_merge($errors,$briefErrors);
        foreach (brief_options() as $briefName=>$options) { $values['brief_'.$briefName] = is_string($_POST['brief_'.$briefName] ?? null) ? $_POST['brief_'.$briefName] : ''; }
    } catch (InvalidArgumentException $exception) { $errors['_form'] = $exception->getMessage(); }
    catch (Throwable $exception) { $errors['_form'] = 'Verification is temporarily unavailable. Please try again later.'; }
    if (!$errors) {
        try {
            $id = save_contact(db(),$values,$brief);
        } catch (Throwable $exception) { log_failure('contact_storage',bin2hex(random_bytes(6))); $errors['_form'] = 'We could not save your enquiry. Your answers are kept below. Please try again later.'; }
    }
    if (!$errors) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
        try { require_once __DIR__ . '/includes/mailer.php'; $state = notify_contact($id,$values + ['brief'=>$brief]); db()->prepare('UPDATE contact_requests SET internal_email_status=? WHERE id=?')->execute([$state,$id]); }
        catch (Throwable $exception) { log_failure('contact_notification',(string)$id); }
        flash('contact_success',['id'=>$id,'conversion_id'=>bin2hex(random_bytes(12))]);
    } else { flash('contact_values',$values); flash('contact_errors',$errors); }
    redirect('contact');
}
$values = take_flash('contact_values',[]); $errors = take_flash('contact_errors',[]); $success = take_flash('contact_success');
$requestedService = is_string($_GET['service'] ?? null) ? $_GET['service'] : '';
if (!$errors && isset(data('services')[$requestedService])) { $values['subject'] ??= data('services')[$requestedService]['name'] . ' enquiry'; }
page_start('contact','contact',[['Contact','contact']]);
page_hero('LET’S CONNECT','Let’s discuss your project.','A new idea, an existing challenge or a question about our services—we’d like to hear what you have in mind.');
?>
<section class="section"><div class="container form-layout"><div>
<?php if ($success): ?>
<div class="enquiry-success" role="status" tabindex="-1" id="enquiry-success" data-conversion="contact" data-conversion-id="<?= e($success['conversion_id']) ?>"><span class="success-mark"><?= icon('check') ?></span><p class="eyebrow">ENQUIRY SAVED</p><h2>Thanks for sharing your project.</h2><p>Your reference is <strong>CONTACT-<?= (int)$success['id'] ?></strong>. Our team will review your message and follow up using the details you provided.</p><div class="success-next"><h3>What happens next?</h3><ol><li>We review your goals and any project context.</li><li>We get in touch to clarify requirements and discuss the next step.</li></ol></div><a class="text-link" href="<?= e(url('services')) ?>">Explore our services <?= icon('arrow') ?></a></div>
<?php else: ?>
<?php errors_summary($errors); ?>
<form class="form-card enquiry-form" id="contact-form" method="post" action="<?= e(url('contact')) ?>"><div class="enquiry-form-heading"><span class="icon-box"><?= icon('compass') ?></span><div><p class="eyebrow">START WITH YOUR GOAL</p><h2>Tell us what you have in mind.</h2></div></div><p class="form-intro">You don’t need a technical brief or a meeting slot. Share the problem you want to solve, and we’ll take it from there.</p><?php hidden_security(); ?><div class="form-grid"><?php field('name','Full name','text',$values,$errors,true,[],'autocomplete="name" maxlength="120"'); field('email','Email','email',$values,$errors,true,[],'autocomplete="email" maxlength="254"'); field('message','What would you like to build or improve?','textarea',$values,$errors,true,[],isset($errors['message']) ? '' : 'aria-describedby="message-hint"'); ?></div><p class="field-hint" id="message-hint">For example: a website for your services, fewer manual approvals, or a clearer view of your business data.</p><details class="enquiry-context" <?= (isset($values['subject']) && $values['subject'] !== 'General project enquiry') || isset($errors['phone']) || isset($errors['subject']) ? 'open' : '' ?>><summary>Add project context <span>(optional)</span></summary><div class="form-grid"><?php field('subject','Project or service','text',$values,$errors,false,[],'maxlength="200" placeholder="e.g. Website redesign"'); field('phone','Phone','tel',$values,$errors,false,[],'autocomplete="tel" maxlength="40" inputmode="tel"'); ?></div></details><details class="enquiry-context brief-builder" <?= array_filter(array_keys($errors),static fn($key)=>str_starts_with($key,'brief_')) ? 'open' : '' ?>><summary>Build a short project brief <span>(optional)</span></summary><p class="note">Share what you know. These preferences help the discussion; they are not a quote or a commitment.</p><div class="form-grid"><?php foreach (brief_options() as $briefName=>$options): ?><?php field('brief_'.$briefName,['goal'=>'What do you need?','timeline'=>'Preferred timeline','budget'=>'Budget range'][$briefName],'select',$values,$errors,false,$options); ?><?php endforeach; ?></div><p class="brief-summary" role="status" aria-live="polite"></p></details><div class="enquiry-submit"><button class="button" type="submit" <?= config('preview_mode') ? 'disabled' : '' ?>>Send enquiry <?= icon('arrow') ?></button><span>No meeting booking required.</span></div><p class="form-note">Fields marked * are required. We’ll use your details to respond to your enquiry. <a href="<?= e(url('privacy')) ?>">Privacy policy</a>.</p></form>
<?php endif; ?>
</div><aside class="enquiry-aside"><div class="enquiry-next"><p class="eyebrow">A CLEAR NEXT STEP</p><h2>From message to a practical plan.</h2><ol><li><strong>Share the context</strong><p>Tell us what is working, what is slowing you down and what you want to change.</p></li><li><strong>Clarify the requirements</strong><p>We discuss the priorities and any questions needed to understand the scope.</p></li><li><strong>Agree the next step</strong><p>Discuss a suitable approach, deliverables and arrangements before committing to a project.</p></li></ol></div><div class="contact-info"><p class="eyebrow">BASED NEAR TENKASI</p><h2><?= e(config('company.name')) ?></h2><p><?= e(str_contains(config('company.address'), '[') ? config('company.city') . ', ' . config('company.region') . ', India' : config('company.address')) ?></p><?php if (valid_phone(config('company.phone'))): ?><a class="contact-direct" href="tel:<?= e(preg_replace('/[^+0-9]/','',config('company.phone'))) ?>"><?= e(config('company.phone')) ?></a><?php endif; ?><?php if (valid_email(config('company.email'))): ?><a class="contact-direct" href="mailto:<?= e(config('company.email')) ?>"><?= e(config('company.email')) ?></a><?php endif; ?><a class="text-link" href="<?= e(url('consultation')) ?>">Choose a consultation time <?= icon('arrow') ?></a><a class="text-link" href="<?= e(url('products')) ?>">Looking for a product demo? <?= icon('arrow') ?></a></div><details class="contact-map"><summary>See our general area</summary><iframe class="map-frame" title="Map of the Tenkasi area; not an exact office pin" src="https://www.google.com/maps?q=Tenkasi%2C%20Tamil%20Nadu%2C%20India&amp;output=embed" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe><p class="note">Area overview. Confirm meeting arrangements with our team.</p></details></aside></div></section>
<?php page_end(); ?>
