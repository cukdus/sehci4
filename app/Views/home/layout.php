<!doctype html>
<html lang="en">
<head>
<?= view('home/partials/head') ?>
</head>
<body class="<?= esc($bodyClass ?? 'index-page') ?>" <?= isset($bodyAttrs) ? $bodyAttrs : 'data-aos-easing="ease-in-out" data-aos-duration="600" data-aos-delay="0"' ?>>
<?= view('home/partials/navbar', ['headerClass' => $headerClass ?? 'fixed-top']) ?>
<?php $hasMain = is_string($content) && strpos($content, '<main') !== false; ?>
<?php if ($hasMain): ?>
<?= $content ?>
<?php else: ?>
<main class="<?= esc($mainClass ?? '') ?>">
<?= $content ?>
</main>
<?php endif; ?>
<?= view('home/partials/footer') ?>
</body>
</html>