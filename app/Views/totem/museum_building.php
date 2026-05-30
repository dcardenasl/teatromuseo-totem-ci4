<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
<?= view('totem/partials/page_shell', [
    'title' => lang('Totem.museum_info.building_title'),
    'content' => '<p>Detalle edificio (mock).</p>',
    'nav' => $nav ?? []
]) ?>
<?= $this->endSection() ?>
