<?php
$cssPath = FCPATH . 'assets/css/style.css';
$cssVersion = file_exists($cssPath) ? filemtime($cssPath) : '1.0.0';
$jsPath = FCPATH . 'assets/js/app.js';
$jsVersion = file_exists($jsPath) ? filemtime($jsPath) : '1.0.0';
?>
<!DOCTYPE html>
<html lang="<?= esc($htmlLang ?? 'es') ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#de5928">
    <title><?= esc($pageTitle ?? 'Teatromuseo - Tótem Interactivo') ?></title>
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>?v=<?= $cssVersion ?>">
    <style>
        :root {
            --paper-texture: url('<?= base_url('assets/img/ui/texture.png') ?>');
        }
    </style>
    <?= $this->renderSection('styles') ?>
</head>
<body class="<?= esc($bodyClass ?? 'totem-app') ?>">
    <div class="orientation-warning">
        <div class="orientation-warning__icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="5" y="2" width="14" height="20" rx="2" ry="2"/>
                <line x1="12" y1="18" x2="12.01" y2="18"/>
            </svg>
        </div>
        <div class="orientation-warning__title"><?= lang('Totem.nav.rotate_title') ?></div>
        <div class="orientation-warning__text"><?= lang('Totem.nav.rotate_text') ?></div>
    </div>

    <div class="kiosk-shell">
        <main class="totem-stage">
            <?= $this->renderSection('content') ?>
        </main>
    </div>
    <script src="<?= base_url('assets/js/app.js') ?>?v=<?= $jsVersion ?>"></script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
