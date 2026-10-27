<?php
require_once __DIR__ . '/includes/functions.php';
$slug = is_string($_GET['slug'] ?? null) ? $_GET['slug'] : '';
$service = data('services')[$slug] ?? null;
if (!$service) { require __DIR__ . '/404.php'; exit; }
$experience = data('service_experience')[$slug];
page_start('service:' . $slug,'services',[['Services','services'],[$service['name'],'services/' . $slug]]);
page_hero($service['tag'],$service['headline'],$service['intro']);
echo '<div class="container page-actions"><a class="button" href="' . e(project_url($slug)) . '">Discuss Your Project ' . icon('arrow') . '</a><a class="text-link" href="#service-details">View deliverables ' . icon('arrow') . '</a><a class="text-link" href="' . e(demo_url($slug)) . '">Request a ' . e($service['name']) . ' discussion ' . icon('arrow') . '</a></div>';
?>
<nav class="container service-nav" aria-label="On this service page"><a href="#service-details">Deliverables</a><a href="#service-approach">Approach</a><a href="#service-process">Process</a><a href="#service-scope">Scope &amp; pricing</a><a href="#service-faq">FAQs</a></nav>
<section class="section" id="service-details"><div class="container content-layout"><div><p class="eyebrow">WHAT WE CAN HELP WITH</p><h2>A useful scope starts here.</h2><p class="lead"><?= e($experience['fit']) ?></p><p class="note">These are starting points for discussion. Final deliverables are agreed in your project scope.</p><dl class="deliverable-list"><?php foreach ($experience['deliverables'] as $i=>[$title,$description]): ?><div><dt><span aria-hidden="true">0<?= $i+1 ?></span><?= e($title) ?></dt><dd><?= e($description) ?></dd></div><?php endforeach; ?></dl><div id="service-approach" class="service-approach"><p class="eyebrow">THE DETAILS</p><h2>How we approach the work.</h2><?php foreach ($service['sections'] as $i=>$text): ?><details class="service-detail"><summary><?= e($i) ?></summary><p><?= e($text) ?></p></details><?php endforeach; ?></div></div><aside class="aside-card"><span class="icon-box"><?= icon($service['icon']) ?></span><h2>Your project, in context.</h2><ul class="check-list"><?php foreach ($service['benefits'] as $benefit): ?><li><?= e($benefit) ?></li><?php endforeach; ?></ul><a class="button" href="<?= e(project_url($slug)) ?>">Discuss Your Project <?= icon('arrow') ?></a><p class="note">Share your goal first. No meeting slot or technical specification is needed for an enquiry.</p></aside></div></section>
<section class="section surface" id="service-process"><div class="container"><p class="eyebrow">FROM IDEA TO DELIVERY</p><h2>A clear path forward.</h2><div class="process-grid"><?php foreach ($service['process'] as $i=>$step): ?><article class="process-step"><strong>0<?= $i+1 ?></strong><h3><?= e($step) ?></h3></article><?php endforeach; ?></div></div></section>
<section class="section" id="service-scope"><div class="container scope-layout"><div><p class="eyebrow">SCOPE &amp; PRICING</p><h2>Understand what shapes the project.</h2><p>We discuss the requirements before proposing a price or delivery schedule. A smaller first release may be appropriate when priorities are still taking shape.</p></div><div><h3>The main factors</h3><ul class="check-list"><?php foreach ($experience['scope'] as $factor): ?><li><?= e($factor) ?></li><?php endforeach; ?></ul><a class="text-link" href="<?= e(project_url($slug)) ?>">Tell us what you have in mind <?= icon('arrow') ?></a></div></div></section>
<div id="service-faq">
<?php
faqs($service['faqs']);
echo '</div>';
$guides = [
 'web-development'=>'why-tirunelveli-businesses-need-a-website-2026',
 'software-development'=>'choose-software-company-tirunelveli',
 'cloud-devops'=>'cloud-migration-readiness-checklist',
 'cybersecurity'=>'vapt-explained-for-small-businesses',
 'ui-ux-design'=>'design-better-enquiry-forms',
 'it-consulting'=>'choose-software-company-tirunelveli',
];
$guideSlug = $guides[$slug];
$relatedSlug = $slug === 'it-consulting' ? 'software-development' : 'it-consulting';
related_links([
 ['blog/' . $guideSlug, data('blog')[$guideSlug]['title']],
 ['services/' . $relatedSlug, data('services')[$relatedSlug]['name']],
]);
page_end(); ?>
