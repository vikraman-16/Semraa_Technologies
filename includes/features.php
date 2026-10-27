<?php
declare(strict_types=1);
require_once __DIR__ . '/forms.php';
require_once __DIR__ . '/case-content.php';

function brief_options(): array {
 return ['goal'=>['New website','Improve an existing website','Custom business software','Design or infrastructure review','Not sure yet'],
 'timeline'=>['Exploring options','Within 1 month','Within 3 months','Flexible'],
 'budget'=>['Need guidance','Under ₹50,000','₹50,000–₹1 lakh','₹1–5 lakh','Over ₹5 lakh']];
}
function validate_brief(array $post): array {
 $errors=[]; $brief=[];
 foreach (brief_options() as $name=>$options) {
  try { $value=input_text($post,'brief_'.$name,100); }
  catch (InvalidArgumentException $e) { $errors['brief_'.$name]='Choose a valid option.'; continue; }
  if ($value!=='' && !in_array($value,$options,true)) $errors['brief_'.$name]='Choose a valid option.';
  if ($value!=='') $brief[$name]=$value;
 }
 return [$brief,$errors];
}
function save_contact(PDO $pdo,array $values,array $brief=[]): int {
 $pdo->beginTransaction();
 try {
  $pdo->prepare('INSERT INTO contact_requests (name,email,phone,subject,message) VALUES (?,?,?,?,?)')->execute([$values['name'],$values['email'],$values['phone'],$values['subject'],$values['message']]);
  $id=(int)$pdo->lastInsertId();
  if ($brief) $pdo->prepare('INSERT INTO enquiry_briefs (contact_id,brief_json) VALUES (?,?)')->execute([$id,json_encode($brief,JSON_THROW_ON_ERROR)]);
  $pdo->commit();return $id;
 } catch (Throwable $e) { if($pdo->inTransaction())$pdo->rollBack();throw $e; }
}
function validate_consultation(array $post): array {
 reject_unknown_fields($post,['csrf','website_url','captcha_token','slot_id','name','email','phone','message']);
 $errors=[];$values=clean_common($post,['name'=>120,'email'=>254,'phone'=>40,'message'=>2000],$errors);
 $values['slot_id']=filter_var($post['slot_id']??'',FILTER_VALIDATE_INT,['options'=>['min_range'=>1]]) ?: 0;
 if(!$values['slot_id'])$errors['slot_id']='Choose an available consultation time.';
 if($values['name']==='')$errors['name']='Full name is required.';
 if(!valid_email($values['email']))$errors['email']='Enter a valid email address.';
 if($values['phone']!==''&&!valid_phone($values['phone']))$errors['phone']='Enter a valid phone number.';
 return [$values,$errors];
}
function book_consultation(PDO $pdo,array $values): array {
 $pdo->beginTransaction();
 try {
  // An atomic claim prevents two concurrent visitors from booking the same slot.
  $claim=$pdo->prepare("UPDATE consultation_slots SET status='booked' WHERE id=? AND status='available' AND starts_at>?");
  $claim->execute([$values['slot_id'],gmdate('Y-m-d H:i:s',time()+3600)]);
  if($claim->rowCount()!==1)throw new InvalidArgumentException('That time is no longer available. Please choose another slot.');
  $slot=$pdo->prepare('SELECT starts_at FROM consultation_slots WHERE id=?');$slot->execute([$values['slot_id']]);$starts=$slot->fetchColumn();
  $pdo->prepare('INSERT INTO consultation_bookings (slot_id,name,email,phone,message) VALUES (?,?,?,?,?)')->execute([$values['slot_id'],$values['name'],$values['email'],$values['phone'],$values['message']]);
  $id=(int)$pdo->lastInsertId();$pdo->commit();return ['id'=>$id,'starts_at'=>$starts];
 } catch(Throwable $e){if($pdo->inTransaction())$pdo->rollBack();throw $e;}
}
function publish_slot(PDO $pdo,string $localTime): void {
 $zone=new DateTimeZone(config('timezone'));
 $date=DateTimeImmutable::createFromFormat('!Y-m-d\TH:i',$localTime,$zone);
 if(!$date||$date->format('Y-m-d\TH:i')!==$localTime||!in_array($date->format('i'),['00','30'],true)||$date->getTimestamp()<time()+3600||$date->getTimestamp()>time()+180*86400)throw new InvalidArgumentException('Choose a time at least one hour ahead, within 180 days, on the hour or half hour.');
 $pdo->prepare('INSERT INTO consultation_slots (starts_at) VALUES (?)')->execute([$date->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d H:i:s')]);
}
function slot_label(string $utc): string { return (new DateTimeImmutable($utc,new DateTimeZone('UTC')))->setTimezone(new DateTimeZone(config('timezone')))->format('D, j M Y · g:i A'); }
function available_slots(PDO $pdo): array {
 $q=$pdo->prepare("SELECT id,starts_at FROM consultation_slots WHERE status='available' AND starts_at>? AND starts_at<? ORDER BY starts_at LIMIT 200");
 $q->execute([gmdate('Y-m-d H:i:s',time()+3600),gmdate('Y-m-d H:i:s',time()+180*86400)]);return $q->fetchAll();
}
function published_cases(): array { return case_records(); }
function validate_case(array $post): array {
 $errors=[];$values=clean_common($post,['slug'=>100,'title'=>180,'summary'=>350,'client_name'=>160,'challenge'=>5000,'solution'=>5000,'outcome'=>5000,'project_url'=>1000,'image_alt'=>250],$errors);
 $values['status']=input_text($post,'status',20);$values['evidence_approved']=($post['evidence_approved']??'')==='1'?1:0;
 if(!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/D',$values['slug']))$errors['slug']='Use lowercase words separated by hyphens.';
 if(!in_array($values['status'],['draft','published'],true))$errors['status']='Choose draft or published.';
 if($values['title']==='')$errors['title']='Title is required.';
 if($values['project_url']!==''&&(!filter_var($values['project_url'],FILTER_VALIDATE_URL)||parse_url($values['project_url'],PHP_URL_SCHEME)!=='https'))$errors['project_url']='Use a complete HTTPS project URL.';
 if($values['status']==='published'){
  foreach(['summary','challenge','solution','outcome'] as $field)if($values[$field]==='')$errors[$field]='Complete this before publishing.';
  if(!$values['evidence_approved'])$errors['evidence_approved']='Confirm that the information is accurate and approved for publication.';
 }
 return [$values,$errors];
}
function admin_require(): void {
 header('X-Robots-Tag: noindex, nofollow');
 if(config('admin.password_hash')===''){http_response_code(503);exit('Admin access is disabled until ADMIN_PASSWORD_HASH is configured.');}
 if(($_SESSION['admin_until']??0)<time()){unset($_SESSION['admin_until']);redirect('admin/login.php');}
}
function admin_csrf(): void {
 if(!hash_equals(csrf_token(),input_text($_POST,'csrf',64)))throw new InvalidArgumentException('Session expired. Please reload.');
}
function admin_navigation(): void {
 echo '<nav class="admin-navigation" aria-label="Administration">';
 foreach(['admin/inbox.php'=>'Enquiry inbox','admin/calendar.php'=>'Consultation calendar','admin/cases.php'=>'Case studies','admin/demos.php'=>'Demo requests'] as $path=>$label)echo '<a href="'.e(url($path)).'">'.e($label).'</a>';
 echo '<form method="post" action="'.e(url('admin/inbox.php')).'">'.csrf_field().'<input type="hidden" name="action" value="logout"><button class="button button-outline button-small">Sign out</button></form></nav>';
}

function case_upload(array $file): string
{
 if(($file['error']??UPLOAD_ERR_NO_FILE)!==UPLOAD_ERR_OK)throw new InvalidArgumentException('The screenshot upload failed. Choose an image under 4 MB.');
 if(($file['size']??0)>4*1024*1024||!is_uploaded_file($file['tmp_name']))throw new InvalidArgumentException('Choose a valid image under 4 MB.');
 $info=@getimagesize($file['tmp_name']);
 if(!$info||!in_array($info[2],[IMAGETYPE_JPEG,IMAGETYPE_PNG,IMAGETYPE_WEBP],true)||$info[0]>3000||$info[1]>3000)throw new InvalidArgumentException('Use a JPEG, PNG or WebP up to 3000 × 3000 pixels.');
 if(!extension_loaded('gd'))throw new InvalidArgumentException('Enable PHP GD on the server before uploading screenshots.');
 $source=match($info[2]){IMAGETYPE_JPEG=>imagecreatefromjpeg($file['tmp_name']),IMAGETYPE_PNG=>imagecreatefrompng($file['tmp_name']),IMAGETYPE_WEBP=>imagecreatefromwebp($file['tmp_name'])};
 if(!$source)throw new InvalidArgumentException('The image could not be read.');
 $scale=min(1,1600/$info[0]);$width=max(1,(int)round($info[0]*$scale));$height=max(1,(int)round($info[1]*$scale));
 $target=imagecreatetruecolor($width,$height);imagefill($target,0,0,imagecolorallocate($target,255,255,255));imagecopyresampled($target,$source,0,0,0,0,$width,$height,$info[0],$info[1]);
 $directory=dirname(__DIR__).'/assets/img/case-studies';if(!is_dir($directory)&&!mkdir($directory,0755,true))throw new RuntimeException('Image storage is unavailable.');
 $relative='assets/img/case-studies/'.bin2hex(random_bytes(16)).'.jpg';
 if(!imagejpeg($target,dirname(__DIR__).'/'.$relative,88))throw new RuntimeException('Image could not be saved.');
 return $relative;
}
