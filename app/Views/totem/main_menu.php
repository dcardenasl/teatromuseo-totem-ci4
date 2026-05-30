<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
<?= view('totem/partials/page_shell', [
    'title' => lang('Totem.nav.main_menu'),
    'content' => view('totem/partials/menu_grid', ['items' => $items]) // Asumiré que el grid es una sección lógica
]) ?>

<?= $this->endSection() ?>
