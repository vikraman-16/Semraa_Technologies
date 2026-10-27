<?php
require_once dirname(__DIR__) . '/includes/forms.php';
header('X-Robots-Tag: noindex, nofollow');
if (config('admin.password_hash') === '') { http_response_code(503); exit('Admin access is disabled. Configure ADMIN_PASSWORD_HASH to enable it.'); }
$error = '';
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    try {
        guard_submission($_POST);
        $password = input_text($_POST,'password',512);
        if (password_verify($password,config('admin.password_hash'))) {
            session_regenerate_id(true); $_SESSION['admin_until'] = time()+1800;
            $_SESSION['csrf'] = bin2hex(random_bytes(32)); redirect('admin/inbox.php');
        }
        $error = 'Unable to sign in with those details.';
    } catch (Throwable $exception) { $error = 'Unable to sign in. Check your session or try again later.'; }
}
page_start('admin','');
?>
<section class="section"><div class="container narrow"><h1>Semraa administration</h1><?php if ($error): ?><p class="error-summary" role="alert"><?= e($error) ?></p><?php endif; ?><form method="post" class="form-card" action="<?= e(url('admin/login.php')) ?>"><?php hidden_security(); ?><?php field('password','Admin password','password',[],[],true,[],'autocomplete="current-password"'); ?><button class="button" type="submit">Sign in</button></form></div></section>
<?php page_end(); ?>
