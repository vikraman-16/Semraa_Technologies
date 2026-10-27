<!doctype html>
<html lang="en-IN">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<?php require __DIR__ . '/seo.php'; ?>
<link rel="icon" type="image/png" sizes="32x32" href="<?= e(url('assets/img/favicon-32.png')) ?>">
<link rel="icon" type="image/png" sizes="16x16" href="<?= e(url('assets/img/favicon-16.png')) ?>">
<link rel="apple-touch-icon" sizes="180x180" href="<?= e(url('assets/img/apple-touch-icon.png')) ?>">
<link rel="manifest" href="<?= e(url('site.webmanifest')) ?>">
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@500;600;700&display=swap">
<link rel="stylesheet" href="<?= e(url('assets/css/style.css')) ?>">
<link rel="stylesheet" href="<?= e(url('assets/css/company.css')) ?>">
<script src="<?= e(url('assets/js/main.min.js')) ?>" defer></script>
</head>
<body class="page-<?= e(explode(':', (string) $key)[0]) ?>"<?= config('preview_mode') ? ' data-preview="true"' : '' ?>>
<?php if (config('preview_mode')): ?><div class="preview-notice" role="note">Website preview · Enquiries and bookings are not active yet.</div><?php endif; ?>
<a class="skip-link" href="#main">Skip to content</a>
<div class="local-presence"><div class="container"><span>Software &amp; web development</span><p>Based near <a href="<?= e(url('locations/tenkasi')) ?>">Tenkasi</a><span aria-hidden="true"> · </span>Serving <a href="<?= e(url('locations/tirunelveli')) ?>">Tirunelveli</a> &amp; nearby areas</p></div></div>
<header class="site-header"><div class="container nav-wrap">
<a class="brand" href="<?= e(url()) ?>" aria-label="Semraa Technologies home"><span class="brand-crop"><img src="<?= e(url('assets/img/logo-nav.webp')) ?>" width="576" height="164" alt="Semraa Technology"></span></a>
<button class="menu-toggle" type="button" aria-expanded="false" aria-controls="primary-nav" aria-label="Open navigation"><span></span><span></span><span></span></button>
<nav id="primary-nav" aria-label="Main navigation">
<?php foreach (['home'=>'Home','about'=>'About','services'=>'Services','products'=>'Products','portfolio'=>'Our Work','blog'=>'Insights','contact'=>'Contact'] as $navKey=>$label): ?>
<a <?= $active === $navKey ? 'aria-current="page"' : '' ?> href="<?= e(url($navKey === 'home' ? '' : $navKey)) ?>"><?= e($label) ?></a>
<?php endforeach; ?>
<a class="button button-small" <?= $key === 'book-demo' ? 'aria-current="page"' : '' ?> href="<?= e(url('book-demo')) ?>">Book a Demo <?= icon('arrow') ?></a>
</nav></div></header>
<main id="main">
<?php if ($crumbs): ?><nav class="breadcrumbs container" aria-label="Breadcrumb"><ol><li><a href="<?= e(url()) ?>">Home</a></li><?php foreach ($crumbs as $i=>$crumb): ?><li><?php if ($i === array_key_last($crumbs)): ?><span aria-current="page"><?= e($crumb[0]) ?></span><?php else: ?><a href="<?= e(url($crumb[1])) ?>"><?= e($crumb[0]) ?></a><?php endif; ?></li><?php endforeach; ?></ol></nav><?php endif; ?>
