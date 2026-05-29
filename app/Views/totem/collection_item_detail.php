<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
<div class="totem-shell">
    <?= view('totem/partials/topbar', ['nav' => $nav]) ?>
    <main class="content-area">
        <h1><?= lang('Totem.collection.item_title') ?></h1>
        <p>Detalle de ficha: <?= $id ?> (mock).</p>
    </main>
</div>
<?= $this->endSection() ?>
