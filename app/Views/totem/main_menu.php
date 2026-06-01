<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
<?= view('totem/partials/page_shell', [
    'title' => lang('Nav.main_menu'),
    'titleWidth' => '8.8ch',
    'content' => view('totem/partials/menu_grid', [
        'items' => $items,
        'showCoda' => true,
    ])
]) ?>
<?= $this->endSection() ?>
