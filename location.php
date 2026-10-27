<?php
require_once __DIR__ . '/includes/functions.php';
$slug = is_string($_GET['slug'] ?? null) ? $_GET['slug'] : '';
$location = data('locations')[$slug] ?? null;
if (!$location) { require __DIR__ . '/404.php'; exit; }
page_start('location:' . $slug,'',[[$location['name'],'locations/' . $slug]]);
page_hero('LOCAL CONTEXT. PRACTICAL TECHNOLOGY.',$location['headline'],$location['intro']);
?>
<section class="section"><div class="container content-layout"><div class="prose"><?php foreach ($location['sections'] as $title=>$text): ?><h2><?= e($title) ?></h2><p><?= e($text) ?></p><?php endforeach; ?><p>Explore our <a href="<?= e(url('services/web-development')) ?>">web development approach</a>, <a href="<?= e(url('services/software-development')) ?>">custom software services</a> and <a href="<?= e(url('services/it-consulting')) ?>">IT consulting process</a>.</p></div><aside class="aside-card"><h2>Let’s talk about your business.</h2><p>Share what you want to improve and your preferred meeting arrangements.</p><a class="button" href="<?= e(project_url()) ?>">Discuss Your Project <?= icon('arrow') ?></a><p class="note">Based in the Tenkasi area. Service arrangements confirmed for each engagement.</p></aside></div></section>
<section class="section surface"><div class="container"><p class="eyebrow">ACROSS THE REGION</p><h2>Explore our service areas.</h2><div class="location-links"><?php foreach (data('locations') as $otherSlug=>$other): ?><a href="<?= e(url('locations/' . $otherSlug)) ?>"><?= e($other['name']) ?></a><?php endforeach; ?></div></div></section>
<?php page_end(); ?>
