<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
<?= view('totem/partials/page_shell', [
    'title' => 'Menú<br>Principal',
    'content' => view('totem/partials/menu_grid', ['items' => $items])
]) ?>
<?= $this->endSection() ?>
