<?php
require_once dirname(__DIR__) . '/includes/features.php';
header('X-Robots-Tag: noindex, nofollow');
if (config('admin.password_hash') === '' || ($_SESSION['admin_until'] ?? 0) < time()) { unset($_SESSION['admin_until']); redirect('admin/login.php'); }
$errors = [];
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    try {
        $token = input_text($_POST,'csrf',64);
        if (!hash_equals(csrf_token(),$token)) { throw new InvalidArgumentException('Session expired.'); }
        if (($_POST['action'] ?? '') === 'logout') {
            unset($_SESSION['admin_until']); session_regenerate_id(true); redirect('admin/login.php');
        }
        $id = filter_var($_POST['id'] ?? '',FILTER_VALIDATE_INT,['options'=>['min_range'=>1]]);
        $status = input_text($_POST,'status',20);
        if (!$id || !in_array($status,['new','contacted','closed'],true)) { throw new InvalidArgumentException('Invalid update.'); }
        db()->prepare('UPDATE demo_requests SET status=? WHERE id=?')->execute([$status,$id]);
        redirect('admin/demos.php');
    } catch (Throwable $exception) { $errors['_form'] = 'The update could not be saved.'; }
}
$status = is_string($_GET['status'] ?? null) && in_array($_GET['status'],['new','contacted','closed'],true) ? $_GET['status'] : '';
$service = is_string($_GET['service'] ?? null) && isset(data('demo_services')[$_GET['service']]) ? $_GET['service'] : '';
$page = max(1,(int)($_GET['page'] ?? 1)); $offset = ($page-1)*50;
$where = []; $params = [];
if ($status) { $where[] = 'status=?'; $params[] = $status; }
if ($service) { $where[] = 'service_slug=?'; $params[] = $service; }
$query = 'SELECT * FROM demo_requests' . ($where ? ' WHERE ' . implode(' AND ',$where) : '') . ' ORDER BY id DESC';
$export = ($_GET['export'] ?? '') === 'csv';
try {
    $statement = db()->prepare($query . ($export ? '' : ' LIMIT 51 OFFSET ' . $offset)); $statement->execute($params);
    if ($export) {
        header('Content-Type: text/csv; charset=UTF-8'); header('Content-Disposition: attachment; filename="semraa-demos.csv"');
        $stream = fopen('php://output','w'); $headerWritten = false;
        while ($row = $statement->fetch()) {
            if (!$headerWritten) { fputcsv($stream,array_keys($row),',','"',''); $headerWritten=true; }
            $safe = array_map(static fn($v)=>is_string($v) && preg_match('/^[\s]*[=+@-]/u',$v) ? "'" . $v : $v,$row);
            fputcsv($stream,$safe,',','"','');
        }
        fclose($stream); exit;
    }
    $rows = $statement->fetchAll(); $hasNext = count($rows)>50; $rows=array_slice($rows,0,50);
} catch (Throwable $exception) { $rows=[]; $hasNext=false; $errors['_form']='The database is unavailable. Check the server configuration.'; }
page_start('admin','');
?>
<section class="section"><div class="container"><h1>Demo requests</h1><?php admin_navigation(); ?><?php errors_summary($errors); ?><div class="admin-bar"><form method="get"><select name="status" aria-label="Filter by status"><option value="">All statuses</option><?php foreach (['new','contacted','closed'] as $option): ?><option <?= $option === $status ? 'selected' : '' ?>><?= e($option) ?></option><?php endforeach; ?></select><select name="service" aria-label="Filter by service"><option value="">All services</option><?php foreach (data('demo_services') as $slug=>$item): ?><option value="<?= e($slug) ?>" <?= $service === $slug ? 'selected' : '' ?>><?= e($item['name']) ?></option><?php endforeach; ?></select><button class="button button-small">Filter</button></form><a class="text-link" href="<?= e(url('admin/demos.php') . '?' . http_build_query(['status'=>$status,'service'=>$service,'export'=>'csv'])) ?>">Export matching CSV</a><form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="logout"><button class="button button-outline button-small">Sign out</button></form></div><div class="table-wrap"><table><thead><tr><th>Reference</th><th>Contact</th><th>Request</th><th>Answers</th><th>Status</th></tr></thead><tbody><?php foreach ($rows as $row): ?><tr><td>DEMO-<?= (int)$row['id'] ?><br><?= e($row['created_at']) ?> UTC</td><td><?= e($row['name']) ?><br><?= e($row['email']) ?><br><?= e($row['phone']) ?><br><?= e($row['company']) ?></td><td><?= e(data('demo_services')[$row['service_slug']]['name'] ?? $row['service_slug']) ?><br><?= e($row['preferred_date'] . ' ' . $row['preferred_time']) ?><br><?= e($row['timezone']) ?><br><?= e($row['mode']) ?><p>Internal mail: <?= e($row['internal_email_status']) ?><br>Confirmation: <?= e($row['confirmation_email_status']) ?></p></td><td><details><summary>View requirements</summary><p><?= nl2br(e($row['message'])) ?></p><?php $extra=json_decode($row['extra_data'],true) ?: []; foreach (data('demo_services')[$row['service_slug']]['extra_fields'] ?? [] as $field): ?><p><strong><?= e($field['label']) ?>:</strong> <?= e(is_array($extra[$field['name']] ?? '') ? implode(', ',$extra[$field['name']]) : ($extra[$field['name']] ?? '')) ?></p><?php endforeach; ?></details></td><td><form method="post"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$row['id'] ?>"><select name="status" aria-label="Status for request <?= (int)$row['id'] ?>"><?php foreach (['new','contacted','closed'] as $option): ?><option <?= $option === $row['status'] ? 'selected' : '' ?>><?= e($option) ?></option><?php endforeach; ?></select><button class="button button-small">Save</button></form></td></tr><?php endforeach; ?></tbody></table></div><?php if (!$rows && !$errors): ?><p>No matching demo requests.</p><?php endif; ?><div class="actions"><?php if ($page > 1): ?><a class="button button-outline" href="<?= e(url('admin/demos.php') . '?' . http_build_query(['status'=>$status,'service'=>$service,'page'=>$page-1])) ?>">Previous page</a><?php endif; ?><?php if ($hasNext): ?><a class="button" href="<?= e(url('admin/demos.php') . '?' . http_build_query(['status'=>$status,'service'=>$service,'page'=>$page+1])) ?>">Next page</a><?php endif; ?></div></div></section>
<?php page_end(); ?>
