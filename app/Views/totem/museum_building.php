<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
<div class="totem-shell">
    <?= view('totem/partials/topbar', ['nav' => $nav]) ?>
    <main class="content-area">
        <h1><?= lang('Totem.museum_info.building_title') ?></h1>
        <p>Detalle edificio (mock).</p>
    </main>
</div>
<?= $this->endSection() ?>
