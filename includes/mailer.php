<?php
declare(strict_types=1);
require_once __DIR__ . '/functions.php';

function mail_header_text(string $value): string { return trim(str_replace(["\r","\n"], '', $value)); }

function send_message(array $recipients, string $subject, string $html, string $plain, string $replyEmail, string $replyName, array $cc = []): bool
{
    $settings = config('mail');
    $recipients = array_map('mail_header_text',$recipients);
    $cc = array_map('mail_header_text',$cc);
    $from = mail_header_text($settings['FROM_EMAIL']);
    $replyEmail = mail_header_text($replyEmail);
    $replyName = mail_header_text($replyName);
    $subject = mail_header_text($subject);
    if (!$recipients || !valid_email($from) || !valid_email($replyEmail)) { return false; }
    foreach (array_merge($recipients,$cc,[$from]) as $email) {
        if (!valid_email($email) || str_ends_with(strtolower($email),'@example.com')) { return false; }
    }
    if ($settings['SMTP_HOST'] !== '') {
        try {
            if (!class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
                foreach (['Exception','PHPMailer','SMTP'] as $file) {
                    $path = $settings['phpmailer_path'] . '/' . $file . '.php';
                    if (!is_file($path)) { throw new RuntimeException('PHPMailer unavailable'); }
                    require_once $path;
                }
            }
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP(); $mail->Host = $settings['SMTP_HOST']; $mail->Port = $settings['SMTP_PORT'];
            $mail->SMTPAuth = $settings['SMTP_USER'] !== '';
            $mail->Username = $settings['SMTP_USER']; $mail->Password = $settings['SMTP_PASS'];
            $mail->SMTPSecure = $settings['SMTP_ENCRYPTION']; $mail->Timeout = 12; $mail->CharSet = 'UTF-8';
            $mail->setFrom($from,mail_header_text($settings['FROM_NAME']));
            foreach ($recipients as $email) { $mail->addAddress($email); }
            foreach ($cc as $email) { $mail->addCC($email); }
            $mail->addReplyTo($replyEmail,$replyName);
            $mail->Subject = $subject; $mail->isHTML(true); $mail->Body = $html; $mail->AltBody = $plain;
            return $mail->send();
        } catch (Throwable $exception) {
            if (!$settings['allow_mail_fallback']) { return false; }
        }
    }
    if (!$settings['allow_mail_fallback'] || !function_exists('mail')) { return false; }
    $boundary = 'semraa_' . bin2hex(random_bytes(18));
    $encodedName = '=?UTF-8?B?' . base64_encode(mail_header_text($settings['FROM_NAME'])) . '?=';
    $headers = ['MIME-Version: 1.0','From: ' . $encodedName . ' <' . $from . '>','Reply-To: ' . $replyEmail,'Content-Type: multipart/alternative; boundary="' . $boundary . '"'];
    if ($cc) { $headers[] = 'Cc: ' . implode(', ',$cc); }
    $body = '--' . $boundary . "\r\nContent-Type: text/plain; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n" . chunk_split(base64_encode($plain))
        . '--' . $boundary . "\r\nContent-Type: text/html; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n" . chunk_split(base64_encode($html)) . '--' . $boundary . "--\r\n";
    return @mail(implode(', ',$recipients),'=?UTF-8?B?' . base64_encode($subject) . '?=',$body,implode("\r\n",$headers));
}

function email_content(string $heading, string $intro, array $rows): array
{
    $plain = $heading . "\n\n" . $intro . "\n\n";
    $html = '<!doctype html><html lang="en"><body style="margin:0;padding:24px;background:#EEF0F3;font-family:Arial,sans-serif;color:#2B3140"><table role="presentation" style="max-width:640px;width:100%;margin:auto;background:#fff;border-collapse:collapse"><tr><td style="padding:24px;background:#2B3140;color:#fff;border-top:5px solid #FF6B1A"><b>SEMRAA TECHNOLOGIES</b></td></tr><tr><td style="padding:24px"><h1 style="font-size:24px">' . e($heading) . '</h1><p>' . e($intro) . '</p><table style="width:100%;border-collapse:collapse">';
    foreach ($rows as $label=>$value) {
        $value = is_array($value) ? implode(', ',$value) : (string)$value;
        $plain .= $label . ': ' . $value . "\n";
        $html .= '<tr><th style="padding:10px 8px;text-align:left;border-bottom:1px solid #EEF0F3;vertical-align:top">' . e($label) . '</th><td style="padding:10px 8px;border-bottom:1px solid #EEF0F3;overflow-wrap:anywhere">' . nl2br(e($value)) . '</td></tr>';
    }
    $html .= '</table><p style="margin-top:24px;font-size:12px;color:#555D6B">Semraa Technologies · Tirunelveli, Tamil Nadu, India</p></td></tr></table></body></html>';
    return [$html,$plain];
}

function notify_demo(int $id, array $values): array
{
    $service = data('demo_services')[$values['service']];
    $rows = ['Request ID'=>'DEMO-' . $id,'Received (UTC)'=>gmdate('Y-m-d H:i:s'),'Service'=>$service['name'],'Full name'=>$values['name'],'Work email'=>$values['email'],'Phone'=>$values['phone'],'Company'=>$values['company'],'Job title'=>$values['job_title'],'Preferred date'=>$values['preferred_date'],'Preferred time'=>$values['preferred_time'],'Timezone'=>$values['timezone'],'Meeting mode'=>$values['mode'],'Message'=>$values['message']];
    foreach ($service['extra_fields'] as $field) { $rows[$field['label']] = $values['extra'][$field['name']] ?? ''; }
    [$html,$plain] = email_content('New demo request','Review the requirements and contact the visitor to confirm scheduling.',$rows);
    $recipients = config('mail.service_recipients')[$values['service']] ?? config('mail.DEMO_RECIPIENT_EMAIL');
    $internal = send_message((array)$recipients,'Demo request #' . $id . ': ' . $service['name'],$html,$plain,$values['email'],$values['name'],config('mail.CC'));
    [$html,$plain] = email_content('Thank you for your demo request','We have received your request. Our team will review it and follow up. Your preferred meeting time is not yet a confirmed appointment.',$rows);
    $confirmation = send_message([$values['email']],'Your Semraa demo request #' . $id,$html,$plain,config('mail.FROM_EMAIL'),config('mail.FROM_NAME'));
    if (!$internal) { log_failure('demo_internal',$id . ''); }
    if (!$confirmation) { log_failure('demo_confirmation',$id . ''); }
    return [$internal ? 'sent' : 'failed',$confirmation ? 'sent' : 'failed'];
}

function notify_contact(int $id, array $values): string
{
    [$html,$plain] = email_content('New website enquiry','A visitor submitted the contact form.', ['Project brief'=>implode(' · ', $values['brief'] ?? []),'Request ID'=>'CONTACT-' . $id,'Received (UTC)'=>gmdate('Y-m-d H:i:s'),'Name'=>$values['name'],'Email'=>$values['email'],'Phone'=>$values['phone'],'Subject'=>$values['subject'],'Message'=>$values['message']]);
    $sent = send_message(config('mail.CONTACT_RECIPIENT_EMAIL'),'Website enquiry #' . $id,$html,$plain,$values['email'],$values['name'],config('mail.CC'));
    if (!$sent) { log_failure('contact_internal',(string)$id); }
    return $sent ? 'sent' : 'failed';
}

function notify_consultation(int $id,array $values,string $startsAt): array
{
 $rows=['Reference'=>'CONSULT-'.$id,'Name'=>$values['name'],'Email'=>$values['email'],'Phone'=>$values['phone'],'Time'=>slot_label($startsAt),'Timezone'=>config('timezone'),'Duration'=>'30 minutes','Message'=>$values['message']];
 [$html,$plain]=email_content('Consultation booked','A visitor booked a published consultation time. Confirm the call or meeting details with them.',$rows);
 $internal=send_message(config('mail.CONTACT_RECIPIENT_EMAIL'),'Consultation booking #'.$id,$html,$plain,$values['email'],$values['name']);
 [$html,$plain]=email_content('Your consultation is booked','Your time is reserved. Our team will contact you with the call or meeting details. To request a change, reply to this email.',$rows);
 $visitor=send_message([$values['email']],'Your Semraa consultation #'.$id,$html,$plain,config('mail.FROM_EMAIL'),config('mail.FROM_NAME'));
 if(!$internal)log_failure('consultation_internal',(string)$id);
 if(!$visitor)log_failure('consultation_confirmation',(string)$id);
 return [$internal?'sent':'failed',$visitor?'sent':'failed'];
}
