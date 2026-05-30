<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
<?= view('totem/partials/page_shell', [
    'title' => lang('Nav.main_menu'),
    'content' => view('totem/partials/menu_grid', ['items' => $items])
]) ?>

<?= $this->endSection() ?>
