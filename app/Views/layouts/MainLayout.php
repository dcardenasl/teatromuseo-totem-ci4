<!DOCTYPE html>
<html lang="<?= esc($htmlLang ?? 'es') ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#d85d10">
    <title><?= esc($pageTitle ?? 'Teatromuseo - Tótem Interactivo') ?></title>
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
    <style>
        :root {
            --paper-texture: url('<?= base_url('assets/img/ui/texture.png') ?>');
        }
    </style>
    <?= $this->renderSection('styles') ?>
</head>
<body class="<?= esc($bodyClass ?? 'totem-app') ?>">
    <div class="kiosk-shell">
        <main class="totem-stage">
            <?= $this->renderSection('content') ?>
        </main>
    </div>
    <script src="<?= base_url('assets/js/app.js') ?>"></script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
