<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
<div class="totem-shell">
    <?= view('totem/partials/topbar', ['nav' => $nav]) ?>
    <main class="content-area">
        <h1><?= lang('Totem.museum_info.today_title') ?></h1>
        <p>Detalle actualidad (mock).</p>
    </main>
</div>
<?= $this->endSection() ?>
