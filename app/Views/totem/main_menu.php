<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
<?= view('totem/partials/page_shell', [
    'title' => 'Menú Principal',
    'titleWidth' => '8.8ch',
    'content' => view('totem/partials/menu_grid', [
        'items' => $items,
        'showCoda' => true,
    ])
]) ?>
<?= $this->endSection() ?>
