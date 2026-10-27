<?php
require_once __DIR__ . '/includes/functions.php';
$slug = is_string($_GET['slug'] ?? null) ? $_GET['slug'] : '';
$post = data('blog')[$slug] ?? null;
if (!$post) { require __DIR__ . '/404.php'; exit; }
page_start('post:' . $slug,'blog',[['Insights','blog'],[$post['title'],'blog/' . $slug]]);
page_hero($post['category'],$post['title'],$post['excerpt']);
?>
<section class="section"><article class="container narrow prose"><p class="article-meta">Semraa Technologies · <time datetime="<?= e($post['date']) ?>"><?= e(date('j F Y',strtotime($post['date']))) ?></time> · <?= e($post['read']) ?></p><?php foreach ($post['sections'] as $title=>$text): ?><h2><?= e($title) ?></h2><p><?= e($text) ?></p><?php endforeach; ?><div class="actions"><a class="button" href="<?= e(project_url()) ?>">Discuss Your Project <?= icon('arrow') ?></a></div><p><a href="<?= e(url('blog')) ?>">Browse more technology articles</a></p></article></section>
<?php
$relatedServices = [
 'why-tirunelveli-businesses-need-a-website-2026'=>'web-development',
 'vapt-explained-for-small-businesses'=>'cybersecurity',
 'choose-software-company-tirunelveli'=>'software-development',
 'cloud-migration-readiness-checklist'=>'cloud-devops',
 'design-better-enquiry-forms'=>'ui-ux-design',
];
$relatedSlug = $relatedServices[$slug] ?? 'it-consulting';
related_links([
 ['services/' . $relatedSlug, data('services')[$relatedSlug]['name']],
 ['portfolio', 'Explore our project concepts'],
]);
page_end(); ?>
