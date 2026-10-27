<?php
require_once __DIR__ . '/includes/features.php';
$errors=[];$values=[];
if(($_SERVER['REQUEST_METHOD']??'GET')==='POST'){
 try { guard_submission($_POST);[$values,$errors]=validate_consultation($_POST); }
 catch(InvalidArgumentException $e){$errors['_form']=$e->getMessage();}
 if(!$errors){
  try{$booking=book_consultation(db(),$values);}
  catch(InvalidArgumentException $e){$errors['slot_id']=$e->getMessage();}
  catch(Throwable $e){log_failure('consultation_storage',bin2hex(random_bytes(6)));$errors['_form']='We could not save the booking. Your details are preserved; please try again later.';}
 }
 if(!$errors){
  $states=['failed','failed'];
  try{require_once __DIR__.'/includes/mailer.php';$states=notify_consultation($booking['id'],$values,$booking['starts_at']);db()->prepare('UPDATE consultation_bookings SET internal_email_status=?,confirmation_email_status=? WHERE id=?')->execute([$states[0],$states[1],$booking['id']]);}
  catch(Throwable $e){log_failure('consultation_mail',(string)$booking['id']);}
  $_SESSION['csrf']=bin2hex(random_bytes(32));
  flash('consultation_success',$booking+['email_sent'=>$states[1]==='sent','conversion_id'=>bin2hex(random_bytes(12))]);
 }else{flash('consultation_values',preserve_safe_values($_POST));flash('consultation_errors',$errors);}
 redirect('consultation');
}
$values=take_flash('consultation_values',[]);$errors=take_flash('consultation_errors',[]);$success=take_flash('consultation_success');
try{$slots=available_slots(db());}catch(Throwable $e){$slots=[];}
page_start('consultation','contact',[['Consultation','consultation']]);
page_hero('LET’S TALK THROUGH THE NEXT STEP','Book a consultation.','Choose a published 30-minute slot to discuss your project. All times are shown in India Standard Time (Asia/Kolkata).');
?>
<section class="section"><div class="container form-layout"><div>
<?php if($success): ?><div class="enquiry-success" role="status" tabindex="-1" id="enquiry-success" data-conversion="consultation" data-conversion-id="<?= e($success['conversion_id']) ?>"><span class="success-mark"><?= icon('check') ?></span><h2>Your time is reserved.</h2><p><strong><?= e(slot_label($success['starts_at'])) ?></strong><br>30 minutes · India Standard Time</p><p>Reference: <strong>CONSULT-<?= (int)$success['id'] ?></strong>. Our team will contact you with call or meeting details.</p><?php if(!$success['email_sent']): ?><p class="note">The booking is saved, but the confirmation email could not be sent. Keep your reference and contact our team if needed.</p><?php endif; ?><a class="text-link" href="<?= e(url('contact')) ?>">Contact our team <?= icon('arrow') ?></a></div>
<?php else: ?><?php errors_summary($errors); ?>
<?php if(!$slots): ?><div class="form-card"><p class="eyebrow">AVAILABILITY</p><h2>No consultation times are published right now.</h2><p>Send an enquiry with your preferred timing, and our team can discuss arrangements with you.</p><a class="button" href="<?= e(url('contact')) ?>">Discuss Your Project <?= icon('arrow') ?></a></div>
<?php else: ?><form method="post" class="form-card" id="consultation-form" action="<?= e(url('consultation')) ?>"><?php hidden_security(); ?><fieldset id="slot_id" tabindex="-1" <?= isset($errors['slot_id']) ? 'aria-invalid="true" aria-describedby="slot-error"' : '' ?>><legend class="booking-legend">Choose an available time *</legend><?php if(isset($errors['slot_id'])): ?><p class="field-error" id="slot-error"><?= e($errors['slot_id']) ?></p><?php endif; ?><p class="note">Published availability only. A time is reserved when your booking is saved.</p><?php $lastDate='';foreach($slots as $slot): $date=(new DateTimeImmutable($slot['starts_at'],new DateTimeZone('UTC')))->setTimezone(new DateTimeZone(config('timezone')));if($lastDate!==$date->format('Y-m-d')):if($lastDate!=='')echo '</div></div>'; $lastDate=$date->format('Y-m-d');?><div class="availability-day"><h2><?= e($date->format('l, j F Y')) ?></h2><div class="slot-grid"><?php endif; ?><label class="slot-choice"><input type="radio" name="slot_id" required value="<?= (int)$slot['id'] ?>" <?= (string)($values['slot_id']??'')===(string)$slot['id']?'checked':'' ?>><span><?= e($date->format('g:i A')) ?></span></label><?php endforeach;if($lastDate!=='')echo '</div></div>'; ?></fieldset><div class="form-grid"><?php field('name','Full name','text',$values,$errors,true,[],'autocomplete="name" maxlength="120"');field('email','Email','email',$values,$errors,true,[],'autocomplete="email" maxlength="254"');field('phone','Phone','tel',$values,$errors,false,[],'autocomplete="tel" maxlength="40"');field('message','What would you like to discuss?','textarea',$values,$errors); ?></div><div class="actions"><button class="button" type="submit" <?= config('preview_mode')?'disabled':'' ?>>Book this consultation <?= icon('arrow') ?></button></div><p class="form-note">We use these details to arrange your consultation. <a href="<?= e(url('privacy')) ?>">Privacy policy</a>. Please contact us to request a cancellation or change.</p></form><?php endif; ?><?php endif; ?>
</div><aside class="booking-side"><h2>A focused first conversation.</h2><ul class="check-list"><li>Share your business goal and current situation.</li><li>Discuss the questions needed to shape a useful scope.</li><li>Agree a practical next step.</li></ul><p class="note">This calendar is managed by Semraa. Product demos remain separate, and no external calendar is connected.</p><a class="text-link" href="<?= e(url('contact')) ?>">Prefer an enquiry first? <?= icon('arrow') ?></a></aside></div></section>
<?php page_end(); ?>
