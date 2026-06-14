<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
<?= view('totem/partials/page_shell', [
    'title' => lang('Nav.main_menu'),
    'titleWidth' => '8.8ch',
    'nav' => $nav ?? [],
    'chromeHidden' => false,
    'content' => view('totem/partials/menu_grid', [
        'items' => $items,
        'showCoda' => true,
        'codaImage' => 'assets/img/menu/collage_referencia.webp',
        'codaAlt' => lang('Splash.collage_alt'),
    ])
]) ?>
<?= $this->endSection() ?>
