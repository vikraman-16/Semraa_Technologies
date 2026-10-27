<?php
require_once __DIR__.'/includes/features.php';
$slug=is_string($_GET['slug']??null)?$_GET['slug']:'';$case=null;
foreach(published_cases() as $record){if($record['slug']===$slug){$case=$record;break;}}
if(!$case){require __DIR__.'/404.php';exit;}
$metadata=['title'=>$case['title'].' | Semraa Case Study','description'=>$case['summary'],'path'=>'case-studies/'.$case['slug'],'case'=>$case];
if($case['image_path']){$metadata['image']=$case['image_path'];$metadata['image_alt']=$case['image_alt'];}
page_start('case:'.$slug,'portfolio',[['Our Work','portfolio'],[$case['title'],'case-studies/'.$slug]],$metadata);
page_hero('REAL WORK. SHARED WITH PERMISSION.',$case['title'],$case['summary']);
?>
<section class="section"><article class="container narrow prose case-study-body"><?php if($case['client_name']): ?><p class="article-meta">For <?= e($case['client_name']) ?></p><?php endif; ?><?php if($case['image_path']): ?><figure><img src="<?= e(url($case['image_path'])) ?>" alt="<?= e($case['image_alt']) ?>" loading="lazy"><figcaption><?= e($case['image_alt']) ?></figcaption></figure><?php endif; ?><h2>The challenge</h2><p class="preserved-text"><?= nl2br(e($case['challenge'])) ?></p><h2>What we delivered</h2><p class="preserved-text"><?= nl2br(e($case['solution'])) ?></p><h2>The outcome</h2><p class="preserved-text"><?= nl2br(e($case['outcome'])) ?></p><?php if($case['project_url']): ?><a class="text-link" href="<?= e($case['project_url']) ?>" rel="noopener noreferrer" target="_blank">Visit the project (opens in a new tab) <?= icon('arrow') ?></a><?php endif; ?><div class="actions"><a class="button" href="<?= e(url('contact')) ?>">Discuss Your Project <?= icon('arrow') ?></a></div></article></section><?php page_end(); ?>
